<?php
/**
 * Edit WP dashboard CSS
 */
function Us_admin_css() {
  echo '<style>
		.menu-icon-generic.toplevel_page_us-theme-options .wp-menu-image {
    		background-position: center center !important;
		}
  </style>';
}
add_action('admin_head', 'Us_admin_css');

// Remove unused items from admin
function Us_admin_remove_unused () { 
   	remove_menu_page('edit.php?post_type=us_portfolio');
	remove_menu_page('edit.php?post_type=us_testimonial');
	remove_menu_page('edit-comments.php');
	
	$current_user = wp_get_current_user();
	if ($current_user->ID != 1) {
		remove_menu_page('activity_log_page');
		//remove_menu_page('themes.php');
		remove_menu_page('plugins.php');
		remove_menu_page('tools.php');
		remove_menu_page('vc-general');
		remove_menu_page('edit.php?post_type=acf-field-group');
		remove_menu_page('about-ultimate');
	}
}
//add_action('admin_menu', 'Us_admin_remove_unused', 9999); 

// Change dashboard Posts to Blog
function Us_change_post_object() {
    $get_post_type = get_post_type_object('post');
    $labels = $get_post_type->labels;
        $labels->name = 'Blog posts';
        $labels->singular_name = 'Blog post';
        $labels->add_new = 'Add post';
        $labels->add_new_item = 'Add post';
        $labels->edit_item = 'Edit post';
        $labels->new_item = 'Blog posts';
        $labels->view_item = 'View posts';
        $labels->search_items = 'Search posts';
        $labels->not_found = 'No posts found';
        $labels->not_found_in_trash = 'No posts found in Trash';
        $labels->all_items = 'All posts';
        $labels->menu_name = 'Blog';
        $labels->name_admin_bar = 'Blog';
}
add_action( 'init', 'Us_change_post_object' );

/**
 * Lowers the metabox priority to 'low' for Yoast SEO's metabox.
 */
function lower_yoast_metabox_priority( $priority ) {
  return 'low';
}

add_filter( 'wpseo_metabox_prio', 'lower_yoast_metabox_priority' );

// Yoast SEO - Disabling the Primary category feature
add_filter( 'wpseo_primary_term_taxonomies', '__return_empty_array' );




/* CUSTOM JS */
function Us_custom_scripts() {
	wp_dequeue_style( 'theme-style' );
	wp_enqueue_style( 'style',  get_stylesheet_uri(), array(), mt_rand() );
    wp_enqueue_script(
        'custom-script',
        get_stylesheet_directory_uri() . '/js/custom-child-theme-script.js',
        array( 'jquery' )
    );
}
add_action( 'wp_enqueue_scripts', 'Us_custom_scripts' );

// ASYNC JAVASCRIPTS
function defer_parsing_of_js( $url ) {
    if ( is_user_logged_in() ) return $url; //don't break WP Admin
    if ( FALSE === strpos( $url, '.js' ) ) return $url;
    if ( strpos( $url, 'jquery.js' ) ) return $url;
    return str_replace( ' src', ' defer src', $url );
}
add_filter( 'script_loader_tag', 'defer_parsing_of_js', 10 );

/* DISABLE PINGBACK TO PREVENT DDOS ATTACKS */
add_filter( 'xmlrpc_methods', function( $methods ) {
	unset( $methods['pingback.ping'] );
	return $methods;
} );

