<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options > Field Styles
 */

$misc = us_config( 'elements_misc' );

return array(
	'title' => __( 'Field Styles', 'us' ),
	'fields' => array(
		'input_fields' => array(
			'type' => 'group',
			'preview' => 'input_fields',
			'preview_class_format' => 'us-field-style_%s',
			'is_accordion' => TRUE,
			'is_duplicate' => TRUE,
			'is_sortable' => TRUE,
			'show_controls' => TRUE,
			'accordion_title' => 'name',
			'params' => array(
				'id' => array(
					'type' => 'hidden',
					'std' => NULL,
				),
				'name' => array(
					'title' => __( 'Field Style Name', 'us' ),
					'type' => 'text',
					'std' => us_translate( 'Style' ),
					'cols' => 2,
				),
				'class' => array(
					'title' => __( 'Extra class', 'us' ),
					'description' => __( 'Will be added to the container with this style', 'us' ),
					'type' => 'text',
					'std' => '',
					'cols' => 2,
					'classes' => 'desc_4',
				),

				// Colors
				'color_bg' => array(
					'title' => us_translate( 'Colors' ),
					'type' => 'color',
					'clear_pos' => 'left',
					'std' => '_content_bg_alt',
					'text' => us_translate( 'Background' ),
					'cols' => 2,
				),
				'color_bg_focus' => array(
					'title' => __( 'Colors on focus', 'us' ),
					'type' => 'color',
					'clear_pos' => 'left',
					'std' => '_content_bg_alt',
					'text' => us_translate( 'Background' ),
					'cols' => 2,
				),
				'color_border' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'with_gradient' => FALSE,
					'std' => '_content_border',
					'text' => us_translate( 'Border' ),
					'cols' => 2,
				),
				'color_border_focus' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'with_gradient' => FALSE,
					'std' => '_content_border',
					'text' => us_translate( 'Border' ),
					'cols' => 2,
				),
				'color_text' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'with_gradient' => FALSE,
					'std' => '_content_text',
					'text' => us_translate( 'Text' ),
					'cols' => 2,
				),
				'color_text_focus' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'with_gradient' => FALSE,
					'std' => '_content_text',
					'text' => us_translate( 'Text' ),
					'cols' => 2,
				),
				'color_shadow' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'with_gradient' => FALSE,
					'std' => 'rgba(0,0,0,0.2)',
					'text' => __( 'Shadow', 'us' ),
					'cols' => 2,
				),
				'color_shadow_focus' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'with_gradient' => FALSE,
					'std' => '_content_primary',
					'text' => __( 'Shadow', 'us' ),
					'cols' => 2,
				),

				// Shadow
				'wrapper_shadow_start' => array(
					'title' => __( 'Shadow', 'us' ),
					'type' => 'wrapper_start',
					'classes' => 'for_shadow',
				),
				'shadow_offset_h' => array(
					'description' => __( 'Hor. offset', 'us' ),
					'type' => 'slider',
					'std' => '0px',
					'options' => array(
						'px' => array(
							'min' => - 50,
							'max' => 50,
						),
						'em' => array(
							'min' => - 5.0,
							'max' => 5.0,
							'step' => 0.1,
						),
					),
					'classes' => 'slider_hide',
				),
				'shadow_offset_v' => array(
					'description' => __( 'Ver. offset', 'us' ),
					'type' => 'slider',
					'std' => '1px',
					'options' => array(
						'px' => array(
							'min' => - 50,
							'max' => 50,
						),
						'em' => array(
							'min' => - 5.0,
							'max' => 5.0,
							'step' => 0.1,
						),
					),
					'classes' => 'slider_hide',
				),
				'shadow_blur' => array(
					'description' => __( 'Blur', 'us' ),
					'type' => 'slider',
					'std' => '0px',
					'options' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
						'em' => array(
							'min' => 0.0,
							'max' => 5.0,
							'step' => 0.1,
						),
					),
					'classes' => 'slider_hide',
				),
				'shadow_spread' => array(
					'description' => __( 'Spread', 'us' ),
					'type' => 'slider',
					'std' => '0px',
					'options' => array(
						'px' => array(
							'min' => - 50,
							'max' => 50,
						),
						'em' => array(
							'min' => - 5.0,
							'max' => 5.0,
							'step' => 0.1,
						),
					),
					'classes' => 'slider_hide',
				),
				'shadow_inset' => array(
					'type' => 'checkboxes',
					'options' => array(
						'1' => __( 'Inner shadow', 'us' ),
					),
					'std' => '1',
				),
				'wrapper_shadow_end' => array(
					'type' => 'wrapper_end',
				),

				// Shadow on focus
				'wrapper_shadow_focus_start' => array(
					'title' => __( 'Shadow on focus', 'us' ),
					'type' => 'wrapper_start',
					'classes' => 'for_shadow',
				),
				'shadow_focus_offset_h' => array(
					'description' => __( 'Hor. offset', 'us' ),
					'type' => 'slider',
					'std' => '0px',
					'options' => array(
						'px' => array(
							'min' => - 50,
							'max' => 50,
						),
						'em' => array(
							'min' => - 5.0,
							'max' => 5.0,
							'step' => 0.1,
						),
					),
					'classes' => 'slider_hide',
				),
				'shadow_focus_offset_v' => array(
					'description' => __( 'Ver. offset', 'us' ),
					'type' => 'slider',
					'std' => '0px',
					'options' => array(
						'px' => array(
							'min' => - 50,
							'max' => 50,
						),
						'em' => array(
							'min' => - 5.0,
							'max' => 5.0,
							'step' => 0.1,
						),
					),
					'classes' => 'slider_hide',
				),
				'shadow_focus_blur' => array(
					'description' => __( 'Blur', 'us' ),
					'type' => 'slider',
					'std' => '0px',
					'options' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
						'em' => array(
							'min' => 0.0,
							'max' => 5.0,
							'step' => 0.1,
						),
					),
					'classes' => 'slider_hide',
				),
				'shadow_focus_spread' => array(
					'description' => __( 'Spread', 'us' ),
					'type' => 'slider',
					'std' => '2px',
					'options' => array(
						'px' => array(
							'min' => - 50,
							'max' => 50,
						),
						'em' => array(
							'min' => - 5.0,
							'max' => 5.0,
							'step' => 0.1,
						),
					),
					'classes' => 'slider_hide',
				),
				'shadow_focus_inset' => array(
					'type' => 'checkboxes',
					'options' => array(
						'1' => __( 'Inner shadow', 'us' ),
					),
					'std' => '',
				),
				'wrapper_shadow_focus_end' => array(
					'type' => 'wrapper_end',
				),

				// Typography & Sizes
				'font' => array(
					'title' => __( 'Font', 'us' ),
					'type' => 'select',
					'options' => us_get_fonts_for_selection(),
					'std' => '',
					'cols' => 2,
				),
				'height' => array(
					'title' => us_translate( 'Height' ),
					'type' => 'slider',
					'std' => '3em',
					'options' => array(
						'px' => array(
							'min' => 30,
							'max' => 80,
						),
						'em' => array(
							'min' => 2.0,
							'max' => 5.0,
							'step' => 0.1,
						),
						'rem' => array(
							'min' => 2.0,
							'max' => 5.0,
							'step' => 0.1,
						),
					),
					'cols' => 2,
				),
				'font_size' => array(
					'title' => __( 'Font Size', 'us' ),
					'type' => 'slider',
					'std' => '1em',
					'options' => array(
						'px' => array(
							'min' => 10,
							'max' => 30,
						),
						'em' => array(
							'min' => 0.5,
							'max' => 2.0,
							'step' => 0.1,
						),
						'rem' => array(
							'min' => 0.5,
							'max' => 2.0,
							'step' => 0.1,
						),
					),
					'cols' => 2,
				),
				'padding' => array(
					'title' => __( 'Side Indents', 'us' ),
					'type' => 'slider',
					'std' => '1em',
					'options' => array(
						'px' => array(
							'min' => 0,
							'max' => 30,
						),
						'em' => array(
							'min' => 0.0,
							'max' => 2.0,
							'step' => 0.1,
						),
						'rem' => array(
							'min' => 0.0,
							'max' => 2.0,
							'step' => 0.1,
						),
					),
					'cols' => 2,
				),
				'font_weight' => array(
					'title' => __( 'Font Weight', 'us' ),
					'type' => 'slider',
					'std' => 400,
					'options' => array(
						'' => array(
							'min' => 100,
							'max' => 900,
							'step' => 100,
						),
					),
					'cols' => 2,
				),
				'border_width' => array(
					'title' => __( 'Border Width', 'us' ),
					'type' => 'slider',
					'std' => '0px',
					'options' => array(
						'px' => array(
							'min' => 0,
							'max' => 10,
						),
					),
					'cols' => 2,
				),
				'letter_spacing' => array(
					'title' => __( 'Letter Spacing', 'us' ),
					'type' => 'slider',
					'std' => '0em',
					'options' => array(
						'em' => array(
							'min' => - 0.10,
							'max' => 0.20,
							'step' => 0.01,
						),
					),
					'cols' => 2,
				),
				'border_radius' => array(
					'title' => __( 'Border Radius', 'us' ),
					'description' => $misc['desc_border_radius'],
					'type' => 'text',
					'std' => '0.3em',
					'classes' => 'desc_4',
					'cols' => 2,
				),
				'text_transform' => array(
					'title' => __( 'Text Transform', 'us' ),
					'type' => 'select',
					'options' => array(
						'none' => us_translate( 'None' ),
						'uppercase' => 'UPPERCASE',
						'lowercase' => 'lowercase',
						'capitalize' => 'Capitalize',
					),
					'std' => 'none',
					'cols' => 2,
				),
				'checkbox_size' => array(
					'title' => __( 'Checkbox Size', 'us' ),
					'type' => 'slider',
					'std' => '1.5em',
					'options' => array(
						'px' => array(
							'min' => 16,
							'max' => 40,
						),
						'em' => array(
							'min' => 1.0,
							'max' => 3.0,
							'step' => 0.1,
						),
						'rem' => array(
							'min' => 1.0,
							'max' => 3.0,
							'step' => 0.1,
						),
					),
					'cols' => 2,
				),
			),

			// Default styles after options reset
			'std' => array(
				array(
					'id' => '1',
					'name' => __( 'Default Style', 'us' ),
					'color_bg' => '_content_bg_alt',
					'color_bg_focus' => '_content_bg_alt',
					'color_border' => '_content_border',
					'color_border_focus' => '_content_border',
					'color_text' => '_content_text',
					'color_text_focus' => '_content_text',
					'color_shadow' => 'rgba(0,0,0,0.08)',
					'color_shadow_focus' => '_content_primary',
					'shadow_offset_h' => '0px',
					'shadow_offset_v' => '1px',
					'shadow_blur' => '0px',
					'shadow_spread' => '0px',
					'shadow_inset' => '1',
					'shadow_focus_offset_h' => '0px',
					'shadow_focus_offset_v' => '0px',
					'shadow_focus_blur' => '0px',
					'shadow_focus_spread' => '2px',
					'shadow_focus_inset' => '',
					'font' => '',
					'font_size' => '1em',
					'font_weight' => '400',
					'letter_spacing' => '0em',
					'text_transform' => 'none',
					'height' => '3em',
					'padding' => '1em',
					'border_radius' => '0.3em',
					'border_width' => '0px',
					'checkbox_size' => '1.5em',
				),
			),
		),
	),
);
