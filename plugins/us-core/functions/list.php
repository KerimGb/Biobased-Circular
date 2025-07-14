<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Method for lists: List Filter, List Order, List Search, Post List, Product List
 */

if ( ! function_exists( 'us_get_list_filter_params' ) ) {
	/**
	 * Generate params for filtering with unique names (used in URL)
	 */
	function us_get_list_filter_params() {

		static $params = array();

		if ( ! empty( $params ) ) {
			return apply_filters( 'us_get_list_filter_params', $params );
		}

		// Predefined post params
		$params = array(
			'post_type' => array(
				'label' => us_translate( 'Post Type' ),
				'group' => us_translate( 'Post Attributes' ),
				'source_type' => 'post',
				'source_name' => 'type',
			),
			'post_author' => array(
				'label' => us_translate( 'Author' ),
				'group' => us_translate( 'Post Attributes' ),
				'source_type' => 'post',
				'source_name' => 'author',
			),
			'post_date' => array(
				'label' => __( 'Date of creation', 'us' ),
				'group' => us_translate( 'Post Attributes' ),
				'source_type' => 'post',
				'source_name' => 'date',
				'value_type' => 'date',
			),
			'post_modified' => array(
				'label' => __( 'Date of update', 'us' ),
				'group' => us_translate( 'Post Attributes' ),
				'source_type' => 'post',
				'source_name' => 'date_modified',
				'value_type' => 'date',
			),
		);

		// Predefined WooCommerce params
		if ( class_exists( 'woocommerce' ) ) {
			$params += array(

				// Price and Stock Status are custom fields, so keep the "meta" source type
				'price' => array(
					'label' => us_translate( 'Price', 'woocommerce' ),
					'group' => us_translate( 'WooCommerce', 'woocommerce' ),
					'source_type' => 'meta',
					'source_name' => '_price',
					'value_type' => 'numeric',
				),
				'instock' => array(
					'label' => us_translate( 'Stock status', 'woocommerce' ),
					'group' => us_translate( 'WooCommerce', 'woocommerce' ),
					'source_type' => 'meta',
					'source_name' => '_stock_status',
					'value_type' => 'bool',
					'bool_value_label' => us_translate( 'In stock', 'woocommerce' ),
					'bool_value' => 'instock', // used instead of the default "1" value
				),

				// Definition Onsale and Featured products is more complex, so make them with "woo" source type
				'onsale' => array(
					'label' => us_translate( 'On Sale', 'woocommerce' ),
					'group' => us_translate( 'WooCommerce', 'woocommerce' ),
					'source_type' => 'woo', // used for custom logic in wp_query args
					'source_name' => 'onsale',
					'value_type' => 'bool',
					'bool_value_label' => us_translate( 'On-sale products', 'woocommerce' ),
				),
				'featured' => array(
					'label' => us_translate( 'Featured', 'woocommerce' ),
					'group' => us_translate( 'WooCommerce', 'woocommerce' ),
					'source_type' => 'woo',
					'source_name' => 'featured',
					'value_type' => 'bool',
					'bool_value_label' => us_translate( 'Featured products', 'woocommerce' ),
				),
			);
		}

		foreach( us_get_taxonomies() as $slug => $label ) {

			// If taxonomy slug is already used in previous params (e.g. 'post_type', 'price', 'featured'), append the count suffix to make it unique
			$unique_tax_name = in_array( $slug, array_merge( array_keys( $params ), array( 'orderby', 's' ) ) )
				? $slug . count( $params )
				: $slug;

			$params[ $unique_tax_name ] = array(
				'label' => $label,
				'group' => __( 'Taxonomies', 'us' ),
				'source_type' => 'tax',
				'source_name' => $slug,
			);
		}

		return apply_filters( 'us_get_list_filter_params', $params );
	}
}

