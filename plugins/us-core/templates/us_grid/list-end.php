<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * End part of post/product list output
 */

$output = '</div>'; // .w-grid-list

// Reset global $wp_query and $post variables.
if ( $source != 'current_wp_query' ) {
	wp_reset_query();
}

// Reset grid items counter
global $us_grid_item_counter;
$us_grid_item_counter = 0;

// Reset grid outputing items
global $us_grid_outputs_items;
$us_grid_outputs_items = FALSE;

// Reset the grid item type
global $us_grid_item_type;
$us_grid_item_type = NULL;

// Reset the image size for the next grid/list element
global $us_grid_img_size;
$us_grid_img_size = NULL;

// Global preloader type
$preloader_type = us_get_option( 'preloader' );
if ( ! is_numeric( $preloader_type ) ) {
	$preloader_type = '1';
}

// For correct work of numbered pagination via ajax we need to get the BASE of the current page URL
// First check if we have reffer URL from ajax request
if ( wp_doing_ajax() ) {
	$current_url = wp_get_referer();
}

// ...if not, get it from the current wp request
if ( empty( $current_url ) ) {
	global $wp;
	$current_url = home_url( $wp->request );
}

// Remove all query strings
$current_url = strtok( $current_url, '?' );

// Remove all "/page/*/" parts
if ( preg_match( '/\/page\/?([0-9]{1,})\/?/', $current_url, $matches ) ) {
	$current_url = str_replace( $matches[0], '', $current_url );
}
$current_url = trailingslashit( $current_url );

// Get the params of List Search, List Order, List Filter elements
$url_params = array();
if ( ! empty( $_REQUEST['_s'] ) ) {
	$url_params['_s'] = sanitize_text_field( (string) $_REQUEST['_s'] );
}
if ( ! empty( $_REQUEST['_orderby'] ) ) {
	$url_params['_orderby'] = (string) $_REQUEST['_orderby'];
}
if ( $filter_url_params = us_get_filter_params_from_request() ) {
	foreach ( $filter_url_params as $name => $value ) {
		$url_params[ '_' . $name ] = $value;
	}
}

// Search results page has its own URL params, get them for correct ajax filtering
if ( is_search() ) {
	$url_params['s'] = get_query_var( 's' );

	if ( isset( $_GET['post_type'] ) ) {
		$url_params['post_type'] = get_query_var( 'post_type' ); // always used in WooCommerce search results page
	}
}

// Encode every param value since the browser automatically decodes cyrillic characters in pagination links
$encoded_url_params = array();
foreach( $url_params as $name => $value ) {

	if ( is_array( $value ) ) {
		foreach ( $value as &$val ) {
			$val = rawurlencode( $val );
		}
		unset( $val );

	} else {
		$value = rawurlencode( $value );
	}

	$encoded_url_params[ $name ] = $value;
}

// Numbered pagination
if (
	$pagination == 'numbered'
	AND (
		$max_num_pages > 1
		OR usb_is_preview()
	)
) {
	$paginate_args = array(
		'base' => $current_url . '%_%', // required to output correct links via ajax
		'add_args' => $encoded_url_params,
		'after_page_number' => '</span>',
		'before_page_number' => '<span>',
		'mid_size' => 3,
		'next_text' => '<span>' . us_translate( 'Next' ) . '</span>',
		'prev_text' => '<span>' . us_translate( 'Previous' ) . '</span>',
		'total' => $max_num_pages,
	);

	// Static front (home) page uses "page" var instead of "paged"
	if ( is_front_page() AND ! us_amp() ) {
		set_query_var( 'paged', get_query_var( 'page' ) );
	}

	$paginate_class = 'nav-links';
	if ( ! empty( $pagination_style ) ) {
		$paginate_class .= ' custom us-nav-style_' . (int) $pagination_style;
	}

	$output .= '<nav class="pagination navigation" role="navigation">';
	$output .= '<div class="' . $paginate_class . '">' . paginate_links( $paginate_args ) . '</div>';
	$output .= '</nav>'; // .pagination
}

