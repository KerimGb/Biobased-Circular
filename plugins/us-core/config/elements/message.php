<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: message
 */

$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );
$effect_options_params = us_config( 'elements_effect_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Message Box', 'us' ),
	'category' => __( 'Interactive', 'us' ),
	'icon' => 'fas fa-exclamation-circle',
	'js_view' => 'VcMessageView', // used in WPBakery editor
	'params' => us_set_params_weight(

		// General section
		array(
			'color' => array(
				'title' => us_translate( 'Color' ),
				'type' => 'select',
				'options' => array(
					'blue' => __( 'Blue', 'us' ),
					'yellow' => __( 'Yellow', 'us' ),
					'green' => __( 'Green', 'us' ),
					'red' => __( 'Red', 'us' ),
				),
				'std' => 'blue',
				'usb_preview' => array(
					'mod' => 'color',
				),
			),
			'content' => array(
				'title' => __( 'Message', 'us' ),
				'type' => 'textarea',
				'show_ai_icon' => TRUE,
				'holder' => 'div',
				'std' => 'I am message box. Click edit button to change this text.',
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-message-body',
				),
			),
			'icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'usb_preview' => TRUE,
			),
			'closing' => array(
				'type' => 'switch',
				'switch_text' => __( 'Enable closing', 'us' ),
				'std' => 0,
				'usb_preview' => array(
					'toggle_class' => 'with_close',
				),
			),
		),

		$effect_options_params,
		$conditional_params,
		$design_options_params
	),

	'usb_init_js' => '$elm.usMessage()',
);