if ( ! function_exists( 'us_get_list_orderby_params' ) ) {
	/**
	 * Generate params for sorting with unique names (used in URL)
	 */
	function us_get_list_orderby_params() {

		static $params = array();

		if ( ! empty( $params ) ) {
			return apply_filters( 'us_get_list_orderby_params', $params );
		}

		// Predefined post params
		$params = array(
			'date' => array(
				'label' => __( 'Date of creation', 'us' ),
				'group' => us_translate( 'Post Attributes' ),
			),
			'modified' => array(
				'label' => __( 'Date of update', 'us' ),
				'group' => us_translate( 'Post Attributes' ),
			),
			'title' => array(
				'label' => us_translate( 'Title' ),
				'group' => us_translate( 'Post Attributes' ),
			),
			'author' => array(
				'label' => us_translate( 'Author' ),
				'group' => us_translate( 'Post Attributes' ),
			),
			'comment_count' => array(
				'label' => us_translate( 'Comments' ),
				'group' => us_translate( 'Post Attributes' ),
			),
			'type' => array(
				'label' => us_translate( 'Post Type' ),
				'group' => us_translate( 'Post Attributes' ),
			),
			'menu_order' => array(
				'label' => us_translate( 'Page Attributes' ) . ': ' . us_translate( 'Order' ),
				'group' => us_translate( 'Post Attributes' ),
			),
		);

		// Predefined WooCommerce params
		if ( class_exists( 'woocommerce' ) ) {
			$params += array(
				'price' => array(
					'label' => us_translate( 'Price', 'woocommerce' ),
					'group' => us_translate( 'WooCommerce', 'woocommerce' ),
					'orderby_param' => 'meta_value_num',
					'meta_key' => '_price',
				),
				'total_sales' => array(
					'label' => us_translate( 'Sales', 'woocommerce' ),
					'group' => us_translate( 'WooCommerce', 'woocommerce' ),
					'orderby_param' => 'meta_value_num',
					'meta_key' => 'total_sales',
				),
				'rating' => array(
					'label' => us_translate( 'Rating', 'woocommerce' ),
					'group' => us_translate( 'WooCommerce', 'woocommerce' ),
					'orderby_param' => 'meta_value_num',
					'meta_key' => '_wc_average_rating',
				),
			);
		}

		return apply_filters( 'us_get_list_orderby_params', $params );
	}
}

if ( ! function_exists( 'us_get_filter_params_from_request' ) ) {
	/**
	 * Get List Filter params from request
	 *
	 * @return array Returns an array of installed filters.
	 */
	function us_get_filter_params_from_request() {

		static $url_params = array();
		if ( ! empty( $url_params ) ) {
			return $url_params;
		}

		$global_filter_params = us_get_list_filter_params();

		foreach ( (array) $_REQUEST as $name => $values ) {
			if ( strpos( $name, '_' ) !== 0 ) {
				continue;
			}
			$name = mb_substr( $name, 1 );
			if ( ! isset( $global_filter_params[ strtok( $name, '|' ) ] ) ) {
				continue;
			}
			$url_params[ $name ] = $values;
		}

		return $url_params;
	}
}

