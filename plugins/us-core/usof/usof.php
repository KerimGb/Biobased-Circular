<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

require US_CORE_DIR . 'usof/functions/fallback.php';

if ( is_admin() ) {
	if ( ! wp_doing_ajax() ) {
		// Front-end interface
		require US_CORE_DIR . 'usof/functions/interface.php';
		require US_CORE_DIR . 'usof/functions/meta-box.php';
		require US_CORE_DIR . 'usof/functions/menu-dropdown.php';
	} elseif (
		(
			isset( $_POST['action'] )
			AND substr( $_POST['action'], 0, 5 ) == 'usof_'
		)
		OR (
			isset( $_GET['action'] )
			AND substr( $_GET['action'], 0, 5 ) == 'usof_'
		)
	) {
		// Ajax methods
		require US_CORE_DIR . 'usof/functions/ajax.php';
		require US_CORE_DIR . 'usof/functions/ajax-menu-dropdown.php';
	}
}

/**
 * Get theme option or return default value
 *
 * @param string $name
 * @param mixed $default_value
 * @return mixed
 */
function usof_get_option( $name, $default_value = NULL ) {
	global $usof_options;
	usof_load_options_once();

	if ( is_null( $default_value ) ) {
		$default_value = usof_defaults( $name );
	}

	$value = $default_value;
	if ( isset( $usof_options[ $name ] ) ) {
		$value = $usof_options[ $name ];
	}

	return apply_filters( 'usof_get_option_' . $name, $value );
}

/**
 * Get default value for a certain USOF field
 *
 * @param array $field
 * @return string
 */
function usof_get_default( &$field ) {

	if ( ! is_array( $field ) OR ! isset( $field['type'] ) ) {
		return '';
	}

	$no_values_types = array(
		'backup',
		'heading',
		'message',
		'transfer',
		'wrapper_start',
		'wrapper_end',
	);

	$selectable_types = array(
		'imgradio',
		'radio',
		'select',
		'style_scheme',
	);

	// Get current field type
	$field_type = $field['type'];

	if ( in_array( $field_type, $no_values_types ) ) {
		return '';
	}

	// Using first value as standard for selectable types
	if ( ! isset( $field['std'] ) AND in_array( $field_type, $selectable_types ) ) {
		if ( ! empty( $field['options'] ) AND is_array( $field['options'] ) ) {
			$field['std'] = key( $field['options'] );
			reset( $field['options'] );
		}
	}

	// Get default values for typography options
	if ( $field_type == 'typography_options' ) {
		if ( isset( $field['fields'] ) AND is_array( $field['fields'] ) ) {
			foreach ( $field['fields'] as $name => $item_field ) {
				if ( isset( $item_field['std'] ) ) {
					$field['std'][ $name ] = $item_field['std'];
				}
			}
		}
	}

	if ( ! isset( $field['std'] ) ) {
		$field['std'] = '';
	}
	return $field['std'];
}

/**
 * Get default values
 *
 * @param string $key If set, retreive only one default value
 * @return mixed Array of values or a single value if the $key is specified
 */
function usof_defaults( $key = NULL ) {
	$config = (array) us_config( 'theme-options' );

	$values = array();
	foreach ( $config as &$section ) {
		if ( ! isset( $section['fields'] ) ) {
			continue;
		}
		foreach ( $section['fields'] as $field_id => &$field ) {
			if ( ! is_null( $key ) AND $field_id != $key ) {
				continue;
			}
			if ( isset( $values[ $field_id ] ) ) {
				continue;
			}

			if ( isset( $field['type'] ) AND $field['type'] == 'style_scheme' ) {
				$options = array_keys( us_config( 'color-schemes' ) );
				if ( empty( $options ) ) {
					continue;
				}
				if ( ! isset( $field['std'] ) ) {
					$field['std'] = $options[ 0 ];
				}
				// If theme has default style scheme, it's values will be used as standard as well
				$values = array_merge( $values, us_config( 'color-schemes.' . $field['std'] . '.values' ) );
			}

			$default_value = usof_get_default( $field );
			if ( ! is_null( $default_value ) ) {
				$values[ $field_id ] = $default_value;
			}
		}
	}

	if ( ! is_null( $key ) ) {
		return isset( $values[ $key ] )
			? $values[ $key ]
			: ''; // default
	}

	return $values;
}

/**
 * If the options were not loaded, load them
 */
function usof_load_options_once( $force_reload = FALSE ) {
	global $usof_options;

	if ( isset( $usof_options ) AND ! $force_reload ) {
		return;
	}
	if ( ! defined( 'US_THEMENAME' ) ) {
		return;
	}
	$usof_options = get_option( 'usof_options_' . US_THEMENAME );
	if ( $usof_options === FALSE ) {
		// Trying to fetch the old good SMOF options
		$usof_options = get_option( US_THEMENAME . '_options' );
		if ( $usof_options !== FALSE ) {
			// Disabling the old options autoload
			update_option( US_THEMENAME . '_options', $usof_options, FALSE );
		} else {
			// Not defined yet, using default values
			$usof_options = usof_defaults();
		}

		update_option( 'usof_options_' . US_THEMENAME, $usof_options, TRUE );
	}

	$usof_options = apply_filters( 'usof_load_options_once', $usof_options );
}

