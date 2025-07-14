<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options > Advanced
 */

global $usof_options, $help_portal_url;

if ( ! empty( $usof_options['portfolio_rename'] ) ) {
	$renamed_portfolio_label = ' (' . strip_tags( $usof_options['portfolio_label_name'] ) . ')';
} else {
	$renamed_portfolio_label = '';
}

$public_post_types = us_get_public_post_types();

// Get CSS & JS assets
$usof_assets = $usof_assets_std = array();

foreach ( us_config( 'assets', array() ) as $component => $component_atts ) {

	// Skip assets without title
	if ( empty( $component_atts['title'] ) ) {
		continue;
	}

	$usof_assets[ $component ] = array(
		'title' => $component_atts['title'],
		'group' => $component_atts['group'] ?? NULL,
	);

	$usof_assets_std[ $component ] = 1;

	// Count files sizes for admin area only
	if ( is_admin() ) {
		if ( isset( $component_atts['css'] ) ) {
			$usof_assets[ $component ]['css_size'] = file_exists( $us_template_directory . $component_atts['css'] )
				? number_format_i18n( filesize( $us_template_directory . $component_atts['css'] ) / 1024 * 0.8, 1 )
				: NULL;
		}
		if ( isset( $component_atts['js'] ) ) {
			$js_filename = str_replace( '.js', '.min.js', $us_template_directory . $component_atts['js'] );
			$usof_assets[ $component ]['js_size'] = file_exists( $js_filename )
				? number_format_i18n( filesize( $js_filename ) / 1024, 1 )
				: NULL;
		}
	}

}

// Check if "uploads" directory is writable
$upload_dir = wp_get_upload_dir();
$upload_dir_not_writable = wp_is_writable( $upload_dir['basedir'] ) ? FALSE : TRUE;

