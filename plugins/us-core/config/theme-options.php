<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options
 *
 * @filter us_config_theme-options
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

global $usof_options, $help_portal_url;

$sidebar_titlebar_are_enabled = ! empty( $usof_options['enable_sidebar_titlebar'] ) ? TRUE : FALSE;
$live_buider_is_enabled = ! empty( $usof_options['live_builder'] ) ? TRUE : FALSE;

if ( ! empty( $usof_options['portfolio_rename'] ) ) {
	$renamed_portfolio_label = ' (' . wp_strip_all_tags( $usof_options['portfolio_label_name'], TRUE ) . ')';
} else {
	$renamed_portfolio_label = '';
}

global $pagenow;
$posts_titles = array();
$color_scheme_exclude_dynamic_colors = 'scheme, custom_field';

// Variables used for config in admin area only
if (
	! wp_doing_ajax()
	AND $pagenow == 'admin.php'
	AND isset( $_GET['page'] )
	AND $_GET['page'] == 'us-theme-options'
) {
	$posts_titles = ( array ) us_get_all_posts_titles_for( array(
		'page',
		'us_header',
		'us_page_block',
		'us_content_template',
	) );

	if ( empty( $usof_options['custom_colors'] ) ) {
		$color_scheme_exclude_dynamic_colors = 'all';
	}
}

// Get Pages and order alphabetically
$us_page_list = us_filter_posts_by_language( us_arr_path( $posts_titles, 'page', array() ) );

// Get Headers
$us_headers_list = us_filter_posts_by_language( us_arr_path( $posts_titles, 'us_header', array() ) );

// Get Reusable Blocks
$us_page_blocks_list = us_filter_posts_by_language( us_arr_path( $posts_titles, 'us_page_block', array() ) );

// Get Page Templates
$us_content_templates_list = us_filter_posts_by_language( us_arr_path( $posts_titles, 'us_content_template', array() ) );

// Use Reusable Blocks as Sidebars, if set in Theme Options
if ( ! empty( $usof_options['enable_page_blocks_for_sidebars'] ) ) {
	$sidebars_list = $us_page_blocks_list;
	$sidebar_hints_for = 'us_page_block';

	// else use regular sidebars
} else {
	$sidebars_list = us_get_sidebars();
	$sidebar_hints_for = NULL;
}
// Descriptions
$misc = us_config( 'elements_misc' );
$misc['headers_description'] .= '<br><img src="' . US_CORE_URI . '/admin/img/l-header.png">';
$misc['content_description'] .= '<br><img src="' . US_CORE_URI . '/admin/img/l-content.png">';
$misc['footers_description'] .= '<br><img src="' . US_CORE_URI . '/admin/img/l-footer.png">';

// Generate 'Pages Layout' options
$pages_layout_config = array();
foreach ( us_get_public_post_types( /* exclude */array( 'page', 'product' ) ) as $type => $title ) {

	// Rename "us_portfolio" suffix to avoid migration from old theme options
	if ( $type == 'us_portfolio' ) {
		$type = 'portfolio';
	}

	// Skip Events settings if the "Default Events Templates" is set
	if (
		$type == 'tribe_events'
		AND function_exists( 'tribe_get_option' )
		AND tribe_get_option( 'tribeEventsTemplate' ) != 'default'
	) {
		continue;
	}

	// Events Calendar separate option
	$tribe_events_full_event_template = array();
	if ( $type == 'tribe_events' AND class_exists( 'Tribe__Events__Query' ) ) {
		$tribe_events_full_event_template = array(
			'tribe_events_full_event_template' => array(
				'type' => 'switch',
				'switch_text' => __( 'Use full event template in the post content', 'us' ),
				'std' => 1,
				'classes' => 'for_above force_right',
			),
		);
	}

	$pages_layout_config = array_merge(
		$pages_layout_config, array_merge(
			array(
				'h_' . $type => array(
					'title' => $title,
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				// Header
				'header_' . $type . '_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_headers_list
					),
					'std' => '__defaults__',
				),
				// Titlebar
				'titlebar_' . $type . '_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				// Content
				'content_' . $type . '_id' => array(
					'title' => __( 'Page Template', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_content_template',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Show content as is', 'us' ) . ' &ndash;',
						), $us_content_templates_list
					),
					'std' => '__defaults__',
				),
			),
			$tribe_events_full_event_template,
			array(
				// Sidebar
				'sidebar_' . $type . '_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '__defaults__',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				// Sidebar Position
				'sidebar_' . $type . '_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_' . $type . '_id', '!=', array( '', '__defaults__' ) ),
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				// Footer
				'footer_' . $type . '_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
				),
			),
		),
	);
}

// Generate 'Archives Layout' options
$archives_layout_config = $tribe_archive_default = array();
$public_taxonomies = us_get_taxonomies( TRUE, FALSE, 'woocommerce_exclude' );
$custom_post_type_archives = (array) us_get_public_post_types( array( 'page', 'post', 'product' ), /* archive_only */TRUE );

foreach ( ( $custom_post_type_archives + $public_taxonomies ) as $type => $title ) {

	if ( $type == 'tribe_events' ) {

		// Skip Events settings if the "Default Events Templates" is set
		if ( function_exists( 'tribe_get_option' ) AND tribe_get_option( 'tribeEventsTemplate' ) != 'default' ) {
			continue;

			// Additional "Default Events Template" for archive
		} else {
			$tribe_archive_default[''] = '&ndash; ' . us_translate( 'Default Events Template', 'the-events-calendar' ) . ' &ndash;';
		}
	}

	$archives_layout_config = array_merge(
		$archives_layout_config, array(
			'h_tax_' . $type => array(
				'title' => $title,
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			// Header
			'header_tax_' . $type . '_id' => array(
				'title' => _x( 'Header', 'site top area', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_header',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $us_headers_list
				),
				'std' => '__defaults__',
			),
			// Titlebar
			'titlebar_tax_' . $type . '_id' => array(
				'title' => __( 'Titlebar', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_page_block',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $us_page_blocks_list
				),
				'std' => '__defaults__',
				'place_if' => $sidebar_titlebar_are_enabled,
			),
			// Content
			'content_tax_' . $type . '_id' => array(
				'title' => __( 'Page Template', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_content_template',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
					), $tribe_archive_default, $us_content_templates_list
				),
				'std' => '__defaults__',
			),
			// Sidebar
			'sidebar_tax_' . $type . '_id' => array(
				'title' => __( 'Sidebar', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $sidebars_list
				),
				'hints_for' => $sidebar_hints_for,
				'std' => '__defaults__',
				'place_if' => $sidebar_titlebar_are_enabled,
			),
			// Sidebar Position
			'sidebar_tax_' . $type . '_pos' => array(
				'title_pos' => 'side',
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'right',
				'classes' => 'for_above',
				'show_if' => array( 'sidebar_tax_' . $type . '_id', '!=', array( '', '__defaults__' ) ),
				'place_if' => $sidebar_titlebar_are_enabled,
			),
			// Footer
			'footer_tax_' . $type . '_id' => array(
				'title' => __( 'Footer', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'hints_for' => 'us_page_block',
				'options' => us_array_merge(
					array(
						'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
						'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
					), $us_page_blocks_list
				),
				'std' => '__defaults__',
			),
		)
	);

}

// Generate Product taxonomies Layout options
$shop_layout_config = array();
if ( class_exists( 'woocommerce' ) ) {
	$product_taxonomies = us_get_taxonomies( TRUE, FALSE, 'woocommerce_only' );
	foreach ( $product_taxonomies as $type => $title ) {

		$shop_layout_config = array_merge(
			$shop_layout_config, array(
				'h_tax_' . $type => array(
					'title' => $title,
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				// Header
				'header_tax_' . $type . '_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_headers_list
					),
					'std' => '__defaults__',
				),
				// Titlebar
				'titlebar_tax_' . $type . '_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				// Content
				'content_tax_' . $type . '_id' => array(
					'title' => __( 'Page Template', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_content_template',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
						), $us_content_templates_list
					),
					'std' => '__defaults__',
				),
				// Sidebar
				'sidebar_tax_' . $type . '_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '__defaults__',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				// Sidebar Position
				'sidebar_tax_' . $type . '_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_tax_' . $type . '_id', '!=', array( '', '__defaults__' ) ),
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				// Footer
				'footer_tax_' . $type . '_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
				),
			)
		);

	}
}