/**
 * Save current usof options values from global $usof_options variable to database
 *
 * @param array $updated_options Array of the new options values
 */
function usof_save_options( $updated_options ) {

	if ( ! is_array( $updated_options ) OR empty( $updated_options ) ) {
		return;
	}

	global $usof_options;
	usof_load_options_once();

	do_action( 'usof_before_save', $updated_options );

	$usof_options = has_filter( 'usof_updated_options' )
		? apply_filters( 'usof_updated_options', $updated_options )
		: $updated_options;

	update_option( 'usof_options_' . US_THEMENAME, $usof_options, TRUE );

	do_action( 'usof_after_save', $updated_options );
}

/**
 * Save a backup with current usof options values
 */
if ( ! function_exists( 'usof_backup' ) ) {
	function usof_backup() {
		global $usof_options;
		usof_load_options_once();

		$backup = array(
			'time' => current_time( 'mysql', TRUE ),
			'usof_options' => $usof_options,
		);

		update_option( 'usof_backup_' . US_THEMENAME, $backup, FALSE );

	}
}

/**
 * Checks if the showing condition is true
 *
 * Note: at any possible syntax error we choose to show the field so it will be functional anyway.
 *
 * @param array $condition Showing condition
 * @param array $values Current values
 *
 * @return bool
 */
function usof_execute_show_if( $condition, &$values = NULL ) {
	if ( ! is_array( $condition ) OR count( $condition ) < 3 ) {
		// Wrong condition
		$result = TRUE;
	} elseif ( in_array( strtolower( $condition[1] ), array( 'and', 'or' ) ) ) {
		// Complex or / and statement
		$result = usof_execute_show_if( $condition[0], $values );
		$index = 2;
		while ( isset( $condition[ $index ] ) ) {
			$condition[ $index - 1 ] = strtolower( $condition[ $index - 1 ] );
			if ( $condition[ $index - 1 ] == 'and' ) {
				$result = ( $result AND usof_execute_show_if( $condition[ $index ], $values ) );
			} elseif ( $condition[ $index - 1 ] == 'or' ) {
				$result = ( $result OR usof_execute_show_if( $condition[ $index ], $values ) );
			}
			$index = $index + 2;
		}
	} else {
		if ( ! isset( $values[ $condition[0] ] ) ) {
			if ( $condition[1] == '=' AND ( ! in_array( $condition[2], array( 0, '', FALSE, NULL ) ) ) ) {
				return FALSE;
			} elseif ( $condition[1] == '!=' AND in_array( $condition[2], array( 0, '', FALSE, NULL ) ) ) {
				return FALSE;
			} else {
				return TRUE;
			}
		}
		$value = $values[ $condition[0] ];
		if ( $condition[1] == '=' ) {
			if ( is_array( $condition[2] ) ) {
				$result = ( in_array( $value, $condition[2] ) );
			} else {
				$result = ( $value == $condition[2] );
			}
		} elseif ( $condition[1] == '!=' ) {
			if ( is_array( $condition[2] ) ) {
				$result = ( ! in_array( $value, $condition[2] ) );
			} else {
				$result = ( $value != $condition[2] );
			}
		} elseif ( $condition[1] == '<=' ) {
			$result = ( $value <= $condition[2] );
		} elseif ( $condition[1] == '<' ) {
			$result = ( $value < $condition[2] );
		} elseif ( $condition[1] == '>' ) {
			$result = ( $value > $condition[2] );
		} elseif ( $condition[1] == '>=' ) {
			$result = ( $value >= $condition[2] );
		} elseif ( $condition[1] == 'str_contains' ) {
			$result = strpos( (string) $value, $condition[2] ) !== FALSE;
		} else {
			$result = TRUE;
		}
	}

	return $result;
}

