<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options > Typography
 */

global $usof_options, $help_portal_url;

$misc = us_config( 'elements_misc' );

$live_buider_is_enabled = ! empty( $usof_options['live_builder'] ) ? TRUE : FALSE;

// Get Uploaded Fonts for selection
$uploaded_fonts_options = array();
if ( isset( $usof_options['uploaded_fonts'] ) AND $uploaded_fonts = $usof_options['uploaded_fonts'] ) {
	foreach ( $uploaded_fonts as $uploaded_font ) {
		$uploaded_font_name = us_sanitize_font_family( $uploaded_font['name'] );
		if (
			empty( $uploaded_font_name )
			OR empty( $uploaded_font['files'] )
		) {
			continue;
		}
		$uploaded_fonts_options[ __( 'Uploaded Fonts', 'us' ) ][ $uploaded_font_name ] = $uploaded_font_name;
	}
}

// Adobe Fonts
$adobe_fonts_options = array();
foreach ( us_get_adobe_fonts() as $font_slug => $font_name ) {
	$adobe_fonts_options[ __( 'Adobe Fonts (loaded from Adobe servers)', 'us' ) ][ $font_slug ] = $font_name;
}

// Get Web Safe fonts for selection
foreach ( us_config( 'web-safe-fonts' ) as $web_safe_font ) {
	$websafe_fonts_options[ __( 'Web safe font combinations (do not need to be loaded)', 'us' ) ][ $web_safe_font ] = $web_safe_font;
}

