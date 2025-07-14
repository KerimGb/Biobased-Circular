<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_list_filter
 */

// Never output inside Grid items or specific Reusable Blocks
global $us_grid_outputs_items, $us_is_page_block_in_no_results, $us_is_page_block_in_menu;
if (
	$us_grid_outputs_items
	OR $us_is_page_block_in_no_results
	OR $us_is_page_block_in_menu
) {
	return;
}

// Never output the Grid Filter on AMP pages
if ( us_amp() ) {
	return;
}

// Don't output if there are no items
if ( empty( $items ) AND ! usb_is_post_preview() ) {
	return;
}

if ( is_string( $items ) ) {
	$items = json_decode( urldecode( $items ), TRUE );
}
if ( ! is_array( $items ) ) {
	$items = array();
}

$_atts = array(
	'class' => 'w-filter for_list state_desktop',
	'action' => '',
	'style' => '',
	'onsubmit' => 'return false;',
);

$_atts['class'] .= $classes ?? '';
$_atts['class'] .= ' layout_' . $layout;
$_atts['class'] .= ' items_' . count( $items );
$_atts['class'] .= ' mod_' . $item_layout;
$_atts['class'] .= ' align_' . $align;

if ( $item_layout == 'dropdown' ) {
	$_dropdown_field_class = ' us-field-style_' . $dropdown_field_style;
	$_atts['class'] .= ' drop_on_' . $values_drop;
} else {
	$_dropdown_field_class = '';
}

