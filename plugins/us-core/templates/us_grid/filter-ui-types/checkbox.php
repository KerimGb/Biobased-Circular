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

$output = '';

foreach ( $item_values as $item_value ) {
	
	$_value = $item_value['value'] ?? $item_value;

	if ( $_value == '' ) {
		continue;
	}

	// Replace comma to escaped QUOTATION MARK, cause comma is used in URL to separate different values
	$encoded_value = rawurlencode( str_replace( ',', /*U+0201A*/'\‚', $_value ) );

	$_atts = array(
		'class' => 'w-filter-item-value',
		'data-value' => $_value,
	);

	if ( ! empty( $show_amount ) ) {
		$_atts['data-post-count'] = 0;
	}
	if ( ! empty( $item_value['depth'] ) ) {
		$_atts['class'] .= ' depth_' . $item_value['depth'];
	}

	$_atts = apply_filters( 'us_list_filter_value_html_atts', $_atts, $item_value, $item_name );

	$_label = esc_html( $item_value['label'] ?? $_value );
	$_label = apply_filters( 'us_list_filter_value_label', $_label, $item_value, $item_name );

	$output .= '<div' . us_implode_atts( $_atts ) . '>';
	$output .= '<label>';

	$input_atts = array(
		'type' => 'checkbox',
		'value' => $encoded_value,
		'name' => $item_name,
		'style' => '',
	);

	// Add color swatch value
	if ( isset( $item_value['color_swatch'] ) ) {
		$input_atts['style'] .= 'background:' . $item_value['color_swatch'] . ';';
		$input_atts['style'] .= 'color:' . us_get_contrast_bg_color( $item_value['color_swatch'] ) . ';';
		$input_atts['title'] = $_label;
	}

	$output .= '<input' . us_implode_atts( $input_atts ) . '>';
	$output .= '<span class="w-filter-item-value-label">' . $_label . '</span>';

	if ( ! empty( $show_amount ) ) {
		$output .= '<span class="w-filter-item-value-amount">0</span>'; // set via JS
	}

	$output .= '</label>';
	$output .= '</div>'; // w-filter-item-value
}

echo $output;
