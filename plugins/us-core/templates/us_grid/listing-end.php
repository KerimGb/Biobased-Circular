<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Closing part of Grid output
 */

// Reset grid items counter
global $us_grid_item_counter;
$us_grid_item_counter = 0;

// Reset grid outputing items
global $us_grid_outputs_items;
$us_grid_outputs_items = FALSE;

// Global preloader type
$preloader_type = us_get_option( 'preloader' );
if ( $preloader_type == 'disabled' ) {
	$preloader_type = 1;
}
if ( $preloader_type == 'custom' AND $preloader_image = us_get_option( 'preloader_image' ) ) {
	$img_arr = explode( '|', $preloader_image );
	$preloader_image_html = wp_get_attachment_image( $img_arr[0], 'medium' );
	if ( empty( $preloader_image_html ) ) {
		$preloader_image_html = us_get_img_placeholder( 'medium' );
	}
} else {
	$preloader_image_html = '';
}
echo '</div>'; // .w-grid-list

$json_data = array();

// Skip extra logic when grid outputs terms or users
// =========================================================================================================
global $us_grid_item_type;
if ( $us_grid_item_type == 'post' ) {
	$us_grid_post_type = $us_grid_post_type ?? NULL;
	$us_grid_index = $us_grid_index ?? 0;
	$post_id = $post_id ?? 0;
	$items_count = $items_count ?? 0;
	$filter_html = $filter_html ?? '';
	$is_widget = $is_widget ?? FALSE;
	$exclude_items = $exclude_items ?? NULL;
	$items_offset = $items_offset ?? NULL;
	$pagination = $pagination ?? NULL;
	$query_args = $query_args ?? array();
	$orderby_query_args = $orderby_query_args ?? array();
	$items_jsoncss_collection = $items_jsoncss_collection ?? array();
	$us_grid_filter_query_string = $us_grid_filter_query_string ?? NULL;

	// Check for filters in query parameters
	$filter_url_prefix = us_get_grid_url_prefix( 'filter' );
	$isset_url_filters = strpos( implode( ',', array_keys( $_GET ) ), $filter_url_prefix );

	// TODO: check if we need this here (already have same code in listing.php)
	if (
		! $is_widget
		AND ! empty( $post_id )
		AND $type != 'carousel'
	) {
		$us_grid_ajax_indexes[ $post_id ] = isset( $us_grid_ajax_indexes[ $post_id ] ) ? ( $us_grid_ajax_indexes[ $post_id ] ) : 1;
	} else {
		$us_grid_ajax_indexes = NULL;
	}

	// Output custom styles from Design settings of every post in the Grid, if it has Post Content with Full Content
	if ( $items_custom_css = us_jsoncss_compile( $items_jsoncss_collection ) ) {
		echo '<style id="grid-post-content-css">' . $items_custom_css . '</style>';
	}

	// Output preloader for not Carousel or Filter
	if ( $filter_html OR $type != 'carousel' ) {
		echo '<div class="w-grid-preloader">';
	}
	?>
	<div class="g-preloader type_<?php echo $preloader_type; ?>">
		<div><?php echo $preloader_image_html ?></div>
	</div>
	<?php
	if ( $filter_html OR $type != 'carousel' ) {
		echo '</div>';
	}

	// Output pagination for not Carousel type
	if (
		(
			isset( $wp_query )
			AND $wp_query->max_num_pages > 1
			AND $type != 'carousel'
		)
		OR $isset_url_filters !== FALSE
	) {
		// Next page elements may have sliders, so embed the needed asset
		wp_enqueue_script( 'us-royalslider' );

		if ( $pagination == 'infinite' ) {
			$is_infinite = TRUE;
			$pagination = 'ajax';
		}

		if ( $pagination == 'regular' ) {

			// The main parameters for the formation of pagination
			$paginate_args = array(
				'after_page_number' => '</span>',
				'before_page_number' => '<span>',
				'mid_size' => 3,
				'next_text' => '<span>' . us_translate( 'Next' ) . '</span>',
				'prev_text' => '<span>' . us_translate( 'Previous' ) . '</span>',
			);

			// Adding filters to pagination, this will allow you to create pagination
			// based on filters for AJAX requests
			if ( wp_doing_ajax() AND ! empty( $us_grid_filter_query_string ) ) {
				parse_str( $us_grid_filter_query_string, $paginate_args['add_args'] );
			}

			// Adding order to pagination
			if ( ! empty( $grid_orderby ) ) {
				$paginate_args['add_args'][ us_get_grid_url_prefix( 'order' ) ] = $grid_orderby;
			}

			// Removes from `admin-ajax.php` links
			$paginate_links = paginate_links( $paginate_args );
			$paginate_home_url = ( has_filter( 'us_tr_home_url' ) )
				? trailingslashit( apply_filters( 'us_tr_home_url', NULL ) )
				: trailingslashit( home_url() );
			$paginate_links = str_replace( $paginate_home_url . 'wp-admin/admin-ajax.php', '', $paginate_links );

			if ( ! empty( $pagination_style ) ) {
				$paginate_class = ' custom us-nav-style_' . (int) $pagination_style;
			} else {
				$paginate_class = '';
			}

			?>
			<nav class="pagination navigation" role="navigation">
				<div class="nav-links<?php echo $paginate_class ?>">
					<?php echo $paginate_links ?>
				</div>
			</nav>
			<?php

		} elseif ( $pagination == 'ajax' ) {
			$pagination_btn_css = us_prepare_inline_css( array( 'font-size' => $pagination_btn_size ) );

			$loadmore_classes = $pagination_btn_fullwidth
				? ' width_full'
				: '';

			if ( $wp_query->max_num_pages <= 1 ) {
				$loadmore_classes .= ' hidden';
			}
			?>
			<div class="g-loadmore <?php echo $loadmore_classes ?>">
				<div class="g-preloader type_<?php echo ( $preloader_type == 'custom' ) ? '1' : $preloader_type; ?>">
					<div></div>
				</div>
				<button class="w-btn <?php echo us_get_btn_class( $pagination_btn_style ) ?>"<?php echo $pagination_btn_css ?>>
					<span class="w-btn-label"><?php echo $pagination_btn_text ?></span>
				</button>
			</div>
			<?php
		}
	}

	// Fix for multi-filter ajax pagination
	if ( isset( $paged ) ) {
		$query_args['posts_per_page'] = $paged;
	}

	if ( $filter_html AND isset( $query_args['tax_query']['relation'] ) ) {
		unset( $query_args['tax_query']['relation'] );
	}

	/**
	 * Recursively removing filters from meta_key by key
	 *
	 * @param array $items The items
	 * @param string $skip_key The skip key
	 *
	 * @return array
	 */
	$func_remove_filters_in_meta_query = function( $items, $skip_key ) use( &$func_remove_filters_in_meta_query ) {
		$results = array();
		foreach ( $items as $index => $item ) {
			if ( ! empty( $item[0] ) AND is_array( $item[0] ) ) {
				$results[ $index ] = $func_remove_filters_in_meta_query( $item, $skip_key );
			} elseif ( us_arr_path( $item, 'key' ) === $skip_key ) {
				continue;
			} else {
				$results[ $index ] = $item;
			}
		}
		return $results;
	};

	// Remove Grid Filters params from $query_args
	if ( ! wp_doing_ajax() ) {
		$_filter_taxonomies = array();
		foreach ( us_get_filter_taxonomies( $filter_url_prefix, $us_grid_filter_query_string ) as $item_name => $item_value ) {

			// Get param_name
			$param = us_grid_filter_parse_param( $item_name );

			$item_source = us_arr_path( $param, 'source' );
			$item_name = us_arr_path( $param, 'param_name', $item_name );

			$filter_name = us_get_grid_url_prefix( 'filter' ) . '_' . $item_name;
			if ( $item_source == 'cf' ) {
				$filter_name .= '_' . (int) us_arr_path( $param, 'acf_field_id' );
			}
			$_filter_taxonomies[ $filter_name ] = implode( ',', $item_value );

			// Remove filters from tax_query
			if ( $item_source === 'tax' ) {
				if ( ! empty( $query_args['tax_query'] ) ) {
					foreach ( $query_args['tax_query'] as $index => $tax ) {
						if ( us_arr_path( $tax, 'taxonomy' ) === $item_name ) {
							$tax_terms = us_arr_path( $tax, 'terms' );
							if ( ! is_array( $tax_terms ) ) {
								$tax_terms = array( $tax_terms );
							}
							foreach ( $item_value as $term_name ) {
								if (
									in_array( $term_name, $tax_terms )
									AND isset( $tax_terms[ array_search( $term_name, $tax_terms ) ] )
								) {
									unset( $tax_terms[ array_search( $term_name, $tax_terms ) ] );
								}
							}
							if ( empty( $tax_terms ) ) {
								unset( $query_args['tax_query'][ $index ] );
							}
						}
					}
				}

				// Remove filters from meta_query
			} elseif ( ! empty( $query_args['meta_query'] ) ) {
				$meta_query = us_arr_path( $query_args, 'meta_query', array() );
				$query_args['meta_query'] = $func_remove_filters_in_meta_query( $meta_query, $item_name );
			}
		}
		if ( is_null( $us_grid_filter_query_string ) AND ! empty( $_filter_taxonomies ) ) {
			$us_grid_filter_query_string = build_query( $_filter_taxonomies );
		}

		// Added default query_args created from grid settings
		if ( ! empty( $_default_query_args ) ) {
			$query_args = array_merge( $query_args, $_default_query_args );
		}

		// Remove price range from `query_args`
		if ( isset( $query_args['_us_product_meta_lookup_prices'] ) ) {
			unset( $query_args['_us_product_meta_lookup_prices'] );
		}

		// Add attributes from the default WooCommerce filter
		if (
			class_exists( 'woocommerce' )
			AND function_exists( 'is_filtered' )
			AND is_filtered()
		) {
			// For attributes
			if ( $wc_filter_get = WC_Query::get_layered_nav_chosen_attributes() ) {
				foreach ( $wc_filter_get as $wc_filter_key => $wc_filter_value ) {
					$query_args['tax_query'][] = array(
						'taxonomy' => $wc_filter_key,
						'terms' => $wc_filter_value['terms'],
						'field' => 'slug',
						'operator' => 'IN'
					);
				}
			}

			// For price
			if ( isset( $_GET['min_price'] ) AND isset( $_GET['max_price'] ) ) {
				$query_args['meta_query'][] = array(
					'key' => '_price',
					'value' => array($_GET['min_price'], $_GET['max_price']),
					'compare' => 'BETWEEN',
					'type'    => 'NUMERIC'
				);
			}
		}
	}

	global $us_page_args;

	// Define and output all JSON data
	$json_data = array(

		// Controller options
		'action' => 'us_ajax_grid',
		'infinite_scroll' => ( isset( $is_infinite ) ? $is_infinite : 0 ),
		'max_num_pages' => isset( $wp_query ) ? $wp_query->max_num_pages : NULL,
		'pagination' => $pagination,

		// Grid listing template variables that will be passed to this file in the next call
		'template_vars' => array(
			'columns' => $columns,
			'exclude_items' => $exclude_items,
			'img_size' => $img_size,
			'ignore_items_size' => $ignore_items_size,
			'items_layout' => $items_layout,
			'items_offset' => $items_offset,
			'load_animation' => $load_animation,
			'overriding_link' => $overriding_link,
			'post_id' => $post_id,
			'query_args' => $query_args,
			'orderby_query_args' => $orderby_query_args,
			'type' => $type,
			'us_grid_post_type' => $us_grid_post_type,
			'us_grid_ajax_index' => ! empty( $us_grid_ajax_indexes[ $post_id ] )
				? $us_grid_ajax_indexes[ $post_id ]
				: $us_grid_index,
			'us_grid_filter_query_string' => $us_grid_filter_query_string,
			'us_grid_index' => $us_grid_index,
			'page_args' => $us_page_args,
		),
	);

	global $us_get_orderby;
	if ( $grid_orderby = (string) us_arr_path( $_GET, us_get_grid_url_prefix( 'order' ), $us_get_orderby ) ) {
		$json_data['template_vars']['grid_orderby'] = trim( $grid_orderby );
	}
}

