<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options > Button Styles
 */

$misc = us_config( 'elements_misc' );

return array(
	'title' => __( 'Button Styles', 'us' ),
	'fields' => array(
		'buttons' => array(
			'type' => 'group',
			'preview' => 'button',
			'preview_class_format' => 'us-btn-style_%s',
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
					'title' => __( 'Button Style Name', 'us' ),
					'type' => 'text',
					'std' => us_translate( 'Style' ),
					'cols' => 3,
				),
				'hover' => array(
					'title' => __( 'Hover Style', 'us' ),
					'description' => __( '"Slide background from the top" may not work with buttons of 3rd-party plugins.', 'us' ),
					'type' => 'select',
					'options' => array(
						'fade' => __( 'Simple color change', 'us' ),
						'slide' => __( 'Slide background from the top', 'us' ),
					),
					'std' => 'fade',
					'cols' => 3,
					'classes' => 'desc_4',
				),
				'class' => array(
					'title' => __( 'Extra class', 'us' ),
					'description' => __( 'Will be added to all buttons with this style', 'us' ),
					'type' => 'text',
					'std' => '',
					'cols' => 3,
					'classes' => 'desc_4',
				),

				// Button Colors
				'color_bg' => array(
					'title' => us_translate( 'Colors' ),
					'type' => 'color',
					'clear_pos' => 'left',
					'std' => '_content_secondary',
					'text' => us_translate( 'Background' ),
					'cols' => 2,
				),
				'color_bg_hover' => array(
					'title' => __( 'Colors on hover', 'us' ),
					'type' => 'color',
					'clear_pos' => 'left',
					'std' => '',
					'text' => us_translate( 'Background' ),
					'cols' => 2,
				),
				'color_border' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'std' => '',
					'text' => us_translate( 'Border' ),
					'cols' => 2,
				),
				'color_border_hover' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'std' => '_content_secondary',
					'text' => us_translate( 'Border' ),
					'cols' => 2,
				),
				'color_text' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'with_gradient' => FALSE,
					'std' => '#fff',
					'text' => us_translate( 'Text' ),
					'cols' => 2,
				),
				'color_text_hover' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'with_gradient' => FALSE,
					'std' => '_content_secondary',
					'text' => us_translate( 'Text' ),
					'cols' => 2,
				),
				'color_shadow' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'with_gradient' => FALSE,
					'std' => '',
					'text' => __( 'Shadow', 'us' ),
					'cols' => 2,
				),
				'color_shadow_hover' => array(
					'type' => 'color',
					'clear_pos' => 'left',
					'with_gradient' => FALSE,
					'std' => '',
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
					'std' => '',
				),
				'wrapper_shadow_end' => array(
					'type' => 'wrapper_end',
				),

				// Shadow on focus
				'wrapper_shadow_hover_start' => array(
					'title' => __( 'Shadow on hover', 'us' ),
					'type' => 'wrapper_start',
					'classes' => 'for_shadow',
				),
				'shadow_hover_offset_h' => array(
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
				'shadow_hover_offset_v' => array(
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
				'shadow_hover_blur' => array(
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
				'shadow_hover_spread' => array(
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
				'shadow_hover_inset' => array(
					'type' => 'checkboxes',
					'options' => array(
						'1' => __( 'Inner shadow', 'us' ),
					),
					'std' => '',
				),
				'wrapper_shadow_hover_end' => array(
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
					'title' => __( 'Relative Height', 'us' ),
					'type' => 'slider',
					'std' => '0.8em',
					'options' => array(
						'em' => array(
							'min' => 0.0,
							'max' => 2.0,
							'step' => 0.1,
						),
					),
					'cols' => 2,
				),
				'font_size' => array(
					'title' => __( 'Font Size', 'us' ),
					'type' => 'slider',
					'std' => '1rem',
					'options' => array(
						'px' => array(
							'min' => 10,
							'max' => 50,
						),
						'em' => array(
							'min' => 0.6,
							'max' => 3.0,
							'step' => 0.1,
						),
						'rem' => array(
							'min' => 0.6,
							'max' => 3.0,
							'step' => 0.1,
						),
					),
					'cols' => 2,
				),
				'width' => array(
					'title' => __( 'Relative Width', 'us' ),
					'type' => 'slider',
					'std' => '1.8em',
					'options' => array(
						'em' => array(
							'min' => 0.0,
							'max' => 5.0,
							'step' => 0.1,
						),
					),
					'cols' => 2,
				),
				'line_height' => array(
					'title' => __( 'Line height', 'us' ),
					'type' => 'slider',
					'std' => '1.2',
					'options' => array(
						'' => array(
							'min' => 1.00,
							'max' => 2.00,
							'step' => 0.01,
						),
						'px' => array(
							'min' => 10,
							'max' => 50,
						),
					),
					'cols' => 2,
				),
				'border_width' => array(
					'title' => __( 'Border Width', 'us' ),
					'type' => 'slider',
					'std' => '2px',
					'options' => array(
						'px' => array(
							'min' => 0,
							'max' => 10,
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
				'border_radius' => array(
					'title' => __( 'Border Radius', 'us' ),
					'description' => $misc['desc_border_radius'],
					'type' => 'text',
					'std' => '0.3em',
					'classes' => 'desc_4',
					'cols' => 2,
				),
				'letter_spacing' => array(
					'title' => __( 'Letter Spacing', 'us' ),
					'type' => 'slider',
					'std' => 0,
					'options' => array(
						'em' => array(
							'min' => - 0.10,
							'max' => 0.20,
							'step' => 0.01,
						),
					),
					'cols' => 2,
				),
				'text_style' => array(
					'title' => __( 'Text Styles', 'us' ),
					'type' => 'checkboxes',
					'options' => array(
						'uppercase' => __( 'Uppercase', 'us' ),
						'italic' => __( 'Italic', 'us' ),
					),
					'std' => '',
					'cols' => 2,
				),
			),

			// Default styles after options reset
			'std' => array(
				array(
					'id' => 1,
					'name' => __( 'Default Button', 'us' ),
					'hover' => 'fade',
					// predefined colors after options reset
					'color_bg' => '_content_primary',
					'color_bg_hover' => '_content_secondary',
					'color_border' => '',
					'color_border_hover' => '',
					'color_text' => '#fff',
					'color_text_hover' => '#fff',
					'font' => '',
					'text_style' => '',
					'font_size' => '16px',
					'line_height' => '1.2',
					'font_weight' => '700',
					'letter_spacing' => '0em',
					'height' => '1.0em',
					'width' => '2.0em',
					'border_radius' => '0.3em',
					'border_width' => '0px',
				),
				array(
					'id' => 2,
					'name' => __( 'Button', 'us' ) . ' 2',
					'hover' => 'fade',
					// predefined colors after options reset
					'color_bg' => '_content_border',
					'color_bg_hover' => '_content_text',
					'color_border' => '',
					'color_border_hover' => '',
					'color_text' => '_content_text',
					'color_text_hover' => '_content_bg',
					'font' => '',
					'text_style' => '',
					'font_size' => '16px',
					'line_height' => '1.2',
					'font_weight' => '700',
					'letter_spacing' => '0em',
					'height' => '1.0em',
					'width' => '2.0em',
					'border_radius' => '0.3em',
					'border_width' => '0px',
				),
			),
		),
	),
);