// Generate Typography settings for Headings 1-6
$typography_heading_settings = array();
for ( $h = 1; $h <= 6; $h++ ) {

	// Separate first options for Heading 1 and Headings 2-5
	if ( $h == 1 ) {
		$first_font_family_option = array( 'inherit' => '– ' . __( 'As in Global Text', 'us' ) . ' –', );
		$first_font_weight_option = array();
		$first_bold_font_weight_option = array();
		$first_text_transform_option = array();
		$first_font_style_option = array();
	} else {
		$first_font_family_option = array(
			'inherit' => '– ' . __( 'As in Global Text', 'us' ) . ' –',
			'var(--h1-font-family)' => '– ' . __( 'As in Heading 1', 'us' ) . ' –',
			);
		$first_font_weight_option = array( 'var(--h1-font-weight)' => '– ' . __( 'As in Heading 1', 'us' ) . ' –', );
		$first_bold_font_weight_option = array( 'var(--h1-bold-font-weight)' => '– ' . __( 'As in Heading 1', 'us' ) . ' –', );
		$first_text_transform_option = array( 'var(--h1-text-transform)' => '– ' . __( 'As in Heading 1', 'us' ) . ' –', );
		$first_font_style_option = array( 'var(--h1-font-style)' => '– ' . __( 'As in Heading 1', 'us' ) . ' –', );
	}

	// Default font-size value based on heading number
	$default_font_size = sprintf( 'calc(%spx + %svw)', round( 12 + 20 / $h ), round( 0.5 + 1.5 / $h, 1 ) );

	$typography_heading_settings[ 'h' . $h ] = array(
		'title' => sprintf( __( 'Heading %s', 'us' ), $h ),
		'type' => 'typography_options',
		'fields' => array(
			'font-family' => array(
				'title' => __( 'Font', 'us' ),
				'type' => 'autocomplete',
				'preview_text' => usb_is_builder_page() ? FALSE : array(
					'text' => sprintf( __( 'Heading %s preview', 'us' ), $h ),
					'typography_tag' => 'h' . $h,
				),
				// TODO: improve autocomplete logic: no need to take separator into account for non-multiple
				'value_separator' => '/',
				'options' => us_array_merge(
					$first_font_family_option,
					$adobe_fonts_options,
					$uploaded_fonts_options,
					$websafe_fonts_options,
					us_get_all_google_fonts()
				),
				'std' => ( $h == 1 ) ? 'inherit' : 'var(--h1-font-family)',
			),
			'font-size' => array(
				'title' => __( 'Font Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => $default_font_size,
				'is_responsive' => TRUE,
				'cols' => 2,
			),
			'line-height' => array(
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
						'min' => 20,
						'max' => 100,
					),
				),
				'is_responsive' => TRUE,
				'cols' => 2,
			),
			'font-weight' => array(
				'title' => __( 'Font Weight', 'us' ),
				'type' => 'select',
				'options' => us_array_merge(
					$first_font_weight_option,
					array(
						'100' => '100 ' . __( 'thin', 'us' ),
						'200' => '200 ' . __( 'extra-light', 'us' ),
						'300' => '300 ' . __( 'light', 'us' ),
						'400' => '400 ' . __( 'normal', 'us' ),
						'500' => '500 ' . __( 'medium', 'us' ),
						'600' => '600 ' . __( 'semi-bold', 'us' ),
						'700' => '700 ' . __( 'bold', 'us' ),
						'800' => '800 ' . __( 'extra-bold', 'us' ),
						'900' => '900 ' . __( 'ultra-bold', 'us' ),
					)
				),
				'std' => ( $h == 1 ) ? '400' : 'var(--h1-font-weight)',
				'is_responsive' => TRUE,
				'cols' => 2,
			),
			'bold-font-weight' => array(
				'title' => __( 'Bold Text Font Weight', 'us' ),
				'type' => 'select',
				'options' => us_array_merge(
					$first_bold_font_weight_option,
					array(
						'100' => '100 ' . __( 'thin', 'us' ),
						'200' => '200 ' . __( 'extra-light', 'us' ),
						'300' => '300 ' . __( 'light', 'us' ),
						'400' => '400 ' . __( 'normal', 'us' ),
						'500' => '500 ' . __( 'medium', 'us' ),
						'600' => '600 ' . __( 'semi-bold', 'us' ),
						'700' => '700 ' . __( 'bold', 'us' ),
						'800' => '800 ' . __( 'extra-bold', 'us' ),
						'900' => '900 ' . __( 'ultra-bold', 'us' ),
					)
				),
				'std' => ( $h == 1 ) ? '700' : 'var(--h1-bold-font-weight)',
				'is_responsive' => TRUE,
				'cols' => 2,
			),
			'text-transform' => array(
				'title' => __( 'Text Transform', 'us' ),
				'type' => 'select',
				'options' => us_array_merge(
					$first_text_transform_option,
					array(
						'none' => us_translate( 'None' ),
						'uppercase' => 'UPPERCASE',
						'lowercase' => 'lowercase',
						'capitalize' => 'Capitalize',
					)
				),
				'std' => ( $h == 1 ) ? 'none' : 'var(--h1-text-transform)',
				'is_responsive' => TRUE,
				'cols' => 2,
			),
			'font-style' => array(
				'title' => __( 'Font Style', 'us' ),
				'type' => 'select',
				'options' => us_array_merge(
					$first_font_style_option,
					array(
						'normal' => __( 'normal', 'us' ),
						'italic' => __( 'italic', 'us' ),
					)
				),
				'std' => ( $h == 1 ) ? 'normal' : 'var(--h1-font-style)',
				'is_responsive' => TRUE,
				'cols' => 2,
			),
			'letter-spacing' => array(
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
				'is_responsive' => TRUE,
				'cols' => 2,
			),
			'margin-bottom' => array(
				'title' => __( 'Bottom indent', 'us' ),
				'type' => 'slider',
				'std' => '1.5rem',
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
					'rem' => array(
						'min' => 0.0,
						'max' => 5.0,
						'step' => 0.1,
					),
				),
				'is_responsive' => TRUE,
				'cols' => 2,
			),
			'color' => array(
				'title' => us_translate( 'Color' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => 'custom_field',
				'std' => '',
				'usb_preview' => TRUE,
				'cols' => 2,
			),
			'color_override' => array(
				'type' => 'checkboxes',
				'options' => array(
					'1' => __( 'Override color globally', 'us' ),
				),
				'std' => '',
				'classes' => 'for_above',
				'usb_preview' => TRUE,
			),
		),
		'usb_preview' => array(
			'elm' => '#' . US_BUILDER_TYPOGRAPHY_TAG_ID,
			'typography' => TRUE,
		),
	);
}

