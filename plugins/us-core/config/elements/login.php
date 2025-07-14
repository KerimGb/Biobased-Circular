<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Login', 'us' ),
	'icon' => 'fas fa-lock',
	'params' => array_merge(
		array(
			'us_field_style' => array(
				'title' => __( 'Field Style', 'us' ),
				'description' => $misc['desc_field_styles'],
				'type' => 'select',
				'options' => us_get_field_styles(),
				'std' => 'default',
				'usb_preview' => array(
					'mod' => 'us-field-style',
				),
			),
			'register' => array(
				'type' => 'text',
				'title' => __( 'Register URL', 'us' ),
				'std' => '',
			),
			'lost_password' => array(
				'type' => 'text',
				'title' => __( 'Lost Password URL', 'us' ),
				'std' => '',
			),
			'login_redirect' => array(
				'type' => 'text',
				'title' => __( 'Login Redirect URL', 'us' ),
				'std' => '',
			),
			'logout_redirect' => array(
				'type' => 'text',
				'title' => __( 'Logout Redirect URL', 'us' ),
				'std' => '',
			),
			'use_ajax' => array(
				'type' => 'switch',
				'description' => __( 'Recommended when page caching is enabled for logged in visitors.', 'us' ),
				'switch_text' => __( 'Show via AJAX', 'us' ),
				'std' => 0,
			),
		),

		$conditional_params,
		$design_options
	),
);