if ( $change_url_params ) {
	$_atts['class'] .= ' change_url_params';
}
if ( $hide_disabled_values ) {
	$_atts['class'] .= ' hide_disabled_values';
}
if ( $hide_disabled_items ) {
	$_atts['class'] .= ' hide_disabled_items';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

$_atts['style'] .= '--items-gap:' . $items_gap . ';';

$json_data = array(
	'mobileWidth' => (int) $mobile_width,
);

if ( $list_selector_to_filter ) {
	$json_data['listSelectorToFilter'] = $list_selector_to_filter;
}

if ( $faceted_filtering AND ! usb_is_preview() ) {
	$_atts['class'] .= ' faceted_filtering loading';

	$json_data['ajaxData'] = array(
		'action' => 'us_list_filter_post_count',
		'_nonce' => wp_create_nonce( 'us_list_filter_post_count' ),
	);
}

$output = '<form' . us_implode_atts( $_atts ) . us_pass_data_to_js( $json_data ) . '>';
$output .= '<div class="w-filter-list">';

if ( ! empty( $mobile_width ) ) {
	$output .= '<div class="w-filter-list-title">' . strip_tags( $mobile_button_label ) . '</div>';
	$output .= '<button class="w-filter-list-closer" type="button" title="' . esc_attr( us_translate( 'Close' ) ) . '" aria=label="' . esc_attr( us_translate( 'Close' ) ) . '">';
	$output .= '</button>';
}

/**
 * Get the number of hierarchy level for the provided term id
 */
$func_get_term_depth = function ( $id, $term_parents ) {
	$depth = 0;
	while ( $id > 0 ) {
		if ( $depth > 5 ) { // limit hierarchy by 5 levels
			break;
		}
		if ( isset( $term_parents[ $id ] ) ) {
			$id = $term_parents[ $id ];
			$depth ++;
		} else {
			$id = 0;
		}
	}

	return $depth;
};

$_available_post_types = us_grid_available_post_types( TRUE, FALSE );
unset( $_available_post_types['attachment'] );
asort( $_available_post_types );

// Define post types for parsing values from database
$_post_types = array_keys( $_available_post_types );
if ( $post_type_for_values ) {
	$_post_types = array_intersect( $_post_types, explode( ',', $post_type_for_values ) );
}
$_sql_post_types = '"' . implode( '","', $_post_types ) . '"';

// This variable limits the output of values in HTML (for browser performance reasons).
// Means every filter item can't show more than 250 checkboxes/radio buttons/options by default
$_values_output_limit = usb_is_preview() ? 50 : (int) apply_filters( 'us_list_filter_values_output_limit', 250 );

$global_filter_params = us_get_list_filter_params();

$output_items = '';

foreach ( $items as $i => $filter_item ) {

	if ( empty( $filter_item['source'] ) ) {
		continue;
	}

	$source_type = us_arr_path( $global_filter_params, $filter_item['source'] . '.source_type', '' );
	$source_name = us_arr_path( $global_filter_params, $filter_item['source'] . '.source_name', '' );
	$value_type = us_arr_path( $global_filter_params, $filter_item['source'] . '.value_type', '' );

	if ( $value_type == 'numeric' ) {
		$selection_type = $filter_item['numeric_selection_type'] ?? 'checkbox';

	} elseif ( $value_type == 'date' OR $value_type == 'date_time' ) {
		$selection_type = $filter_item['date_selection_type'] ?? 'date_picker';

	} else {
		$selection_type = $filter_item['selection_type'] ?? 'checkbox';
	}

	$item_title = $filter_item['label'] ?? '';

	$selector_vars = array(
		'item_name' => $filter_item['source'],
		'item_values' => array(),
		'show_amount' => $faceted_filtering ?? 0,
	);

	$item_atts = array(
		'class' => sprintf( 'w-filter-item number_%s type_%s', $i + 1, $selection_type ),
		'data-name' => $filter_item['source'],
	);

	// Show color swatches (only for product attributes)
	if ( $show_color_swatch = (bool) us_arr_path( $filter_item, 'show_color_swatch' ) ) {
		$item_atts['class'] .= ' with_color_swatch';
		if ( us_arr_path( $filter_item, 'hide_color_swatch_label' ) ) {
			$item_atts['class'] .= ' hide_color_swatch_label';
		}
	}

	// First check if the current filterable param has predefined values for selection, to prevent parse values from database
	// - ACF Checkbox
	// - ACF Radio
	// - ACF Select
	// - ACF Button Group
	if ( $choices = us_arr_path( $global_filter_params, $filter_item['source'] . '.choices' ) ) {
		foreach ( $choices as $choise_value => $choise_label ) {
			$selector_vars['item_values'][] = array(
				'label' => $choise_label,
				'value' => $choise_value,
			);
		}

		// Source: Post Type
	} elseif ( $source_type == 'post' AND $source_name == 'type' AND ! is_post_type_archive() ) {

		if ( ! empty( $filter_item['post_type'] ) ) {
			$_specified_post_types = explode( ',', $filter_item['post_type'] );
		} else {
			$_specified_post_types = array();
		}
		foreach( $_available_post_types as $post_type => $post_type_label ) {
			if ( $_specified_post_types AND ! in_array( $post_type, $_specified_post_types ) ) {
				continue;
			}
			$selector_vars['item_values'][] = array(
				'label' => $post_type_label,
				'value' => $post_type,
			);
		}

		// Source: Post Author
	} elseif ( $source_type == 'post' AND $source_name == 'author' AND ! is_author() ) {

		$author_args = array(
			'has_published_posts' => TRUE,
			'number' => $_values_output_limit,
		);

		$post_author = $filter_item['post_author'] ?? 'all';
		$post_author_ids = $filter_item['post_author_ids'] ?? '';

		if ( $post_author == 'include' ) {
			$author_args['include'] = explode( ',', $post_author_ids );
			$author_args['orderby'] = 'include';

		} elseif ( $post_author == 'exclude' ) {
			$author_args['exclude'] = explode( ',', $post_author_ids );
		}

		$author_args = apply_filters( 'us_list_filter_author_args', $author_args );

		foreach( get_users( $author_args ) as $user ) {

			// Show first and last names disregarding displayed name
			$_label = $user->first_name . ' ' . $user->last_name;

			if ( trim( $_label ) == '' ) {
				$_label = $user->display_name;
			}

			$selector_vars['item_values'][] = array(
				'label' => ucfirst( trim( $_label ) ), // remove spaces and up the first letter for correct sorting
				'value' => $user->ID,
			);
		}

		// Order alphabetically because get_users() doesn't allow to order by first/last name
		if ( $post_author != 'include' ) {
			asort( $selector_vars['item_values'] );
		}

		// Source: Taxonomy 
	} elseif ( $source_type == 'tax' ) {

		if ( ! $taxanomy_obj = get_taxonomy( $source_name ) ) {
			continue;
		}

		if ( $item_title == '' ) {
			$item_title = $taxanomy_obj->labels->singular_name;
		}

		$term_compare = $filter_item['term_compare'] ?? 'all';
		$term_ids = $filter_item['term_ids'] ?? '';
		$term_operator = $filter_item['term_operator'] ?? 'OR';

		// Apply the "AND term operator for checkboxes only
		if ( $selection_type == 'checkbox' AND $term_operator == 'AND' ) {
			$item_atts['data-value-compare'] = 'and';
		}

		$_include_term_depth = $taxanomy_obj->hierarchical;

		$terms_args = array(
			'taxonomy' => $source_name,
			'hide_empty' => TRUE,
			'number' => $_values_output_limit,
			'orderby' => 'menu_order',
			'update_term_meta_cache' => FALSE,
		);

		// Archive taxonomy page should show its child terms, if no childs - show nothing
		$current_term_id = 0;
		if (
			is_tax( $source_name )
			OR (
				is_category()
				AND $source_name == 'category'
			)
			OR (
				is_tag()
				AND $source_name == 'post_tag'
			)
		) {
			if ( $taxanomy_obj->hierarchical AND $current_term_id = get_queried_object_id() ) {
				$terms_args['child_of'] = $current_term_id;
			} else {
				continue;
			}
		}

		// Include selected terms
		if ( $term_compare == 'include' ) {
			$terms_args['include'] = explode( ',', $term_ids );
			$terms_args['orderby'] = 'include';

			// Disable depth which doesn't make sense for selected terms
			$_include_term_depth = FALSE;

			// Exclude selected terms
		} elseif ( $term_compare == 'exclude' ) {

			// Exclude child terms or not
			if ( ! empty( $filter_item['term_exclude_children'] ) AND $term_ids ) {
				$terms_args['exclude_tree'] = explode( ',', $term_ids );
			} else {
				$terms_args['exclude'] = explode( ',', $term_ids );
			}

			// All terms
		} else {
			if ( empty( $filter_item['term_show_children'] ) AND $_include_term_depth ) {
				$terms_args['parent'] = $current_term_id; // when 'parent' is set, the 'number' will be ignored
			}
		}

		$terms_args = apply_filters( 'us_list_filter_terms_args', $terms_args, $filter_item );

		$terms = get_terms( $terms_args );

		// Sort terms hierarchically
		if ( $_include_term_depth ) {
			$start_parent = ! empty( $terms_args['child_of'] )
				? $terms_args['child_of']
				: 0;
			$terms = us_sort_terms_hierarchically( $terms, $start_parent );
		}

		$term_parents = array();

		foreach( $terms as $term ) {
			$selector_vars['item_values'][] = array(
				'term_id' => $term->term_id,
				'label' => $term->name,
				'value' => urldecode( $term->slug ),
				'color_swatch' => ( $show_color_swatch AND strpos( $term->taxonomy, 'pa_' ) === 0 )
					? (string) get_term_meta( $term->term_id, 'color_swatch', TRUE )
					: NULL,
			);

			if ( $_include_term_depth ) {
				$term_parents[ $term->term_id ] = $term->parent;
			}
		}

		// Calculate depth for every term based on their hierarchy
		if ( $_include_term_depth ) {
			foreach( $selector_vars['item_values'] as &$value ) {
				$value['depth'] = $func_get_term_depth( $value['term_id'], $term_parents );
			}
		}

		// Sources with bool value
		// - ACF True/False
	} elseif ( $value_type == 'bool' ) {
		$selector_vars['item_values'][] = array(
			'label' => ! empty( $filter_item['bool_value_label'] )
				? $filter_item['bool_value_label']
				: us_arr_path( $global_filter_params, $filter_item['source'] . '.bool_value_label', '' ),

			'value' => us_arr_path( $global_filter_params, $filter_item['source'] . '.bool_value', '1' ),
		);

		// Date Sources:
		// - Post Date
		// - Post Modified
		// - ACF Date Picker
		// - ACF Date Time Picker
	} elseif ( in_array( $value_type, array( 'date', 'date_time' ) ) ) {

		if ( is_date() AND $source_name == 'date' ) {
			continue;
		}

		if ( $selection_type == 'date_picker' ) {
			$selector_vars['date_format'] = $filter_item['date_values_format'] ?? '';

			// TODO: define min and max dates from database to pass values to date picker

			$date_picker_fields = $filter_item['date_picker_fields'] ?? '';

			if ( $date_picker_fields == 'start_end' ) {
				$selector_vars['item_values'][] = array(
					'name' => 'start',
					'label' => ! empty( $filter_item['date_picker_placeholder'] )
						? $filter_item['date_picker_placeholder']
						: __( 'Start Date', 'us' ),
				);
				$selector_vars['item_values'][] = array(
					'name' => 'end',
					'label' => ! empty( $filter_item['date_picker_placeholder_2'] )
						? $filter_item['date_picker_placeholder_2']
						: __( 'End Date', 'us' ),
				);
				$item_atts['data-value-compare'] = 'between';

			} elseif ( $date_picker_fields == 'start' ) {
				$selector_vars['item_values'][] = array(
					'name' => 'start',
					'label' => ! empty( $filter_item['date_picker_placeholder'] )
						? $filter_item['date_picker_placeholder']
						: __( 'Start Date', 'us' ),
				);
				$item_atts['data-value-compare'] = 'after';

			} elseif ( $date_picker_fields == 'end' ) {
				$selector_vars['item_values'][] = array(
					'name' => 'end',
					'label' => ! empty( $filter_item['date_picker_placeholder'] )
						? $filter_item['date_picker_placeholder']
						: __( 'End Date', 'us' ),
				);
				$item_atts['data-value-compare'] = 'before';

			} else {
				$selector_vars['item_values'][] = array(
					'name' => 'exact',
					'label' => ! empty( $filter_item['date_picker_placeholder'] )
						? $filter_item['date_picker_placeholder']
						: us_translate( 'Date' ),
				);
			}

		} else {

			if ( $source_type == 'meta' ) {
				$_sql_col = 'cast( meta_value as DATE )';

			} elseif ( $source_name == 'date_modified' ) {
				$_sql_col = 'post_modified';

			} else {
				$_sql_col = 'post_date';
			}

			$date_values_range = $filter_item['date_values_range'] ?? 'yearly';

			if ( $date_values_range == 'yearly' ) {
				$item_atts['class'] .= ' range_by_year';
			}

			$_sql_select = ( $date_values_range == 'monthly' )
				? 'YEAR(' . $_sql_col . ') AS `year`, MONTH(' . $_sql_col . ') AS `month`'
				: 'YEAR(' . $_sql_col . ') AS `year`';

			$_sql_group_by = ( $date_values_range == 'monthly' )
				? 'YEAR(' . $_sql_col . '), MONTH(' . $_sql_col . ')'
				: 'YEAR(' . $_sql_col . ')';

			global $wpdb, $wp_locale;

			// Get grouped results by date values range, used wp_get_archives() as reference
			if ( $source_type == 'meta' ) {
				$_query = "
					SELECT {$_sql_select}
					FROM {$wpdb->postmeta}
					LEFT JOIN {$wpdb->posts} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
					WHERE
						meta_key = " . $wpdb->prepare( '%s', $source_name ) . "
						AND meta_value != ''
						AND {$wpdb->posts}.post_type IN ({$_sql_post_types})
						AND {$wpdb->posts}.post_status = 'publish'
					GROUP BY {$_sql_group_by }
					ORDER BY {$_sql_col} DESC
					LIMIT {$_values_output_limit}";
			} else {
				$_query = "
					SELECT {$_sql_select}
					FROM {$wpdb->posts}
					WHERE
						post_type IN ({$_sql_post_types})
						AND post_status = 'publish'
					GROUP BY {$_sql_group_by}
					ORDER BY {$_sql_col} DESC
					LIMIT {$_values_output_limit}";
			}

			foreach( $wpdb->get_results( $_query ) as $_result ) {
				$selector_vars['item_values'][] = array(
					'label' => ( $date_values_range == 'monthly' )
						? sprintf( us_translate( '%1$s %2$d' ), $wp_locale->get_month( $_result->month ), $_result->year )
						: $_result->year,

					'value' => ( $date_values_range == 'monthly' )
						? sprintf( '%s-%s', $_result->year, zeroise( $_result->month, 2 ) )
						: $_result->year,
				);
			}
		}

		// Source: Custom Field 
	} elseif ( $source_type == 'meta' ) {

		if ( $value_type == 'numeric' ) {

			// For numeric values we need to get MIN and MAX existing values from database
			global $wpdb;
			$minmax = $wpdb->get_row( "
				SELECT
					MIN( cast( meta_value as DECIMAL(10,3) ) ) AS min,
					MAX( cast( meta_value as DECIMAL(10,3) ) ) AS max
				FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
				WHERE
					meta_key = " . $wpdb->prepare( '%s', $source_name ) . "
					AND meta_value != ''
					AND {$wpdb->posts}.post_type IN ({$_sql_post_types})
					AND {$wpdb->posts}.post_status = 'publish'
				LIMIT 1;
			", ARRAY_A );

			// Generate values divided by Numeric Values Range
			if ( in_array( $selection_type, array( 'checkbox', 'radio', 'dropdown' ) ) ) {

				$_range_step = abs( (float) us_arr_path( $filter_item, 'num_values_range' ) );

				if ( $_range_step ) {
					for ( $i = 0, $count = 0; $i < $minmax['max']; $i += $_range_step ) {

						if ( $i + $_range_step < $minmax['min'] ) {
							continue;
						} else {
							$count++;
						}
						if ( $count > $_values_output_limit ) {
							break;
						}
						$selector_vars['item_values'][] = sprintf( '%s-%s', $i, $i + $_range_step );
					}
				}
			}

			// Range input/slider
			if ( in_array( $selection_type, array( 'range_input', 'range_slider' ) ) ) {

				$min_value = (string) us_arr_path( $filter_item, 'num_min_value' );
				if ( $min_value === '' ) {
					$min_value = (float) $minmax['min'];
				}

				$max_value = (string) us_arr_path( $filter_item, 'num_max_value' );
				if ( $max_value === '' ) {
					$max_value = (float) $minmax['max'];
				}

				$selector_vars['item_values'] = array(
					'min_value' => $min_value,
					'max_value' => $max_value,
					'step_size' => us_arr_path( $filter_item, 'num_step_size' ),
				);
			}

			if ( ! empty( $selector_vars['item_values'] ) ) {
				$item_atts['data-value-compare'] = 'between';
			}
		}

		// If no values get all existing values of specific custom field from database
		if ( empty( $selector_vars['item_values'] ) ) {

			global $wpdb;
			$meta_values = $wpdb->get_col( "
				SELECT DISTINCT meta_value
				FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
				WHERE
					meta_key = " . $wpdb->prepare( '%s', $source_name ) . "
					AND meta_value != ''
					AND {$wpdb->posts}.post_type IN ({$_sql_post_types})
					AND {$wpdb->posts}.post_status = 'publish'
				LIMIT {$_values_output_limit}
			" );

			$meta_values = array_map( 'trim', $meta_values );

			natsort( $meta_values );

			$selector_vars['item_values'] = $meta_values;
		}
	}

	if ( in_array( $selection_type, array( 'radio', 'dropdown' ) ) AND ! empty( $filter_item['first_value_label'] ) ) {

		// Add the "placeholder" value to the beginning of values array
		array_unshift( $selector_vars['item_values'],
			array(
				'id' => 0,
				'label' => $filter_item['first_value_label'],
				'value' => '*',
			)
		);
	}

	if ( empty( $selector_vars['item_values'] ) ) {
		continue;
	}

	if (
		! empty( $filter_item['has_search'] )
		AND ( $selection_type == 'radio' OR $selection_type == 'checkbox' )
	) {
		$selector_vars['has_search'] = TRUE;
		$item_atts['class'] .= ' has_search_field';
	}

	// Use the param label, if title is empty
	if ( $item_title == '' ) {
		$item_title = us_arr_path( $global_filter_params, $filter_item['source'] . '.label', '' );
	}

	$selector_vars['item_title'] = $item_title;

	// Output single item
	$item_title_atts = array(
		'class' => 'w-filter-item-title' . $_dropdown_field_class,
		'type' => 'button',
	);
	if ( $item_layout == 'default' ) {
		$item_title_atts['tabindex'] = '-1';
	}
	$output_items .= '<div' . us_implode_atts( $item_atts ) . '>';
	$output_items .= '<button' . us_implode_atts( $item_title_atts ) . '>';
	$output_items .= strip_tags( $item_title );

	// When filter has togglable appearance, the "Reset" link shoud be inside the item title
	if ( $item_layout == 'toggle' ) {
		$output_items .= ' <span class="w-filter-item-reset" role="button">' . strip_tags( __( 'Reset', 'us' ) ) . '</span>';

		// Use empty span for the "Dropdown" layout to indicate selected values
	} elseif ( $item_layout == 'dropdown' ) {
		$output_items .= '<span></span>';
	}

	$output_items .= '</button>'; // w-filter-item-title

	if ( $item_layout != 'toggle' ) {
		$output_items .= '<a class="w-filter-item-reset' . $_dropdown_field_class . '" href="#" title="' . esc_attr( __( 'Reset', 'us' ) ) . '">';
		$output_items .= '<span>' . strip_tags( __( 'Reset', 'us' ) ) . '</span>';
		$output_items .= '</a>';
	}

	$output_items .= '<div class="w-filter-item-content">';

	if ( ! empty( $selector_vars['has_search'] ) ) {
		$item_search_atts = array(
			'name' => 'search_values',
			'type' => 'text',
			'autocomplete' => 'off',
			'placeholder' => $filter_item['search_placeholder'] ?? '',
		);
		$output_items .= '<div class="w-filter-item-search">';
		$output_items .= '<input' . us_implode_atts( $item_search_atts ) . '>';
		$output_items .= '<i class="fas fa-search"></i>';
		$output_items .= '</div>'; // w-filter-item-search
	}

	$item_values_atts = array(
		'class' => 'w-filter-item-values',
	);
	if ( ! empty( $values_max_height ) ) {
		$item_values_atts['style'] = 'max-height:' . $values_max_height;
	}
	$output_items .= '<div' . us_implode_atts( $item_values_atts ) . '>';

	$output_items .= us_get_template( 'templates/us_grid/filter-ui-types/' . $selection_type, $selector_vars );

	if ( count( $selector_vars['item_values'] ) >= $_values_output_limit ) {
		$output_items .= '<small>' . sprintf( __( 'Only the first %s values are displayed.', 'us' ), $_values_output_limit ) . '</small>';
	}

	$output_items .= '</div>'; // w-filter-item-values

	if ( ! empty( $selector_vars['has_search'] ) ) {
		$output_items .= '<div class="w-filter-item-message hidden">' .  us_translate( 'No results found.' ) . '</div>';
	}

	$output_items .= '</div>'; // w-filter-item-content
	$output_items .= '</div>'; // w-filter-item
}

$output .= $output_items;

$output .= '</div>'; // w-filter-list

// Mobiles related button and styles
if ( ! empty( $mobile_width ) AND $output_items !== '' ) {
	$output .= '<div class="w-filter-list-panel">';
	$output .= '<button class="w-btn w-filter-button-submit ' . us_get_btn_class() . '" type="button">';
	$output .= '<span class="w-btn-label">' . strip_tags( us_translate( 'Apply' ) ) . '</span>';
	$output .= '</button>';
	$output .= '</div>';

	$mobile_button_atts = array(
		'class' => 'w-filter-opener',
		'type' => 'button',
	);

	// Make link as Button if set
	if ( ! empty( $mobile_button_style ) ) {
		$mobile_button_atts['class'] .= ' w-btn ' . us_get_btn_class( $mobile_button_style );
	}

	$mobile_button_icon_html = '';
	if ( ! empty( $mobile_button_icon ) ) {
		$mobile_button_icon_html = us_prepare_icon_tag( $mobile_button_icon );
		$mobile_button_atts['class'] .= ' icon_at' . $mobile_button_iconpos;

		if ( is_rtl() ) {
			$mobile_button_iconpos = ( $mobile_button_iconpos == 'left' ) ? 'right' : 'left';
		}
	}

	// Force aria-label when label is empty
	if ( empty( $mobile_button_label ) ) {
		$mobile_button_atts['class'] .= ' text_none';
		$mobile_button_atts['aria-label'] = __( 'Filters', 'us' );
	}

	$_inline_style = '@media( max-width:' . (int) $mobile_width . 'px ) {';
	$_inline_style .= '.w-filter.state_desktop .w-filter-list,';
	$_inline_style .= '.w-filter-item-title > span { display: none !important; }';
	$_inline_style .= '.w-filter-opener { display: inline-block; }';
	$_inline_style .= '}';

	$output .= '<style>' . us_minify_css( $_inline_style ) . '</style>';
	$output .= '<button' . us_implode_atts( $mobile_button_atts ) . '>';
	if ( $mobile_button_iconpos == 'left' ) {
		$output .= $mobile_button_icon_html;
	}
	$output .= '<span>' . strip_tags( $mobile_button_label ) . '</span>';
	if ( $mobile_button_iconpos == 'right' ) {
		$output .= $mobile_button_icon_html;
	}
	$output .= '</button>'; // w-filter-opener
}

$output .= '</form>'; // w-filter

echo $output;
