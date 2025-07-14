<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Type: Range slider
 *
 * @action Before the template: 'us_before_template:templates/us_grid/filter-ui-types/range_slider'
 * @action After the template: 'us_after_template:templates/us_grid/filter-ui-types/range_slider'
 * @filter Template variables: 'us_template_vars:templates/us_grid/filter-ui-types/range_slider'
 */

if ( empty( $item_values ) ) {
	return;
}

// Enqueue ui-slider script
if ( ! wp_script_is( 'jquery-ui-slider' ) ) {
	wp_enqueue_script( 'jquery-ui-slider' );
	wp_enqueue_script( 'jquery-touch-punch' );
}

// Get values from URL param
if ( $values_from_url = (string) us_arr_path( $_GET, sprintf( '_%s|between', $item_name ) ) ) {
	$values_from_url = explode( '-', $values_from_url );
	$values_from_url = array_map( 'floatval', $values_from_url );
} else {
	$values_from_url = array();
}

$min = (float) $item_values['min_value'];
$max = (float) $item_values['max_value'];
$step = abs( (float) $item_values['step_size'] );

$input_atts = array(
	'type' => 'hidden',
	'name' => $item_name,
	'min' => $min,
	'max' => $max,
	'value' => implode( '-', $values_from_url ),
);
$output = '<input' . us_implode_atts( $input_atts ) . '>';

// https://api.jqueryui.com/slider/
$slider_options = array(
	'slider' => array(
		'min' => $min,
		'max' => $max,
		'step' => ( $step ) ? $step : 1,
		'values' => $values_from_url,
	),
);

$output .= '<div class="ui-slider"' . us_pass_data_to_js( apply_filters( 'us_list_filter_range_slider_options', $slider_options, $item_name ) ) . '>';
$output .= '<div class="ui-slider-handle" title="' . esc_attr( __( 'Min', 'us' ) ) . '"></div>';
$output .= '<div class="ui-slider-handle" title="' . esc_attr( __( 'Max', 'us' ) ) . '"></div>';
$output .= '</div>'; // ui-slider

$min_value_label = $values_from_url[0] ?? $min;
$max_value_label = $values_from_url[1] ?? $max;

$output .= '<div class="w-filter-item-slider-result">';
$output .= '<div class="for_min_value">' . apply_filters( 'us_list_filter_value_label', $min_value_label, $min_value_label, $item_name ) . '</div>';
$output .= '<div class="for_max_value">' . apply_filters( 'us_list_filter_value_label', $max_value_label, $max_value_label, $item_name ) . '</div>';
$output .= '</div>'; // w-filter-item-slider-result

echo $output;