// Generate Images Sizes description
$img_size_info = '';
if ( ! wp_doing_ajax() AND $pagenow == 'admin.php' ) {
	$img_size_info .= '<span class="usof-tooltip"><strong>';
	$img_size_info .= sprintf( __( '%s different images sizes are registered.', 'us' ), count( us_get_image_sizes_list( FALSE ) ) );
	$img_size_info .= '</strong><span class="usof-tooltip-text">';
	foreach ( us_get_image_sizes_list( FALSE ) as $size_name => $size_title ) {
		$img_size_info .= $size_title . ' <code>' . $size_name . '</code>';
		$img_size_info .= '<br>';
	}
	$img_size_info .= '</span></span><br>';

	// Add link to Media Settings admin page
	$img_size_info .= sprintf( __( 'To change the default image sizes, go to %s.', 'us' ), '<a target="_blank" href="' . admin_url( 'options-media.php' ) . '">' . us_translate( 'Media Settings' ) . '</a>' );

	// Add link to Customizing > WooCommerce > Product Images
	if ( class_exists( 'woocommerce' ) ) {
		$img_size_info .= '<br>' . sprintf(
				__( 'To change the Product image sizes, go to %s.', 'us' ), '<a target="_blank" href="' . esc_url(
					add_query_arg(
						array(
							'autofocus' => array(
								'panel' => 'woocommerce',
								'section' => 'woocommerce_product_images',
							),
							'url' => wc_get_page_permalink( 'shop' ),
						), admin_url( 'customize.php' )
					)
				) . '">' . us_translate( 'WooCommerce settings', 'woocommerce' ) . '</a>'
			);
	}
}

// Generate Icon Sets settings
$icon_sets_config = array();
$icon_sets = us_config( 'icon-sets', array() );
foreach ( $icon_sets as $icon_set_slug => $icon_set ) {

	$icon_sets_config = array_merge(
		$icon_sets_config, array(
			'icons_' . $icon_set_slug => array(
				'title' => $icon_set['set_name'],
				'title_pos' => 'side',
				'type' => 'radio',
				'options' => array(
					'default' => us_translate( 'Default' ),
					'custom' => __( 'Custom', 'us' ),
					'none' => us_translate( 'None' ),
				),
				'std' => 'default',
			),
			'icons_' . $icon_set_slug . '_custom_font' => array(
				'title_pos' => 'side',
				'description' => __( 'Link to "woff2" font file.', 'us' ),
				'type' => 'text',
				'std' => '',
				'show_if' => array( 'icons_' . $icon_set_slug, '=', 'custom' ),
				'classes' => 'for_above',
			),
		)
	);

}

// Get White Label settings
$white_label_config = us_config( 'white-label.white_label', array(), TRUE );
$white_label_config['place_if'] = FALSE;

// Create edit link for Typography in Live
$front_page_id = (int) get_option( 'page_on_front' );
$usb_edit_layout_link = usb_get_edit_link(
	$front_page_id,
	array(
		'action' => US_BUILDER_SITE_SETTINGS_SLUG,
		'group' => 'layout'
	)
);