// Create edit link for Typography in Live
$front_page_id = (int) get_option( 'page_on_front' );
$usb_edit_typography_link = usb_get_edit_link(
	$front_page_id,
	array(
		'action' => US_BUILDER_SITE_SETTINGS_SLUG,
		'group' => 'typography'
	)
);

return array(
	'title' => __( 'Typography', 'us' ),
	'fields' => array_merge(

		array(
			'typography_head_message' => array(
				'description' => '<a target="_blank" href="' . esc_url( $usb_edit_typography_link ) . '"><strong>' . us_translate( 'Customize Live' ) . '</strong></a>',
				'type' => 'message',
				'classes' => 'customize_live',
				'place_if' => $live_buider_is_enabled,
			),

			// Global Text
			'body' => array(
				'title' => __( 'Global Text', 'us' ),
				'type' => 'typography_options',
				'fields' => array(
					'font-family' => array(
						'title' => __( 'Font', 'us' ),
						'type' => 'autocomplete',
						'preview_text' => usb_is_builder_page() ? FALSE : array(
							'text' => __( 'Here\'s a preview of what your website\'s text will look like <strong>by default</strong>. You can also adjust the typography of most elements separately. Note that the Font Size setting affects all the sizes defined in "rem" units, that is, almost all areas of your site.', 'us' ),
							'typography_tag' => 'body',
						),
						// TODO: improve autocomplete logic: no need to take separator into account for non-multiple
						'value_separator' => '/',
						'options' => us_array_merge(
							array( 'none' => __( 'No font specified', 'us' ) ),
							$uploaded_fonts_options,
							$adobe_fonts_options,
							$websafe_fonts_options,
							us_get_all_google_fonts()
						),
						'std' => 'Georgia, serif',
					),
					'font-size' => array(
						'title' => __( 'Font Size', 'us' ),
						'description' => $misc['desc_font_size'],
						'type' => 'text',
						'std' => '16px',
						'is_responsive' => TRUE,
						'cols' => 2,
					),
					'line-height' => array(
						'title' => __( 'Line height', 'us' ),
						'type' => 'slider',
						'std' => '28px',
						'options' => array(
							'' => array(
								'min' => 1.00,
								'max' => 2.00,
								'step' => 0.01,
							),
							'px' => array(
								'min' => 20,
								'max' => 100,
							),
						),
						'is_responsive' => TRUE,
						'cols' => 2,
					),
					'font-weight' => array(
						'title' => __( 'Font Weight', 'us' ),
						'type' => 'select', // Note: sync with the font is attached to this type, do not change the type!
						'options' => array(
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
						'std' => '400',
						'is_responsive' => TRUE,
						'cols' => 2,
					),
					'bold-font-weight' => array(
						'title' => __( 'Bold Text Font Weight', 'us' ),
						'type' => 'select', // Note: sync with the font is attached to this type, do not change the type!
						'options' => array(
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
						'std' => '700',
						'is_responsive' => TRUE,
						'cols' => 2,
					),
					'text-transform' => array(
						'title' => __( 'Text Transform', 'us' ),
						'type' => 'select',
						'options' => array(
							'none' => us_translate( 'None' ),
							'uppercase' => 'UPPERCASE',
							'lowercase' => 'lowercase',
							'capitalize' => 'Capitalize',
						),
						'std' => 'none',
						'is_responsive' => TRUE,
						'cols' => 2,
					),
					'font-style' => array(
						'title' => __( 'Font Style', 'us' ),
						'type' => 'select',
						'options' => array(
							'normal' => __( 'normal', 'us' ),
							'italic' => __( 'italic', 'us' ),
						),
						'std' => 'normal',
						'is_responsive' => TRUE,
						'cols' => 2,
					),
					'letter-spacing' => array(
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
						'is_responsive' => TRUE,
						'cols' => 2,
					),
				),
				'usb_preview' => array(
					'elm' => '#' . US_BUILDER_TYPOGRAPHY_TAG_ID,
					'typography' => TRUE,
				),
			),
		),
		$typography_heading_settings,
		array(

			// Additional Google Fonts
			'h_typography_3' => array(
				'title' => __( 'Additional Google Fonts', 'us' ),
				'description' => __( 'In case when you need more Google Fonts in theme elements.', 'us' ),
				'type' => 'heading',
			),
			'custom_font' => array(
				'type' => 'group',
				'accordion_title' => 'font_family',
				'is_accordion' => FALSE,
				'is_duplicate' => FALSE,
				'show_controls' => TRUE,
				'std' => array(),
				'params' => array(
					'font_family' => array(
						'type' => 'font',
						'preview_text' => array(
							'text' => __( 'Google Font Preview', 'us' ),
						),
						'std' => 'Open Sans',
					),
				),
			),

			// Uploaded Fonts
			'h_typography_4' => array(
				'title' => __( 'Uploaded Fonts', 'us' ),
				'description' => sprintf( __( 'Add custom fonts via uploading %s files.', 'us' ), '<strong>woff</strong>, <strong>woff2</strong>' ) . ' <a target="_blank" href="' . $help_portal_url . '/' . strtolower( US_THEMENAME ) . '/typography/#uploaded-fonts">' . __( 'Learn more', 'us' ) . '</a>.',
				'type' => 'heading',
			),
			'uploaded_fonts' => array(
				'type' => 'group',
				'accordion_title' => 'name',
				'is_accordion' => FALSE,
				'is_duplicate' => FALSE,
				'show_controls' => TRUE,
				'std' => array(),
				'params' => array(
					'name' => array(
						'title' => __( 'Font Name', 'us' ),
						'type' => 'text',
						'std' => 'Uploaded Font',
						'classes' => 'cols_2',
					),
					'weight' => array(
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
						'classes' => 'cols_2',
					),
					'italic' => array(
						'type' => 'checkboxes',
						'options' => array(
							'italic' => __( 'Italic', 'us' ),
						),
						'std' => '',
						'classes' => 'for_above',
					),
					'files' => array(
						'title' => __( 'Font Files', 'us' ),
						'type' => 'upload',
						'is_multiple' => TRUE,
						'preview_type' => 'text',
					),
				),
			),

			// Font Display
			'h_typography_5' => array(
				'title' => __( 'Font Display', 'us' ),
				'description' => __( 'Sets behavior of fonts rendering.', 'us' ) . ' <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/font-display" target="_blank">' . __( 'Learn more', 'us' ) . '</a>.',
				'type' => 'heading',
			),
			'font_display' => array(
				'type' => 'radio',
				'options' => array(
					'block' => 'block',
					'swap' => 'swap',
					'fallback' => 'fallback',
					'optional' => 'optional',
				),
				'std' => 'swap',
				'classes' => 'for_above',
			),

			// Adobe Fonts
			'h_typography_6' => array(
				'title' => __( 'Adobe Fonts', 'us' ),
				'description' => sprintf( __( 'Paste the Project ID from your %sAdobe Web Project%s.', 'us' ), ' <a href="https://fonts.adobe.com/my_fonts#web_projects-section" target="_blank">', '</a>' ) . ' ' . __( 'Examples:', 'us' ) . ' abc9def, zyx8wuv',
				'type' => 'heading',
			),
			'adobe_fonts' => array(
				'type' => 'adobe_fonts',
				'classes' => 'for_above',
			),
		)
	),
);