// Carousel settings
if ( $type == 'carousel' ) {

	// Force disabling loop when nothing to rotate to avoid creating item clones
	if ( $items == 'auto' ) {
		$_disable_loop = FALSE;
	} else {
		$_disable_loop = ( $items_count <= $items );
	}

	// Owl Carousel options https://owlcarousel2.github.io/OwlCarousel2/docs/api-options.html
	$js_options = array(
		'aria_labels' => array(
			'prev' => us_translate( 'Previous' ),
			'next' => us_translate( 'Next' ),
		),
		'autoplayContinual' => (bool) $autoplay_continual,
		'autoplayHoverPause' => (bool) $autoplay_pause_on_hover,
		'autoplayTimeout' => intval( (float) $autoplay_timeout * 1000, 10 ),
		'autoWidth' => ( $items == 'auto' ),
		'smartSpeed' => (int) $transition_speed,
		'margin' => 0, // should always be 0, because gap in the Carousel is set by CSS only
		'mouseDrag' => (bool) $mouse_drag,
		'rtl' => is_rtl(),
		'slideBy' => ( ! $slide_by_one OR $items == 'auto' ) ? 'page' : '1',
		'touchDrag' => (bool) $touch_drag,
		'slideTransition' => strip_tags( $transition_timing_function ),
	);

	// https://owlcarousel2.github.io/OwlCarousel2/demos/animate.html
	if ( $transition_animation == 'fade' ) {
		$js_options['animateIn'] = 'fadeIn';
		$js_options['animateOut'] = 'fadeOut';
	}

	// Responsive options https://owlcarousel2.github.io/OwlCarousel2/demos/responsive.html
	$breakpoints = array();
	if ( is_string( $responsive ) ) {
		$responsive = json_decode( urldecode( $responsive ), TRUE );
	}
	if ( ! is_array( $responsive ) ) {
		$responsive = array();
	}
	foreach ( $responsive as $responsive_data ) {
		if ( $responsive_data['breakpoint'] == 'laptops' ) {
			$breakpoint_width = (int) us_get_option( 'laptops_breakpoint' ) + 1;
		} elseif ( $responsive_data['breakpoint'] == 'tablets' ) {
			$breakpoint_width = (int) us_get_option( 'tablets_breakpoint' ) + 1;
		} elseif ( $responsive_data['breakpoint'] == 'mobiles' ) {
			$breakpoint_width = (int) us_get_option( 'mobiles_breakpoint' ) + 1;
		} else {
			$breakpoint_width = (int) $responsive_data['breakpoint_width'];
		}
		$breakpoints[ $breakpoint_width ] = array(
			'autoHeight' => (bool) $responsive_data['autoheight'],
			'autoplay' => (bool) $responsive_data['autoplay'],
			'autoWidth' => ( $responsive_data['items'] == 'auto' ),
			'center' => ( $responsive_data['items'] == 'auto' ) ? FALSE : (bool) $responsive_data['center_item'],
			'dots' => (bool) $responsive_data['dots'],
			'items' => (int) $responsive_data['items'],
			'loop' => $_disable_loop ? FALSE : (bool) $responsive_data['loop'],
			'nav' => (bool) $responsive_data['arrows'],
			'stagePadding' => (int) $responsive_data['items_offset'],
			'slideBy' => $responsive_data['items'] == 'auto' ? 'page' : '1',
		);
	}
	ksort( $breakpoints );

	$breakpoint_widths = array_merge( array( 0 ), array_keys( $breakpoints ) ); // e.g. array( 0, 601, 1025, 1381 )

	$breakpoint_values = array_values( $breakpoints );

	// Options below MAY NOT be duplicated in the $js_options
	$breakpoint_values[] = array(
		'items' => (int) $items,
		'autoplay' => (bool) $autoplay,
		'center' => ( $items == 'auto' ) ? FALSE : (bool) $center_item,
		'dots' => (bool) $dots,
		'nav' => (bool) $arrows,
		'autoHeight' => (bool) $autoheight,
		'autoWidth' => ( $items == 'auto' ),
		'loop' => $_disable_loop ? FALSE : (bool) $loop,
		'stagePadding' => (int) $items_offset,
	);

	$js_options['responsive'] = array_combine( $breakpoint_widths, $breakpoint_values );

	unset( $breakpoints, $breakpoint_widths, $breakpoint_values );

	// Note: `$vars` is needed to be able to determine the current carousel
	// by attributes, for example by "el_class", "el_id" or etc.
	$json_data['carousel_settings'] = apply_filters( 'us_carousel_js_options', $js_options, $vars );
}

