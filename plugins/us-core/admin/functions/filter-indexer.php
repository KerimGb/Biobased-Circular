<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'US_Filter_Indexer' ) ) {
	/**
	 * Class for indexing filters.
	 */
	class US_Filter_Indexer {

		/**
		 * Number of posts to index before updating progress.
		 *
		 * @var int
		 */
		protected $chunk_size = 50;

		/**
		 * Whether a temporary table is active.
		 *
		 * @var string
		 */
		private $table;

		/**
		 * wp_insert_post running?
		 *
		 * @var bool
		 */
		private $is_post_saving = FALSE;

		/**
		 * Cached terms hierarchy
		 *
		 * @var array
		 */
		public $term_hierarchy_cache;

		/**
		 * Constructs a new instance.
		 */
		function __construct() {

			$this->set_table( 'auto' );
			$this->run_cron();

			if ( ! static::is_table_created() ) {
				$this->create_table();
			}

			// Event listeners.
			if ( us_get_option( 'enable_auto_filter_reindex', FALSE ) ) {
				add_action( 'save_post', array( $this, 'save_post' ) );
				add_action( 'delete_post', array( $this, 'delete_post' ) );
				add_action( 'edited_term', array( $this, 'edit_term' ), 10, 3 );
				add_action( 'delete_term', array( $this, 'delete_term' ), 10, 3 );
				add_action( 'set_object_terms', array( $this, 'set_object_terms' ) );
				add_action( 'wp_insert_post_parent', array( $this, 'is_wp_insert_post' ) );
			}

			add_action( 'us_filter_indexer_cron', array( $this, 'get_progress' ) );
			add_action( 'us_filter_indexer_resume_index', array( $this, 'resume_index' ) );

			// Delete index tables
			register_deactivation_hook( US_CORE_DIR . 'us-core.php', array( $this, 'delete_tables' ) );
		}

		/**
		 * @return self Returns an instance of the current class.
		 */
		static function instance() {
			static $instance;
			if ( ! isset( $instance ) ) {
				$instance = new self;
			}

			return $instance;
		}

		/**
		 * Cron task.
		 */
		private function run_cron() {
			if ( ! wp_next_scheduled( 'us_filter_indexer_cron' ) ) {
				wp_schedule_single_event( time() + 300, 'us_filter_indexer_cron' );
			}
		}

		/**
		 * Determines if indexing.
		 *
		 * @return bool True if indexing, False otherwise.
		 */
		function is_indexing() {
			return get_option( 'us_filter_indexer_indexing', '' ) !== '';
		}

		/**
		 * Determines if table created.
		 *
		 * @return bool True if table created, False otherwise.
		 */
		static function is_table_created() {
			static $is_created;
			if ( ! isset( $is_created ) ) {
				global $wpdb;
				$is_created = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}us_filter_index'" ) !== NULL;
			}

			return $is_created;
		}

		/**
		 * Create a table.
		 */
		private function create_table() {
			global $wpdb;

			$int = apply_filters( 'us_filter_indexer_use_bigint', FALSE ) ? 'bigint' : 'int';

			$charset_collate = $wpdb->get_charset_collate();

			$table_structure = "
				CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}us_filter_index` (
					`id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
					`post_id` $int UNSIGNED,
					`filter_name` varchar(50) DEFAULT NULL,
					`filter_value` varchar(50) DEFAULT NULL,
					`term_id` $int UNSIGNED DEFAULT '0',
					`parent_id` $int UNSIGNED DEFAULT '0',
					`depth` tinyint UNSIGNED DEFAULT '0',
					`variation_id` $int UNSIGNED DEFAULT '0',
					PRIMARY KEY (`id`),
					INDEX `post_id` (`post_id`),
					INDEX `filter_name` (`filter_name`),
					INDEX `term_id` (`term_id`)
				) $charset_collate
			";

			if ( ! function_exists( 'dbDelta' ) ) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}

			dbDelta( $table_structure );
		}

		/**
		 * Delete index tables.
		 */
		function delete_tables() {
			global $wpdb;

			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}us_filter_index" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}us_filter_index_temp" );
		}

		/**
		 * Set either the index or index_temp table.
		 *
		 * @param string $table 'auto', 'index' or 'index_temp'.
		 */
		protected function set_table( $table = 'auto' ) {
			global $wpdb;

			if ( $table == 'auto' ) {
				$table = $this->is_indexing() ? 'index_temp' : 'index';
			}

			$this->table = $wpdb->prefix . 'us_filter_' . $table;
		}

		/**
		 * Index table management.
		 *
		 * @param string $action The action.
		 */
		private function manage_temp_table( $action = 'create' ) {
			global $wpdb;

			$table = $wpdb->prefix . 'us_filter_index';

			if ( $action == 'create' ) {
				$wpdb->query( "CREATE TABLE {$table}_temp LIKE $table" );
				$this->set_table( 'index_temp' );

			} elseif ( $action == 'replace' ) {
				$wpdb->query( "TRUNCATE TABLE $table" );
				$wpdb->query( "INSERT INTO $table SELECT * FROM {$table}_temp" );

			} elseif ( $action == 'delete' ) {
				$wpdb->query( "DROP TABLE IF EXISTS {$table}_temp" );
				$this->set_table( 'index' );
			}
		}

		/**
		 * Get indexer transient variables.
		 *
		 * @param mixed $name
		 * @return mixed
		 */
		protected function get_transient( $name = FALSE ) {
			$transients = get_option( 'us_filter_indexer_transients' );

			if ( ! empty( $transients ) ) {
				$transients = json_decode( $transients, TRUE );
				if ( $name ) {
					return $transients[ $name ] ?? FALSE;
				}

				return $transients;
			}

			return FALSE;
		}

		/**
		 * Get last indexed date
		 *
		 * @return string
		 */
		function get_last_indexed() {
			$last_indexed = get_option( 'us_filter_indexer_last_indexed' );
			if ( $last_indexed ) {
				return sprintf( us_translate( '%s ago' ), human_time_diff( $last_indexed ) );
			}
			return '&ndash;';
		}

		/**
		 * Get amount of database table rows.
		 *
		 * @return string
		 */
		function get_row_count() {
			global $wpdb;
			if ( static::is_table_created() ) {
				return number_format( $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->prefix}us_filter_index`" ) );
			}
			return 0;
		}

		/**
		 * Get an array of post IDs to index.
		 *
		 * @param mixed $post_id
		 */
		private function get_post_ids_to_index( $post_id = FALSE ) {

			$post_types = array_keys( us_grid_available_post_types( TRUE, FALSE ) );

			$query_args = array(
				'post_type' => $post_types,
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'fields' => 'ids',
				'orderby' => 'ID',
				'cache_results' => FALSE,
				'no_found_rows' => TRUE,
				'suppress_filters' => TRUE,
			);

			if ( is_int( $post_id ) ) {
				$query_args['p'] = $post_id;
				$query_args['posts_per_page'] = 1;
			}

			$query_args = apply_filters( 'us_filter_indexer_query_args', $query_args );

			$query = new WP_Query( $query_args );

			return (array) $query->posts;
		}

		/**
		 * Get filter params used on a website
		 *
		 * @param array
		 */
		function get_used_filter_params() {

			static $params = array();
			if ( ! empty( $params ) ) {
				return $params;
			}

			$used_params = array();

			// Check in database the relevant postmeta
			global $wpdb;
			$sql = "
				SELECT meta_value
				FROM {$wpdb->postmeta}
				WHERE meta_key = '_us_faceted_filter_items'
			";
			foreach( $wpdb->get_results( $sql, ARRAY_A ) as $result ) {
				$used_params = array_merge( $used_params, maybe_unserialize( $result['meta_value'] ) );
			}
			$used_params = array_unique( $used_params );

			if ( empty( $used_params ) ) {
				return array();
			}

			foreach( us_get_list_filter_params() as $name => $param ) {
				if ( in_array( $name, $used_params ) ) {
					$params[ $name ] = $param;
				}
			}

			return $params;
		}

		/**
		 * Get the indexing completion percentage.
		 *
		 * @return mixed The decimal percentage, or -1.
		 */
		function get_progress() {
			$return = -1;

			$num_indexed = (int) $this->get_transient( 'num_indexed' );
			$num_total = (int) $this->get_transient( 'num_total' );
			$retries = (int) $this->get_transient( 'retries' );
			$touch = (int) $this->get_transient( 'touch' );

			if ( $num_total > 0 ) {

				// Resume a stalled indexer
				if ( ( time() - $touch ) > 60 ) {
					$post_args = array(
						'blocking' => FALSE,
						'timeout' => 0.02,
						'body' => array(
							'action' => 'us_filter_indexer_resume_index',
							'offset' => $num_indexed,
							'retries' => $retries + 1,
							'touch' => $touch
						)
					);

					$post_args = apply_filters( 'us_filter_indexer_remote_post_args', $post_args );

					wp_remote_post( admin_url( 'admin-ajax.php' ), $post_args );
				}

				// Calculate the percent completion
				if ( $num_indexed != $num_total ) {
					$return = floor( 100 * ( $num_indexed / $num_total ) );
				}
			}

			return $return;
		}

		/**
		 * Get an array of terms hierarchy
		 * @param string $taxonomy The taxonomy name
		 * @return array Term information
		 */
		private function get_term_hierarchy( $taxonomy ) {

			// Empty result if taxonomy is not hierarchical
			if ( $tax = get_taxonomy( $taxonomy ) AND ! $tax->hierarchical ) {
				return array();
			}

			if ( isset( $this->term_hierarchy_cache[ $taxonomy ] ) ) {
				return $this->term_hierarchy_cache[ $taxonomy ];
			}

			$result = array();
			$parents = array();

			global $wpdb;
			$sql = "
				SELECT
					`t`.`term_id`, `t`.`name`, `t`.`slug`, `tt`.`parent`
				FROM `{$wpdb->term_taxonomy}` tt
				INNER JOIN `{$wpdb->terms}` t
					ON `t`.`term_id` = `tt`.`term_id`
				WHERE `tt`.`taxonomy` = %s
			";

			$terms = $wpdb->get_results( $wpdb->prepare( $sql, $taxonomy ) );

			// Get term parents
			foreach ( $terms as $term ) {
				$parents[ $term->term_id ] = $term->parent;
			}

			// Build the term array
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = array(
					'term_id' => $term->term_id,
					'slug' => $term->slug,
					'parent_id' => $term->parent,
					'depth' => 0,
				);

				$current_parent = $term->parent;

				while ( 0 < (int) $current_parent ) {
					$current_parent = $parents[ $current_parent ];
					$result[ $term->term_id ]['depth']++;

					// Prevent an infinite loop
					if ( 30 < $result[ $term->term_id ]['depth'] ) {
						break;
					}
				}
			}

			$this->term_hierarchy_cache[ $taxonomy ] = $result;

			return $result;
		}

		/**
		 * Update the index when posts get saved.
		 *
		 * @param int $post_id The post id.
		 */
		function save_post( $post_id ) {

			if (
				( defined( 'DOING_AUTOSAVE' ) AND DOING_AUTOSAVE )
				OR get_post_status( $post_id ) === 'auto-draft'
				OR wp_is_post_revision( $post_id ) !== FALSE
			) {
				return;
			}

			$this->index( $post_id );
			$this->is_post_saving = FALSE;
		}

		/**
		 * Update the index when posts get deleted.
		 *
		 * @param int $post_id The post id.
		 */
		function delete_post( $post_id ) {
			global $wpdb;

			$wpdb->query( "DELETE FROM {$this->table} WHERE post_id = $post_id" );
		}

		/**
		 * Update the index when terms get saved.
		 *
		 * @param int $term_id Term id.
		 * @param int $tt_id Term taxonomy ID.
		 * @param string $taxonomy The taxonomy.
		 */
		function edit_term( $term_id, $tt_id, $taxonomy ) {
			global $wpdb;

			$used_params = $this->get_used_filter_params();

			// TODO: param_name can be differ from taxonomy name
			if ( ! isset( $used_params[ $taxonomy ] ) ) {
				return;
			}

			$term = get_term( $term_id, $taxonomy );

			$update_indexes_query = "
				UPDATE `{$this->table}`
				SET `filter_value` = %s, `parent_id` = %d,
				WHERE `filter_name` = %s AND `term_id` = %d
			";
			$wpdb->query( $wpdb->prepare( $update_indexes_query, $term->slug, $term->parent, $taxonomy, $term_id ) );
		}

		/**
		 * Update the index when terms get deleted.
		 *
		 * @param int $term_id Term id.
		 * @param int $tt_id Term taxonomy ID.
		 * @param string $taxonomy The taxonomy.
		 */
		function delete_term( $term_id, $tt_id, $taxonomy ) {
			global $wpdb;

			$wpdb->query( "DELETE FROM {$this->table} WHERE term_id = $term_id" );
		}

		/**
		 * We're hijacking wp_insert_post_parent.
		 *
		 * @param WP_Post $post_parent The post parent.
		 * @return WP_Post
		 */
		function is_wp_insert_post( $post_parent ) {
			$this->is_post_saving = TRUE;
			return $post_parent;
		}

		/**
		 * Support for manual taxonomy associations.
		 *
		 * @param int $object_id The object id.
		 */
		function set_object_terms( $object_id ) {
			if ( $this->is_post_saving ) {
				return;
			}
			$this->index( $object_id );
		}

		/**
		 * Resume stalled filters indexer.
		 */
		function resume_index() {
			$touch = (int) $this->get_transient( 'touch' );
			if ( $touch > 0 AND $_POST['touch'] == $touch ) {
				$this->index();
			}
			exit;
		}

		/**
		 * Build the index.
		 *
		 * @param mixed $post_id The post ID (set to FALSE to index everything).
		 */
		function index( $post_id = FALSE ) {

			global $wpdb;

			$index_all = ( $post_id === FALSE );

			if ( $index_all ) {

				// Query Monitor to cease operating for the remainder of the page generation.
				// It will detach itself from further data collection, discard any data
				// it’s collected so far, and skip the output of its information.
				if ( class_exists( 'QueryMonitor' ) ) {
					do_action( 'qm/cease' );
				}

				do_action( 'us_filter_indexer_index_all' );

				// PHP sessions are blocking, so close if active
				if ( session_status() === PHP_SESSION_ACTIVE ) {
					session_write_close();
				}

				// Bypass the PHP timeout
				ini_set( 'max_execution_time', 0 );

				// Prevent multiple indexing processes
				$touch = (int) $this->get_transient( 'touch' );

				if ( $touch > 0 ) {

					// Run only if the indexer is inactive or stalled
					if ( ( time() - $touch ) < 60 ) {
						exit;
					}

					// Create temp table
				} else {
					$this->manage_temp_table( 'create' );
				}

				// Index a single post
			} else if ( is_int( $post_id ) ) {

				// Clear table values
				$wpdb->query( "DELETE FROM {$this->table} WHERE post_id = $post_id" );

				// Exit
			} else {
				return;
			}

			// Resume indexing?
			$offset = (int) ( $_POST['offset'] ?? 0 );
			$attempt = (int) ( $_POST['retries'] ?? 0 );

			if ( 0 < $offset ) {
				$post_ids = json_decode( get_option( 'us_filter_indexer_indexing' ), TRUE );

			} else {
				$post_ids = $this->get_post_ids_to_index( $post_id );

				// Store post IDs
				if ( $index_all ) {
					update_option( 'us_filter_indexer_indexing', json_encode( $post_ids ) );
				}
			}

			// Count total posts
			$num_total = count( $post_ids );

			foreach ( $post_ids as $counter => $post_id ) {

				// Advance until we reach the offset
				if ( $counter < $offset ) {
					continue;
				}

				// Update the progress bar
				if ( $index_all ) {
					if ( ( $counter % $this->chunk_size ) === 0 ) {
						$num_retries = (int) $this->get_transient( 'retries' );

						// Exit if newer retries exist
						if ( $attempt < $num_retries ) {
							exit;
						}

						// Exit if the indexer was cancelled
						wp_cache_delete( 'us_filter_indexer_indexing_cancelled', 'options' );

						if ( get_option( 'us_filter_indexer_indexing_cancelled', 'no' ) === 'yes' ) {
							update_option( 'us_filter_indexer_transients', '' );
							update_option( 'us_filter_indexer_indexing', '' );
							$this->manage_temp_table( 'delete' );
							exit;
						}

						$transients = array(
							'num_indexed' => $counter,
							'num_total' => $num_total,
							'retries' => $attempt,
							'touch' => time(),
						);
						update_option( 'us_filter_indexer_transients', json_encode( $transients ) );
					}
				}

				// If the indexer stalled, start from the last valid chunk
				if ( $offset > 0 AND ( $counter - $offset < $this->chunk_size ) ) {
					$wpdb->query( "DELETE FROM {$this->table} WHERE post_id = $post_id" );
				}

				$this->index_post( $post_id );
			}

			// Indexing complete
			if ( $index_all ) {
				update_option( 'us_filter_indexer_last_indexed', time(), 'no' );
				update_option( 'us_filter_indexer_transients', '', 'no' );
				update_option( 'us_filter_indexer_indexing', '', 'no' );

				$this->manage_temp_table( 'replace' );
				$this->manage_temp_table( 'delete' );
			}

			do_action( 'us_filter_indexer_complete' );
		}

		/**
		 * Index an individual post.
		 *
		 * @param int $post_id
		 * @param array $filter_items
		 */
		protected function index_post( int $post_id ) {

			// Force WPML to change the language
			do_action( 'us_filter_indexer_index_post', array( 'post_id' => $post_id ) );

			// Set default insert_row() values
			$defaults = array(
				'post_id' => $post_id,
				'filter_name' => '',
				'filter_value' => '',
				'term_id' => 0,
				'parent_id' => 0,
				'depth' => 0,
				'variation_id' => 0,
			);

			// Bypass default indexing
			$bypass = apply_filters( 'us_filter_indexer_is_post_indexing', $post_id );
			if ( empty( $bypass ) ) {
				return;
			}

			$indexes = array();

			foreach( $this->get_used_filter_params() as $name => $param ) {

				$source_type = $param['source_type'] ?? '';
				$source_name = $param['source_name'] ?? '';

				// Taxonomy Terms indexing
				if ( $source_type == 'tax' ) {
					$used_terms = array();

					$term_objects = wp_get_object_terms( $post_id, $source_name );
					if ( is_wp_error( $term_objects ) ) {
						continue;
					}

					// Get the hierarchy of taxonomy terms
					$term_hierarchy = $this->get_term_hierarchy( $source_name );

					foreach ( $term_objects as $term ) {

						// Prevent duplicate terms
						if ( isset( $used_terms[ $term->term_id ] ) ) {
							continue;
						}
						$used_terms[ $term->term_id ] = TRUE;

						// Handle hierarchical taxonomies
						$depth = 0;
						if ( $term_hierarchy ) {
							$term_info = $term_hierarchy[ $term->term_id ];
							$depth = $term_info['depth'];
						}

						$indexes[] = array_merge(
							$defaults,
							array(
								'filter_name' => $name,
								'filter_value' => $term->slug,
								'term_id' => $term->term_id,
								'parent_id' => $term->parent,
								'depth' => $depth,
							)
						);

						// Index parents
						while ( $depth > 0 ) {
							$term_id = $term_info['parent_id'];
							$term_info = $term_hierarchy[ $term_id ];
							$depth = $depth - 1;

							if ( ! isset( $used_terms[ $term_id ] ) ) {
								$used_terms[ $term_id ] = TRUE;

								$indexes[] = array_merge(
									$defaults,
									array(
										'filter_name' => $name,
										'filter_value' => $term_info['slug'],
										'term_id' => $term_id,
										'parent_id' => $term_info['parent_id'],
										'depth' => $depth,
									)
								);
							}
						}
					}

					// Post Attributes
				} elseif ( $source_type == 'post' ) {
					if ( $post = get_post( $post_id ) ) {
						$indexes[] = array_merge(
							$defaults,
							array(
								'filter_name' => $name,
								'filter_value' => $post->{$name},
							)
						);
					}

					// Custom Fields indexing
				} elseif ( $source_type == 'meta' ) {
					if ( empty( $source_name ) ) {
						continue;
					}

					$values = get_post_meta( $post_id, $source_name, FALSE );

					foreach ( $values as $value ) {

						// TODO: add support for fields with 'value_type' => 'bool'

						// The value can be array, so we need to extract its values
						if ( ! is_array( $value ) ) {
							$value = array( $value );
						}

						foreach ( $value as $val ) {
							if ( $val === '' ) {
								continue;
							}
							$indexes[] = array_merge(
								$defaults,
								array(
									'filter_name' => $name,
									'filter_value' => $val,
								)
							);
						}
					}
				}
			}

			// Allow for custom indexing
			$indexes = (array) apply_filters( 'us_filter_indexer_insert_indexes', $indexes, $defaults );

			// Allow hooks to bypass the indexes insertion
			if ( empty( $indexes ) ) {
				return;
			}

			global $wpdb;

			// Prepare values
			foreach( $indexes as &$index ) {
				$index = $wpdb->prepare( '(%d, %s, %s, %d, %d, %d, %d)',
					$index['post_id'],
					$index['filter_name'],
					$index['filter_value'],
					$index['term_id'],
					$index['parent_id'],
					$index['depth'],
					$index['variation_id']
				);
			}
			unset( $index );

			// Save indexes to DB
			$wpdb->query( "
				INSERT INTO `{$this->table}` (`post_id`, `filter_name`, `filter_value`, `term_id`, `parent_id`, `depth`, `variation_id`)
				VALUES " . implode( ',', $indexes ) . ";
			" );
		}
	}

	// Init.
	US_Filter_Indexer::instance();
}

if ( ! function_exists( 'us_define_tables_in_wpdb' ) ) {
	add_action( 'init', 'us_define_tables_in_wpdb' );

	/**
	 * Add tables to $wpdb object.
	 */
	function us_define_tables_in_wpdb() {
		global $wpdb;

		// List of tables without prefixes.
		$tables = array(
			'us_filter_index',
		);

		foreach ( $tables as $name ) {
			$wpdb->$name = $wpdb->prefix . $name;
		}
	}
}

if ( ! function_exists( 'us_index_filters_by_ajax' ) ) {

	if ( wp_doing_ajax() ) {
		add_action( 'wp_ajax_us_index_filters', 'us_index_filters_by_ajax' );
	}

	/**
	 * Indexing process by AJAX.
	 */
	function us_index_filters_by_ajax() {
		if ( ! check_ajax_referer( __FUNCTION__, '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
		}

		$us_filter_indexer = US_Filter_Indexer::instance();

		// Get indexer action
		$indexerAction = (string) us_arr_path( $_POST, 'indexerAction' );
		if ( ! in_array( $indexerAction, array( 'start-index', 'heartbeat' ) ) ) {
			$indexerAction = 'stop-index';
		}

		$response = array();

		// Rebuild the index table
		if ( $indexerAction == 'start-index' ) {

			update_option( 'us_filter_indexer_indexing_cancelled', 'no', 'no' );

			$us_filter_indexer->index();

			$response['status'] = 'completed';

			// Keep track of indexing progress
		} else if ( $indexerAction == 'heartbeat' ) {
			$progress = $us_filter_indexer->get_progress();

			if ( $progress !== -1 ) {
				$response['message'] = sprintf( __( 'Indexing' ) . ' %s%%', $progress );
			} else {
				$response['status'] = 'completed';
			}

			// Stop indexing filters
		} else {
			update_option( 'us_filter_indexer_indexing_cancelled', 'yes' );

			$response = array(
				'message' => __( 'Indexing cancelled', 'us' ),
				'last_indexed' => $us_filter_indexer->get_last_indexed(),
				'status' => 'cancelled',
			);
		}

		// Indexing completed
		if ( us_arr_path( $response, 'status' ) == 'completed' ) {
			$response += array(
				'message' => __( 'Indexing completed', 'us' ),
				'last_indexed' => $us_filter_indexer->get_last_indexed(),
				'row_count' => $us_filter_indexer->get_row_count(),
			);
		}

		wp_send_json_success( $response );
	}
}