if ( ! function_exists( 'usof_get_color_vars' ) ) {
	/**
	 * Get a list of colors variables for $usof.field['color']
	 *
	 * @param bool $include_dynamic_colors The add custom variables to result [optional].
	 * @return array Returns an array of dynamic colors.
	 */
	function usof_get_color_vars() {
		$result = array();
		$group_name = NULL;

		// Custom Global Colors first
		foreach( us_get_custom_global_colors() as $custom_color ) {
			$result[ __( 'Custom Global Colors', 'us' ) ][] = array(
				'name' => $custom_color['slug'],
				'title' => $custom_color['name'],
				'value' => $custom_color['color'],
				'type' => 'global_colors',
			);
		}

		// Predefined scheme colors from Theme Options > Colors
		foreach ( (array) us_config( 'theme-options.colors.fields' ) as $field_name => $field ) {
			$type = (string) us_arr_path( $field, 'type' );

			// Group Search
			if ( $type === 'heading' AND ! empty( $field['title'] ) ) {
				$group_name = $field['title'];
			}

			// Skip all types except color
			if ( $type !== 'color' ) {
				continue;
			}

			// Remove "color" prefix for better UI
			if ( strpos( $field_name, 'color' ) === 0 ) {
				$field_name = substr( $field_name, strlen( 'color' ) );
			}

			// Settings for the current color option
			$option = array(
				'name' => $field_name,
				'title' => (string) us_arr_path( $field, 'text' ),
				'value' => us_get_color( $field_name, /* gradient */ TRUE, /* cssvar */ FALSE ),
				'type' => 'scheme_colors',
			);

			// Remove empty color values
			if ( empty( $option['value'] ) ) {
				continue;
			}

			if ( ! is_null( $group_name ) ) {
				$result[ $group_name ][] = $option;
			} else {
				$result[] = $option;
			}
		}

		// Include custom field colors
		foreach ( (array) us_config( 'meta-boxes', array() ) as $metabox_config ) {
			if ( $metabox_config['title'] !== __( 'Additional Settings', 'us' ) ) {
				continue;
			}
			foreach ( $metabox_config['fields'] as $field_name => $field ) {
				if ( $field['type'] !== 'color' ) {
					continue;
				}
				$result[ $metabox_config['title'] ][] = array(
					'name' => sprintf( '{{%s}}', $field_name ),
					'title' => us_arr_path( $field, 'title', '' ),
					'type' => 'cf_colors',
				);
			}
		}

		return apply_filters( 'usof_get_color_vars', $result );
	}
}

if ( ! function_exists( 'usof_output_global_colors' ) ) {
	/**
	 * Output the list of dynamic colors once globally.
	 *
	 * @param string $handle [required] Name of the script to add the inline script to.
	 */
	function usof_output_global_colors( $handle ) {
		$color_list = (array) usof_get_color_vars();

		// If Additional Settings is disabled, remove us_tile colors from dynamic values
		if ( ! us_get_option( 'enable_additional_settings', 1 ) ) {
			unset( $color_list[ __( 'Additional Settings', 'us' ) ] );
		}

		$color_vars = array();

		foreach( $color_list as $colors ) {
			foreach ( $colors as $color ) {
				if ( ! isset( $color['value'] ) ) {
					$color['value'] = us_get_color( $color['name'], TRUE, TRUE );
				}
				$color_vars[ $color['name'] ] = $color['value'];
			}
		}

		// Global data
		$js_script = '
			window.usofGlobalData = window.usofGlobalData || {};
			window.usofGlobalData.colorList = ' . json_encode( $color_list, JSON_HEX_APOS ) . ';
			window.usofGlobalData.colorVars = ' . json_encode( $color_vars, JSON_HEX_APOS ) . ';
		';
		wp_add_inline_script( $handle, $js_script, 'before' );
	}
}

if ( ! function_exists( 'usof_extract_tinymce_options' ) ) {
	/**
	 * Extracting mceInit settings for editor by ID
	 * Note: The current method is called for the editor field in the context of the header.
	 *
	 * @param string $id The editor ID
	 * @param array $set The settings
	 * @return string $mceInit
	 */
	function usof_extract_tinymce_options( $id, $set ) {
		if ( ! is_array( $set ) ) {
			$set = array();
		}
		$mceInit = array();
		/**
		 * Filter function to extract data
		 *
		 * @param array $mceInit The mce init settings
		 * @param string $editor_id The editor ID
		 * @return array
		 */
		$func_tiny_mce_before_init = function ( $_mceInit, $editor_id ) use( $id, &$mceInit ) {
			if ( $id === $editor_id ) {
				$mceInit = (array) $_mceInit;
			}
			return $mceInit;
		};

		// Add a filter to extract `$mceInit`
		add_filter( 'tiny_mce_before_init', $func_tiny_mce_before_init, 1, 2 );

		// Init of editor settings to form all options
		if ( ! class_exists( '_WP_Editors', false ) ) {
			require ABSPATH . WPINC . '/class-wp-editor.php';
		}
		$set = \_WP_Editors::parse_settings( $id, $set );
		\_WP_Editors::editor_settings( $id, $set );

		// Remove the filter after extracting the `$mceInit`
		remove_filter( 'tiny_mce_before_init', $func_tiny_mce_before_init );

		// Parsing received options
		$options = '';
		foreach ( $mceInit as $key => $value ) {
			if ( is_bool( $value ) ) {
				$val = $value ? 'true' : 'false';
				$options .= $key . ':' . $val . ',';
				continue;
			} elseif (
				! empty( $value )
				&& is_string( $value )
				&& (
					( '{' === $value[0] && '}' === $value[ strlen( $value ) - 1 ] )
					|| ( '[' === $value[0] && ']' === $value[ strlen( $value ) - 1 ] )
					|| preg_match( '/^\(?function ?\(/', $value )
				)
			) {

				$options .= $key . ':' . $value . ',';
				continue;
			}
			$options .= $key . ':"' . $value . '",';
		}

		return '{' . trim( $options, ' ,' ) . '}';
	}
}
