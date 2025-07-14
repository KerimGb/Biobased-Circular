<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: us_carousel
 */

$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );

// Get params from Grid and Content Carousel to avoid params duplication
$grid_params = array();

if ( us_is_elm_editing_page() ) {
	$grid_params = us_config( 'elements/grid.params' );
}

foreach( $grid_params as $_param_name => &$_param ) {

	if ( in_array( $_param_name, array_keys( $conditional_params ) ) ) {
		unset( $grid_params[ $_param_name ] );
	}
	if ( in_array( $_param_name, array_keys( $design_options_params ) ) ) {
		unset( $grid_params[ $_param_name ] );
	}

	if ( ! empty( $_param['exclude_for_us_carousel'] ) ) {
		unset( $grid_params[ $_param_name ] );
	}
	if ( isset( $_param['weight'] ) ) {
		unset( $grid_params['weight'] );
	}
	if ( $_param_name === 'items_gap' ) {
		$_param['group'] = __( 'Carousel', 'us' );
		$_param['weight'] = 34;
	}
}

$content_carousel_params = us_config( 'elements/content_carousel.params' );

foreach ( $content_carousel_params as $_param_name => &$_param ) {

	if ( in_array( $_param_name, array_keys( $conditional_params ) ) ) {
		unset( $content_carousel_params[ $_param_name ] );
	}
	if ( in_array( $_param_name, array_keys( $design_options_params ) ) ) {
		unset( $content_carousel_params[ $_param_name ] );
	}

	if ( ! empty( $_param['exclude_for_us_carousel'] ) ) {
		unset( $content_carousel_params[ $_param_name ] );
	}
	if ( ! isset( $_param['group'] ) ) {
		$_param['group'] = __( 'Carousel', 'us' );
	}

	// Change default values for correct fallback
	if ( $_param_name === 'items' ) {
		$_param['std'] = '2';
	}
	if ( $_param_name === 'arrows' ) {
		$_param['std'] = '0';
	}
	if ( $_param_name === 'loop' ) {
		$_param['std'] = '1';
	}
}
unset( $_param );

return array(
	'title' => __( 'Carousel', 'us' ),
	'description' => __( 'List of images, posts, pages or any custom post types', 'us' ),
	'category' => __( 'Lists', 'us' ),
	'icon' => 'fas fa-laptop-code',
	'usb_reload_element' => TRUE,
	'params' => us_set_params_weight(
		$grid_params,
		$content_carousel_params,
		$conditional_params,
		$design_options_params
	),
	'fallback_params' => array(
		'columns',
		'carousel_arrows',
		'carousel_arrows_style',
		'carousel_arrows_size',
		'carousel_arrows_pos',
		'carousel_arrows_offset',
		'carousel_items_offset',
		'carousel_dots',
		'carousel_center',
		'carousel_slideby',
		'carousel_loop',
		'carousel_autoheight',
		'carousel_fade',
		'carousel_autoplay',
		'carousel_interval',
		'carousel_autoplay_smooth',
		'carousel_speed',
		'carousel_transition',
		'breakpoint_1_width',
		'breakpoint_1_cols',
		'breakpoint_1_offset',
		'breakpoint_1_autoplay',
		'breakpoint_2_width',
		'breakpoint_2_cols',
		'breakpoint_2_offset',
		'breakpoint_2_autoplay',
		'breakpoint_3_width',
		'breakpoint_3_cols',
		'breakpoint_3_offset',
		'breakpoint_3_autoplay',
	),
	'usb_init_js' => '$elm.wGrid()',
);
