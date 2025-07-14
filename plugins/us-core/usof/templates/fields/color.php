<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Color
 *
 * Improved color selector
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['text'] string Field additional text
 * @param $field ['exclude_dynamic_colors'] bool which type of dynamic colors to exclude from the list of color variables
 * @param $field ['us_vc_field'] bool Field used in Visual Composer
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @var   $value string Current value
 */

$_atts = array(
	'class' => 'usof-color',
);

if ( isset( $field['clear_pos'] ) ) {
	$_atts['class'] .= ' clear_' . $field['clear_pos'];
}

$with_gradient = ( ! isset( $field['with_gradient'] ) OR $field['with_gradient'] !== FALSE );

if ( $with_gradient ) {
	$_atts['class'] .= ' with_gradient';
}

$show_color_vars_list = us_arr_path( $field, 'exclude_dynamic_colors' ) !== 'all';

if ( $show_color_vars_list ) {
	$_atts['class'] .= ' with_color_list';
	if ( strpos( us_arr_path( $field, 'exclude_dynamic_colors', '' ), 'scheme' ) !== FALSE ) {
		$_atts['class'] .= ' hide_scheme_vars';
	}
	if ( strpos( us_arr_path( $field, 'exclude_dynamic_colors', '' ), 'custom_field' ) !== FALSE ) {
		$_atts['class'] .= ' hide_cf_vars';
	}
}

$input_atts = array(
	'autocomplete' => 'off',
	'class' => 'usof-color-value',
	'name' => $name,
	'type' => 'text',
	'value' => $value,
);

// Field for editing in Visual Composer
if ( isset( $field['us_vc_field'] ) ) {
	$input_atts['class'] .= ' wpb_vc_param_value';
}

$color_value = preg_match( "/{{([^}]+)}}/", $value )
	? 'white'
	: us_get_color( $value, /* gradient */ TRUE, /* cssvar */ FALSE );

if ( strpos( $value, '_' ) === 0 ) {
	$_atts['data-value'] = $color_value;
}

// Color picker HTML template (a square where the value is editable by mouse drag)
$picker_html = '<div class="usof-color-picker">';
$picker_html .= '<div class="usof-color-picker-color"><span></span></div>';
$picker_html .= '<div class="usof-color-picker-hue"><span></span></div>';
$picker_html .= '<div class="usof-color-picker-alpha"><span></span></div>';
$picker_html .= '</div>';

// Output control
$output = '<div' . us_implode_atts( $_atts ) . '>';

$output .= '<div class="usof-color-field">';
$output .= '<div class="usof-color-preview" style="background: ' . $color_value . '">';
$output .= '<input' . us_implode_atts( $input_atts ) . '>';

if ( $show_color_vars_list ) {
	$output .= '<button class="action_toggle-list" title="' . __( 'Select Dynamic Value', 'us' ) . '" type="button"></button>';
	$output .= '</div>'; // usof-color-preview
	$output .= '<div class="usof-color-list"></div>';
} else {
	$output .= '</div>'; // usof-color-preview
}

if ( isset( $field['clear_pos'] ) ) {
	$output .= '<button class="action_clear '. esc_attr( $field['clear_pos'] ) .'" title="' . us_translate( 'Clear' ) . '" type="button"></button>';
}

$output .= '</div>'; // usof-color-field

// Panel to edit the color (shown by click to the input field)
$output .= '<div class="usof-color-edit-panel">';

if ( $with_gradient ) {

	// Color type switch: solid or gradient
	$output .= '<div class="usof-radio">';
	$output .= '<label>';
	$output .= '<input name="usof-color-editor-mode" type="radio" value="solid">';
	$output .= '<span class="usof-radio-value">' . _x( 'Solid', 'color type', 'us' ) . '</span>';
	$output .= '</label>';
	$output .= '<label>';
	$output .= '<input name="usof-color-editor-mode" type="radio" value="gradient">';
	$output .= '<span class="usof-radio-value">' . _x( 'Gradient', 'color type', 'us' ) . '</span>';
	$output .= '</label>';
	$output .= '</div>';
	
	$output .= '<div class="usof-color-edit-panel-row for_gradient">';

	// Gradient angle slider
	$output .= '<div class="usof-gradient-angle">';
	$output .= '<input type="range" class="usof-gradient-angle-range" min="0" max="360" step="5">';
	$output .= '<div class="usof-gradient-angle-value">0deg</div>';
	$output .= '</div>'; // usof-gradient-angle

	// Gradient sub colors list
	$output .= '<div class="usof-gradient-colors">';

	$output .= '<div class="usof-gradient-color">';
	$output .= '<div class="usof-color-field">';
	$output .= '<div class="usof-color-preview">';
	$output .= '<input class="usof-color-value" type="text">';
	if ( $show_color_vars_list ) {
		$output .= '<button class="action_toggle-list" title="' . __( 'Select Dynamic Value', 'us' ) . '" type="button"></button>';
		$output .= '</div>'; // usof-gradient-color-preview
		$output .= '<div class="usof-color-list"></div>';
	} else {
		$output .= '</div>'; // usof-gradient-color-preview
	}
	$output .= $picker_html;
	$output .= '</div>'; // .usof-color-field
	$output .= '<input class="usof-gradient-color-position" type="number" min="0" max="100">';
	$output .= '<button class="action_delete-gradient-color ui-icon_close" title="' . us_translate( 'Delete' ) . '" type="button"></button>';
	$output .= '</div>'; // .usof-gradient-color

	$output .= '</div>'; // .usof-gradient-colors

	// Gradient add sub color button
	$output .= '<button class="action_add-gradient-color" type="button">' . us_translate( 'Add' ) . '</button>';

	$output .= '</div>'; // .usof-color-edit-panel-row.for_gradient
}

$output .= '<div class="usof-color-edit-panel-row for_solid">' . $picker_html . '</div>';

$output .= '</div>'; // .usof-color-edit-panel

if ( ! empty( $field['text'] ) ) {
	$output .= '<div class="usof-color-text">' . $field['text'] . '</div>';
}

$output .= '</div>'; // .usof-color

echo $output;
