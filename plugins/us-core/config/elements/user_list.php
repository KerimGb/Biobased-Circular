<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: us_user_list
 */

$elm_config = array(
	'title' => __( 'User List', 'us' ),
	'category' => __( 'Lists', 'us' ),
	'description' => __( 'List of registered users.', 'us' ),
	'icon' => 'fas fa-th-large',
	'params' => array(),
);

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );

// User Roles
$user_roles = array();

// Avoid DB queries on the frontend
if ( us_is_elm_editing_page() ) {

	// Check if the get_editable_roles function exists for AJAX calls of other plugins compatibility
	$editable_roles = function_exists( 'get_editable_roles' ) ? get_editable_roles() : array();

	foreach ( $editable_roles as $_slug => $_data ) {
		$user_roles[ $_slug ] = translate_user_role( $_data['name'] );
	}
}

// Source options for Users
$source_options = array(
	'all' => us_translate( 'All Users' ),
	'include' => __( 'Selected users', 'us' ),
	'exclude' => __( 'Users except selected', 'us' ),
	'role__in' => __( 'Users with selected roles', 'us' ),
	'role__not_in' => __( 'Users except selected roles', 'us' ),
	'current_post_author' => __( 'Author of the current post', 'us' ),
);

// Order options for Users
$orderby_options = array(
	'display_name' => us_translate( 'User Display Name' ),
	'post_count' => __( 'Amount of posts', 'us' ),
	'registered' => __( 'Registration Date', 'us' ),
	'rand' => us_translate( 'Random' ),
	'include' => __( 'Order of selected users', 'us' ),
	'custom' => __( 'Custom Field', 'us' ),
);

