<?php
// MODULES 
get_template_part( 'modules/mod','user_roles' );
get_template_part( 'modules/mod','theme_modifications' );

// Shortcodes 
get_template_part( 'shortcodes/sc','breadcrumb' );

//[get_post_title]
function get_post_title( $atts ) {
	$html = get_the_title();
	
	return $html;
	
}
add_shortcode( 'get_post_title', 'get_post_title' );

// Load custom functionality files
// require_once get_stylesheet_directory() . '/inc/acf-functions.php';