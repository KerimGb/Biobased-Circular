<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: post_date
 */

$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );

/**
 * @return array
 */
return array(
	'title' => us_translate( 'Event Date and Time', 'the-events-calendar' ),
	'category' => __( 'Post Elements', 'us' ),
	'icon' => 'fas fa-calendar-alt',
	'place_if' => class_exists( 'Tribe__Events__Query' ),
	'params' => us_set_params_weight(

		// General section
		array(
			'type' => array(
				'title' => us_translate( 'Show' ),
				'type' => 'select',
				'options' => array(
					'default' => us_translate( 'Event Date and Time', 'the-events-calendar' ),
					'start' => us_translate( 'Start Date', 'the-events-calendar' ),
					'end' => us_translate( 'End Date', 'the-events-calendar' ),
				),
				'std' => 'default',
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
			),
			'format' => array(
				'title' => us_translate( 'Date Format' ),
				'description' => '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">' . __( 'Documentation on date and time formatting.', 'us' ) . '</a>',
				'placeholder' => 'F j, Y',
				'type' => 'text',
				'std' => '',
				'show_if' => array( 'type', '!=', 'default' ),
				'usb_preview' => TRUE,
			),
			'icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'usb_preview' => TRUE,
			),
			'text_before' => array(
				'title' => __( 'Text before value', 'us' ),
				'type' => 'text',
				'std' => '',
				'usb_preview' => array(
					'elm' => '.w-post-elm-before',
					'attr' => 'html',
				),
			),
			'text_after' => array(
				'title' => __( 'Text after value', 'us' ),
				'type' => 'text',
				'std' => '',
				'usb_preview' => array(
					'elm' => '.w-post-elm-after',
					'attr' => 'html',
				),
			),
		),

		$conditional_params,
		$design_options_params,
		$hover_options_params
	),
);
