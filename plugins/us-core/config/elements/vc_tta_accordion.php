<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: vc_column_accordion
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

$source_options = array(
	'' => '– ' . us_translate( 'Default' ) . ' –',
);

/**
 * General section
 *
 * @var array
 */
$general_params = array(
	'toggle' => array(
		'switch_text' => __( 'Allow several sections to be opened at the same time', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'usb_preview' => TRUE,
	),
	'scrolling' => array(
		'switch_text' => __( 'Scroll to the beginning of the section when opening', 'us' ),
		'type' => 'switch',
		'std' => 1,
		'show_if' => array( 'toggle', '=', 0 ),
		'usb_preview' => TRUE, // Note: Settings for js library (tabs.js)
	),
	'remove_indents' => array(
		'switch_text' => __( 'Remove left and right indents', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'usb_preview' => array(
			'toggle_class' => 'remove_indents',
		),
	),
	'faq_markup' => array(
		'switch_text' => __( 'Add FAQ structured data markup', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'place_if' => us_get_option( 'schema_markup' ),
	),
	'c_align' => array(
		'title' => __( 'Title Alignment', 'us' ),
		'type' => 'radio',
		'labels_as_icons' => 'fas fa-align-*',
		'options' => array(
			'none' => us_translate( 'Default' ),
			'center' => us_translate( 'Center' ),
		),
		'std' => 'none',
		'usb_preview' => array(
			'elm' => '.w-tabs-sections',
			'mod' => 'titles-align',
		),
	),
	'title_size' => array(
		'title' => __( 'Title Size', 'us' ),
		'description' => $misc['desc_font_size'],
		'type' => 'text',
		'std' => 'inherit',
		'cols' => 2,
		'usb_preview' => array(
			'css' => '--sections-title-size',
		),
	),
	'title_tag' => array(
		'title' => __( 'Title HTML tag', 'us' ),
		'type' => 'select',
		'options' => $misc['html_tag_values'],
		'std' => 'div',
		'cols' => 2,
		'usb_preview' => array(
			'elm' => '.w-tabs-section-title',
			'attr' => 'tag',
		),
	),
	'c_icon' => array(
		'title' => __( 'Control Icon', 'us' ),
		'type' => 'radio',
		'options' => array(
			'' => us_translate( 'None' ),
			'chevron' => __( 'Chevron', 'us' ),
			'plus' => __( 'Plus', 'us' ),
			'triangle' => __( 'Triangle', 'us' ),
		),
		'std' => 'chevron',
		'usb_preview' => array(
			'elm' => '.w-tabs-sections',
			'mod' => 'icon',
		),
	),
	'c_position' => array(
		'title' => __( 'Control Position', 'us' ),
		'type' => 'radio',
		'options' => array(
			'left' => __( 'Before title', 'us' ),
			'right' => __( 'After title', 'us' ),
		),
		'std' => 'right',
		'show_if' => array( 'c_icon', '!=', '' ),
		'usb_preview' => array(
			'elm' => '.w-tabs-sections',
			'mod' => 'cpos',
		),
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

/**
 * @return array
 */
return array(
	'title' => us_translate( 'Accordion', 'js_composer' ),
	'category' => __( 'Containers', 'us' ),
	'icon' => 'fas fa-plus-square',
	'is_container' => TRUE,
	'usb_root_container_selector' => '.w-tabs-sections:first',
	'weight' => 370, // go after all US elements, which have "380" weight
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

	// Default VC params which are not supported by the theme
	'vc_remove_params' => array(
		'active_section',
		'autoplay',
		'collapsible_all',
		'color',
		'css_animation',
		'gap',
		'no_fill',
		'shape',
		'spacing',
		'style',
		'title',
		'section_title_tag',
	),

	'usb_init_js' => '$elm.wTabs()',
);
