<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Advanced Custom Fields
 *
 * @link https://www.advancedcustomfields.com/
 *
 * TODO: Globally replace the architecture of storing and working fields,
 * use an identifier instead of a name, since now there is a problem if fields in
 * different groups have the same name does not work correctly.
 */

if ( ! class_exists( 'ACF' ) ) {
	return;
}

// Register Google Maps API key
// https://www.advancedcustomfields.com/resources/google-map/
if ( ! function_exists( 'us_acf_google_map_api' ) ) {
	function us_acf_google_map_api( $api ) {
		// Get the Google Maps API key from the Theme Options
		$gmaps_api_key = trim( esc_attr( us_get_option('gmaps_api_key', '') ) );
		/*
		 * Set the API key for ACF only if it is not empty,
		 * to prevent possible erase of the same value set in other plugins
		 */
		if ( ! empty( $gmaps_api_key ) ) {
			$api['key'] = $gmaps_api_key;
		}

		return $api;
	}

	add_filter( 'acf/fields/google_map/api', 'us_acf_google_map_api' );
}

// Removing custom plugin messages for ACF Pro
if ( ! function_exists( 'us_acf_pro_remove_update_message' ) ) {
	function us_acf_pro_remove_update_message() {
		if ( class_exists( 'ACF_Updates' ) ) {
			$class = new ACF_Updates();
			remove_filter( 'pre_set_site_transient_update_plugins', array( $class, 'modify_plugins_transient' ), 15 );
		}

		// Remove additional messages for buying license
		if (
			function_exists( 'acf_get_setting' )
			AND $acf_basename = acf_get_setting( 'basename' )
		) {
			remove_all_actions( 'in_plugin_update_message-' . $acf_basename );
		}
	}

	add_action( 'init', 'us_acf_pro_remove_update_message', 30 );
}

if ( ! function_exists( 'us_acf_get_fields' ) ) {
	/**
	 * Get ACF fields grouped by their group name
	 *
	 * @param string|array $types The field types to get
	 * @param bool $to_list If true, the result will be [ key => value ]
	 * @param bool $include_options_page If false, the result will not include fields assigned to any Options page
	 * @param string $options_page_prefix The prefix used to differentiate fields assigned to any Options page, can be 'option|' or 'option/'
	 * @return array Returns a list of fields
	 */
	function us_acf_get_fields( $types = array(), $to_list = FALSE, $include_options_page = TRUE, $options_page_prefix = 'option|' ) {

		if ( ! is_array( $types ) AND ! empty( $types ) ) {
			$types = array( $types );
		}
		$result = array();

		foreach ( (array) acf_get_field_groups() as $group ) {

			/**
			 * If the group is used in ACF Options page, use the predefined prefix or abort
			 * @link https://www.advancedcustomfields.com/resources/get-values-from-an-options-page/
			 */
			$field_name_prefix = '';
			if ( is_array( $group['location'] ) ) {
				foreach ( $group['location'] as $location_or ) {
					foreach ( $location_or as $location_and ) {
						if ( $location_and['param'] === 'options_page' AND $location_and['operator'] === '==' ) {
							if ( $include_options_page ) {
								$field_name_prefix = $options_page_prefix;
								break 2;
							} else {
								continue 3;
							}
						}
					}
				}
			}

			// Get all the fields of the group and generating the result.
			$fields = array();
			foreach( (array) acf_get_fields( $group['ID'] ) as $field ) {

				if ( $types AND ! in_array( $field['type'], array_merge( $types, array( 'group' ) ) ) ) {
					continue;
				}

				$field['name'] = $field_name_prefix . $field['name'];

				if ( $to_list ) {

					// Get sub fields from "Group" type
					if ( $field['type'] == 'group' ) {
						foreach( $field['sub_fields'] as $sub_field ) {
							if ( $types AND ! in_array( $sub_field['type'], $types ) ) {
								continue;
							}
							$fields[ $field['name'] . '_' . $sub_field['name'] ] = $field['label'] . ': ' . $sub_field['label'];
						}

					} else {
						$fields[ $field['name'] ] = $field['label'];
					}

				} else {
					$fields[] = $field;
				}
			}

			if ( count( $fields ) ) {
				// This is the full name of the group that can be used for output in dropdowns or other controls
				$result[ $group['ID'] ] = array( '__group_label__' => $group['title'] );
				$result[ $group['ID'] ] += $fields;
			}
		}
		return $result;
	}
}