// General
$general_params = array(

	'source' => array(
		'title' => us_translate( 'Show' ),
		'type' => 'select',
		'options' => apply_filters( 'us_user_list_source_options', $source_options ),
		'std' => 'all',
		'admin_label' => TRUE,
		'usb_preview' => TRUE,
	),
	'user_ids' => array(
		'type' => 'autocomplete',
		'search_text' => __( 'Select users', 'us' ),
		'is_multiple' => TRUE,
		'is_sortable' => TRUE,
		'ajax_data' => array(
			'_nonce' => wp_create_nonce( 'us_ajax_get_user_ids_for_autocomplete' ),
			'action' => 'us_get_user_ids_for_autocomplete',
		),
		'options' => array(), // will be loaded via ajax
		'std' => '',
		'classes' => 'for_above',
		'show_if' => array( 'source', '=', array( 'include', 'exclude' ) ),
		'usb_preview' => TRUE,
	),
	'role' => array(
		'type' => 'autocomplete',
		'search_text' => __( 'Select roles', 'us' ),
		'is_multiple' => TRUE,
		'options' => $user_roles,
		'std' => 'administrator',
		'classes' => 'for_above',
		'show_if' => array( 'source', '=', array( 'role__in', 'role__not_in' ) ),
		'usb_preview' => TRUE,
	),
	'has_published_posts' => array(
		'type' => 'switch',
		'switch_text' => __( 'Only with published posts', 'us' ),
		'std' => 0,
		'show_if' => array( 'source', '!=', array( 'include', 'exclude' ) ),
		'classes' => 'for_above',
		'usb_preview' => TRUE,
	),
	'exclude_current' => array(
		'type' => 'switch',
		'switch_text' => __( 'Exclude the current author', 'us' ),
		'description' => __( 'Works only on the author\'s archive.', 'us' ),
		'std' => 0,
		'show_if' => array( 'source', '!=', array( 'include' ) ),
		'classes' => 'for_above desc_2',
		'usb_preview' => TRUE,
	),

	// ORDER
	'orderby' => array(
		'title' => __( 'Order by', 'us' ),
		'type' => 'select',
		'options' => apply_filters( 'us_user_list_orderby_options', $orderby_options ),
		'std' => 'display_name',
		'usb_preview' => TRUE,
	),
	'orderby_custom_field' => array(
		'placeholder' => 'custom_field_name',
		'description' => __( 'Enter custom field name to order items by its value', 'us' ),
		'type' => 'text',
		'std' => '',
		'classes' => 'for_above',
		'show_if' => array( 'orderby', '=', 'custom' ),
		'usb_preview' => TRUE,
	),
	'orderby_custom_type' => array(
		'type' => 'switch',
		'switch_text' => __( 'Order by numeric values', 'us' ),
		'std' => 0,
		'classes' => 'for_above',
		'show_if' => array( 'orderby', '=', 'custom' ),
		'usb_preview' => TRUE,
	),
	'order_invert' => array(
		'type' => 'switch',
		'switch_text' => __( 'Invert order', 'us' ),
		'std' => 0,
		'classes' => 'for_above',
		'show_if' => array( 'orderby', '!=', array( 'include', 'rand' ) ),
		'usb_preview' => TRUE,
	),

	// NUMBER
	'show_all' => array(
		'title' => __( 'Quantity', 'us' ),
		'type' => 'switch',
		'switch_text' => __( 'Show all users', 'us' ),
		'std' => 0,
		'usb_preview' => TRUE,
	),
	'number' => array(
		'type' => 'slider',
		'options' => array(
			'' => array(
				'min' => 1,
				'max' => 30,
			),
		),
		'std' => '12',
		'classes' => 'for_above',
		'show_if' => array( 'show_all', '=', 0 ),
		'usb_preview' => TRUE,
	),

	// CUSTOM FIELDS
	'meta_query_relation' => array(
		'title' => __( 'Show users with specific custom fields', 'us' ),
		'type' => 'select',
		'options' => array(
			'none' => us_translate( 'None' ),
			'AND' => __( 'If EVERY condition below is met', 'us' ),
			'OR' => __( 'If ANY condition below is met', 'us' ),
		),
		'std' => 'none',
		'show_if' => array( 'source', '!=', array( 'include', 'exclude' ) ),
		'usb_preview' => TRUE,
	),
	'meta_query' => array(
		'type' => 'group',
		'show_controls' => TRUE,
		'label_for_add_button' => __( 'Add condition', 'us' ),
		'is_sortable' => FALSE,
		'is_accordion' => FALSE,
		'accordion_title' => 'key',
		'params' => array(
			'key' => array(
				'title' => __( 'Custom Field', 'us' ),
				'placeholder' => us_translate( 'Field name' ),
				'type' => 'text',
				'std' => 'custom_field_name',
				'admin_label' => TRUE,
			),
			'compare' => array(
				'type' => 'select',
				'options' => array(
					'=' => '=',
					'!=' => '!=',
					'>' => '>',
					'>=' => '≥',
					'<' => '<',
					'<=' => '≤',
					'LIKE' => __( 'Includes', 'us' ),
					'NOT LIKE' => __( 'Excludes', 'us' ),
					'EXISTS' => __( 'Has a value', 'us' ),
					'NOT EXISTS' => __( 'Doesn\'t have a value', 'us' ),
				),
				'std' => '=',
				'classes' => 'for_above',
			),
			'value' => array(
				'placeholder' => us_translate( 'Value' ),
				'type' => 'text',
				'std' => '',
				'show_if' => array( 'compare', '!=', array( 'EXISTS', 'NOT EXISTS' ) ),
				'classes' => 'for_above',
			),
		),
		'std' => array(
			array(
				'key' => 'custom_field_name',
				'compare' => '=',
				'value' => '',
			),
		),
		'show_if' => array( 'meta_query_relation', '!=', 'none' ),
		'usb_preview' => TRUE,
	),

	// NO RESULTS
	'no_items_action'=> array(
		'title' => __( 'Action when no users found', 'us' ),
		'type' => 'select',
		'options' => array(
			'message' => __( 'Show the message', 'us' ),
			'hide_grid' => __( 'Hide this element', 'us' ),
		),
		'std' => 'message',
		'usb_preview' => TRUE,
	),
	'no_items_message' => array(
		'type' => 'text',
		'std' => us_translate( 'No results found.' ),
		'classes' => 'for_above',
		'show_if' => array( 'no_items_action', '=', 'message' ),
		'usb_preview' => array(
			'elm' => '.w-grid-none',
			'attr' => 'html',
		),
	),
);

