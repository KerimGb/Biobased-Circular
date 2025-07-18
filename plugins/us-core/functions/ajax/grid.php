<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'us_ajax_grid_get_current_id' ) AND wp_doing_ajax() ) {
	/**
	 * The filter extracts an id from the passed data for AJAX requests.
	 *
	 * @param int $current_id The current object id.
	 * @return int Returns the object ID on success, otherwise `0` or `-1`.
	 */
	function us_ajax_grid_get_current_id( $current_id ) {
		global $us_page_args;

		// Get id from the transmitted data in AJAX requests
		if ( is_array( $us_page_args ) AND $current_id < 1 ) {
			$page_type = us_arr_path( $us_page_args, 'page_type' );

			// Search Results page ID if set
			if ( $page_type == 'search' AND ( $search_page = us_get_option( 'search_page' ) ) !== 'default' ) {
				return (int) $search_page;
			}
			// 404 page ID if set
			if ( $page_type == '404' AND ( $page_404 = us_get_option( 'page_404' ) ) !== 'default' ) {
				return (int) $page_404;
			}

			return (int) us_arr_path( $us_page_args, 'post_ID', $current_id );
		}

		return $current_id;
	}

	if ( us_arr_path( $_POST, 'action' ) == 'us_ajax_grid' ) {
		add_filter( 'us_get_current_id', 'us_ajax_grid_get_current_id', 1, 1 );
	}
}

/**
 * Ajax method for grids ajax pagination.
 */