if ( ! function_exists( 'us_apply_filtering_to_list_query' ) ) {
	/**
	 * Apply the List Filter params to the provided query_args.
	 */
	function us_apply_filtering_to_list_query( &$query_args, $list_filter ) {

		if ( ! is_array( $list_filter ) OR empty( $list_filter ) ) {
			return;
		}

		$global_filter_params = us_get_list_filter_params();

		foreach ( $list_filter as $name => $values ) {

			$values = rawurldecode( $values );
			$values = explode( ',', $values ); // transform to array in all cases
			$values = array_map( 'rawurldecode', $values );

			// Restore comma from escaped QUOTATION MARK in every value (comma was used above to explode different values into array)
			foreach ( $values as &$value ) {
				$value = str_replace( /*U+0201A*/'\‚', ',', $value );
			}
			unset( $value );

			$query_args = apply_filters( 'us_apply_filtering_to_list_query', $query_args, $name, $values );

			// Source name may include the value compare type: 'post_date|after', 'price|between', 'category|and'
			$value_compare = '';

			if ( strpos( $name, '|' ) !== FALSE ) {
				$name = strtok( $name, '|' );
				$value_compare = strtok( '|' );
			}

			if ( empty( $value_compare ) ) {
				$value_compare = $global_filter_params[ $name ]['value_compare'] ?? '';
			}

			$source_type = $global_filter_params[ $name ]['source_type']; // required for conditions below
			$source_name = $global_filter_params[ $name ]['source_name'] ?? '';
			$value_type = $global_filter_params[ $name ]['value_type'] ?? '';

			if ( $source_type == 'post' ) {
				if ( $source_name == 'type' ) {
					$query_args['post_type'] = $values;

				} elseif ( $source_name == 'author' ) {
					$query_args['author__in'] = $values;

				} elseif ( $source_name == 'date' OR $source_name == 'date_modified' ) {
					if ( $source_name == 'date_modified' ) {
						$query_args['date_query']['column'] = 'post_modified';
					}

					if ( $value_compare == 'between' ) {
						$default_values = array(
							'1970-01-01 00:00:00',
							'3000-01-01 00:00:00',
						);
						foreach( $default_values as $i => $default_value ) {
							if ( empty( $values[ $i ] ) ) {
								$values[ $i ] = $default_value;
							}
						}
						$query_args['date_query'][] = array(
							'after' => $values[0],
							'before' => $values[1],
							'inclusive' => TRUE,
							'compare' => 'BETWEEN',
						);

					} elseif ( $value_compare == 'before' ) {
						$query_args['date_query'][] = array(
							'before' => $values[0],
							'inclusive' => TRUE,
						);

					} elseif ( $value_compare == 'after' ) {
						$query_args['date_query'][] = array(
							'after' => $values[0],
							'inclusive' => TRUE,
						);

					} else {
						foreach ( $values as $value ) {
							$query_args['date_query'][] = array(
								'before' => $value,
								'after' => $value,
								'inclusive' => TRUE,
							);
						}
						$query_args['date_query']['relation'] = 'OR';
					}
				}

			} elseif ( $source_type === 'tax' ) {
				$query_args['tax_query']['relation'] = 'AND';

				$tax_query_item = array(
					'taxonomy' => $source_name,
					'field' => 'slug',
					'terms' => $values,
				);

				if ( $value_compare == 'and' ) {
					$tax_query_item['operator'] = 'AND';
					$tax_query_item['include_children'] = FALSE;
				}

				$query_args['tax_query'][] = apply_filters( 'us_apply_filtering_tax_query_item', $tax_query_item, $source_name );

			} elseif ( $source_type === 'woo' ) {
				if ( $source_name == 'onsale' AND $values ) {
					$onsale_ids = wc_get_product_ids_on_sale();

					// Exclude ids matching 'post__not_in' first
					if ( ! empty( $query_args['post__not_in'] ) ) {
						$onsale_ids = array_diff( $onsale_ids, $query_args['post__not_in'] );
					}

					// then add ids matching 'post__in' if set
					if ( ! empty( $query_args['post__in'] ) ) {
						$query_args['post__in'] = array_intersect( $onsale_ids, $query_args['post__in'] );
					} else {
						$query_args['post__in'] = $onsale_ids;
					}

					// Use the non-existing id to get no results, because empty 'post__in' is ignored by query
					if ( ! $query_args['post__in'] ) {
						$query_args['post__in'] = array( 0 );
					}

				} elseif ( $source_name == 'featured' AND $values ) {
					$featured_ids = wc_get_featured_product_ids();

					// Exclude ids matching 'post__not_in' first
					if ( ! empty( $query_args['post__not_in'] ) ) {
						$featured_ids = array_diff( $featured_ids, $query_args['post__not_in'] );
					}

					// then add ids matching 'post__in' if set
					if ( ! empty( $query_args['post__in'] ) ) {
						$query_args['post__in'] = array_intersect( $featured_ids, $query_args['post__in'] );
					} else {
						$query_args['post__in'] = $featured_ids;
					}

					// Use the non-existing id to get no results, because empty 'post__in' is ignored by query
					if ( ! $query_args['post__in'] ) {
						$query_args['post__in'] = array( 0 );
					}
				}

			} elseif ( $source_type === 'meta' ) {
				$query_args['meta_query']['relation'] = 'AND';

				// ACF "Date Picker" values use 'Ymd' format: 20240915
				if ( $value_type == 'date' ) {

					if ( $value_compare == 'before' ) {
						$query_args['meta_query'][] = array(
							'key' => $source_name,
							'value' => array( '19700101', str_replace( '-', '', $values[0] ) ),
							'compare' => 'BETWEEN',
						);

					} elseif ( $value_compare == 'after' ) {
						$query_args['meta_query'][] = array(
							'key' => $source_name,
							'value' => array( str_replace( '-', '', $values[0] ), '30000101' ),
							'compare' => 'BETWEEN',
						);

					} elseif ( $value_compare == 'between' ) {

						$max = $values[1] ?? '30000101';

						$query_args['meta_query'][] = array(
							'key' => $source_name,
							'value' => array( str_replace( '-', '', $values[0] ), str_replace( '-', '', $max ) ),
							'compare' => 'BETWEEN',
						);

					} else {
						$_meta_query_inner = array(
							'relation' => 'OR',
						);
						foreach ( $values as $value ) {

							// 2024
							if ( strlen( $value ) === 4 ) {
								$min = $value . '0101';
								$max = $value . '1231';

								// 2024-09
							} elseif ( strlen( $value ) === 7 ) {
								$min = str_replace( '-', '', $value ) . '01';
								$max = str_replace( '-', '', $value ) . '31';

								// 2024-09-30
							} else {
								$min = str_replace( '-', '', $value );
								$max = str_replace( '-', '', $value );
							}

							$_meta_query_inner[] = array(
								'key' => $source_name,
								'value' => array( $min, $max ),
								'compare' => 'BETWEEN',
							);
						}
						$query_args['meta_query'][] = $_meta_query_inner;
					}

					// ACF "Date Time Picker" values use 'Y-m-d H:i:s' format: 2024-09-15 21:02:59
				} elseif ( $value_type == 'date_time' ) {

					if ( $value_compare == 'before' ) {
						$query_args['meta_query'][] = array(
							'key' => $source_name,
							'value' => array( '1970-01-01 00:00:00', $values[0] . ' 00:00:00' ),
							'compare' => 'BETWEEN',
						);

					} elseif ( $value_compare == 'after' ) {
						$query_args['meta_query'][] = array(
							'key' => $source_name,
							'value' => array( $values[0] . ' 00:00:00', '3000-01-01 00:00:00' ),
							'compare' => 'BETWEEN',
						);

					} elseif ( $value_compare == 'between' ) {

						$max = $values[1] ?? '3000-01-01';

						$query_args['meta_query'][] = array(
							'key' => $source_name,
							'value' => array( $values[0] . ' 00:00:00', $max . ' 00:00:00' ),
							'compare' => 'BETWEEN',
						);

					} else {
						$_meta_query_inner = array(
							'relation' => 'OR',
						);
						foreach ( $values as $value ) {

							// 2024
							if ( strlen( $value ) === 4 ) {
								$min = $value . '-01-01 00:00:00';
								$max = $value . '-12-31 23:59:00';

								// 2024-09
							} elseif ( strlen( $value ) === 7 ) {
								$min = $value . '-01 00:00:00';
								$max = $value . '-31 23:59:00';

								// 2024-09-30
							} else {
								$min = $value . ' 00:00:00';
								$max = $value . ' 23:59:00';
							}

							$_meta_query_inner[] = array(
								'key' => $source_name,
								'value' => array( $min, $max ),
								'compare' => 'BETWEEN',
							);
						}
						$query_args['meta_query'][] = $_meta_query_inner;
					}

					// Not Date and Time comparisons
				} elseif ( $value_compare == 'between' ) {
					$_meta_query_inner = array(
						'relation' => 'OR',
					);
					foreach ( $values as $value ) {
						if ( strpos( $value, '-' ) === FALSE ) {
							continue;
						}
						$_meta_query_inner[] = array(
							'key' => $source_name,
							'value' => explode( '-', $value ),
							'compare' => 'BETWEEN',
							'type' => 'DECIMAL(10,3)',
						);
					}
					$query_args['meta_query'][] = $_meta_query_inner;

				} elseif ( $value_compare == 'like' ) {
					$_meta_query_inner = array(
						'relation' => 'OR',
					);
					foreach ( $values as $value ) {
						$_meta_query_inner[] = array(
							'key' => $source_name,
							'value' => sprintf( ':"%s";', $value ),
							'compare' => 'LIKE',
						);
					}
					$query_args['meta_query'][] = $_meta_query_inner;

				} else {
					$query_args['meta_query'][] = array(
						'key' => $source_name,
						'value' => $values,
						'compare' => 'IN',
					);
				}
			}
		}
	}
}

