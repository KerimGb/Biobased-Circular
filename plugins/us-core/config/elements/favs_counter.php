<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for element: favs_counter
 */

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );
$effect_options_params = us_config( 'elements_effect_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Favorites Counter', 'us' ),
	'icon' => 'fas fa-heart',
	'params' => us_set_params_weight(
		array(
			'hide_empty' => array(
				'type' => 'switch',
				'switch_text' => __( 'Hide if no favorites', 'us' ),
				'std' => 0,
			),
			'vstretch' => array(
				'type' => 'switch',
				'switch_text' => __( 'Stretch to the full available height', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
			),
			'link' => array(
				'title' => __( 'Link to Favorites list', 'us' ),
				'type' => 'link',
				'dynamic_values' => TRUE,
				'std' => '{"url":""}',
			),
			'quantity_color_bg' => array(
				'title' => __( 'Quantity Badge Background', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => '_header_middle_text_hover',
				'cols' => 2,
			),
			'quantity_color_text' => array(
				'title' => __( 'Quantity Badge Text', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '_header_middle_bg',
				'cols' => 2,
			),
			'icon' => array(
				'title' => us_translate( 'Icon' ),
				'type' => 'icon',
				'std' => 'fas|heart',
			),
		),

		$effect_options_params,
		$conditional_params,
		$design_options_params
	)
);
