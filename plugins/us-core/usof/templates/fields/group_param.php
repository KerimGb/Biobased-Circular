<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Group
 *
 * Grouped options
 *
 * @var   $field array Group options
 * @var   $params_values array Group values
 *
 */

$output = '<div class="usof-form-group-item">';

$output .= '<style></style>';

$group_content_styles = '';

// Output group title block, if "is_accordion" is set
if ( ! empty( $field['is_accordion'] ) ) {

	$group_content_styles = ' style="display:none;"';

	$accordion_title = $field['accordion_title'] ?? '';

	foreach ( $field['params'] as $param_name => $param ) {
		if ( strpos( $accordion_title, $param_name ) !== FALSE ) {
			$param_value = $params_values[ $param_name ] ?? $field['params'][ $param_name ]['std'];
			if ( $param['type'] == 'select' AND ! empty( $param['options'][ $param_value ] ) ) {
				$param_value = $param['options'][ $param_value ];
			}
			$param_value = esc_attr( trim( (string) $param_value ) );
			$accordion_title = str_replace( $param_name, $param_value, $accordion_title );
		}
	}

	$output .= '<div class="usof-form-group-item-title">';

	// Customized preview
	if ( isset( $field['preview'] ) ) {

		$preview_class_format = $field['preview_class_format'] ?? '';

		// Box with the style class
		$_main_class = $_custom_class = '';
		if ( isset( $params_values['id'] ) AND $preview_class_format ) {
			$_main_class = sprintf( $preview_class_format, $params_values['id'] );
		}
		if ( ! empty( $params_values['class'] ) ) {
			$_custom_class = esc_attr( $params_values['class'] );
		}

		$_preview_atts = array(
			'class' => 'usof-preview-class',
			'data-preview-class-format' => $preview_class_format,
		);
		$output .= '<div ' . us_implode_atts( $_preview_atts ) . '>';
		$output .= '<span class="usof-preview-class-main">' . $_main_class . '</span>';
		$output .= ' <span class="usof-preview-class-extra">' . $_custom_class . '</span>';
		$output .= '</div>';

		if ( $field['preview'] == 'button' ) {
			$output .= '<div class="usof-btn-preview hov_fade">';
			$output .= '<div class="usof-btn">';
			$output .= '<span class="usof-btn-label">' . esc_html( $accordion_title ) . '</span>';
			$output .= '</div>';
			$output .= '</div>'; // .usof-btn-preview

			// Customized preview for Field Styles
		} elseif ( $field['preview'] == 'input_fields' ) {

			$output .= '<div class="usof-input-preview">';

			$output .= '<input class="usof-input-preview-elm" type="text" placeholder="' . esc_attr( $accordion_title ) . '">';

			$output .= '<div class="usof-input-preview-select">';
			$output .= '<select class="usof-input-preview-elm">';
			$output .= '<option>' . __( 'Dropdown', 'us' ) . '</option>';
			$output .= '<option>' . __( 'Dropdown', 'us' ) . ' 2</option>';
			$output .= '<option>' . __( 'Dropdown', 'us' ) . ' 3</option>';
			$output .= '</select>';
			$output .= '</div>';

			$output .= '<label class="usof-input-preview-checkbox">';
			$output .= '<input class="usof-input-preview-elm" type="checkbox" value="1" checked>';
			$output .= '<span>' . __( 'Checkboxes', 'us' ) . '</span>';
			$output .= '</label>';

			$output .= '<label class="usof-input-preview-radio">';
			$output .= '<input class="usof-input-preview-elm" type="radio" value="1" checked>';
			$output .= '<span>' . __( 'Radio buttons', 'us' ) . '</span>';
			$output .= '</label>';

			$output .= '</div>'; // .usof-input-preview
		}

	} else {
		$output .= esc_html( $accordion_title );
	}

	$output .= '</div>'; // .usof-form-group-item-title
}

// Output group content block
$output .= '<div class="usof-form-group-item-content"' . $group_content_styles . '>';
ob_start();
foreach ( $field['params'] as $param_name => $param ) {
	us_load_template(
		'usof/templates/field', array(
			'name' => $param_name,
			'id' => 'usof_' . $param_name,
			'field' => $param,
			'values' => $params_values,
			'context' => $context,
		)
	);
}
$output .= ob_get_clean();
$output .= '</div>';

// Output controls, if set
if ( ! empty( $field['show_controls'] ) ) {
	$output .= '<div class="usof-form-group-item-controls">';

	// Show "Move" button, if "is_sortable" is set
	if ( ! empty( $field['is_sortable'] ) ) {
		$output .= '<div class="ui-icon_move" title="' . us_translate( 'Move' ) . '"></div>';
	}

	// Show "Duplicate" button, if "is_duplicate" is set
	if ( ! empty( $field['is_duplicate'] ) ) {
		$output .= '<div class="ui-icon_duplicate" title="' . __( 'Duplicate', 'us' ) . '"></div>';
	}
	$output .= '<div class="ui-icon_delete" title="' . us_translate( 'Delete' ) . '"></div>';
	$output .= '</div>';
}

$output .= '</div>';

echo $output;