if ( ! function_exists( 'us_apply_orderby_to_list_query' ) ) {
	/**
	 * Apply the orderby params to the provided query_args.
	 */
	function us_apply_orderby_to_list_query( &$query_args, $orderby_params ) {

		if ( empty( $orderby_params ) OR ! is_string( $orderby_params ) ) {
			return;
		}

		$orderby_params = rawurldecode( $orderby_params );

		// Examples of $orderby_params values:
		// 'date'
		// 'date,asc'
		// 'comment_count'
		// 'comment_count,asc'
		// 'custom_field'
		// 'custom_field,num'
		// 'custom_field,num,asc'
		$orderby_params = array_map( 'trim', explode( ',', $orderby_params ) );

		$orderby = $orderby_params[0] ?? '';

		// Cancel sorting for this specific values
		if ( $orderby == 'current_wp_query' ) {
			return;
		}

		$predefined_params = us_get_list_orderby_params();

		if ( isset( $predefined_params[ $orderby ] ) ) {

			if ( isset( $predefined_params[ $orderby ]['orderby_param'] ) ) {
				$query_args['orderby'] = $predefined_params[ $orderby ]['orderby_param'];
				$query_args['meta_key'] = $predefined_params[ $orderby ]['meta_key'] ?? '';
			} else {
				$query_args['orderby'] = $orderby;
			}

			// if provided param is not predefined but can be used by Post List, use it as is
		} elseif ( in_array( $orderby, array( 'rand', 'post__in' ) ) ) {
			$query_args['orderby'] = $orderby;

			// in other cases use it as custom field value
		} else {
			$query_args['orderby'] = ( isset( $orderby_params[1] ) AND $orderby_params[1] == 'num' )
				? 'meta_value_num'
				: 'meta_value';
			$query_args['meta_key'] = $orderby;
		}

		$query_args['order'] = ( end( $orderby_params ) == 'asc' ) ? 'DESC' : 'ASC';

		$query_args = apply_filters( 'us_apply_orderby_to_list_query', $query_args, $orderby );
	}
}