add_action( 'wp_ajax_nopriv_us_ajax_grid', 'us_ajax_grid' );
add_action( 'wp_ajax_us_ajax_grid', 'us_ajax_grid' );
function us_ajax_grid() {

	if ( class_exists( 'WPBMap' ) AND method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
		WPBMap::addAllMappedShortcodes();
	}

	// Filtering $template_vars, as is will be extracted to the template as local variables
	$template_vars = shortcode_atts(
		array(
			'columns' => 2,
			'exclude_items' => 'none',
			'filters_args' => NULL,
			'grid_orderby' => NULL,
			'ignore_items_size' => FALSE,
			'img_size' => 'default',
			'items_layout' => 'blog_1',
			'items_offset' => 0,
			'lang' => FALSE,
			'load_animation' => 'none',
			'orderby_query_args' => array(),
			'page_args' => array(),
			'overriding_link' => '{"url":""}',
			'pagination' => 'regular',
			'post_id' => FALSE,
			'query_args' => array(),
			'type' => 'grid',
			'us_grid_post_type' => NULL,
			'us_grid_ajax_index' => FALSE,
			'us_grid_filter_query_string' => NULL,
			'us_grid_index' => FALSE,
		), us_get_HTTP_POST_json( 'template_vars' )
	);

	// Get grid data from all page content by grid caption
	global $us_page_args;
	if ( $page_args = (array) us_arr_path( $template_vars, 'page_args', array() ) ) {
		$us_page_args = $page_args;
	}

	// Get related parameters for getting data, number of records for taxonomy, price range for WooCommerce, etc.
	$filters_args = ! empty( $template_vars['filters_args'] )
		? $template_vars['filters_args']
		: array();
	if ( isset( $template_vars['filters_args'] ) ) {
		unset( $template_vars['filters_args'] );
	}

	// If the parameters were passed from the filter, then recount the number of items
	if ( ! empty( $filters_args['taxonomies_query_args'] ) ) {
		foreach ( $filters_args['taxonomies_query_args'] as &$items ) {
			foreach ( $items as &$item_query_args ) {
				// Add options from Grid Filter
				if ( ! is_null( $template_vars['us_grid_filter_query_string'] ) ) {
					us_apply_grid_filters( $item_query_args, $template_vars['us_grid_filter_query_string'] );
				}
				$item_query_args = us_get_post_count_by_args( $item_query_args );
			}
			unset( $item_query_args );
		}
		unset( $items, $item_query_args );
	}

	// Get min max prices of products, taking into account tax etc.
	if ( function_exists( 'us_wc_get_min_max_price' ) AND ! empty( $filters_args['wc_min_max_price'] ) ) {
		$min_max_price_query_vars = array(
			'tax_query' => us_arr_path( $template_vars, 'query_args.tax_query', array() ),
			'meta_query' => us_arr_path( $template_vars, 'query_args.meta_query', array() ),
		);
		if ( ! is_null( $template_vars['us_grid_filter_query_string'] ) ) {
			us_apply_grid_filters( $min_max_price_query_vars, $template_vars['us_grid_filter_query_string'] );
		}
		$min_max_price = (array) us_wc_get_min_max_price( $min_max_price_query_vars );
		$filters_args['wc_min_max_price'] = array_map( 'floor', $min_max_price );
	}
	if ( ! empty( $filters_args ) AND ! us_amp() ) {
		echo '<div class="w-grid-filter-json-data hidden"' . us_pass_data_to_js( $filters_args ) . '></div>';
	}

	// Switch to main website language for AJAX requests, not to take user's language
	if ( wp_doing_ajax() AND get_user_locale() != get_locale() ) {
		switch_to_locale( get_locale() );
	}

	if ( has_action( 'us_tr_switch_language' ) AND $template_vars['lang'] ) {
		global $sitepress;
		do_action( 'us_tr_switch_language', (string) $template_vars['lang'] );
	}

	if ( $post_id = us_get_current_id() AND is_array( $us_page_args ) ) {
		// Note: Update post_ID to include archive pages.
		if ( ! $page_content = us_get_page_content_for_grid( array_merge( $us_page_args, array( 'post_ID' => $post_id ) ) ) ) {
			wp_send_json_error();
		}

		// Get current grid index
		$us_grid_ajax_index = us_arr_path( $template_vars, 'us_grid_ajax_index' );
		if ( ! is_numeric( $us_grid_ajax_index ) ) {
			$us_grid_ajax_index = 1;
		}

		// If there is no grid then we will return an error
		preg_match_all( '/' . get_shortcode_regex( array( 'us_grid' ) ) . '/', $page_content, $matches );
		if ( ! isset( $matches[0][ $us_grid_ajax_index - 1 ] ) ) {
			wp_send_json_error();
		}

		// Get the relevant shortcode options
		$shortcode_atts_string = $matches[3][ $us_grid_ajax_index - 1 ];
		$shortcode_atts = shortcode_parse_atts( $shortcode_atts_string );

		// Get default and set attributes for a grid
		$shortcode_atts = us_shortcode_atts( $shortcode_atts, 'us_grid' );

		// "Hide on" values are needed for the "No results" block
		global $us_grid_hide_on_states;
		$us_grid_hide_on_states = us_arr_path( $shortcode_atts, 'hide_on_states' );

		if ( $shortcode_atts['post_type'] == 'current_query' ) {
			$allowed_post_types = NULL;

		} elseif ( $shortcode_atts['post_type'] == 'related' ) {
			if ( ! empty( $shortcode_atts['related_post_type'] ) ) {
				$allowed_post_types = explode( ',', $shortcode_atts['related_post_type'] );
			} else {
				$allowed_post_types = array( 'any' );
			}

		} elseif ( in_array( $shortcode_atts['post_type'], array( 'ids', 'current_child_pages' ) ) ) {
			$allowed_post_types = array( 'any' );

		} elseif ( $shortcode_atts['post_type'] == '' ) {
			$shortcode_atts['post_type'] = 'post';
			$allowed_post_types = array( 'post' );

			// In several cases set post type to attachment
		} elseif (
			$shortcode_atts['post_type'] === 'product_gallery' // Product Gallery field
			OR strpos( $shortcode_atts['post_type'], 'acf_gallery' ) !== FALSE // ACF Gallery field
			OR strpos( $shortcode_atts['post_type'], 'us_tile_additional_image' ) !== FALSE // Additional Settings Gallery
		) {
			$template_vars['query_args']['post_type'] = array( 'attachment' );
			$allowed_post_types = array( 'attachment' );

		} else {
			$allowed_post_types = array( $shortcode_atts['post_type'] );
		}

		if (
			$shortcode_atts['post_type'] == 'current_query'
			AND isset( $template_vars['query_args']['post_type'] )
			AND us_post_type_is_available( $template_vars['query_args']['post_type'], array( 'product' ) )
		) {
			$add_wc_hooks = TRUE;
		}

		if ( ! empty( $shortcode_atts['pagination_style'] ) ) {
			$template_vars['pagination_style'] = (int) $shortcode_atts['pagination_style'];
		}

		if ( ! empty( $shortcode_atts['events_calendar_show_past'] ) ) {
			$template_vars['events_calendar_show_past'] = $shortcode_atts['events_calendar_show_past'];
		}

		if ( ! empty( $shortcode_atts['no_items_action'] ) ) {
			$template_vars['no_items_action'] = $shortcode_atts['no_items_action'];
		}

		if ( ! empty( $shortcode_atts['no_items_page_block'] ) ) {
			$template_vars['no_items_page_block'] = $shortcode_atts['no_items_page_block'];
		}

		if ( ! empty( $shortcode_atts['no_items_message'] ) ) {
			$template_vars['no_items_message'] = $shortcode_atts['no_items_message'];
		}
	}

	// Filtering query_args
	if ( isset( $template_vars['query_args'] ) AND is_array( $template_vars['query_args'] ) ) {

		// Query Args keys, that won't be filtered
		$allowed_query_keys = array(

			// Grid listing shortcode requests
			'author_name',
			'us_portfolio_category',
			'us_portfolio_tag',
			'category_name',
			'tax_query',

			// Custom query used to filter products by price.
			'_us_product_meta_lookup_prices',

			// Archive requests
			'year',
			'monthnum',
			'day',
			'cat',
			'tag',
			'product_cat',
			'product_tag',

			// Search requests
			's',

			// Pagination
			'paged',
			'order',
			'orderby',
			'posts_per_page',
			'post__not_in',
			'post__in',
			'post_parent',

			// For excluding 'out of stock' products
			'meta_query',

			// For products sorting
			'order',
			'meta_key',

			// Custom users' queries
			'post_type',
		);

		$taxonomies = us_get_taxonomies( TRUE );
		foreach ( $taxonomies as $taxonomy_name => $taxonomy_title ) {
			if ( ! in_array( $taxonomy_name, $allowed_query_keys ) ) {
				$allowed_query_keys[] = $taxonomy_name;
			}
		}

		// Delete unavailable parameters, only allowed parameters can be applied in the query to the database
		foreach ( $template_vars['query_args'] as $query_key => $query_val ) {
			if ( ! in_array( $query_key, $allowed_query_keys ) ) {
				unset( $template_vars['query_args'][ $query_key ] );
			}
		}

		// Get grid available post types as allowed for ajax
		$allowed_post_types = array_keys( us_grid_available_post_types() );

		// Exclude inaccessible post types for search
		if ( ! empty( $template_vars['query_args'] ) AND isset( $template_vars['query_args']['s'] ) ) {
			$exclude_post_types = us_get_option( 'exclude_post_types_in_search' );

			// Fallback for var type
			if ( is_array( $exclude_post_types ) ) {
				$exclude_post_types = implode( ',', $exclude_post_types );
			}

			foreach ( $allowed_post_types as $key => $item ) {
				if ( strpos( $exclude_post_types, $item ) !== FALSE ) {
					unset( $allowed_post_types[ $key ] );
				}
			}
		}

		if ( isset( $template_vars['query_args']['post_type'] ) ) {
			$is_allowed_post_type = TRUE;
			if ( is_array( $template_vars['query_args']['post_type'] ) ) {
				foreach ( $template_vars['query_args']['post_type'] as $post_type ) {
					if ( ! in_array( $post_type, $allowed_post_types ) ) {
						$is_allowed_post_type = FALSE;
						break;
					}
				}
			} elseif ( ! in_array( $template_vars['query_args']['post_type'], $allowed_post_types ) ) {
				$is_allowed_post_type = FALSE;
			}

			if ( ! $is_allowed_post_type ) {
				unset( $template_vars['query_args']['post_type'] );
			}
		}

		// For grid related post_type
		if (
			in_array( us_arr_path( $template_vars, 'us_grid_post_type' ), array( 'related', 'ids', 'current_child_pages' ) )
			AND empty( $template_vars['query_args']['post_type'] )
		) {
			$template_vars['query_args']['post_type'] = 'any';
		}
		if ( ! isset( $template_vars['query_args']['s'] ) AND ! isset( $template_vars['query_args']['post_type'] ) ) {
			$template_vars['query_args']['post_type'] = 'post';
		}

		// Providing proper post statuses
		if (
			! empty( $template_vars['query_args']['post_type'] )
			AND (
				$template_vars['query_args']['post_type'] == 'attachment'
				OR (
					is_array( $template_vars['query_args']['post_type'] )
					AND in_array( 'attachment', $template_vars['query_args']['post_type'] )
					AND count( $template_vars['query_args']['post_type'] ) == 1
				)
			)
		) {
			$template_vars['query_args']['post_status'] = 'inherit';
			$template_vars['query_args']['post_mime_type'] = 'image';
		} else {
			$template_vars['query_args']['post_status'] = array( 'publish' => 'publish' );
			$template_vars['query_args']['post_status'] += (array) get_post_stati( array( 'public' => TRUE ) );

			// Add private states if user is capable to view them
			if ( is_user_logged_in() AND current_user_can( 'read_private_posts' ) ) {
				$template_vars['query_args']['post_status'] += (array) get_post_stati( array( 'private' => TRUE ) );
			}
			$template_vars['query_args']['post_status'] = array_values( $template_vars['query_args']['post_status'] );
		}

		// Exclude sticky posts from rand query after 1 page
		if ( isset( $template_vars['query_args']['orderby'] ) AND ! ( is_array( $template_vars['query_args']['orderby'] ) ) ) {
			if ( ( substr( $template_vars['query_args']['orderby'], 0, 4 ) == 'rand' ) AND ( $template_vars['query_args']['paged'] > '1' ) ) {
				$sticky_posts = get_option( 'sticky_posts' );
				$template_vars['query_args']['ignore_sticky_posts'] = TRUE;
				foreach ( $sticky_posts as $sticky_post_id ) {
					$template_vars['query_args']['post__not_in'][] = $sticky_post_id;
				}
			}
		}

		// Show hide empty for Post views counter
		if (
			isset( $template_vars['query_args']['orderby'] )
			AND $template_vars['query_args']['orderby'] == 'post_views'
			AND class_exists( 'Post_Views_Counter' )
		) {
			$template_vars['query_args']['views_query']['hide_empty'] = FALSE;
		}
	}

	// Applying orderby options
	if (
		NULL !== ( $grid_orderby = us_arr_path( $template_vars, 'grid_orderby' ) )
		AND $orderby_params = (array) us_grid_orderby_str_to_params( $grid_orderby )
	) {
		$orderby_params['post_type'] = us_arr_path( $template_vars, 'query_args.post_type', 'post' );
		us_grid_set_orderby_to_query_args( $template_vars['orderby_query_args'], $orderby_params );
		unset( $orderby_params );
	}

	// Apply parameters received through AJAX
	if ( ! is_null( $template_vars['us_grid_filter_query_string'] ) ) {
		us_apply_grid_filters( $template_vars['query_args'], $template_vars['us_grid_filter_query_string'] );
	}

	if ( class_exists( 'woocommerce' ) AND is_object( wc() ) ) {
		add_action( 'pre_get_posts', 'us_ajax_pre_get_posts' );

		// Apply WooCommerce filters only to the current query.
		function us_ajax_pre_get_posts( $wp_query ) {
			global $us_is_page_block_in_no_results; // defines if it is Reusable Block in "no results"
			global $us_page_args;
			if ( ! is_array( $us_page_args ) ) {
				$us_page_args = array();
			}

			$page_type = us_arr_path( $us_page_args, 'page_type' );
			$template_vars = us_get_HTTP_POST_json( 'template_vars' );

			if (
				! $us_is_page_block_in_no_results
				AND method_exists( wc()->query, 'product_query' )
				AND us_arr_path( $template_vars, 'us_grid_post_type' ) == 'current_query'
				AND in_array( 'product', (array) $wp_query->get( 'post_type' ) )
				AND (
					$page_type == 'shop'
					OR $page_type == 'archive'
				)
			) {
				wc()->query->product_query( $wp_query );
			}
		}

		// Apply WooCommerce product ordering if set.
		if ( ! empty( $add_wc_hooks ) AND empty( $grid_orderby ) ) {
			foreach ( array( 'order', 'orderby' ) as $param ) {
				if ( ! isset( $_GET[ $param ] ) AND ! empty( $template_vars['query_args'][ $param ] ) ) {
					$_GET[ $param ] = (string) $template_vars['query_args'][ $param ];
				}
			}
		}
	}

	if ( ! function_exists( 'us_woocommerce_get_catalog_ordering_args' ) ) {
		add_filter( 'woocommerce_get_catalog_ordering_args', 'us_woocommerce_get_catalog_ordering_args', 100, 1 );

		/**
		 * Sorting check and correction if necessary.
		 *
		 * @param array $args The arguments
		 * @return array
		 */
		function us_woocommerce_get_catalog_ordering_args( $args ) {
			$template_vars = us_get_HTTP_POST_json( 'template_vars' );
			if ( empty( $template_vars['grid_orderby'] ) ) {
				foreach ( array( 'order', 'orderby' ) as $param ) {
					if ( ! empty( $template_vars['query_args'][ $param ] ) ) {
						$args[ $param ] = (string) $template_vars['query_args'][ $param ];
					}
				}
			}

			return $args;
		}
	}

	if ( ! function_exists( 'us_pre_get_posts_for_search' ) AND empty( $grid_orderby ) ) {
		add_action( 'pre_get_posts', 'us_pre_get_posts_for_search', 101, 1 );

		/**
		 * Check order for search.
		 *
		 * @param WP_Query $wp_query The WP_Query instance (passed by reference).
		 */
		function us_pre_get_posts_for_search( $wp_query ) {
			$order = NULL;
			if ( ! empty( $wp_query->query['order'] ) ) {
				$order = $wp_query->query['order'];
			}
			if ( $wp_query->get( 's' ) AND $wp_query->get( 'order' ) !== $order ) {
				$wp_query->set( 'order', $order );
			}
		}
	}

	if ( ! function_exists( 'us_posts_orderby_for_search' ) AND empty( $grid_orderby ) ) {
		add_filter( 'posts_orderby', 'us_posts_orderby_for_search', 10, 2 );

		/**
		 * Verification and preparation of search queries.
		 *
		 * @param string $orderby The ORDER BY clause of the query.
		 * @param WP_Query $wp_query The WP_Query instance (passed by reference).
		 * @return string orderby Returns the orderby string.
		 */
		function us_posts_orderby_for_search( $orderby, $wp_query ) {
			global $wpdb;

			// Adjust search query to match from internal wp_query regeneration
			if ( $wp_query->is_search AND $order = $wp_query->get( 'order' ) ) {
				$order = esc_sql( $order );

				// Enable search query for proper sorting and pagination.
				if ( $search = (string) $wp_query->get( 's' ) ) {
					$search = ' LIKE \'%'. $wpdb->esc_like( $search ) .'%\'';
				}

				return "{$wpdb->posts}.post_title {$search} {$order}, {$wpdb->posts}.post_date {$order}";
			}

			return $orderby;
		}
	}

	if ( ! function_exists( 'us_woocommerce_page_title' ) ) {
		add_filter( 'woocommerce_page_title', 'us_woocommerce_page_title', 501, 1 );

		/**
		 * Handler for redefining the page name in AJAX requests
		 *
		 * @param string $page_title The page title
		 * @return string
		 */
		function us_woocommerce_page_title( $page_title ) {
			if ( is_tax() ) {
				return get_the_title( wc_get_page_id( 'shop' ) );
			}
			return $page_title;
		}
	}

	us_load_template( 'templates/us_grid/listing', $template_vars );

	// We don't use JSON to reduce data size
	die;
}
