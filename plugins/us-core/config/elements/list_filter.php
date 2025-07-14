<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: grid_filter
 */

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );

$source_options = $tax_options = $numeric_options = $bool_options = $date_options = $post_type_options = array();

if ( us_is_elm_editing_page() ) {
	$_group = '';

	foreach( us_get_list_filter_params() as $name => $param ) {

		if ( $_group != $param['group'] ) {
			$_group = $param['group'];
		}
		$source_options[ $_group ][ $name ] = $param['label'];

		if ( $param['source_type'] == 'tax' ) {
			$tax_options[] = $name;
		}
		if ( isset( $param['value_type'] ) ) {
			if ( $param['value_type'] == 'numeric' ) {
				$numeric_options[] = $name;

			} elseif ( $param['value_type'] == 'bool' ) {
				$bool_options[] = $name;

			} elseif ( $param['value_type'] == 'date' OR $param['value_type'] == 'date_time' ) {
				$date_options[] = $name;
			}
		}
	}

	$post_type_options = us_grid_available_post_types( TRUE );
	unset( $post_type_options['attachment'] );
}

/**
 * @return array
 */
return array(
	'title' => __( 'List Filter', 'us' ),
	'category' => __( 'Lists', 'us' ),
	'icon' => 'fas fa-filter',
	'params' => us_set_params_weight(

		// General section
		array(
			'items' => array(
				'title' => __( 'Filter by', 'us' ),
				'type' => 'group',
				'show_controls' => TRUE,
				'is_sortable' => TRUE,
				'is_accordion' => TRUE,
				'accordion_title' => 'source',
				'params' => array(

					'source' => array(
						'title' => us_translate( 'Source' ),
						'type' => 'select',
						'options' => apply_filters( 'us_list_filter_source_options', $source_options ),
						'std' => 'post_type',
						'admin_label' => TRUE,
					),

					'post_type' => array(
						'type' => 'checkboxes',
						'options' => apply_filters( 'us_list_filter_post_types', $post_type_options ),
						'std' => '',
						'show_if' => array( 'source', '=', 'post_type' ),
						'classes' => 'for_above',
					),
					'post_author' => array(
						'type' => 'select',
						'options' => array(
							'all' => __( 'All authors', 'us' ),
							'include' => __( 'Selected authors', 'us' ),
							'exclude' => __( 'Exclude selected authors', 'us' ),
						),
						'std' => 'all',
						'show_if' => array( 'source', '=', 'post_author' ),
						'classes' => 'for_above',
					),
					'post_author_ids' => array(
						'type' => 'autocomplete',
						'search_text' => __( 'Select authors', 'us' ),
						'is_multiple' => TRUE,
						'is_sortable' => TRUE,
						'ajax_data' => array(
							'_nonce' => wp_create_nonce( 'us_ajax_get_user_ids_for_autocomplete' ),
							'action' => 'us_get_user_ids_for_autocomplete',
						),
						'options' => array(), // will be loaded via ajax
						'std' => '',
						'show_if' => array( 'post_author', '=', array( 'include', 'exclude' ) ),
						'classes' => 'for_above',
					),

					// Taxonomy params
					'term_compare' => array(
						'type' => 'select',
						'options' => array(
							'all' => __( 'All terms', 'us' ),
							'include' => __( 'Selected terms', 'us' ),
							'exclude' => __( 'Terms except selected', 'us' ),
						),
						'std' => 'all',
						'classes' => 'for_above',
						'show_if' => array( 'source', '=', $tax_options ),
					),
					'term_ids' => array(
						'type' => 'autocomplete',
						'search_text' => __( 'Select terms', 'us' ),
						'is_multiple' => TRUE,
						'is_sortable' => TRUE,
						'ajax_data' => array(
							'_nonce' => wp_create_nonce( 'us_ajax_get_terms_for_autocomplete' ),
							'action' => 'us_get_terms_for_autocomplete',
							'use_term_ids' => TRUE,
						),
						'options' => array(), // will be loaded via ajax
						'options_filtered_by_param' => 'source',
						'std' => '',
						'classes' => 'for_above',
						'show_if' => array( 'term_compare', '=', array( 'include', 'exclude' ) ),
					),
					'term_show_children' => array(
						'type' => 'switch',
						'switch_text' => __( 'Include child terms', 'us' ),
						'std' => 0,
						'classes' => 'for_above',
						'show_if' => array( 'term_compare', '=', 'all' ),
					),
					'term_exclude_children' => array(
						'type' => 'switch',
						'switch_text' => __( 'Also exclude child terms', 'us' ),
						'std' => 1,
						'classes' => 'for_above',
						'show_if' => array( 'term_compare', '=', 'exclude' ),
					),

					// Appearance
					'selection_type' => array(
						'title' => __( 'Selection Type', 'us' ),
						'type' => 'select',
						'options' => array(
							'checkbox' => __( 'Checkboxes', 'us' ),
							'radio' => __( 'Radio buttons', 'us' ),
							'dropdown' => __( 'Dropdown', 'us' ),
						),
						'std' => 'checkbox',
						'show_if' => array( 'source', '!=', array_merge( $date_options, $numeric_options ) ),
					),
					'term_operator' => array(
						'title' => __( 'Selection Operator', 'us' ),
						'description' => __( 'Applies to checkboxes only.', 'us' ),
						'type' => 'radio',
						'options' => array(
							'OR' => __( 'OR', 'us' ),
							'AND' => __( 'AND', 'us' ),
						),
						'std' => 'OR',
						'show_if' => array( 'source', '=', $tax_options ),
					),
					'bool_value_label' => array(
						'title' => __( 'Value Label', 'us' ),
						'description' => __( 'This label will appear near the value.', 'us' ) . ' ' . __( 'Leave blank to use the default.', 'us' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'source', '=', $bool_options ),
					),

					// Numeric related
					'numeric_selection_type' => array(
						'title' => __( 'Selection Type', 'us' ),
						'type' => 'select',
						'options' => array(
							'range_slider' => __( 'Range Slider', 'us' ),
							'range_input' => __( 'Range Input', 'us' ),
							'checkbox' => __( 'Checkboxes', 'us' ),
							'radio' => __( 'Radio buttons', 'us' ),
							'dropdown' => __( 'Dropdown', 'us' ),
						),
						'std' => 'range_slider',
						'show_if' => array( 'source', '=', $numeric_options ),
					),
					'num_values_range' => array(
						'title' => __( 'Numeric Values Range', 'us' ),
						'description' => __( 'All existing values will be divided into groups by this range. Leave blank to display actual values instead.', 'us' ),
						'type' => 'text',
						'std' => '10',
						'show_if' => array( 'numeric_selection_type', '=', array( 'checkbox', 'radio', 'dropdown' ) ),
					),
					'num_step_size' => array(
						'title' => __( 'Step Size', 'us' ),
						'type' => 'text',
						'std' => '10',
						'show_if' => array( 'numeric_selection_type', '=', 'range_slider' ),
					),
					'num_min_value' => array(
						'title' => __( 'Min Value', 'us' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'numeric_selection_type', '=', array( 'range_input', 'range_slider' ) ),
					),
					'num_max_value' => array(
						'title' => __( 'Max Value', 'us' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'numeric_selection_type', '=', array( 'range_input', 'range_slider' ) ),
					),

					// Date related
					'date_selection_type' => array(
						'title' => __( 'Selection Type', 'us' ),
						'type' => 'select',
						'options' => array(
							'date_picker' => __( 'Date Picker', 'us' ),
							'checkbox' => __( 'Checkboxes', 'us' ),
							'radio' => __( 'Radio buttons', 'us' ),
							'dropdown' => __( 'Dropdown', 'us' ),
						),
						'std' => 'date_picker',
						'show_if' => array( 'source', '=', $date_options ),
					),
					'date_picker_fields' => array(
						'title' => __( 'Fields to Show', 'us' ),
						'type' => 'select',
						'options' => array(
							'exact' => __( 'Exact Date', 'us' ),
							'start' => __( 'Start Date', 'us' ),
							'end' => __( 'End Date', 'us' ),
							'start_end' => __( 'Start + End Dates', 'us' ),
						),
						'std' => 'exact',
						'show_if' => array( 'date_selection_type', '=', 'date_picker' ),
					),
					'date_values_format' => array(
						'title' => us_translate( 'Date Format' ),
						'type' => 'text',
						'std' => 'd MM yy',
						'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">yy-mm-dd</span>, <span class="usof-example">dd/mm/y</span>, <span class="usof-example">d MM, D</span>. <a href="https://api.jqueryui.com/datepicker/#utility-formatDate" target="_blank">' . __( 'Learn more', 'us' ) . '</a>',
						'show_if' => array( 'date_selection_type', '=', 'date_picker' ),
					),
					'date_picker_placeholder' => array(
						'title' => __( 'Placeholder', 'us' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'date_selection_type', '=', 'date_picker' ),
					),
					'date_picker_placeholder_2' => array(
						'type' => 'text',
						'std' => '',
						'classes' => 'for_above',
						'show_if' => array( 'date_picker_fields', '=', 'start_end' ),
					),
					'date_values_range' => array(
						'title' => __( 'Date Values Range', 'us' ),
						'description' => __( 'All existing values will be grouped by selected range.', 'us' ),
						'type' => 'select',
						'options' => array(
							'yearly' => __( 'Yearly', 'us' ),
							'monthly' => __( 'Monthly', 'us' ),
						),
						'std' => 'yearly',
						'show_if' => array( 'date_selection_type', '=', array( 'checkbox', 'radio', 'dropdown' ) ),
					),

					'label' => array(
						'title' => us_translate( 'Title' ),
						'description' => __( 'Leave blank to use the default.', 'us' ),
						'type' => 'text',
						'std' => '',
						'admin_label' => TRUE,
					),
					'first_value_label' => array(
						'title' => __( 'First Value Label', 'us' ),
						'description' => __( 'Applies to radio buttons and dropdown only.', 'us' ),
						'type' => 'text',
						'std' => __( 'Any', 'us' ),
					),
					'has_search' => array(
						'type' => 'switch',
						'switch_text' => __( 'Show search field to narrow choices', 'us' ),
						'description' => __( 'Applies to checkboxes and radio buttons only.', 'us' ),
						'std' => 0,
					),
					'search_placeholder' => array(
						'title' => __( 'Placeholder', 'us' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'has_search', '=', 1 ),
					),
					'show_color_swatch' => array(
						'switch_text' => __( 'Show color swatches', 'us' ),
						'description' => sprintf( __( 'Works with %s only.', 'us' ), '<a href="' . admin_url( 'edit.php?post_type=product&page=product_attributes' ) . '" target="_blank">' . us_translate_x( 'Product attributes', 'block title', 'woocommerce' ) . '</a>' ),
						'type' => 'switch',
						'std' => 0,
						'show_if' => array( 'source', '=', $tax_options ),
						'place_if' => class_exists( 'woocommerce' ),
					),
					'hide_color_swatch_label' => array(
						'switch_text' => __( 'Hide color names', 'us' ),
						'type' => 'switch',
						'std' => 0,
						'place_if' => class_exists( 'woocommerce' ),
						'show_if' => array( 'show_color_swatch', '=', '1' ),
					),
				),
				'std' => array(
					array(
						'source' => 'post_type',
						'term_compare' => 'all',
						'term_show_children' => 0,
						'term_exclude_children' => 1,
						'selection_type' => 'checkbox',
						'label' => '',
						'first_value_label' => __( 'Any', 'us' ),
						'has_search' => 0,
						'show_color_swatch' => 0,
					),
				),
				'usb_preview' => TRUE,
			),
		),

		// Appearance section
		array(
			'layout' => array(
				'title' => __( 'Orientation', 'us' ),
				'type' => 'radio',
				'options' => array(
					'ver' => __( 'Vertical', 'us' ),
					'hor' => __( 'Horizontal', 'us' ),
				),
				'std' => 'ver',
				'admin_label' => TRUE,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'layout',
				),
			),
			'item_layout' => array(
				'title' => __( 'Layout of Filter Items', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Titles on top', 'us' ),
					'toggle' => __( 'Titles as toggles', 'us' ),
					'dropdown' => __( 'Titles as dropdowns', 'us' ),
					// 'no_titles' => __( 'Without Titles', 'us' ),
				),
				'std' => 'default',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'mod',
				),
			),
			'dropdown_field_style' => array(
				'title' => __( 'Dropdown Field Style', 'us' ),
				'description' => $misc['desc_field_styles'],
				'type' => 'select',
				'options' => us_get_field_styles(),
				'std' => 'default',
				'show_if' => array( 'item_layout', '=', 'dropdown' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'elm' => '.w-filter-item-title',
					'mod' => 'us-field-style',
				),
			),
			'values_drop' => array(
				'title' => __( 'Show the list of values', 'us' ),
				'type' => 'radio',
				'options' => array(
					'hover' => __( 'On hover', 'us' ),
					'click' => __( 'On click', 'us' ),
				),
				'std' => 'hover',
				'show_if' => array( 'item_layout', '=', 'dropdown' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'drop_on',
				),
			),
			'align' => array(
				'title' => us_translate( 'Alignment' ),
				'type' => 'radio',
				'labels_as_icons' => 'fas fa-align-*',
				'options' => array(
					'none' => us_translate( 'Default' ),
					'left' => us_translate( 'Left' ),
					'center' => us_translate( 'Center' ),
					'right' => us_translate( 'Right' ),
					'justify' => us_translate( 'Justify' ),
				),
				'std' => 'none',
				'show_if' => array( 'layout', '=', 'hor' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'align',
				),
			),
			'items_gap' => array(
				'title' => __( 'Gap between Items', 'us' ),
				'type' => 'slider',
				'std' => '1.5em',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'css' => '--items-gap',
				),
			),
			'values_max_height' => array(
				'title' => __( 'Max Height of the list of values', 'us' ),
				'description' => $misc['desc_height'],
				'type' => 'text',
				'std' => '',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'elm' => '.w-filter-item-values',
					'css' => 'max-height',
				),
			),
			'us_field_style' => array(
				'title' => __( 'Field Style', 'us' ),
				'description' => $misc['desc_field_styles'],
				'type' => 'select',
				'options' => us_get_field_styles(),
				'std' => 'default',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'us-field-style',
				),
			),
		),

		// Mobiles section
		array(
			'mobile_width' => array(
				'title' => __( 'Mobile view at screen width', 'us' ),
				'description' => __( 'Leave blank to not apply mobile view.', 'us' ),
				'type' => 'text',
				'std' => '600px',
				'group' => __( 'Mobiles', 'us' ),
				'usb_preview' => TRUE,
			),
			'mobile_button_label' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => __( 'Filters', 'us' ),
				'group' => __( 'Mobiles', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-filter-opener > span',
					'attr' => 'html',
				),
			),
			'mobile_button_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'' => '– ' . us_translate( 'None' ) . ' –'
					),
					us_get_btn_styles()
				),
				'std' => '',
				'group' => __( 'Mobiles', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-filter-opener',
					'mod' => 'us-btn-style',
				),
			),
			'mobile_button_icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'group' => __( 'Mobiles', 'us' ),
				'usb_preview' => TRUE,
			),
			'mobile_button_iconpos' => array(
				'title' => __( 'Icon Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'left',
				'group' => __( 'Mobiles', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		// More Options
		array(
			'list_to_filter' => array(
				'title' => __( 'List to filter', 'us' ),
				'type' => 'select',
				'options' => array(
					'first' => __( 'First List on a page', 'us' ),
					'selector' => __( 'Custom List selector', 'us' ),
				),
				'std' => 'first',
				'group' => __( 'More Options', 'us' ),
			),
			'list_selector_to_filter' => array(
				'description' => __( 'Use class or ID.', 'us' ) . ' ' . __( 'Examples:', 'us' ) . ' <span class="usof-example">.filter-me</span>, <span class="usof-example">#filterable-list</span>',
				'type' => 'text',
				'std' => '',
				'classes' => 'for_above',
				'show_if' => array( 'list_to_filter', '=', 'selector' ),
				'group' => __( 'More Options', 'us' ),
			),
			'change_url_params' => array(
				'switch_text' => __( 'Change URL params', 'us' ),
				'type' => 'switch',
				'std' => 1,
				'group' => __( 'More Options', 'us' ),
			),
			'faceted_filtering' => array(
				'type' => 'switch',
				'switch_text' => __( 'Faceted Filtering', 'us' ),
				'description' => __( 'Allows to show the number of relevant posts next to each filter value when filtering.', 'us' ),
				'std' => 0,
				'group' => __( 'More Options', 'us' ),
				'usb_preview' => TRUE,
			),
			'faceted_filtering_message' => array(
				'type' => 'message',
				'description' => sprintf( __( 'You need to index filter items in %sTheme Options%s to take effect.', 'us' ), '<a target="_blank" href="' . admin_url( 'admin.php?page=us-theme-options#advanced' ) . '">', '</a>' ),
				'show_if' => array( 'faceted_filtering', '=', '1' ),
				'group' => __( 'More Options', 'us' ),
			),
			'hide_disabled_values' => array(
				'switch_text' => __( 'Hide unavailable values', 'us' ),
				'description' => __( 'When turned off, unavailable values will remain visible, but not clickable.', 'us' ),
				'type' => 'switch',
				'std' => 0,
				'show_if' => array( 'faceted_filtering', '=', '1' ),
				'group' => __( 'More Options', 'us' ),
				'usb_preview' => array(
					'toggle_class' => 'hide_disabled_values',
				),
			),
			'hide_disabled_items' => array(
				'switch_text' => __( 'Hide items with all unavailable values', 'us' ),
				'description' => __( 'When turned off, items with all unavailable values will remain visible, but not clickable.', 'us' ),
				'type' => 'switch',
				'std' => 0,
				'show_if' => array( 'faceted_filtering', '=', '1' ),
				'group' => __( 'More Options', 'us' ),
				'usb_preview' => array(
					'toggle_class' => 'hide_disabled_items',
				),
			),
			'post_type_for_values' => array(
				'title' => __( 'Post types to show values (optional)', 'us' ),
				'description' => __( 'By default, values from all post types are displayed.', 'us' ),
				'type' => 'checkboxes',
				'options' => apply_filters( 'us_list_filter_post_types', $post_type_options ),
				'std' => '',
				'show_if' => array( 'faceted_filtering', '!=', '1' ),
				'group' => __( 'More Options', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		$conditional_params,
		$design_options_params
	),
	'fallback_params' => array(
		'filter_items',
		'enable_toggles',
	),

	'usb_init_js' => '$elm.usListFilter()',
);
