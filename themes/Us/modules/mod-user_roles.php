<?php
/**
*  Remove roles
*/
add_action('init', 'remove_unnecessary_roles');
function remove_unnecessary_roles() {
    remove_role( 'subscriber' );
	remove_role( 'contributor' );
	remove_role( 'author' );
	remove_role( 'editor' );
}

/**
 * Create 'Us client' role with restricted admin capabilities
 */
add_action('init', 'add_us_client_role');
function add_us_client_role() {
    // Only create the role if it doesn't already exist
    if (get_role('us_client')) {
        return;
    }

    // Clone admin capabilities
    $admin_caps = get_role('administrator')->capabilities;

    // Capabilities to remove
    $caps_to_remove = array(
        'activate_plugins',
        'delete_plugins',
        'delete_themes',
        'delete_users',
        'edit_files',
        'edit_plugins',
        'edit_theme_options',
        'edit_themes',
        'install_plugins',
        'install_themes',
        'promote_users',
        'remove_users',
        'switch_themes',
        'update_core',
        'update_plugins',
        'update_themes',
    );

    // Remove unwanted capabilities
    foreach ( $caps_to_remove as $cap ) {
        unset( $admin_caps[ $cap ] );
    }

    // Add the new role with the filtered capabilities
    add_role('us_client', 'Us Client', $admin_caps);
}