if ( ! function_exists( 'us_list_filter_for_current_wp_query' ) ) {
	add_action( 'pre_get_posts', 'us_list_filter_for_current_wp_query', 501 );
	/**
	 * Applies "List Filter" query to the global wp_query.
	 */
	function us_list_filter_for_current_wp_query( $wp_query ) {
		if (
			! $wp_query->is_main_query()
			AND ! $wp_query->get( 'us_faceted_filtering' ) // prevent applying filters while processing in us_get_faceted_filter_post_ids()
			AND $wp_query->get( 'apply_list_url_params' )
			AND (
				! is_admin()
				OR wp_doing_ajax()
			)
		) {
			us_apply_filtering_to_list_query( $wp_query->query_vars, us_get_filter_params_from_request() );
		}
	}
}

if ( ! function_exists( 'us_list_order_for_current_wp_query' ) ) {
	add_action( 'pre_get_posts', 'us_list_order_for_current_wp_query', 501 );
	/**
	 * Applies "List Order" query to the global wp_query.
	 */
	function us_list_order_for_current_wp_query( $wp_query ) {
		if (
			! $wp_query->is_main_query()
			AND $wp_query->get( 'apply_list_url_params' )
			AND (
				! is_admin()
				OR wp_doing_ajax()
			)
			AND $orderby_params = $_REQUEST['_orderby'] ?? ''
		) {
			us_apply_orderby_to_list_query( $wp_query->query_vars, $orderby_params );
		}
	}
}

if ( ! function_exists( 'us_list_search_for_current_wp_query' ) ) {
	add_action( 'pre_get_posts', 'us_list_search_for_current_wp_query', 501 );
	/**
	 * Applies "List Search" query to the global wp_query.
	 */
	function us_list_search_for_current_wp_query( $wp_query ) {
		if (
			! $wp_query->is_main_query()
			AND $wp_query->get( 'apply_list_url_params' )
			AND (
				! is_admin()
				OR wp_doing_ajax()
			)
			AND $search_query = $_REQUEST['_s'] ?? ''
		) {
			$wp_query->set( 's', sanitize_text_field( $search_query ) );
		}
	}
}

