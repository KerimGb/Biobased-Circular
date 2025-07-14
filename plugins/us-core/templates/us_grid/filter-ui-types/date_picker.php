<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Type: Checkbox
 *
 * @action Before the template: 'us_before_template:templates/us_grid/filter-ui-types/checkbox'
 * @action After the template: 'us_after_template:templates/us_grid/filter-ui-types/checkbox'
 * @filter Template variables: 'us_template_vars:templates/us_grid/filter-ui-types/checkbox'
 */

if ( empty( $item_values ) ) {
	return;
}

// Enqueue datepicker script
if ( ! wp_script_is( 'jquery-ui-datepicker' ) ) {
	wp_enqueue_script( 'jquery-ui-datepicker' );
	if ( function_exists( 'wp_localize_jquery_ui_datepicker' ) ) {
		wp_localize_jquery_ui_datepicker();
	}
}

$output = '';

foreach ( $item_values as $item_value ) {

	$_value = $item_value['value'] ?? '';
	$_name = $item_value['name'] ?? $item_name;
	$_label = $item_value['label'] ?? $_value;

	$datepicker_options = array(
		'changeMonth' => TRUE,
		'changeYear' => TRUE,
	);
	$datepicker_options = apply_filters( 'us_list_filter_datepicker_options', $datepicker_options, $_name );

	$_atts = array(
		'class' => 'w-filter-item-value for_' . $_name,
		'onclick' => us_pass_data_to_js( $datepicker_options, /*onclick*/FALSE ),
	);

	$input_atts = array(
		'type' => 'text',
		'placeholder' => apply_filters( 'us_list_filter_value_label', $_label, $item_value, $item_name ),
		'name' => $_name,
		'value' => $_value,
		'inputmode' => 'none', // remove keyboard appearance on focus for mobiles
		'data-date-format' => $date_format ?? '',
	);

	$output .= '<div' . us_implode_atts( $_atts ) . '>';
	$output .= '<input' . us_implode_atts( $input_atts ) . '>';
	$output .= '</div>'; // w-filter-item-value
}

echo $output;