if ( ! function_exists( 'us_acf_get_custom_field' ) ) {
	add_filter( 'us_get_custom_field', 'us_acf_get_custom_field', 2, 4 );
	/**
	 * Filters a custom field value to apply the ACF return format
	 *
	 * @param mixed $value The meta value
	 * @param string $name The field name
	 * @param int|string $current_id The current id
	 * @param string|null $meta_type The meta type
	 * @return mixed Returns a value given specific fields
	 */
	function us_acf_get_custom_field( $value, $name, $current_id, $meta_type = NULL ) {

		// Built-in fields where the name starts with us_ are returned unchanged
		if ( strpos( $name, 'us_' ) === 0 ) {
			return $value;
		}

		// Use the meta slug as prefix in the current id for ACF functions
		// https://www.advancedcustomfields.com/resources/get_field/#get-a-value-from-different-objects
		if (
			$meta_type == 'term'
			AND $term = get_term( $current_id )
			AND $term instanceof WP_Term
		) {
			$current_id = $term->taxonomy . '_' . $current_id;
		}
		if ( $meta_type == 'user' ) {
			$current_id = 'user_' . $current_id;
		}

		// Get field object
		// https://www.advancedcustomfields.com/resources/get_field_object/
		$field = get_field_object( $name, $current_id );

		// In case the field is not exist in ACF return the initial value
		// This allows getting values for non-ACF custom fields correctly (_wp_attachment_image_alt, etc.)
		if ( $field === FALSE ) {
			return $value;
		}

		// Return value from an field object
		return us_arr_path( $field, 'value', /* default */$value );
	}
}

if ( ! function_exists( 'us_acf_link_dynamic_values' ) ) {
	add_filter( 'us_link_dynamic_values', 'us_acf_link_dynamic_values' );
	/**
	 * Append ACF predefined field types to link dynamic values
	 *
	 * @param array $dynamic_values Groups of dynamic values
	 * @return array Returns an expanded array of variables
	 */
	function us_acf_link_dynamic_values( $dynamic_values ) {

		// Skip adding values if it's not edit mode
		if ( ! us_is_elm_editing_page() OR empty( $dynamic_values['acf_types'] ) ) {
			return $dynamic_values;
		}

		$acf_dynamic_values = array();

		foreach( us_acf_get_fields( $dynamic_values['acf_types'], TRUE, TRUE, 'option/' ) as $fields ) {
			$group_label = (string) us_arr_path( $fields, '__group_label__' );
			foreach ( $fields as $field_key => $field_name ) {
				if ( $field_key == '__group_label__' ) {
					continue;
				}
				$acf_dynamic_values[ $group_label ][ 'custom_field|' . $field_key ] = $field_name;
			}
		}

		return array_merge( $dynamic_values, $acf_dynamic_values );
	}
}

if ( ! function_exists( 'us_acf_dynamic_values' ) ) {
	add_filter( 'us_text_dynamic_values', 'us_acf_dynamic_values' );
	add_filter( 'us_image_dynamic_values', 'us_acf_dynamic_values' );
	/**
	 * Append ACF predefined field types to text dynamic values
	 *
	 * @param array $dynamic_values Groups of dynamic values
	 * @return array Returns an expanded array of variables
	 */
	function us_acf_dynamic_values( $dynamic_values ) {

		// Skip adding values if it's not edit mode
		if ( ! us_is_elm_editing_page() OR empty( $dynamic_values['acf_types'] ) ) {
			return $dynamic_values;
		}

		$acf_dynamic_values = array();

		foreach( us_acf_get_fields( $dynamic_values['acf_types'], TRUE, TRUE, 'option/' ) as $fields ) {
			$group_label = (string) us_arr_path( $fields, '__group_label__' );
			foreach ( $fields as $field_key => $field_name ) {
				if ( $field_key == '__group_label__' ) {
					continue;
				}
				$acf_dynamic_values[ $group_label ][ '{{' . $field_key . '}}' ] = $field_name;
			}
		}

		return array_merge( $dynamic_values, $acf_dynamic_values );

	}
}