// Theme Options Config
$theme_options_config = array(
	'general' => array(
		'title' => us_translate( 'General' ),
		'fields' => array(

			'maintenance_mode' => array(
				'title' => __( 'Maintenance Mode', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'When this option is ON, all non-logged in users will see only the selected page. This is useful when your site is under construction.', 'us' ),
				'type' => 'switch',
				'switch_text' => __( 'Show site visitors only one specific page', 'us' ),
				'std' => 0,
				'classes' => 'color_yellow desc_3',
				// show the setting, but disable it, if true
				'disabled' => get_option( 'us_license_dev_activated', 0 ),
			),
			'maintenance_mode_alert' => array(
				'title_pos' => 'side',
				'description' => sprintf( __( 'It\'s not possible to switch off this setting, while %s is activated for development.', 'us' ), US_THEMENAME ) . ' ' . sprintf( __( 'You can deactivate it on your %sLicenses%s page.', 'us' ), '<a href="' . $help_portal_url . '/user/licenses/" target="_blank">', '</a>' ),
				'type' => 'message',
				'classes' => 'string',
				'place_if' => get_option( 'us_license_dev_activated', 0 ),
			),
			'maintenance_page' => array(
				'title_pos' => 'side',
				'type' => 'select',
				'options' => $us_page_list,
				'std' => '',
				'hints_for' => 'page',
				'classes' => 'for_above',
				'show_if' => array( 'maintenance_mode', '=', 1 ),
			),
			'maintenance_503' => array(
				'title_pos' => 'side',
				'description' => __( 'When this option is ON, your site will send HTTP 503 response to search engines. Use this option only for short period of time.', 'us' ),
				'type' => 'switch',
				'switch_text' => __( 'Enable "503 Service Unavailable" status', 'us' ),
				'std' => 0,
				'classes' => 'for_above desc_3',
				'show_if' => array( 'maintenance_mode', '=', 1 ),
			),
			'site_icon' => array(
				'title' => us_translate( 'Site Icon' ),
				'title_pos' => 'side',
				'description' => us_translate( 'Site Icons are what you see in browser tabs, bookmark bars, and within the WordPress mobile apps. Upload one here!' ) . '<br>' . sprintf( us_translate( 'Site Icons should be square and at least %s pixels.' ), '<strong>512</strong>' ),
				'type' => 'upload',
				'classes' => 'desc_3',
			),
			'dark_theme' => array(
				'title' => __( 'Dark Theme', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'The selected color scheme will be automatically applied when the device is switched to a dark theme.', 'us' ),
				'type' => 'select',
				'options' => array_merge(
					array(
						'none' => '&ndash; ' . us_translate( 'None' ) . ' &ndash;',
					),
					us_get_color_schemes( TRUE )
				),
				'std' => 'none',
				'classes' => 'desc_3',
			),
			'preloader' => array(
				'title' => __( 'Preloader Screen', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'options' => array(
					'disabled' => '&ndash; ' . us_translate( 'None' ) . ' &ndash;',
					'1' => sprintf( __( 'Shows Preloader %d', 'us' ), 1 ),
					'2' => sprintf( __( 'Shows Preloader %d', 'us' ), 2 ),
					'3' => sprintf( __( 'Shows Preloader %d', 'us' ), 3 ),
					'4' => sprintf( __( 'Shows Preloader %d', 'us' ), 4 ),
					'5' => sprintf( __( 'Shows Preloader %d', 'us' ), 5 ),
					'custom' => __( 'Shows Custom Image', 'us' ),
				),
				'std' => 'disabled',
			),
			'preloader_image' => array(
				'title' => '',
				'title_pos' => 'side',
				'type' => 'upload',
				'classes' => 'for_above',
				'show_if' => array( 'preloader', '=', 'custom' ),
			),
			'img_placeholder' => array(
				'title' => __( 'Image Placeholder', 'us' ),
				'title_pos' => 'side',
				'type' => 'upload',
				'std' => sprintf( '%s/assets/images/placeholder.svg', US_CORE_URI ),
			),
			'ripple_effect' => array(
				'title' => __( 'Ripple Effect', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Show the ripple effect when theme elements are clicked', 'us' ),
				'std' => 0,
			),
			'rounded_corners' => array(
				'title' => __( 'Rounded Corners', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Round corners of theme elements', 'us' ),
				'std' => 1,
			),

			// Links Underline
			'links_underline' => array(
				'title' => __( 'Underlining Links', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Underline text links globally', 'us' ),
				'std' => 0,
			),
			'wrapper_links_underline_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array( 'links_underline', '=', 1 ),
			),
			'links_underline_thickness' => array(
				'title' => __( 'Line options by default', 'us' ),
				'description' => __( 'Thickness', 'us' ),
				'type' => 'slider',
				'std' => '0px',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
					'em' => array(
						'min' => 0.0,
						'max' => 1.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
			),
			'links_underline_thickness_hover' => array(
				'title' => __( 'Line options on hover', 'us' ),
				'description' => __( 'Thickness', 'us' ),
				'type' => 'slider',
				'std' => '1px',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
					'em' => array(
						'min' => 0.0,
						'max' => 1.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
			),
			'links_underline_offset' => array(
				'description' => __( 'Offset', 'us' ),
				'type' => 'slider',
				'std' => '0.2em',
				'options' => array(
					'px' => array(
						'min' => -10,
						'max' => 10,
					),
					'em' => array(
						'min' => -1.0,
						'max' => 1.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'classes' => 'for_above',
			),
			'links_underline_offset_hover' => array(
				'description' => __( 'Offset', 'us' ),
				'type' => 'slider',
				'std' => '0.2em',
				'options' => array(
					'px' => array(
						'min' => -10,
						'max' => 10,
					),
					'em' => array(
						'min' => -1.0,
						'max' => 1.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'classes' => 'for_above',
			),
			'links_underline_style' => array(
				'description' => us_translate( 'Style' ),
				'type' => 'select',
				'options' => array(
					'solid' => __( 'Solid', 'us' ),
					'dashed' => __( 'Dashed', 'us' ),
					'dotted' => __( 'Dotted', 'us' ),
					'double' => __( 'Double', 'us' ),
					'wavy' => __( 'Wavy', 'us' ),
				),
				'std' => 'solid',
				'cols' => 2,
				'classes' => 'for_above',
			),
			'links_underline_style_hover' => array(
				'description' => us_translate( 'Style' ),
				'type' => 'select',
				'options' => array(
					'solid' => __( 'Solid', 'us' ),
					'dashed' => __( 'Dashed', 'us' ),
					'dotted' => __( 'Dotted', 'us' ),
					'double' => __( 'Double', 'us' ),
					'wavy' => __( 'Wavy', 'us' ),
				),
				'std' => 'solid',
				'cols' => 2,
				'classes' => 'for_above',
			),
			'links_underline_skip_ink' => array(
				'description' => __( 'Skip glyph descenders', 'us' ),
				'type' => 'select',
				'options' => array(
					'auto' => us_translate( 'Auto' ),
					'none' => us_translate( 'None' ),
				),
				'std' => 'auto',
				'cols' => 2,
				'classes' => 'for_above',
			),
			'links_underline_skip_ink_hover' => array(
				'description' => __( 'Skip glyph descenders', 'us' ),
				'type' => 'select',
				'options' => array(
					'auto' => us_translate( 'Auto' ),
					'none' => us_translate( 'None' ),
				),
				'std' => 'auto',
				'cols' => 2,
				'classes' => 'for_above',
			),
			'links_underline_color' => array(
				'description' => us_translate( 'Color' ),
				'type' => 'color',
				'with_gradient' => FALSE,
				'clear_pos' => 'left',
				'exclude_dynamic_colors' => 'custom_field',
				'std' => '',
				'cols' => 2,
				'classes' => 'for_above',
			),
			'links_underline_color_hover' => array(
				'description' => us_translate( 'Color' ),
				'type' => 'color',
				'with_gradient' => FALSE,
				'clear_pos' => 'left',
				'exclude_dynamic_colors' => 'custom_field',
				'std' => '',
				'cols' => 2,
				'classes' => 'for_above',
			),
			'wrapper_links_underline_end' => array(
				'type' => 'wrapper_end',
			),

			// Back to Top
			'back_to_top' => array(
				'title' => sprintf( __( '"%s" Button', 'us' ), __( 'Back to Top', 'us' ) ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Show the button that helps users navigate to the top of long pages', 'us' ),
				'std' => 1,
			),
			'wrapper_back_to_top_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array( 'back_to_top', '=', 1 ),
			),
			'back_to_top_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => '<a href="' . admin_url() . 'admin.php?page=us-theme-options#buttons">' . __( 'Edit Button Styles', 'us' ) . '</a>',
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'' => '&ndash; ' . us_translate( 'Default' ) . ' &ndash;',
					), us_get_btn_styles()
				),
				'std' => '',
			),
			'back_to_top_icon' => array(
				'title' => __( 'Button Icon', 'us' ),
				'type' => 'icon',
				'std' => ( US_THEMENAME === 'Impreza' ) ? 'far|angle-up' : 'material|keyboard_arrow_up',
			),
			'back_to_top_pos' => array(
				'title' => __( 'Button Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'right',
				'classes' => 'cols_2',
			),
			'back_to_top_color' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => 'custom_field',
				'title' => __( 'Button Color', 'us' ),
				'std' => 'rgba(0,0,0,0.3)',
				'classes' => 'cols_2',
				'show_if' => array( 'back_to_top_style', '=', '' ),
			),
			'back_to_top_display' => array(
				'title' => __( 'Page Scroll Amount to Show the Button', 'us' ),
				'type' => 'slider',
				'std' => '100vh',
				'options' => array(
					'vh' => array(
						'min' => 10,
						'max' => 200,
						'step' => 10,
					),
				),
				'classes' => 'desc_3',
			),
			'wrapper_back_to_top_end' => array(
				'type' => 'wrapper_end',
			),
			'smooth_scroll_duration' => array(
				'title' => __( 'Smooth Scroll Duration', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '1000ms',
				'options' => array(
					'ms' => array(
						'min' => 0,
						'max' => 3000,
						'step' => 100,
					),
				),
			),

			// Cookie Notice
			'cookie_notice' => array(
				'title' => __( 'Cookie Notice', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Show floating notice for new site visitors', 'us' ),
				'std' => 0,
			),
			'wrapper_cookie_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array( 'cookie_notice', '=', 1 ),
			),
			'cookie_message' => array(
				'title' => __( 'Message', 'us' ),
				'type' => 'textarea',
				'std' => 'This website uses cookies to improve your experience. If you continue to use this site, you agree with it.',
				'classes' => 'desc_3',
			),
			'cookie_privacy' => array(
				'type' => 'checkboxes',
				'options' => array(
					'page_link' => sprintf( __( 'Show link to the %s page', 'us' ), '<a href="' . admin_url( 'options-privacy.php' ) . '" target="_blank">' . us_translate( 'Privacy Policy' ) . '</a>' ),
				),
				'std' => '',
				'classes' => 'for_above',
			),
			'cookie_message_pos' => array(
				'title' => us_translate( 'Position' ),
				'type' => 'radio',
				'options' => array(
					'top' => us_translate( 'Top' ),
					'bottom' => us_translate( 'Bottom' ),
				),
				'std' => 'bottom',
			),
			'cookie_btn_label' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => 'Ok',
				'classes' => 'cols_2',
			),
			'cookie_btn_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => '<a href="' . admin_url() . 'admin.php?page=us-theme-options#buttons">' . __( 'Edit Button Styles', 'us' ) . '</a>',
				'type' => 'select',
				'options' => us_get_btn_styles(),
				'std' => '1',
				'classes' => 'cols_2',
			),
			'wrapper_cookie_end' => array(
				'type' => 'wrapper_end',
			),

			// Block 3rd-party files
			'block_third_party_files' => array(
				'title' => __( 'GDPR Compliance', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Block loading of third-party files until the consent of the site visitor', 'us' ),
				'description' => __( 'Applies to Map and Video Player elements.', 'us' ),
				'std' => 0,
				'classes' => 'desc_3',
			),

			// Keyboard Accessibility
			'h_keyboard_accessibility' => array(
				'title' => __( 'Keyboard Accessibility', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'wrapper_focus_outline_start' => array(
				'title' => __( 'Outline for clickable elements', 'us' ),
				'type' => 'wrapper_start',
				'classes' => 'force_right',
			),
			'focus_outline_width' => array(
				'title' => __( 'Line Thickness', 'us' ),
				'type' => 'slider',
				'std' => '2px',
				'options' => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
					'em' => array(
						'min' => 0.1,
						'max' => 1.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
			),
			'focus_outline_style' => array(
				'title' => us_translate( 'Style' ),
				'type' => 'select',
				'options' => array(
					'solid' => __( 'Solid', 'us' ),
					'dashed' => __( 'Dashed', 'us' ),
					'dotted' => __( 'Dotted', 'us' ),
					'double' => __( 'Double', 'us' ),
				),
				'std' => 'solid',
				'cols' => 2,
			),
			'focus_outline_offset' => array(
				'title' => __( 'Line Offset', 'us' ),
				'type' => 'slider',
				'std' => '2px',
				'options' => array(
					'px' => array(
						'min' => -2,
						'max' => 10,
					),
					'em' => array(
						'min' => -0.2,
						'max' => 1.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
			),
			'focus_outline_color' => array(
				'title' => us_translate( 'Color' ),
				'type' => 'color',
				'with_gradient' => FALSE,
				'clear_pos' => 'left',
				'exclude_dynamic_colors' => 'custom_field',
				'std' => '_content_primary',
				'cols' => 2,
			),
			'wrapper_focus_outline_end' => array(
				'type' => 'wrapper_end',
			),

			'skip_to_content_btn' => array(
				'title' => sprintf( __( '"%s" Button', 'us' ), __( 'Skip to main content', 'us' ) ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Show button to skip page header', 'us' ),
				'std' => 0,
			),
			'wrapper_skip_to_content_btn_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array( 'skip_to_content_btn', '=', 1 ),
			),
			'skip_to_content_btn_label' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => __( 'Skip to main content', 'us' ),
				'cols' => 2,
			),
			'skip_to_content_btn_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => '<a href="' . admin_url() . 'admin.php?page=us-theme-options#buttons">' . __( 'Edit Button Styles', 'us' ) . '</a>',
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'' => '&ndash; ' . us_translate( 'Default' ) . ' &ndash;',
					), us_get_btn_styles()
				),
				'std' => '',
				'cols' => 2,
			),
			'wrapper_skip_to_content_btn_end' => array(
				'type' => 'wrapper_end',
			),

			'skip_to_footer_btn' => array(
				'title' => sprintf( __( '"%s" Button', 'us' ), __( 'Skip to footer', 'us' ) ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Show button to skip page header and content', 'us' ),
				'std' => 0,
			),
			'wrapper_skip_to_footer_btn_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array( 'skip_to_footer_btn', '=', 1 ),
			),
			'skip_to_footer_btn_label' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => __( 'Skip to footer', 'us' ),
				'cols' => 2,
			),
			'skip_to_footer_btn_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => '<a href="' . admin_url() . 'admin.php?page=us-theme-options#buttons">' . __( 'Edit Button Styles', 'us' ) . '</a>',
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'' => '&ndash; ' . us_translate( 'Default' ) . ' &ndash;',
					), us_get_btn_styles()
				),
				'std' => '',
				'cols' => 2,
			),
			'wrapper_skip_to_footer_btn_end' => array(
				'type' => 'wrapper_end',
			),
		),
	),

	// Site Layout
	'layout' => array(
		'title' => __( 'Site Layout', 'us' ),
		'fields' => array(
			'layout_head_message' => array(
				'description' => '<a target="_blank" href="' . esc_url( $usb_edit_layout_link ) . '"><strong>' . us_translate( 'Customize Live' ) . '</strong></a>',
				'type' => 'message',
				'classes' => 'customize_live',
				'place_if' => $live_buider_is_enabled,
			),
			'canvas_layout' => array(
				'title' => __( 'Site Canvas Layout', 'us' ),
				'title_pos' => 'side',
				'type' => 'imgradio',
				'preview_path' => '/admin/img/%s.png',
				'options' => array(
					'wide' => '',
					'boxed' => '',
				),
				'std' => 'wide',
				'usb_preview' => array(
					'elm' => '.l-canvas',
					'mod' => 'type',
				),
			),
			'color_body_bg' => array(
				'title_pos' => 'side',
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => 'custom_field',
				'title' => __( 'Body Background Color', 'us' ),
				'std' => '_content_bg_alt',
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
				'usb_preview' => TRUE,
			),
			'body_bg_image' => array(
				'title' => __( 'Body Background Image', 'us' ),
				'title_pos' => 'side',
				'type' => 'upload',
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
				'usb_preview' => TRUE,
			),
			'wrapper_body_bg_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array(
					array( 'canvas_layout', '=', 'boxed' ),
					'and',
					array( 'body_bg_image', '!=', '' ),
				),
			),
			'body_bg_image_size' => array(
				'title' => __( 'Background Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'cover' => __( 'Fill Area', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'initial' => __( 'Initial', 'us' ),
				),
				'std' => 'cover',
				'usb_preview' => array(
					'css' => 'background-size',
					'elm' => 'body',
				),
			),
			'body_bg_image_repeat' => array(
				'title' => __( 'Background Repeat', 'us' ),
				'type' => 'radio',
				'options' => array(
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
					'no-repeat' => us_translate( 'None' ),
				),
				'std' => 'repeat',
				'usb_preview' => array(
					'css' => 'background-repeat',
					'elm' => 'body',
				),
			),
			'body_bg_image_position' => array(
				'title' => __( 'Background Position', 'us' ),
				'type' => 'radio',
				'labels_as_icons' => 'fas fa-arrow-up',
				'options' => array(
					'top left' => us_translate( 'Top Left' ),
					'top center' => us_translate( 'Top' ),
					'top right' => us_translate( 'Top Right' ),
					'center left' => us_translate( 'Left' ),
					'center center' => us_translate( 'Center' ),
					'center right' => us_translate( 'Right' ),
					'bottom left' => us_translate( 'Bottom Left' ),
					'bottom center' => us_translate( 'Bottom' ),
					'bottom right' => us_translate( 'Bottom Right' ),
				),
				'std' => 'top left',
				'classes' => 'bgpos',
				'usb_preview' => array(
					'css' => 'background-position',
					'elm' => 'body',
				),
			),
			'body_bg_image_attachment' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Scroll with Page' ),
				'std' => 1,
				'usb_preview' => TRUE,
			),
			'wrapper_body_bg_end' => array(
				'type' => 'wrapper_end',
			),
			'site_canvas_width' => array(
				'title' => __( 'Site Canvas Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '1300px',
				'options' => array(
					'px' => array(
						'min' => 1000,
						'max' => 1700,
						'step' => 10,
					),
				),
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
				'usb_preview' => array(
					'css' => '--site-canvas-width',
					'elm' => 'html',
				),
			),
			'site_content_width' => array(
				'title' => __( 'Site Content Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '1140px',
				'options' => array(
					'px' => array(
						'min' => 900,
						'max' => 1600,
						'step' => 10,
					),
				),
				'usb_preview' => array(
					'css' => '--site-content-width',
					'elm' => 'html',
				),
			),
			'sidebar_width' => array(
				'title' => __( 'Sidebar Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '25%',
				'options' => array(
					'%' => array(
						'min' => 15,
						'max' => 45,
					),
				),
				'place_if' => $sidebar_titlebar_are_enabled,
				'usb_preview' => array(
					'css' => '--site-sidebar-width',
					'elm' => 'html',
				),
			),
			'row_height' => array(
				'title' => __( 'Default Vertical Row Indents', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'options' => array(
					'auto' => us_translate( 'None' ),
					'small' => 'S',
					'medium' => 'M',
					'large' => 'L',
					'huge' => 'XL',
					'custom' => __( 'Custom', 'us' ),
				),
				'std' => 'medium',
				'usb_preview' => TRUE,
			),
			'row_height_custom' => array(
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '5vmax',
				'classes' => 'for_above',
				'options' => array(
					'rem' => array(
						'min' => 0,
						'max' => 8,
						'step' => 0.5,
					),
					'vh' => array(
						'min' => 0,
						'max' => 25,
					),
					'vmax' => array(
						'min' => 0,
						'max' => 25,
					),
				),
				'show_if' => array( 'row_height', '=', 'custom' ),
				'usb_preview' => array(
					'css' => '--section-custom-padding',
					'elm' => 'html',
				),
			),
			'text_bottom_indent' => array(
				'title' => __( 'Bottom Indent of Text Blocks', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '0rem',
				'options' => array(
					'rem' => array(
						'min' => 0,
						'max' => 3,
						'step' => 0.1,
					),
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'usb_preview' => array(
					'css' => '--text-block-margin-bottom',
					'elm' => 'html',
				),
			),
			'footer_reveal' => array(
				'title' => __( 'Footer', 'us' ),
				'title_pos' => 'side',
				'type' => 'switch',
				'switch_text' => __( 'Enable Footer Reveal Effect', 'us' ),
				'std' => 0,
				'usb_preview' => TRUE,
			),
			'disable_effects_width' => array(
				'title' => __( 'Animations Disable Width', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'When the screen width is less than this value, vertical parallax and appearance animations are disabled.', 'us' ),
				'type' => 'slider',
				'std' => '900px',
				'options' => array(
					'px' => array(
						'min' => 300,
						'max' => 1025,
					),
				),
				'classes' => 'desc_3',
				'usb_preview' => TRUE,
			),
			'columns_stacking_width' => array(
				'title' => __( 'Columns Stacking Width', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'When screen width is less than this value, all columns within a row become a single column.', 'us' ),
				'type' => 'slider',
				'std' => '600px',
				'options' => array(
					'px' => array(
						'min' => 600,
						'max' => 1025,
					),
				),
				'classes' => 'desc_3',
				'usb_preview' => TRUE,
			),
			'laptops_breakpoint' => array(
				'title' => __( 'Laptops Screen Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '1380px',
				'options' => array(
					'px' => array(
						'min' => 1024,
						'max' => 1500,
					),
				),
				'classes' => 'desc_3',
				'usb_preview' => TRUE,
			),
			'tablets_breakpoint' => array(
				'title' => __( 'Tablets Screen Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '1024px',
				'options' => array(
					'px' => array(
						'min' => 768,
						'max' => 1280,
					),
				),
				'classes' => 'desc_3',
				'usb_preview' => TRUE,
			),
			'mobiles_breakpoint' => array(
				'title' => __( 'Mobiles Screen Width', 'us' ),
				'title_pos' => 'side',
				'type' => 'slider',
				'std' => '600px',
				'options' => array(
					'px' => array(
						'min' => 320,
						'max' => 768,
					),
				),
				'classes' => 'desc_3',
				'usb_preview' => TRUE,
			),
		),
	),

	// Pages Layout
	'pages_layout' => array(
		'title' => __( 'Pages Layout', 'us' ),
		'fields' => array_merge(
			array(

				// Search Results
				'search_page' => array(
					'title' => __( 'Search Results', 'us' ),
					'title_pos' => 'side',
					'description' => __( 'The selected page must contain a Grid element showing items of the current query.', 'us' ),
					'type' => 'select',
					'options' => us_array_merge(
						array( 'default' => '&ndash; ' . __( 'Show results via Grid element with defaults', 'us' ) . ' &ndash;' ), $us_page_list
					),
					'std' => 'default',
					'hints_for' => 'page',
					'classes' => 'desc_3',
				),
				'exclude_post_types_in_search' => array(
					'title' => __( 'Exclude from Search Results', 'us' ),
					'title_pos' => 'side',
					'type' => 'checkboxes',
					'options' => us_get_public_post_types(),
					'std' => '',
				),

				// 404 page
				'page_404' => array(
					'title' => __( 'Page "404 Not Found"', 'us' ),
					'title_pos' => 'side',
					'description' => __( 'The selected page will be shown instead of the "Page not found" message.', 'us' ),
					'type' => 'select',
					'options' => us_array_merge(
						array( 'default' => '&ndash; ' . us_translate( 'Default' ) . ' &ndash;' ), $us_page_list
					),
					'std' => 'default',
					'hints_for' => 'page',
					'classes' => 'desc_3',
				),

				// Pages
				'h_defaults' => array(
					'title' => us_translate_x( 'Pages', 'post type general name' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'header_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'description' => $misc['headers_description'],
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array( '' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;' ), $us_headers_list
					),
					'std' => '',
					'classes' => 'desc_3',
				),
				'titlebar_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '',
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'content_id' => array(
					'title' => __( 'Page Template', 'us' ),
					'title_pos' => 'side',
					'description' => $misc['content_description'],
					'type' => 'select',
					'hints_for' => 'us_content_template',
					'options' => us_array_merge(
						array( '' => '&ndash; ' . __( 'Show content as is', 'us' ) . ' &ndash;' ), $us_content_templates_list
					),
					'std' => '',
					'classes' => 'desc_3',
				),
				'sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'sidebar_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_id', '!=', '' ),
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'footer_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'description' => $misc['footers_description'],
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array( '' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;' ), $us_page_blocks_list
					),
					'std' => '',
					'classes' => 'desc_3',
				),

			), $pages_layout_config
		),
	),

	// Archives Layout
	'archives_layout' => array(
		'title' => __( 'Archives Layout', 'us' ),
		'fields' => array_merge(
			array(

				// Archives
				'h_archive_defaults' => array(
					'title' => us_translate( 'Archives' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'header_archive_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'description' => $misc['headers_description'],
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						),
						$us_headers_list
					),
					'std' => '__defaults__',
					'classes' => 'desc_3',
				),
				'titlebar_archive_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'content_archive_id' => array(
					'title' => __( 'Page Template', 'us' ),
					'title_pos' => 'side',
					'description' => $misc['content_description'],
					'type' => 'select',
					'hints_for' => 'us_content_template',
					'options' => us_array_merge(
						array( '' => '&ndash; ' . __( 'Show results via Grid element with defaults', 'us' ) . ' &ndash;' ), $us_content_templates_list
					),
					'std' => '',
					'classes' => 'desc_3',
				),
				'sidebar_archive_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '__defaults__',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'sidebar_archive_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_archive_id', '!=', '' ),
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'footer_archive_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'description' => $misc['footers_description'],
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						),
						$us_page_blocks_list
					),
					'std' => '__defaults__',
					'classes' => 'desc_3',
				),

			), $archives_layout_config, array(

				// Authors
				'h_authors' => array(
					'title' => __( 'Authors', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'header_author_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_headers_list
					),
					'std' => '__defaults__',
				),
				'titlebar_author_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'content_author_id' => array(
					'title' => __( 'Page Template', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
						), $us_content_templates_list
					),
					'std' => '__defaults__',
				),
				'sidebar_author_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '__defaults__',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'sidebar_author_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_author_id', '!=', array( '', '__defaults__' ) ),
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'footer_author_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Archives', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
				),

			)

		),
	),

	// Colors
	'colors' => array(
		'title' => us_translate( 'Colors' ),
		'fields' => array(

			// Custom Global Colors
			'h_custom_colors' => array(
				'title' => __( 'Custom Global Colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'custom_colors' => array(
				'type' => 'group',
				'show_controls' => TRUE,
				'is_sortable' => TRUE,
				'is_accordion' => FALSE,
				'params' => array(
					'color' => array(
						'type' => 'color',
						'with_gradient' => TRUE,
						'exclude_dynamic_colors' => 'all',
						'std' => '',
					),
					'name' => array(
						'placeholder' => us_translate( 'Name' ),
						'type' => 'text',
						'std' => us_translate( 'Custom color' ),
					),
					'slug' => array(
						'placeholder' => us_translate( 'Slug' ),
						'type' => 'text',
						'unique_value' => array(), // unique value in a group (only works in group context)
						'sanitize_color_slug' => TRUE, // sanitize color slug (only works in group context)
						'std' => 'custom',
					),
				),
				'std' => array(),
			),

			// Color Schemes
			'style_scheme' => array(
				'type' => 'style_scheme',
			),

			// Header colors
			'change_header_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_1' => array(
				'title' => __( 'Header colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_header_middle_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Background' ),
			),
			'color_header_middle_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
			),
			'color_header_middle_text_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Link on hover', 'us' ),
			),
			'color_header_transparent_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'std' => 'transparent',
				'text' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Background' ),
			),
			'color_header_transparent_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
			),
			'color_header_transparent_text_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Transparent Header', 'us' ) . ': ' . __( 'Link on hover', 'us' ),
			),
			'color_chrome_toolbar' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Browser Toolbar', 'us' ),
			),
			'change_header_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Alternate Header colors
			'change_header_alt_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_2' => array(
				'title' => __( 'Alternate Header colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_header_top_bg' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Background' ),
			),
			'color_header_top_text' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
			),
			'color_header_top_text_hover' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Link on hover', 'us' ),
			),
			'color_header_top_transparent_bg' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'std' => 'rgba(0,0,0,0.2)',
				'text' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Background' ),
			),
			'color_header_top_transparent_text' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'std' => 'rgba(255,255,255,0.66)',
				'text' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
			),
			'color_header_top_transparent_text_hover' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'std' => '#fff',
				'text' => __( 'Transparent Header', 'us' ) . ': ' . __( 'Link on hover', 'us' ),
			),
			'change_header_alt_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Content colors
			'change_content_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_3' => array(
				'title' => __( 'Content colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_content_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Background' ),
			),
			'color_content_bg_alt' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Alternate Background', 'us' ),
			),
			'color_content_border' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Border' ),
			),
			'color_content_heading' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Headings', 'us' ),
			),
			'color_content_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Text' ),
			),
			'color_content_link' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Link' ),
			),
			'color_content_link_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Link on hover', 'us' ),
			),
			'color_content_primary' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Primary Color', 'us' ),
			),
			'color_content_secondary' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Secondary Color', 'us' ),
			),
			'color_content_faded' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Faded Text', 'us' ),
			),
			'color_content_overlay' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'std' => 'rgba(0,0,0,0.75)',
				'text' => __( 'Background Overlay', 'us' ),
			),
			'change_content_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Alternate Content colors
			'change_alt_content_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_4' => array(
				'title' => __( 'Alternate Content colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_alt_content_bg' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Background' ),
			),
			'color_alt_content_bg_alt' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Alternate Background', 'us' ),
			),
			'color_alt_content_border' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Border' ),
			),
			'color_alt_content_heading' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Headings', 'us' ),
			),
			'color_alt_content_text' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Text' ),
			),
			'color_alt_content_link' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Link' ),
			),
			'color_alt_content_link_hover' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Link on hover', 'us' ),
			),
			'color_alt_content_primary' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Primary Color', 'us' ),
			),
			'color_alt_content_secondary' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Secondary Color', 'us' ),
			),
			'color_alt_content_faded' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Faded Text', 'us' ),
			),
			'color_alt_content_overlay' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'std' => 'rgba(0,0,0,0.75)',
				'text' => __( 'Background Overlay', 'us' ),
			),
			'change_alt_content_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Footer colors
			'change_footer_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_6' => array(
				'title' => __( 'Footer colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_footer_bg' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Background' ),
			),
			'color_footer_bg_alt' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Alternate Background', 'us' ),
			),
			'color_footer_border' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Border' ),
			),
			'color_footer_heading' => array(
				'type' => 'color',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Headings', 'us' ),
			),
			'color_footer_text' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Text' ),
			),
			'color_footer_link' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Link' ),
			),
			'color_footer_link_hover' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Link on hover', 'us' ),
			),
			'change_footer_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Alternate Footer colors
			'change_subfooter_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'for_colors',
			),
			'h_colors_5' => array(
				'title' => __( 'Alternate Footer colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'color_subfooter_bg' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Background' ),
			),
			'color_subfooter_bg_alt' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Alternate Background', 'us' ),
			),
			'color_subfooter_border' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Border' ),
			),
			'color_subfooter_heading' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => TRUE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Headings', 'us' ),
			),
			'color_subfooter_text' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Text' ),
			),
			'color_subfooter_link' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => us_translate( 'Link' ),
			),
			'color_subfooter_link_hover' => array(
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'exclude_dynamic_colors' => $color_scheme_exclude_dynamic_colors,
				'text' => __( 'Link on hover', 'us' ),
			),
			'change_subfooter_colors_end' => array(
				'type' => 'wrapper_end',
			),
		),
	),

	// Typography
	'typography' => us_config( 'theme-options/typography' ),

	// Button Styles
	'buttons' => us_config( 'theme-options/buttons' ),

	// Field Styles
	'input_fields' => us_config( 'theme-options/input_fields', array(), TRUE ),

	// Portfolio
	'portfolio' => array(
		'title' => __( 'Portfolio', 'us' ) . $renamed_portfolio_label,
		'place_if' => ! empty( $usof_options['enable_portfolio'] ),
		'fields' => array(

			'portfolio_breadcrumbs_page' => array(
				'title' => __( 'Intermediate Breadcrumbs Page', 'us' ),
				'title_pos' => 'side',
				'type' => 'select',
				'options' => us_array_merge(
					array( '' => '&ndash; ' . us_translate( 'None' ) . ' &ndash;' ), $us_page_list
				),
				'std' => '',
			),

			// Slugs
			'portfolio_slug' => array(
				'title' => __( 'Portfolio Page Slug', 'us' ),
				'title_pos' => 'side',
				'type' => 'text',
				'std' => 'portfolio',
			),
			'portfolio_category_slug' => array(
				'title' => __( 'Portfolio Category Slug', 'us' ),
				'title_pos' => 'side',
				'type' => 'text',
				'std' => 'portfolio_category',
				'classes' => 'for_above',
			),
			'portfolio_tag_slug' => array(
				'title' => __( 'Portfolio Tag Slug', 'us' ),
				'title_pos' => 'side',
				'type' => 'text',
				'std' => 'portfolio_tag',
				'classes' => 'for_above',
			),
			'portfolio_slug_ignore_prefix' => array(
				'switch_text' => __( 'Ignore the prefix of the post permalink structure', 'us' ),
				'type' => 'switch',
				'std' => 0,
			),

			// Rename Portfolio
			'portfolio_rename' => array(
				'switch_text' => sprintf( __( 'Rename "%s" labels', 'us' ), __( 'Portfolio', 'us' ) ),
				'type' => 'switch',
				'std' => 0,
			),
			'portfolio_label_name' => array(
				'title' => __( 'Portfolio', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Portfolio', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
			'portfolio_label_singular_name' => array(
				'title' => __( 'Portfolio Page', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Portfolio Page', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
			'portfolio_label_add_new' => array(
				'title' => __( 'Add Portfolio Page', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Add Portfolio Page', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
			'portfolio_label_edit_item' => array(
				'title' => __( 'Edit Portfolio Page', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Edit Portfolio Page', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
			'portfolio_label_category' => array(
				'title' => __( 'Portfolio Categories', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Portfolio Categories', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
			'portfolio_label_tag' => array(
				'title' => __( 'Portfolio Tags', 'us' ),
				'title_pos' => 'side',
				'std' => __( 'Portfolio Tags', 'us' ),
				'type' => 'text',
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_rename', '=', 1 ),
			),
		),
	),

	// Shop
	'woocommerce' => array(
		'title' => us_translate_x( 'Shop', 'Page title', 'woocommerce' ),
		'place_if' => class_exists( 'woocommerce' ),
		'fields' => array_merge(
			array(

				// Global Settings
				'h_more' => array(
					'title' => us_translate( 'Global Settings' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'shop_catalog' => array(
					'title' => __( 'Catalog Mode', 'us' ),
					'title_pos' => 'side',
					'type' => 'switch',
					'switch_text' => sprintf( __( 'Remove "%s" buttons', 'us' ), us_translate( 'Add to cart', 'woocommerce' ) ),
					'std' => 0,
				),
				'shop_primary_btn_style' => array(
					'title' => __( 'Primary Buttons Style', 'us' ),
					'title_pos' => 'side',
					'description' => '<a href="' . admin_url() . 'admin.php?page=us-theme-options#buttons">' . __( 'Edit Button Styles', 'us' ) . '</a>',
					'type' => 'select',
					'options' => us_get_btn_styles(),
					'std' => '1',
				),
				'shop_secondary_btn_style' => array(
					'title' => __( 'Secondary Buttons Style', 'us' ),
					'title_pos' => 'side',
					'description' => '<a href="' . admin_url() . 'admin.php?page=us-theme-options#buttons">' . __( 'Edit Button Styles', 'us' ) . '</a>',
					'type' => 'select',
					'options' => us_get_btn_styles(),
					'std' => '2',
				),

				// Product gallery
				'product_gallery' => array(
					'title' => us_translate( 'Product gallery', 'woocommerce' ),
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'slider' => __( 'Slider', 'us' ),
						'gallery' => us_translate( 'Gallery' ),
					),
					'std' => 'slider',
				),
				'wrapper_product_gallery_start' => array(
					'type' => 'wrapper_start',
					'classes' => 'force_right',
				),
				'product_gallery_thumbs_pos' => array(
					'title' => __( 'Thumbnails Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'bottom' => us_translate( 'Bottom' ),
						'left' => us_translate( 'Left' ),
					),
					'std' => 'bottom',
					'show_if' => array( 'product_gallery', '=', 'slider' ),
				),
				'product_gallery_thumbs_cols' => array(
					'title' => us_translate( 'Columns' ),
					'type' => 'radio',
					'options' => array(
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
					),
					'std' => '4',
					'show_if' => array(
						array( 'product_gallery', '=', 'slider' ),
						'and',
						array( 'product_gallery_thumbs_pos', '=', 'bottom' ),
					),
				),
				'product_gallery_thumbs_width' => array(
					'title' => __( 'Thumbnails Width', 'us' ),
					'type' => 'slider',
					'options' => array(
						'px' => array(
							'min' => 40,
							'max' => 200,
						),
						'rem' => array(
							'min' => 3,
							'max' => 15,
							'step' => 0.1,
						),
					),
					'std' => '6rem',
					'show_if' => array(
						array( 'product_gallery', '=', 'slider' ),
						'and',
						array( 'product_gallery_thumbs_pos', '=', array( 'left', 'right' ) ),
					),
				),
				'product_gallery_thumbs_gap' => array(
					'title' => __( 'Gap between Thumbnails', 'us' ),
					'type' => 'slider',
					'options' => array(
						'px' => array(
							'min' => 0,
							'max' => 20,
						),
					),
					'std' => '4px',
					'show_if' => array( 'product_gallery', '=', 'slider' ),
				),
				'product_gallery_options' => array(
					'type' => 'checkboxes',
					'options' => array(
						'zoom' => __( 'Zoom images on hover', 'us' ),
						'lightbox' => __( 'Allow Full Screen view', 'us' ),
					),
					'std' => 'zoom,lightbox',
					'classes' => 'vertical',
				),
				'wrapper_product_gallery_end' => array(
					'type' => 'wrapper_end',
				),

				// Products
				'h_product' => array(
					'title' => us_translate( 'Products', 'woocommerce' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'header_product_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_headers_list
					),
					'std' => '__defaults__',
				),
				'titlebar_product_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'content_product_id' => array(
					'title' => __( 'Page Template', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'' => '&ndash; ' . __( 'Default WooCommerce template', 'us' ) . ' &ndash;',
						), $us_content_templates_list
					),
					'std' => '',
				),
				'sidebar_product_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '__defaults__',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'sidebar_product_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_product_id', '!=', array( '', '__defaults__' ) ),
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'footer_product_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
				),

				// Shop page
				'h_shop' => array(
					'title' => us_translate( 'Shop Page', 'woocommerce' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'header_shop_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_headers_list
					),
					'std' => '__defaults__',
				),
				'titlebar_shop_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'content_shop_id' => array(
					'title' => __( 'Page Template', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'' => '&ndash; ' . __( 'Default WooCommerce template', 'us' ) . ' &ndash;',
						), $us_content_templates_list
					),
					'std' => '',
				),
				'wrapper_shop_start' => array(
					'type' => 'wrapper_start',
					'classes' => 'force_right',
					'show_if' => array( 'content_shop_id', '=', '' ),
				),
				'shop_columns' => array(
					'title' => us_translate( 'Columns' ),
					'type' => 'radio',
					'options' => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'std' => '3',
				),
				'wrapper_shop_end' => array(
					'type' => 'wrapper_end',
				),
				'sidebar_shop_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '__defaults__',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'sidebar_shop_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_shop_id', '!=', array( '', '__defaults__' ) ),
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'footer_shop_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Pages', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
				),

				// Products Search Results Page
				'h_shop_search' => array(
					'title' => __( 'Products Search Results Page', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'header_shop_search_id' => array(
					'title' => _x( 'Header', 'site top area', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_header',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_headers_list
					),
					'std' => '__defaults__',
				),
				'titlebar_shop_search_id' => array(
					'title' => __( 'Titlebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'content_shop_search_id' => array(
					'title' => __( 'Page Template', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
						), $us_content_templates_list
					),
					'std' => '',
				),
				'sidebar_shop_search_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $sidebars_list
					),
					'std' => '__defaults__',
					'hints_for' => $sidebar_hints_for,
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'sidebar_shop_search_pos' => array(
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'classes' => 'for_above',
					'show_if' => array( 'sidebar_shop_search_id', '!=', array( '', '__defaults__' ) ),
					'place_if' => $sidebar_titlebar_are_enabled,
				),
				'footer_shop_search_id' => array(
					'title' => __( 'Footer', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_page_block',
					'options' => us_array_merge(
						array(
							'__defaults__' => '&ndash; ' . __( 'As in Shop Page', 'us' ) . ' &ndash;',
							'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
						), $us_page_blocks_list
					),
					'std' => '__defaults__',
				),

			), $shop_layout_config, array(

				// Orders template
				'h_order' => array(
					'title' => us_translate_x( 'Orders', 'Admin menu name', 'woocommerce' ),
					'description' => sprintf( __( 'Selected template will be applied to the "%s" page.', 'us' ), us_translate( 'Checkout', 'woocommerce' ) . ' &rarr; ' . us_translate( 'Order received', 'woocommerce' ) ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'content_order_id' => array(
					'title' => __( 'Page Template', 'us' ),
					'title_pos' => 'side',
					'type' => 'select',
					'hints_for' => 'us_content_template',
					'options' => us_array_merge(
						array(
							'' => '&ndash; ' . __( 'Default WooCommerce template', 'us' ) . ' &ndash;',
						), $us_content_templates_list
					),
					'std' => '',
				),

				// Cart page
				'h_cart' => array(
					'title' => us_translate( 'Cart Page', 'woocommerce' ),
					'type' => 'heading',
					'classes' => 'with_separator sticky',
				),
				'shop_cart' => array(
					'title' => __( 'Layout', 'us' ),
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'standard' => __( 'Standard', 'us' ),
						'compact' => __( 'Compact', 'us' ),
					),
					'std' => 'compact',
				),
				'product_related_qty' => array(
					'title' => us_translate( 'Cross-sells', 'woocommerce' ),
					'title_pos' => 'side',
					'type' => 'radio',
					'options' => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'std' => '3',
				),
			)
		),
	),

	// Icons
	'icons' => array(
		'title' => __( 'Icons', 'us' ),
		'fields' => array_merge(
			array(
				'used_icons_info' => array(
					'button_text' => __( 'Show used icons', 'us' ),
					'type' => 'used_icons_info',
					'classes' => 'desc_4',
				),
				'h_icons_2' => array(
					'title' => __( 'Icon Sets', 'us' ),
					'description' => __( 'If "None" is selected, the corresponding icon set won\'t load font files and won\'t appear in the icon selection of elements settings.', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
			),
			$icon_sets_config,
			array(
				'fallback_icon_font' => array(
					'title' => __( 'Fallback icon font', 'us' ),
					'title_pos' => 'side',
					'description' => '<a href="' . $help_portal_url . '/' . strtolower( US_THEMENAME ) . '/icons/#fallback-icon-font" target="_blank">' . __( 'Learn more', 'us' ) . '</a>',
					'type' => 'switch',
					'switch_text' => __( 'Use fallback icon font for theme UI controls', 'us' ),
					'std' => 1,
					'classes' => 'desc_2',
					'place_if' => ( US_THEMENAME === 'Impreza' ), // fallback icon font exists in Impreza only
					'show_if' => array(
						array( 'icons_fas', '!=', 'default' ),
						'and',
						array( 'icons_far', '!=', 'default' ),
						'and',
						array( 'icons_fal', '!=', 'default' ),
					),
				),
			)
		),
	),

	// Image Sizes
	'image_sizes' => array(
		'title' => us_translate( 'Image sizes' ),
		'fields' => array(

			'img_size_info' => array(
				'description' => $img_size_info,
				'type' => 'message',
				'classes' => 'color_blue for_above',
			),

			'h_image_sizes' => array(
				'title' => __( 'Additional Image Sizes', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'img_size' => array(
				'type' => 'group',
				'is_accordion' => FALSE,
				'is_duplicate' => FALSE,
				'show_controls' => TRUE,
				'params' => array(
					'width' => array(
						'title' => us_translate( 'Max Width' ),
						'type' => 'slider',
						'std' => '600px',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 1000,
							),
						),
						'classes' => 'inline slider_below',
					),
					'height' => array(
						'title' => us_translate( 'Max Height' ),
						'type' => 'slider',
						'std' => '400px',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 1000,
							),
						),
						'classes' => 'inline slider_below',
					),
					'crop' => array(
						'type' => 'checkboxes',
						'options' => array(
							'crop' => __( 'Crop to exact dimensions', 'us' ),
						),
						'std' => '',
						'classes' => 'inline',
					),
				),
				'std' => array(),
			),

			'h_more_options' => array(
				'title' => __( 'More Options', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'big_image_size_threshold' => array(
				'title' => __( 'Big Image Size Threshold', 'us' ),
				'title_pos' => 'side',
				'description' => sprintf( __( 'If an image height or width is above this threshold, it will be scaled down and used as the "%s".', 'us' ), us_translate( 'Full Size' ) ) . '<br><br><strong>' . __( 'Set "0px" to disable threshold.', 'us' ) . '</strong> <a target="blank" href="https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/">' . __( 'Learn more', 'us' ) . '</a>',
				'type' => 'slider',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 4000,
						'step' => 20,
					),
				),
				'std' => '2560px',
				'classes' => 'desc_3',
			),
			'delete_unused_images' => array(
				'title' => __( 'Unused Thumbnails', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'When this option is ON, all the thumbnails of non-registered image sizes are deleted.', 'us' ) . ' ' . __( 'It helps keep free space in your storage.', 'us' ),
				'type' => 'switch',
				'switch_text' => __( 'Delete unused image thumbnails', 'us' ),
				'std' => 0,
				'classes' => 'desc_3',
			),
		),
	),

	// Advanced
	'advanced' => us_config( 'theme-options/advanced', array(), TRUE ),

	// Custom Code
	'code' => array(
		'title' => __( 'Custom Code', 'us' ),
		'fields' => array(
			'custom_css' => array(
				'title' => __( 'Custom CSS', 'us' ),
				'description' => sprintf( __( 'CSS code from this field will overwrite theme styles. It will be located inside the %s tags just before the %s tag of every site page.', 'us' ), '<code>&lt;style&gt;&lt;/style&gt;</code>', '<code>&lt;/head&gt;</code>' ),
				'type' => 'css',
				'std' => '',
				'classes' => 'desc_4',
			),
			'custom_html_head' => array(
				'title' => sprintf( __( 'Code before %s', 'us' ), '&lt;/head&gt;' ),
				'description' => sprintf( __( 'Use this field for Google Analytics code or other tracking code. If you paste custom JavaScript, use it inside the %s tags.', 'us' ), '<code>&lt;script&gt;&lt;/script&gt;</code>' ) . '<br><br>' . sprintf( __( 'Content from this field will be located just before the %s tag of every site page.', 'us' ), '<code>&lt;/head&gt;</code>' ),
				'type' => 'html',
				'std' => '',
				'classes' => 'desc_4',
			),
			'custom_html_body' => array(
				'title' => sprintf( __( 'Code after %s', 'us' ), '&lt;body&gt;' ),
				'description' => sprintf( __( 'Use this field for Google Analytics code or other tracking code. If you paste custom JavaScript, use it inside the %s tags.', 'us' ), '<code>&lt;script&gt;&lt;/script&gt;</code>' ) . '<br><br>' . sprintf( __( 'Content from this field will be located just after the %s tag of every site page.', 'us' ), '<code>&lt;body&gt;</code>' ),
				'type' => 'html',
				'std' => '',
				'classes' => 'desc_4',
			),
			'custom_html' => array(
				'title' => sprintf( __( 'Code before %s', 'us' ), '&lt;/body&gt;' ),
				'description' => sprintf( __( 'Use this field for Google Analytics code or other tracking code. If you paste custom JavaScript, use it inside the %s tags.', 'us' ), '<code>&lt;script&gt;&lt;/script&gt;</code>' ) . '<br><br>' . sprintf( __( 'Content from this field will be located just before the %s tag of every site page.', 'us' ), '<code>&lt;/body&gt;</code>' ),
				'type' => 'html',
				'std' => '',
				'classes' => 'desc_4',
			),
		),
	),

	'manage' => array(
		'title' => __( 'Manage Options', 'us' ),
		'fields' => array(
			'of_reset' => array(
				'title' => __( 'Reset Theme Options', 'us' ),
				'title_pos' => 'side',
				'type' => 'reset',
			),
			'of_backup' => array(
				'title' => __( 'Backup Theme Options', 'us' ),
				'title_pos' => 'side',
				'type' => 'backup',
			),
			'of_transfer' => array(
				'title' => __( 'Transfer Theme Options', 'us' ),
				'title_pos' => 'side',
				'type' => 'transfer',
				'description' => __( 'You can transfer the saved options data between different installations by copying the text inside the text box. To import data from another installation, replace the data in the text box with the one from another installation and click "Import Options".', 'us' ),
				'classes' => 'desc_3',
			),
		),
	),

	'white_label' => $white_label_config,
);

// Generate a list of predefined color slugs, which cannot be used in Global Custom Colors
$reserved_color_slugs = array();
foreach ( array_keys( $theme_options_config['colors']['fields'] ) as $field_name ) {
	if ( strpos( $field_name, 'color_' ) === 0 ) {
		$reserved_color_slugs[] = substr( $field_name, strlen( 'color' ) );
	}
}
$theme_options_config['colors']['fields']['custom_colors']['params']['slug']['unique_value'] = $reserved_color_slugs;

return $theme_options_config;