// Add lang variable if WPML is active
if ( class_exists( 'SitePress' ) ) {
	global $sitepress;
	if ( apply_filters( 'us_tr_default_language', NULL ) != apply_filters( 'us_tr_current_language', NULL ) ) {
		$json_data['template_vars']['lang'] = apply_filters( 'us_tr_current_language', NULL );
	}
}

// Output json params
if ( ! us_amp() ) {
	?>
	<div class="w-grid-json hidden"<?= us_pass_data_to_js( $json_data ) ?>></div>
	<?php
}

// Load popup html
$popup_vars = array(
	'overriding_link' => $overriding_link,
	'popup_width' => $popup_width,
	'preloader_type' => $preloader_type,
	'popup_arrows' => $popup_arrows,
);
us_load_template( 'templates/us_grid/popup', $popup_vars );

echo '</div>'; // .w-grid

// Output the "No results" block after the "w-grid" div container
if ( $no_results ) {
	us_grid_shows_no_results();
}

if ( ! empty( $use_custom_query ) ) {

	us_close_wp_query_context();

	// Remove filters added for events calendar
	if ( class_exists( 'Tribe__Events__Query' ) ) {

		// Prevent custom queries from messing main events query
		remove_filter( 'tribe_events_views_v2_should_hijack_page_template', 'us_the_events_calendar_return_true_for_hijack' );
	}

	// Reset the products loop
	if (
		! empty( $query_args['post_type'] )
		AND us_post_type_is_available( $query_args['post_type'], array( 'product', 'any' ) )
		AND function_exists( 'wc_reset_loop' )
	) {
		wc_reset_loop();
	}
}

// If we are in WPB front end editor mode, apply JS to the current grid
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
	echo '<script>
	jQuery( function( $ ) {
		if ( typeof $us !== "undefined" && typeof $us.WGrid === "function" ) {
			var $gridContainer = $("#' . $grid_elm_id . '");
			$gridContainer.wGrid();
		}
	} );
	</script>';
}

// Reset the grid item type
$us_grid_item_type = NULL;

// Reset the image size for the next grid/list element
global $us_grid_img_size;
$us_grid_img_size = NULL;