return array(
	'title' => _x( 'Advanced', 'Advanced Settings', 'us' ),
	'fields' => array(

		// Global Values
		'h_advanced_2' => array(
			'title' => __( 'Global Values', 'us' ),
			'type' => 'heading',
			'classes' => 'with_separator',
		),
		'reCAPTCHA_site_key' => array(
			'title' => __( 'reCAPTCHA Site Key', 'us' ),
			'title_pos' => 'side',
			'description' => '<a href="https://www.google.com/recaptcha/admin/" target="_blank">' . strip_tags( __( 'Get reCAPTCHA keys', 'us' ) ) . '</a>',
			'type' => 'text',
			'std' => '',
			'classes' => 'desc_3',
		),
		'reCAPTCHA_secret_key' => array(
			'title' => __( 'reCAPTCHA Secret Key', 'us' ),
			'title_pos' => 'side',
			'description' => '<a href="https://www.google.com/recaptcha/admin/" target="_blank">' . strip_tags( __( 'Get reCAPTCHA keys', 'us' ) ) . '</a>',
			'type' => 'text',
			'std' => '',
			'classes' => 'desc_3',
		),
		'reCAPTCHA_hide_badge' => array(
			'switch_text' => __( 'Hide reCAPTCHA badge', 'us' ),
			'type' => 'switch',
			'std' => '0',
			'classes' => 'for_above force_right',
			'show_if' => array( 'reCAPTCHA_secret_key', '!=', '' ),
		),
		'reCAPTCHA_policy_text' => array(
			'title' => __( 'Text in Contact Forms', 'us' ),
			'description' => __( 'This text will be shown in every contact form with reCAPTCHA enabled.', 'us' ),
			'type' => 'textarea',
			'std' => sprintf(
				'This site is protected by reCAPTCHA and the Google %s and %s apply.',
				'<a href="https://policies.google.com/privacy" target="_blank">Privacy Policy</a>',
				'<a href="https://policies.google.com/terms" target="_blank">Terms of Service</a>'
			),
			'classes' => 'for_above force_right desc_3',
			'show_if' => array(
				array( 'reCAPTCHA_hide_badge', '=', '1' ),
				'and',
				array( 'reCAPTCHA_secret_key', '!=', '' ),
			),
		),
		'gmaps_api_key' => array(
			'title' => __( 'Google Maps API Key', 'us' ),
			'title_pos' => 'side',
			'description' => '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">' . strip_tags( __( 'Get API key', 'us' ) ) . '</a>',
			'type' => 'text',
			'std' => '',
			'classes' => 'desc_3',
		),
		'facebook_app_id' => array(
			'title' => __( 'Facebook Application ID', 'us' ),
			'title_pos' => 'side',
			'description' => __( 'Required for Sharing Buttons on AMP version of website.', 'us' ) . ' <a href="https://developers.facebook.com/apps" target="_blank">developers.facebook.com</a>',
			'type' => 'text',
			'std' => '',
			'classes' => 'desc_3',
			'place_if' => function_exists( 'amp_is_request' ),
		),
		'grid_filter_url_prefix' => array(
			'title' => __( 'Grid Filter URL prefix', 'us' ),
			'title_pos' => 'side',
			'type' => 'text',
			'placeholder' => 'filter',
			'std' => '',
		),
		'grid_order_url_prefix' => array(
			'title' => __( 'Grid Order URL prefix', 'us' ),
			'title_pos' => 'side',
			'type' => 'text',
			'placeholder' => 'order',
			'std' => '',
		),

		// Faceted Filter
		'h_advanced_4' => array(
			'title' => __( 'Faceted Filtering', 'us' ),
			'description' => sprintf( __( 'Manage a separate database table for using by the %s element.', 'us' ), __( 'List Filter', 'us' ) ) . ' ' . __( 'Allows to show the number of relevant posts next to each filter value when filtering.', 'us' ) . ' <a href="' . $help_portal_url . '/' . strtolower( US_THEMENAME ) . '/faceted-filter/" target="_blank">' . __( 'Learn more', 'us' ) . '</a>',
			'type' => 'heading',
			'classes' => 'with_separator',
		),
		'index_filter_panel' => array(
			'type' => 'index_filter_panel',
		),
		'enable_auto_filter_reindex' => array(
			'type' => 'switch',
			'switch_text' => __( 'Automatic re-indexing', 'us' ),
			'description' => __( 'Performs a single re-index when an individual post or term is added, edited or deleted.', 'us' ),
			'std' => 0,
			'classes' => 'desc_2',
		),

		// Theme Modules
		'h_advanced_1' => array(
			'title' => __( 'Theme Modules', 'us' ),
			'type' => 'heading',
			'classes' => 'with_separator',
		),
		'live_builder' => array(
			'type' => 'switch',
			'switch_text' => __( '“Live Builder”', 'us' ),
			'description' => __( 'Allows to edit website pages on the front end via green "Edit Live" button.', 'us' ) . ' <a href="https://youtu.be/lcTFtiFGZng" target="_blank">' . __( 'Learn more', 'us' ) . '</a>',
			'std' => 1,
			'classes' => 'desc_2',
		),
		'section_templates' => array(
			'type' => 'switch',
			'switch_text' => __( 'Section Templates', 'us' ),
			'description' => __( 'Shows a categorized list of templates in the “Live Builder”.', 'us' ) . ' <a href="https://youtu.be/1eV1GesTnjs" target="_blank">' . __( 'Learn more', 'us' ) . '</a>',
			'std' => 1,
			'show_if' => array( 'live_builder', '=', 1 ),
			'classes' => 'for_above desc_2',
		),
		'section_favorites' => array(
			'type' => 'switch',
			'switch_text' => _x( 'Favorites', 'Favorite Sections', 'us' ),
			'description' => _x( 'Save your favorite sections to make them quickly available on all of your websites.', 'Favorite Sections', 'us' ) . ' <a href="' . $help_portal_url . '/' . strtolower( US_THEMENAME ) . '/fav-sections/" target="_blank">' . __( 'Learn more', 'us' ) . '</a>',
			'std' => 1,
			'show_if' => array( 'live_builder', '=', 1 ),
			'classes' => 'for_above desc_2',
		),
		'grid_columns_layout' => array(
			'type' => 'switch',
			'switch_text' => __( 'Columns Layout via CSS grid', 'us' ),
			'std' => 1,
			'show_if' => array( 'live_builder', '=', 1 ),
			'classes' => 'for_above',
		),
		'block_editor' => array(
			'type' => 'switch',
			'switch_text' => __( 'Gutenberg (block editor)', 'us' ),
			'std' => 0,
			'classes' => 'for_above',
		),
		'enable_sidebar_titlebar' => array(
			'type' => 'switch',
			'switch_text' => __( 'Titlebars & Sidebars', 'us' ),
			'std' => 0,
			'classes' => 'for_above',
		),
		'enable_page_blocks_for_sidebars' => array(
			'type' => 'switch',
			'switch_text' => __( 'Use Reusable Blocks for Sidebars', 'us' ),
			'std' => 0,
			'classes' => 'for_above',
			'show_if' => array( 'enable_sidebar_titlebar', '=', 1 ),
		),
		'enable_portfolio' => array(
			'type' => 'switch',
			'switch_text' => __( 'Portfolio', 'us' ) . $renamed_portfolio_label,
			'std' => 1,
			'classes' => 'for_above',
		),
		'enable_testimonials' => array(
			'type' => 'switch',
			'switch_text' => __( 'Testimonials', 'us' ),
			'std' => 1,
			'classes' => 'for_above',
		),
		'media_category' => array(
			'type' => 'switch',
			'switch_text' => __( 'Media Categories', 'us' ),
			'std' => 1,
			'classes' => 'for_above',
		),
		'enable_additional_settings' => array(
			'type' => 'switch',
			'switch_text' => __( 'Additional Settings', 'us' ),
			'std' => 1,
			'classes' => 'for_above',
		),
		'additional_settings_post_types' => array(
			'type' => 'checkboxes',
			'options' => $public_post_types,
			'std' => implode( ',', array_keys( $public_post_types ) ),
			'classes' => 'for_above align_with_switch vertical',
			'show_if' => array( 'enable_additional_settings', '=', 1 ),
		),
		'og_enabled' => array(
			'type' => 'switch',
			'switch_text' => __( 'SEO meta tags', 'us' ),
			'description' => __( 'If you\'re using any SEO plugin, turn OFF this option to avoid conflicts.', 'us' ) . ' <a href="' . $help_portal_url . '/' . strtolower( US_THEMENAME ) . '/seo/" target="_blank">' . __( 'Learn more', 'us' ) . '</a>',
			'std' => 1,
			'classes' => 'desc_2 for_above',
		),
		'schema_markup' => array(
			'type' => 'switch',
			'switch_text' => __( 'Schema.org markup', 'us' ),
			'std' => 1,
			'classes' => 'for_above',
		),
		'templates_access_for_editors' => array(
			'type' => 'switch',
			'switch_text' => __( 'Access to Templates for Editors', 'us' ),
			'description' => sprintf( __( 'When this option is ON, all users who can edit pages, will also be able to edit the following: %s, %s, %s and %s.', 'us' ), _x( 'Headers', 'site top area', 'us' ), __( 'Page Templates', 'us' ), __( 'Reusable Blocks', 'us' ), __( 'Grid Layouts', 'us' ) ),
			'std' => 0,
			'classes' => 'desc_2 for_above',
			'place_if' => empty( $usof_options['white_label'] ),
		),

		// Website Performance
		'h_advanced_3' => array(
			'title' => __( 'Website Performance', 'us' ),
			'type' => 'heading',
			'classes' => 'with_separator',
		),
		'keep_url_protocol' => array(
			'type' => 'switch',
			'switch_text' => __( 'Keep "http/https" in the paths to files', 'us' ),
			'description' => __( 'If your site uses both "HTTP" and "HTTPS" and has some appearance issues, turn OFF this option.', 'us' ),
			'std' => 1,
			'classes' => 'desc_2',
		),
		'disable_jquery_migrate' => array(
			'type' => 'switch',
			'switch_text' => __( 'Disable jQuery migrate script', 'us' ),
			'description' => __( 'When this option is ON, "jquery-migrate.min.js" file won\'t be loaded on the front end.', 'us' ) . ' ' . __( 'This will improve page loading speed.', 'us' ),
			'std' => 1,
			'classes' => 'desc_2 for_above',
		),
		'jquery_footer' => array(
			'type' => 'switch',
			'switch_text' => __( 'Move jQuery scripts to the footer', 'us' ),
			'description' => __( 'When this option is ON, jQuery library files will be loaded after the page content.', 'us' ) . ' ' . __( 'This will improve page loading speed.', 'us' ),
			'std' => 1,
			'classes' => 'desc_2 for_above',
		),
		'disable_extra_vc' => array(
			'type' => 'switch',
			'switch_text' => __( 'Disable extra features of WPBakery Page Builder', 'us' ),
			'description' => __( 'When this option is ON, the original CSS and JS files of WPBakery Page Builder won\'t be loaded on the front end.', 'us' ) . ' ' . __( 'This will improve page loading speed.', 'us' ),
			'std' => 1,
			'place_if' => class_exists( 'Vc_Manager' ),
			'classes' => 'desc_2 for_above',
		),
		'optimize_assets' => array(
			'type' => 'switch',
			'switch_text' => __( 'Optimize JS and CSS size', 'us' ),
			'description' => __( 'When this option is ON, your site will compress scripts to a single JS file and compress styles to a single CSS file. You can disable unused components to reduce their sizes.', 'us' ) . ' ' . __( 'This will improve page loading speed.', 'us' ),
			'std' => 0,
			'classes' => 'desc_2 for_above',
			'disabled' => $upload_dir_not_writable,
		),
		'optimize_assets_alert' => array(
			'description' => __( 'Your uploads folder is not writable. Change your server permissions to use this option.', 'us' ),
			'type' => 'message',
			'classes' => 'string',
			'place_if' => $upload_dir_not_writable,
		),
		'optimize_assets_start' => array(
			'type' => 'wrapper_start',
			'show_if' => array( 'optimize_assets', '=', 1 ),
		),
		'assets' => array(
			'type' => 'check_table',
			'show_auto_optimize_button' => TRUE,
			'options' => $usof_assets,
			'std' => $usof_assets_std,
			'classes' => 'desc_4',
		),
		'optimize_assets_end' => array(
			'type' => 'wrapper_end',
		),
		'include_gfonts_css' => array(
			'type' => 'switch',
			'switch_text' => __( 'Merge Google Fonts styles into single CSS file', 'us' ),
			'description' => __( 'When this option is ON, Google Fonts CSS file won\'t be loaded separately.', 'us' ) . ' ' . __( 'This will improve page loading speed.', 'us' ),
			'std' => 0,
			'classes' => 'desc_2',
			'show_if' => array( 'optimize_assets', '=', 1 ),
		),
	),
);
