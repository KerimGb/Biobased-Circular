<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: vc_column_tabs
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

$source_options = array(
	'' => '– ' . us_translate( 'Default' ) . ' –',
);

$general_params = array(

	// Tabs
	'layout' => array(
		'title' => us_translate( 'Style' ),
		'type' => 'select',
		'options' => array(
			'default' => us_translate( 'Default' ),
			'simple' => __( 'Simple', 'us' ),
			'simple2' => __( 'Simple', 'us' ) . ' 2',
			'simple3' => __( 'Simple', 'us' ) . ' 3',
			'radio' => __( 'Switch', 'us' ),
			'radio2' => __( 'Switch', 'us' ) . ' 2',
			'radio3' => __( 'Switch', 'us' ) . ' 3',
			'modern' => __( 'Modern', 'us' ),
			'trendy' => __( 'Trendy', 'us' ),
			'timeline' => __( 'Timeline', 'us' ),
			'timeline2' => __( 'Timeline', 'us' ) . ' 2',
		),
		'std' => 'default',
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => TRUE,
	),
	'tabs_alignment' => array(
		'title' => us_translate( 'Alignment' ),
		'type' => 'radio',
		'labels_as_icons' => 'fas fa-align-*',
		'options' => array(
			'none' => us_translate( 'Default' ),
			'center' => us_translate( 'Center' ),
			'justify' => us_translate( 'Justify' ),
		),
		'std' => 'none',
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'elm' => '> .w-tabs-list',
			'mod' => 'align',
		),
	),
	'switch_sections' => array(
		'title' => __( 'Switch sections', 'us' ),
		'type' => 'radio',
		'options' => array(
			'click' => __( 'On click', 'us' ),
			'hover' => __( 'On hover', 'us' ),
		),
		'std' => 'click',
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => TRUE,
	),
	'title_font' => array(
		'title' => __( 'Font', 'us' ),
		'type' => 'select',
		'options' => us_get_fonts_for_selection(),
		'std' => '',
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'elm' => '> .w-tabs-list',
			'css' => 'font-family',
		),
	),
	'title_weight' => array(
		'title' => __( 'Font Weight', 'us' ),
		'type' => 'select',
		'options' => array(
			'' => us_translate( 'Default' ),
			'100' => '100 ' . __( 'thin', 'us' ),
			'200' => '200 ' . __( 'extra-light', 'us' ),
			'300' => '300 ' . __( 'light', 'us' ),
			'400' => '400 ' . __( 'normal', 'us' ),
			'500' => '500 ' . __( 'medium', 'us' ),
			'600' => '600 ' . __( 'semi-bold', 'us' ),
			'700' => '700 ' . __( 'bold', 'us' ),
			'800' => '800 ' . __( 'extra-bold', 'us' ),
			'900' => '900 ' . __( 'ultra-bold', 'us' ),
		),
		'std' => '',
		'cols' => 2,
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'elm' => '> .w-tabs-list',
			'css' => 'font-weight',
		),
	),
	'title_transform' => array(
		'title' => __( 'Text Transform', 'us' ),
		'type' => 'select',
		'options' => array(
			'' => us_translate( 'Default' ),
			'none' => us_translate( 'None' ),
			'uppercase' => 'UPPERCASE',
			'lowercase' => 'lowercase',
			'capitalize' => 'Capitalize',
		),
		'std' => '',
		'cols' => 2,
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'elm' => '> .w-tabs-list',
			'css' => 'text-transform',
		),
	),
	'title_size' => array(
		'title' => __( 'Font Size', 'us' ),
		'description' => $misc['desc_font_size'],
		'type' => 'text',
		'std' => '1em',
		'cols' => 2,
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'css' => '--sections-title-size',
		),
	),
	'title_lineheight' => array(
		'title' => __( 'Line height', 'us' ),
		'description' => us_arr_path( $misc, 'desc_line_height', '' ),
		'type' => 'text',
		'cols' => 2,
		'group' => __( 'Tabs', 'us' ),
		'usb_preview' => array(
			'elm' => '> .w-tabs-list',
			'css' => 'line-height',
		),
	),

	// Accordion
	'accordion_at_width' => array(
		'title' => __( 'Transform to Accordion at the screen width', 'us' ),
		'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">300px</span>, <span class="usof-example">768px</span>. ' . __( 'Leave empty to transform automatically based on the total width of the tabs.', 'us' ),
		'type' => 'text',
		'std' => '',
		'group' => us_translate( 'Accordion', 'js_composer' ),
		'usb_preview' => TRUE,
	),

	// Data Source
	'data_source' => array(
		'title' => __( 'Data Source', 'us' ),
		'type' => 'select',
		'options' => apply_filters( 'us_tta_source_options', $source_options ),
		'std' => '',
		'group' => __( 'Data Source', 'us' ),
	),
	'title_source' => array(
		'title' => __( 'Title Field', 'us' ),
		'description' => __( 'Set the name of sub field.', 'us' ) . ' ' . __( 'Examples:', 'us' ) . ' <span class="usof-example">question</span>, <span class="usof-example">sub_field_1</span>',
		'type' => 'text',
		'std' => 'question',
		'cols' => 2,
		'show_if' => array( 'data_source', '!=', '' ),
		'group' => __( 'Data Source', 'us' ),
	),
	'content_source' => array(
		'title' => __( 'Content Field', 'us' ),
		'description' => __( 'Set the name of sub field.', 'us' ) . ' ' . __( 'Examples:', 'us' ) . ' <span class="usof-example">answer</span>, <span class="usof-example">sub_field_2</span>',
		'type' => 'text',
		'std' => 'answer',
		'cols' => 2,
		'show_if' => array( 'data_source', '!=', '' ),
		'group' => __( 'Data Source', 'us' ),
	),
);

// Copy the parameters from vc_tta_accordion
$copy_params = array(
	'scrolling',
	'remove_indents',
	'c_align',
	'title_tag',
	'c_icon',
	'c_position',
);
$accordion_params = us_config( 'elements/vc_tta_accordion.params', array() );
foreach ( $copy_params as $param_name ) {
	if ( ! empty( $accordion_params[ $param_name ] ) ) {

		// Remove weight for correct order
		if ( isset( $accordion_params[ $param_name ]['weight'] ) ) {
			unset( $accordion_params[ $param_name ]['weight'] );
		}

		// Add Accordion group name
		$accordion_params[ $param_name ]['group'] = us_translate( 'Accordion', 'js_composer' );

		$general_params[ $param_name ] = $accordion_params[ $param_name ];
	}
}

/**
 * @return array
 */
return array(
	'title' => __( 'Tabs', 'us' ),
	'category' => __( 'Containers', 'us' ),
	'icon' => 'fas fa-folder-plus',
	'is_container' => TRUE,
	'usb_moving_only_x_axis' => TRUE,
	'usb_root_container_selector' => '.w-tabs-sections:first',
	'weight' => 360, // go after Accordion element, which has "370" weight
	'as_child' => array(
		'except' => 'vc_tta_section',
	),
	'as_parent' => array(
		'only' => 'vc_tta_section',
	),
	'params' => us_set_params_weight(
		$general_params,
		$design_options_params
	),
	'fallback_params' => array(
		'stretch',
	),

	// Default VC params which are not supported by the theme
	'vc_remove_params' => array(
		'active_section',
		'alignment',
		'autoplay',
		'color',
		'css_animation',
		'gap',
		'no_fill_content_area',
		'pagination_color',
		'pagination_style',
		'shape',
		'spacing',
		'style',
		'tab_position',
		'title',
		'section_title_tag',
	),

	'usb_init_js' => '$elm.wTabs()',
);