// Always output "Load more" block to show preloader on ajax requests
$loadmore_class = 'g-loadmore';
if ( $pagination_btn_fullwidth ) {
	$loadmore_class .= ' width_full';
}
if ( $max_num_pages <= 1 ) {
	$loadmore_class .= ' hidden';
}
$output .= '<div class="' . $loadmore_class . '">';
$output .= '<div class="g-preloader type_' . $preloader_type . '"><div></div></div>';

if ( $pagination == 'load_on_btn'  ) {
	$output .= '<button class="w-btn ' . us_get_btn_class( $pagination_btn_style ) . '"' . us_prepare_inline_css( array( 'font-size' => $pagination_btn_size ) ) . '>';
	$output .= '<span class="w-btn-label">' . us_replace_dynamic_value( $pagination_btn_text ) . '</span>';
	$output .= '</button>'; // .w-btn
}

$output .= '</div>'; // .g-loadmore

// Popup html
if ( strpos( $overriding_link, 'popup_post' ) !== FALSE ) {
	$popup_vars = array(
		'overriding_link' => $overriding_link,
		'popup_width' => $popup_width,
		'preloader_type' => $preloader_type,
		'popup_arrows' => $popup_arrows,
	);
	$output .= us_get_template( 'templates/us_grid/popup', $popup_vars );
}

// Query args unfiltered for "Faceted Filtering"
$query_args_unfiltered = $query_args_unfiltered ?? array();
if ( isset( $vars['query_args_unfiltered'] ) ) {
	unset( $vars['query_args_unfiltered'] );
}

// JSON data for ajax requests
$json_data = array(
	'max_num_pages' => $max_num_pages,
	'pagination' => $pagination,
	'paged' => $paged,
	'ajaxData' => array(),
	'facetedFilter' => array(),
);

if ( $source == 'current_wp_query' ) {

	if ( $pagination != 'numbered' ) {
		$encoded_url_params['paged'] = rawurlencode( '{num_page}' );
	}
	$json_data['ajaxUrl'] = add_query_arg( $encoded_url_params, $current_url );

	global $us_post_list_index;
	if ( is_null( $us_post_list_index ) ) {
		$us_post_list_index = 0;
	}
	$json_data['ajaxData'] = array(
		'us_ajax_list_pagination' => 1,
		'us_ajax_list_index' => $us_post_list_index++,
	);

} else {
	$json_data['ajaxData'] = array(
		'_nonce' => ! empty( $is_product_list ) ? wp_create_nonce( 'us_product_list' ) : wp_create_nonce( 'us_post_list' ),
		'action' => ! empty( $is_product_list ) ? 'us_ajax_product_list' : 'us_ajax_post_list',
		'meta_type' => us_get_current_meta_type(),
		'object_id' => us_get_current_id(),
		'template_vars' => $vars,
	);
	if ( $apply_url_params ) {
		$json_data['ajaxData'] += $url_params;
	}
}

// Generate post count data for "Faceted Filtering"
if ( $apply_url_params OR $source == 'current_wp_query' ) {

	$list_filters = us_get_HTTP_POST_json( 'list_filters' ) ?? array();

	global $us_ajax_list_pagination;

	// Get post_count from ajax requests only to avoid heavy queries on page load
	if ( ( wp_doing_ajax() OR $us_ajax_list_pagination ) AND $paged == 1 ) {
		$json_data['facetedFilter']['post_count'] = us_list_filter_get_post_count( $query_args_unfiltered, $list_filters );
	} else {
		$json_data['facetedFilter']['query_args'] = json_encode( $query_args_unfiltered );
	}

	$json_data['ajaxData']['list_filters'] = json_encode( $list_filters );
}

$output .= '<div class="w-grid-list-json hidden"' . us_pass_data_to_js( $json_data ) . '></div>';
$output .= '</div>'; // .w-grid

echo $output;

// Output the "No results" block AFTER the "w-grid" div container
if ( $no_results ) {
	us_grid_shows_no_results();
}