if ( ! function_exists( 'us_ajax_output_list_pagination' ) ) {
	/**
	 * Filters a page HTML to return the div with the "for_current_wp_query" class.
	 *
	 * @param string $content The post content.
	 * @return string Returns HTML of div with the "for_current_wp_query" class.
	 */
	function us_ajax_output_list_pagination( $content ) {
		if (
			class_exists( 'DOMDocument' )
			AND strpos( $content, 'for_current_wp_query' ) !== FALSE
		) {
			$document = new DOMDocument;
			// LIBXML_NOERROR is used to disable errors when HTML5 tags are not recognized by DOMDocument (which supports only HTML4).
			$document->loadHTML( '<meta http-equiv="Content-Type" content="text/html; charset=' . get_bloginfo( 'charset' ) . '">' . $content, LIBXML_NOERROR );
			$nodes = ( new DOMXpath( $document ) )->query('//div[contains(@class,"for_current_wp_query")]');
			if ( $nodes->count() ) {
				$element = $nodes->item( (int) us_arr_path( $_POST, 'us_ajax_list_index' ) );
				$new_document = new DOMDocument;
				$new_document->appendChild( $new_document->importNode( $element, TRUE ) );
				if ( $next_element = $element->nextSibling ) {
					$next_element_class = (string) $next_element->getAttribute( 'class' );
					if ( strpos( $next_element_class, 'w-grid-none' ) !== FALSE ) {
						$new_document->appendChild( $new_document->importNode( $next_element, TRUE ) );
					}
				}
				return $new_document->saveHTML();
			}
		}
		return $content;
	}
}