// Appearance
$appearance_params = array(
	'items_layout' => array(
		'title' => __( 'Grid Layout', 'us' ),
		'description' => $misc['desc_grid_layout'],
		'type' => 'select',
		'options' => us_is_elm_editing_page()
			? us_get_grid_layouts_for_selection( array( 'user' ) )
			: array(),
		'std' => 'user_1',
		'classes' => 'for_grid_layouts',
		'settings' => array(
			'html-data' => array(
				'edit_link' => admin_url( '/post.php?post=%d&action=edit' ),
			),
		),
		'admin_label' => TRUE,
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'columns' => array(
		'title' => us_translate( 'Columns' ),
		'type' => 'slider',
		'options' => array(
			'' => array(
				'min' => 1,
				'max' => 10,
			),
		),
		'std' => '3',
		'admin_label' => TRUE,
		'cols' => 2,
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => array(
			array(
				'elm' => '.w-grid',
				'mod' => 'cols',
			),
			array(
				'elm' => '.w-grid',
				'css' => '--columns',
			),
		),
	),
	'items_gap' => array(
		'title' => __( 'Gap between Items', 'us' ),
		'type' => 'slider',
		'std' => '10px',
		'options' => $misc['items_gap'],
		'cols' => 2,
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => array(
			'elm' => '.w-grid',
			'css' => '--gap',
		),
	),
	'load_animation' => array(
		'title' => __( 'Items animation on load', 'us' ),
		'type' => 'select',
		'options' => array(
			'none' => us_translate( 'None' ),
			'fade' => __( 'Fade', 'us' ),
			'afc' => __( 'Appear From Center', 'us' ),
			'afl' => __( 'Appear From Left', 'us' ),
			'afr' => __( 'Appear From Right', 'us' ),
			'afb' => __( 'Appear From Bottom', 'us' ),
			'aft' => __( 'Appear From Top', 'us' ),
			'hfc' => __( 'Height Stretch', 'us' ),
			'wfc' => __( 'Width Stretch', 'us' ),
		),
		'std' => 'none',
		'group' => us_translate( 'Appearance' ),
	),
	'overriding_link' => array(
		'title' => __( 'Overriding Link', 'us' ),
		'description' => __( 'Applies to every user of this list.', 'us' ) . ' ' . __( 'All inner elements become not clickable.', 'us' ),
		'type' => 'link',
		'dynamic_values' => array(
			'global' => array(),
			'post' => array(),
		),
		'std' => '{"url":""}',
		'group' => us_translate( 'Appearance' ),
	),
	'popup_width' => array(
		'title' => __( 'Popup Width', 'us' ),
		'description' => $misc['desc_width'],
		'type' => 'text',
		'std' => '',
		'show_if' => array( 'overriding_link', 'str_contains', 'popup_post' ),
		'group' => us_translate( 'Appearance' ),
	),
	'popup_arrows' => array(
		'switch_text' => __( 'Prev/Next arrows', 'us' ),
		'type' => 'switch',
		'std' => 1,
		'show_if' => array( 'overriding_link', 'str_contains', 'popup_post' ),
		'group' => us_translate( 'Appearance' ),
	),
);

// Responsive Options
$responsive_params = us_config( 'elements_responsive_options' );

$elm_config['params'] = us_set_params_weight(
	$general_params,
	$appearance_params,
	$responsive_params,
	$conditional_params,
	$design_options_params
);

$elm_config['usb_init_js'] = '$elm.wGrid();$us.$window.trigger( \'scroll.waypoints\' );';

/**
 * @return array
 */
return $elm_config;
