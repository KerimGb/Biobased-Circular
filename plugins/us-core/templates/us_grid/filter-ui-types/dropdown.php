<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Type: Dropdown <select>
 *
 * @action Before the template: 'us_before_template:templates/us_grid/filter-ui-types/dropdown'
 * @action After the template: 'us_after_template:templates/us_grid/filter-ui-types/dropdown'
 * @filter Template variables: 'us_template_vars:templates/us_grid/filter-ui-types/dropdown'
 */

if ( empty( $item_values ) ) {
	return;
}

$select_atts = array(
	'class' => 'w-filter-item-value-select',
	'name' => $item_name,
	'aria-label' => $item_title,
);

$output = '<select' . us_implode_atts( $select_atts ) . '>';

foreach ( $item_values as $i => $item_value ) {

	$_value = $item_value['value'] ?? $item_value;

	if ( $_value == '' ) {
		continue;
	}

	// Replace comma to escaped QUOTATION MARK, cause comma is used in URL to separate different values
	$encoded_value = rawurlencode( str_replace( ',', /*U+0201A*/'\‚', $_value ) );

	$_atts = array(
		'class' => '',
		'value' => $encoded_value,
	);

	$_label = '';

	// Prepend non-breaking spaces for visual hierarchy
	if ( ! empty( $item_value['depth'] ) ) {
		$_label .= implode( '', array_fill( 0, $item_value['depth'] - 1, html_entity_decode( '&nbsp;&nbsp;&nbsp;' ) ) );
	}

	$_label .= $item_value['label'] ?? $_value;

	if ( ! empty( $show_amount ) AND $_value != '*' ) {
		$_atts['data-label-template'] = $_label . ' (%d)';
		$_label .= ' (0)';
	}

	$output .= '<option' . us_implode_atts( $_atts ) . '>' . esc_html( apply_filters( 'us_list_filter_value_label', $_label, $item_value, $item_name ) ) . '</option>';
}

$output .= '</select>'; // w-filter-item-value-select

echo $output;
