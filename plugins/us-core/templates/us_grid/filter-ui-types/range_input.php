<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Type: Range input
 *
 * @action Before the template: 'us_before_template:templates/us_grid/filter-ui-types/range_input'
 * @action After the template: 'us_after_template:templates/us_grid/filter-ui-types/range_input'
 * @filter Template variables: 'us_template_vars:templates/us_grid/filter-ui-types/range_input'
 */

if ( empty( $item_values ) ) {
	return;
}

$min_value = $item_values['min_value'];
$max_value = $item_values['max_value'];

$min_input = array(
	'type' => 'text',
	'class' => 'w-filter-item-value for_min_value',
	'inputmode' => 'none', // remove keyboard appearance on focus for mobiles
	'aria-label' => __( 'Min', 'us' ),
	'placeholder' => apply_filters( 'us_list_filter_value_label', $min_value, $min_value, $item_name ),
	'data-value' => (float) $min_value,
	'value' => '',
);
echo '<input ' . us_implode_atts( $min_input ) . '>';

$max_input = array(
	'type' => 'text',
	'class' => 'w-filter-item-value for_max_value',
	'inputmode' => 'none', // remove keyboard appearance on focus for mobiles
	'aria-label' => __( 'Max', 'us' ),
	'placeholder' => apply_filters( 'us_list_filter_value_label', $max_value, $max_value, $item_name ),
	'data-value' => (float) $max_value,
	'value' => '',
);
echo '<input ' . us_implode_atts( $max_input ) . '>';

// Collect the number format options to pass them to JS and apply on ajax
if ( ! empty( $show_amount ) ) {
	$onclick_attr = us_pass_data_to_js( apply_filters( 'us_list_filter_range_input_options', array(), $item_name ) );
	echo '<div class="for_range_input_options hidden"' . $onclick_attr . '></div>';
}