if ( ! function_exists( 'us_acf_color_dynamic_values' ) ) {
	add_filter( 'usof_get_color_vars', 'us_acf_color_dynamic_values', 1, 1 );
	/**
	 * Append ACF predefined field types to color dynamic values
	 *
	 * @param array $result The dynamic colors.
	 * @return array Returns an array of dynamic colors.
	 */
	function us_acf_color_dynamic_values( $result ) {

		foreach( us_acf_get_fields( 'color_picker', TRUE, TRUE, 'option/' ) as $fields ) {
			$group_label = (string) us_arr_path( $fields, '__group_label__' );
			foreach ( $fields as $field_key => $field_name ) {
				if ( $field_key == '__group_label__' ) {
					continue;
				}
				$result[ $group_label ][] = array(
					'name' => sprintf( '{{%s}}', $field_key ),
					'title' => $field_name,
					'type' => 'cf_colors',
				);
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'us_add_acf_orderby_params' ) ) {
	add_filter( 'us_get_list_orderby_params', 'us_add_acf_orderby_params', 101 );
	/**
	 * Append ACF predefined field types to List Orderby options
	 */
	function us_add_acf_orderby_params( $params ) {

		$supported_types = array(
			'text',
			'number',
			'range',
			'select',
			'radio',
			'button_group',
			'date_picker',
			'date_time_picker',
		);

		foreach( us_acf_get_fields( $supported_types, FALSE, FALSE ) as $fields ) {
			$group_label = (string) us_arr_path( $fields, '__group_label__' );

			foreach ( $fields as $field_key => $field ) {
				if ( ! is_array( $field ) ) {
					continue;
				}

				// If field name is already used in previous params (e.g. 'date', 'title', 'menu_order'), append the count suffix to make it unique
				$unique_name = in_array( $field['name'], array_keys( $params ) )
					? $field['name'] . count( $params )
					: $field['name'];
				$unique_name = sanitize_title( $unique_name );

				$params[ $unique_name ] = array(
					'label' => $field['label'],
					'group' => $group_label,
					'orderby_param' => 'meta_value',
					'meta_key' => $field['name'],
				);

				// ACF types for numeric values
				if ( in_array( $field['type'], array( 'number', 'range' ) ) ) {
					$params[ $unique_name ]['orderby_param'] = 'meta_value_num';
				}
			}
		}

		return $params;
	}
}

if ( ! function_exists( 'us_add_acf_filter_params' ) ) {
	add_filter( 'us_get_list_filter_params', 'us_add_acf_filter_params' );
	/**
	 * Append ACF predefined field types to List Filter
	 */
	function us_add_acf_filter_params( $params ) {

		$acf_relevant_fields = array();

		$supported_types = array(
			'text',
			'number',
			'range',
			'select',
			'checkbox',
			'radio',
			'button_group',
			'true_false',
			'date_picker',
			'date_time_picker',
		);

		foreach ( (array) acf_get_field_groups() as $group ) {

			// Skip groups that used for Options page
			if ( is_array( $group['location'] ) ) {
				foreach ( $group['location'] as $location_or ) {
					foreach ( $location_or as $location_and ) {
						if ( $location_and['param'] === 'options_page' AND $location_and['operator'] === '==' ) {
							continue 3;
						}
					}
				}
			}

			foreach ( (array) acf_get_fields( $group['ID'] ) as $field ) {
				if ( ! in_array( $field['type'], array_merge( $supported_types, array( 'group' ) ) ) ) {
					continue;
				}

				// Get sub fields from "Group" type
				if ( $field['type'] == 'group' ) {
					foreach( $field['sub_fields'] as $sub_field ) {
						if ( ! in_array( $sub_field['type'], $supported_types ) ) {
							continue;
						}
						$acf_relevant_fields[ $field['name'] . '_' . $sub_field['name'] ] = array(
							'group_title' => $group['title'],
							'label' => $field['label'] . ': ' . $sub_field['label'],
							'type' => $sub_field['type'],
							'choices' => $sub_field['choices'] ?? NULL,
							'message' => $sub_field['message'] ?? NULL,
							'multiple' => $sub_field['multiple'] ?? NULL,
						);
					}

				} else {
					$acf_relevant_fields[ $field['name'] ] =  array(
						'group_title' => $group['title'],
						'label' => $field['label'],
						'type' => $field['type'],
						'choices' => $field['choices'] ?? NULL,
						'message' => $field['message'] ?? NULL,
						'multiple' => $field['multiple'] ?? NULL,
					);
				}
			}
		}

		foreach ( $acf_relevant_fields as $name => $field ) {

			// If field name is already used in previous params (e.g. 'category', 'price', 'featured'), append the count suffix to make it unique
			$unique_name = in_array( $name, array_merge( array_keys( $params ), array( 'orderby', 's' ) ) )
				? $name . count( $params )
				: $name;

			$params[ $unique_name ] = array(
				'label' => $field['label'],
				'group' => $field['group_title'],
				'source_type' => 'meta',
				'source_name' => $name,
			);

			// ACF types with predefined choices (used to prevent parsing values from database)
			if ( in_array( $field['type'], array( 'button_group', 'checkbox', 'radio', 'select' ) ) ) {
				$params[ $unique_name ]['choices'] = $field['choices'];
			}

			// ACF types for numeric values
			if ( in_array( $field['type'], array( 'number', 'range' ) ) ) {
				$params[ $unique_name ]['value_type'] = 'numeric';
			}

			// ACF types for bool values
			if ( $field['type'] == 'true_false' ) {
				$params[ $unique_name ]['value_type'] = 'bool';
				$params[ $unique_name ]['bool_value_label'] = $field['message'];
			}

			// ACF types for date values
			if ( $field['type'] == 'date_picker' ) {
				$params[ $unique_name ]['value_type'] = 'date';
			}

			// ACF types for date_time values
			if ( $field['type'] == 'date_time_picker' ) {
				$params[ $unique_name ]['value_type'] = 'date_time';
			}

			// ACF Checkbox keeps its value as serialize array, that's why only 'LIKE' meta compare is possible
			if ( $field['type'] == 'checkbox' ) {
				$params[ $unique_name ]['value_compare'] = 'like';
			}

			// ACF Select keeps its value as serialize array, if multiple is enabled, that's why only 'LIKE' meta compare is possible
			if ( $field['type'] == 'select' AND $field['multiple'] == '1' ) {
				$params[ $unique_name ]['value_compare'] = 'like';
			}
		}

		return $params;
	}
}

if ( ! function_exists( 'us_acf_post_list_element_config' ) ) {
	add_filter( 'us_config_elements/post_list', 'us_acf_post_list_element_config', 501, 1 );
	/**
	 * Extends the configuration of the "Post List" element to output posts from ACF.
	 *
	 * @param array $config The configuration.
	 * @return array Returns the extended configuration.
	 */
	function us_acf_post_list_element_config( $config ) {
		if (
			! isset( $config['params'], $config['params']['source'] )
			OR ! is_array( $config['params']['source']['options'] )
		) {
			return $config;
		}

		// Get a list of all fields of type "Post Object" and "Relationship"
		$field_list = array(
			'' => '– ' . us_translate( 'None' ) . ' –',
		);
		foreach ( us_acf_get_fields( array( 'post_object', 'relationship' ), TRUE ) as $field ) {
			$group_label = (string) us_arr_path( $field, '__group_label__' );
			foreach ( $field as $field_key => $field_name ) {
				if ( $field_key !== '__group_label__' ) {
					$field_list[ $field_key ] = $group_label . ': ' . $field_name;
				}
			}
		}
		$config['params']['source']['options']['custom_field_posts'] = __( 'Posts from ACF custom field', 'us' );
		$config['params'] = us_array_merge_insert(
			$config['params'],
			array(
				'custom_field_name' => array(
					'type' => 'select',
					'options' => $field_list,
					'std' => '',
					'classes' => 'for_above',
					'show_if' => array( 'source', '=', 'custom_field_posts' ),
					'usb_preview' => TRUE,
				)
			),
			'after',
			'source'
		);
		foreach ( $config['params'] as &$param  ) {
			if ( isset( $param['weight'] ) ) {
				unset( $param['weight'] );
			}
		}
		unset( $param );
		$config['params'] = us_set_params_weight( $config['params'] );
		return $config;
	}
}

if ( ! function_exists( 'us_acf_posts_from_custom_field' ) ) {

	add_filter( 'us_post_list_query_args', 'us_acf_posts_from_custom_field', 501, 2 );
	add_filter( 'us_post_list_query_args_unfiltered', 'us_acf_posts_from_custom_field', 501, 2 );

	/**
	 * Modify the post list query to return posts from ACF custom field.
	 *
	 * @param array $query_args The query arguments.
	 * @param array $filled_atts The filled atts.
	 * @return array Returns array of arguments passed to WP_Query.
	 */
	function us_acf_posts_from_custom_field( $query_args, $filled_atts ) {
		if (
			us_arr_path( $filled_atts, 'source' ) === 'custom_field_posts'
			AND $field_name = us_arr_path( $filled_atts, 'custom_field_name' )
			AND ! usb_is_template_preview()
		) {
			if ( wp_doing_ajax() ) {
				$object_id = (int) us_arr_path( $_POST, 'object_id' );
				$meta_type = (string) us_arr_path( $_POST, 'meta_type' );

				// Validate string
				if ( ! in_array( $meta_type, array( 'post', 'term', 'user' ) ) ) {
					$meta_type = 'post';
				}

			} else {
				$object_id = us_get_current_id();
				$meta_type = us_get_current_meta_type();
			}

			if ( ! $post_ids = us_get_custom_field( $field_name, /*acf_format*/FALSE, $object_id, $meta_type ) ) {
				$post_ids = array( 0 ); // Use the non-existing id to get no results, because empty 'post__in' is ignored by query
			}
			$query_args['post__in'] = (array) $post_ids;
		}
		return $query_args;
	}
}
