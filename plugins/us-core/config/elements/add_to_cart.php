<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: add_to_cart
 */

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );

/**
 * @return array
 */
return array(
	'title' => sprintf( __( '"%s" Button', 'us' ), us_translate( 'Add to cart', 'woocommerce' ) ),
	'category' => 'WooCommerce',
	'icon' => 'fas fa-cart-plus',
	'show_for_post_types' => array( 'us_content_template', 'us_page_block', 'product' ),
	'place_if' => class_exists( 'woocommerce' ),
	'params' => us_set_params_weight(

		// General section
		array(
			'view_cart_link' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show link to cart when adding products', 'us' ),
				'std' => 0,
				'context' => array( 'grid' ),
				'usb_preview' => array(
					'toggle_class' => 'no_view_cart_link',
				),
			),
			'btn_size' => array(
				'title' => __( 'Button Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'context' => array( 'shortcode' ),
				'usb_preview' => array(
					'css' => '--btn-size',
				),
			),
			'btn_fullwidth' => array(
				'type' => 'switch',
				'switch_text' => __( 'Stretch to the full width', 'us' ),
				'std' => 0,
				'context' => array( 'shortcode' ),
				'usb_preview' => array(
					'toggle_class' => 'btn_fullwidth',
				),
			),
			'qty_btn_style' => array(
				'title' => __( 'Quantity Buttons Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'0' => us_translate( 'Default' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
				'std' => '0',
				'context' => array( 'shortcode' ),
				'usb_preview' => array(
					'mod' => 'qty-btn-style',
				),
			),
			'qty_btn_size' => array(
				'title' => __( 'Quantity Buttons Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '1rem',
				'context' => array( 'shortcode' ),
				'usb_preview' => array(
					'css' => '--qty-btn-size',
				),
			),
		),
		$conditional_params,
		$design_options_params,
		$hover_options_params
	),
);