if ( ! function_exists( 'us_sort_terms_hierarchically' ) ) {
	/**
	 * Sort terms taking into account their hierarchy
	 *
	 * @param array $terms
	 * @param int $parent
	 * @return array
	 */
	function us_sort_terms_hierarchically( &$terms, $parent = 0 ) {
		$result = array();
		foreach ( $terms as $i => $term ) {
			if ( $term->parent == $parent ) {
				$result[] = $term;
				unset( $terms[ $i ] );
				foreach ( $terms as $item ) {
					if ( $item->parent AND $item->parent === $term->term_id ) {
						$result = array_merge( $result, us_sort_terms_hierarchically( $terms, $term->term_id ) );
					}
				}
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'us_get_faceted_filter_post_ids' ) ) {
	/**
	 * Get only post ids by provided query and applied filters
	 *
	 * @param array $query_args
	 * @param array $filters Specified filter params
	 *
	 * @return array
	 */
	function us_get_faceted_filter_post_ids( $query_args, $filters = array() ) {

		// Force needed params to speed up the query
		$query_args = array_merge( $query_args, array(
			'paged' => 1,
			'posts_per_page' => -1,
			'update_post_meta_cache' => FALSE,
			'update_post_term_cache' => FALSE,
			'cache_results' => FALSE,
			'no_found_rows' => TRUE,
			'nopaging' => TRUE,
			'fields' => 'ids',
			'us_faceted_filtering' => TRUE, // prevent applying filters from URL
		) );

		$query_args = apply_filters( 'us_get_faceted_filter_post_ids_args', $query_args );

		us_apply_filtering_to_list_query( $query_args, $filters );

		$query = new WP_Query( $query_args );

		return ( $query->posts ) ? $query->posts : array( 0 );
	}
}

if ( ! function_exists( 'us_list_filter_get_post_count' ) ) {

	/**
	 * Get post count for every value of all filter items.
	 *
	 * @param array $query_args Post/Product List query arguments.
	 * @param array $list_filters List of names and types of all defined filters.
	 *
	 * @return array
	 */
	function us_list_filter_get_post_count( $query_args, $list_filters = array() ) {

		if ( ! is_array( $query_args ) OR ! is_array( $list_filters ) OR empty( $list_filters ) ) {
			return array();
		}

		if ( ! US_Filter_Indexer::instance()->get_row_count() ) {
			return array();
		}

		// Get unfiltered post_ids
		$unfiltered_post_ids = us_get_faceted_filter_post_ids( $query_args );

		// Get filtered post_ids
		$filters_with_or_values = $filtered_post_ids = array();
		if ( $selected_filters = us_get_filter_params_from_request() ) {

			$filtered_post_ids = us_get_faceted_filter_post_ids( $query_args, $selected_filters );

			// Collect filtered post IDs separately for every filter_name
			foreach ( $list_filters as $filter_name => $filter_type ) {

				// Exclude filter types that don't change their values when filtering
				if ( $filter_type == 'date_picker' ) {
					continue;
				}

				// Exclude taxonomies that use the "AND" term operator
				if ( mb_substr( $filter_name, -4 ) == '|and' ) {
					continue;
				}

				// Get post IDs from applied filter items EXCEPT the current one to support "OR" choise
				if ( isset( $selected_filters[ $filter_name ] ) ) {

					$processed_filters = $selected_filters; // keep 'selected_filters' untouched
					unset( $processed_filters[ $filter_name ] );

					if ( ! empty( $processed_filters ) ) {
						$post_ids = us_get_faceted_filter_post_ids( $query_args, $processed_filters );

					} else {
						$post_ids = $unfiltered_post_ids;
					}

					// Remove suffix: 'price|between' => 'price'
					$filter_name = strtok( $filter_name, '|' );

					$filters_with_or_values[ $filter_name ] = $post_ids;
				}
			}
		}

		global $wpdb;

		$filter_names = $filters_with_ranges = array();

		foreach ( $list_filters as $filter_name => $filter_type ) {
			$name = strtok( $filter_name, '|' );

			if (
				$filter_type == 'range_slider'
				OR $filter_type == 'range_input'
			) {
				$filters_with_ranges[] = $name;
			}

			// Escape values for using in SQL
			$filter_names[] = $wpdb->prepare( '%s', $name );
		}

		// Collect unfiltered post IDs grouped by filter_name and filter_value
		$all_filters = array();
		$sql = "
			SELECT
				`post_id`, `filter_name`, `filter_value`
			FROM `{$wpdb->us_filter_index}`
			WHERE
				`filter_name` IN (" . implode( ',', $filter_names ) . ")
				AND `post_id` IN (" . implode( ',', $unfiltered_post_ids ) . ");
		";
		foreach ( (array) $wpdb->get_results( $sql ) as $row ) {
			foreach ( (array) maybe_unserialize( $row->filter_value ) as $filter_value ) {

				$filter_name = $row->filter_name;

				// For Date Ranges
				if ( in_array( $filter_name, array( 'post_date', 'post_modified' ) ) ) {
					$filter_value = wp_date( 'Y-m', (int) strtotime( $filter_value ) );
				}

				$all_filters[ $filter_name ][ $filter_value ][] = (int) $row->post_id;
			}
		}

		$results = array();

		// Intersect unfiltered post IDs with filtered post IDs to get post count for every filter value
		foreach ( $all_filters as $filter_name => $filter_values ) {
			foreach ( $filter_values as $filter_value => $post_ids ) {

				if ( isset( $filters_with_or_values[ $filter_name ] ) ) {
					$post_ids = array_intersect( $filters_with_or_values[ $filter_name ], $post_ids );

				} elseif ( $filtered_post_ids ) {
					$post_ids = array_intersect( $filtered_post_ids, $post_ids );
				}

				$post_count = count( $post_ids );

				// Filters with range values don't need post count, but need all values to define their min and max
				if ( in_array( $filter_name, $filters_with_ranges ) ) {
					if ( $post_count ) {
						$results[ $filter_name ][] = (float) $filter_value;
					}

				} else {
					$results[ $filter_name ][ $filter_value ] = $post_count;
				}
			}
		}

		// Collect min and max values for filters with range values
		foreach ( $filters_with_ranges as $filter_name ) {
			if ( ! isset( $results[ $filter_name ] ) ) {
				$results[ $filter_name ] = array( 0, 0 );
				continue;
			}
			$results[ $filter_name ] = array(
				min( $results[ $filter_name ] ),
				max( $results[ $filter_name ] ),
			);
		}

		return $results;
	}
}
