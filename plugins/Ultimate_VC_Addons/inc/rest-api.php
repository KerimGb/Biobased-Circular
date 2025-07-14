<?php
/**
 * REST API endpoints for Ultimate Addons for WPBakery Page Builder
 *
 * @package Ultimate_VC_Addons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Verify nonce for REST API requests
 *
 * @param WP_REST_Request $request Request object.
 * @return bool|WP_Error True if nonce is valid, WP_Error if invalid.
 */
function uavc_verify_rest_nonce( $request ) {
	// Skip nonce verification for GET requests that only read data
	if ( 'GET' === $request->get_method() ) {
		return true;
	}

	$nonce = $request->get_header( 'X-WP-Nonce' );

	if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
		return new WP_Error( 'invalid_nonce', __( 'Invalid nonce', 'ultimate_vc' ), array( 'status' => 403 ) );
	}

	return true;
}

/**
 * Register REST API endpoints for Ultimate Addons
 */
function uavc_register_rest_routes() {
        // Register endpoint for Smooth Scroll settings
        register_rest_route(
                'ultimate-vc/v1',
                '/smooth-scroll-settings',
                array(
                        array(
                                'methods'             => WP_REST_Server::READABLE,
                                'callback'            => 'uavc_get_smooth_scroll_settings',
                                'permission_callback' => function() {
                                        return current_user_can( 'manage_options' );
                                },
                        ),
                        array(
                                'methods'             => WP_REST_Server::CREATABLE,
                                'callback'            => 'uavc_update_smooth_scroll_settings',
                                'permission_callback' => function() {
                                        return current_user_can( 'manage_options' );
                                },
                        ),
                )
        );

        // Register endpoint for Smooth Scroll status
        register_rest_route(
                'ultimate-vc/v1',
                '/smooth-scroll-status',
                array(
                        'methods'             => WP_REST_Server::READABLE,
                        'callback'            => 'uavc_get_smooth_scroll_status',
                        'permission_callback' => function() {
                                return current_user_can( 'manage_options' );
                        },
                )
        );

	// Register endpoint for Smooth Scroll options
	register_rest_route(
		'ultimate-vc/v1',
		'/smooth-scroll-options',
		array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'uavc_get_smooth_scroll_options',
				'permission_callback' => function() {
					return current_user_can( 'manage_options' );
				},
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'uavc_update_smooth_scroll_options',
				'permission_callback' => function() {
					return current_user_can( 'manage_options' );
				},
			),
		)
	);

	// Register endpoint for Combined CSS settings
	register_rest_route(
		'ultimate-vc/v1',
		'/combined-css',
		array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'uavc_get_combined_css_settings',
				'permission_callback' => function() {
					return current_user_can( 'manage_options' );
				},
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'uavc_update_combined_css_settings',
				'permission_callback' => function() {
					return current_user_can( 'manage_options' );
				},
			),
		)
	);

	// Register endpoint for Combined JS settings
        register_rest_route(
                'ultimate-vc/v1',
                '/combined-js',
                array(
                        array(
                                'methods'             => WP_REST_Server::READABLE,
                                'callback'            => 'uavc_get_combined_js_settings',
                                'permission_callback' => function() {
                                        return current_user_can( 'manage_options' );
                                },
                        ),
                        array(
                                'methods'             => WP_REST_Server::CREATABLE,
                                'callback'            => 'uavc_update_combined_js_settings',
                                'permission_callback' => function() {
                                        return current_user_can( 'manage_options' );
                                },
                        ),
                )
        );

        // Register endpoint for Analytics opt-in settings.
        register_rest_route(
                'ultimate-vc/v1',
                '/analytics-optin',
                array(
                        array(
                                'methods'             => WP_REST_Server::READABLE,
                                'callback'            => 'uavc_get_analytics_optin',
                                'permission_callback' => function() {
                                        return current_user_can( 'manage_options' );
                                },
                        ),
                        array(
                                'methods'             => WP_REST_Server::CREATABLE,
                                'callback'            => 'uavc_update_analytics_optin',
                                'permission_callback' => function() {
                                        return current_user_can( 'manage_options' );
                                },
                        ),
                )
        );

// Register endpoint for Google Map API key
register_rest_route(
        'ultimate-vc/v1',
        '/map-key',
        array(
                array(
                        'methods'             => WP_REST_Server::READABLE,
                        'callback'            => 'uavc_get_map_key',
                        'permission_callback' => function() {
                                return current_user_can( 'manage_options' );
                        },
                ),
                array(
                        'methods'             => WP_REST_Server::CREATABLE,
                        'callback'            => 'uavc_update_map_key',
                        'permission_callback' => function() {
                                return current_user_can( 'manage_options' );
                        },
                ),
        )
);

       // Register endpoint to validate Google Map API key
       register_rest_route(
               'ultimate-vc/v1',
               '/map-key/validate',
               array(
                       array(
                               'methods'             => WP_REST_Server::CREATABLE,
                               'callback'            => 'uavc_validate_map_key',
                               'permission_callback' => function() {
                                       return current_user_can( 'manage_options' );
                               },
                       ),
               )
       );

       // Register endpoint for creating a blank WPBakery page
       register_rest_route(
               'ultimate-vc/v1',
               '/create-page',
               array(
                       'methods'             => WP_REST_Server::CREATABLE,
                       'callback'            => 'uavc_create_blank_page',
                       'permission_callback' => function() {
                               return current_user_can( 'edit_pages' );
                       },
               )
       );
}
add_action( 'rest_api_init', 'uavc_register_rest_routes' );

/**
 * Get Smooth Scroll settings
 *
 * @return WP_REST_Response
 */
function uavc_get_smooth_scroll_settings() {
	$ultimate_smooth_scroll = get_option( 'ultimate_smooth_scroll', 'disable' );
	$ultimate_smooth_scroll_options = get_option( 'ultimate_smooth_scroll_options', array() );
	
	return rest_ensure_response( array(
		'enabled' => $ultimate_smooth_scroll,
		'options' => $ultimate_smooth_scroll_options,
	) );
}

/**
 * Update Smooth Scroll settings
 *
 * @param WP_REST_Request $request Full details about the request.
 * @return WP_REST_Response
 */
function uavc_update_smooth_scroll_settings( $request ) {
        // Verify nonce for security
        $nonce_check = uavc_verify_rest_nonce( $request );
        if ( is_wp_error( $nonce_check ) ) {
                return $nonce_check;
        }

        $params = $request->get_params();
        $enabled = isset( $params['enabled'] ) ? sanitize_text_field( $params['enabled'] ) : 'disable';

        // Update option in database
        update_option( 'ultimate_smooth_scroll', $enabled );

        return rest_ensure_response( array(
                'success' => true,
                'enabled' => $enabled,
        ) );
}

/**
 * Get Smooth Scroll status only
 *
 * @return WP_REST_Response
 */
function uavc_get_smooth_scroll_status() {
        $ultimate_smooth_scroll = get_option( 'ultimate_smooth_scroll', 'disable' );

        return rest_ensure_response( array(
                'enabled' => $ultimate_smooth_scroll,
        ) );
}

/**
 * Get Smooth Scroll options
 *
 * @return WP_REST_Response
 */
function uavc_get_smooth_scroll_options() {
	$ultimate_smooth_scroll_options = get_option( 'ultimate_smooth_scroll_options', array() );
	
	return rest_ensure_response( array(
		'options' => $ultimate_smooth_scroll_options,
	) );
}

/**
 * Update Smooth Scroll options
 *
 * @param WP_REST_Request $request Full details about the request.
 * @return WP_REST_Response
 */
function uavc_update_smooth_scroll_options( $request ) {
	// Verify nonce for security
	$nonce_check = uavc_verify_rest_nonce( $request );
	if ( is_wp_error( $nonce_check ) ) {
		return $nonce_check;
	}

	$params = $request->get_params();
	$options = array();
	
	if ( isset( $params['step'] ) ) {
		$options['step'] = sanitize_text_field( $params['step'] );
	}
	
	if ( isset( $params['speed'] ) ) {
		$options['speed'] = sanitize_text_field( $params['speed'] );
	}
	
	// Update option in database
	update_option( 'ultimate_smooth_scroll_options', $options );
	
	return rest_ensure_response( array(
		'success' => true,
		'options' => $options,
	) );
}

/**
 * Get Combined CSS settings
 *
 * @return WP_REST_Response
 */
function uavc_get_combined_css_settings() {
	$ultimate_css = get_option( 'ultimate_css', 'disable' );
	
	return rest_ensure_response( array(
		'enabled' => $ultimate_css,
	) );
}

/**
 * Update Combined CSS settings
 *
 * @param WP_REST_Request $request Full details about the request.
 * @return WP_REST_Response
 */
function uavc_update_combined_css_settings( $request ) {
	// Verify nonce for security
	$nonce_check = uavc_verify_rest_nonce( $request );
	if ( is_wp_error( $nonce_check ) ) {
		return $nonce_check;
	}

	$params = $request->get_params();
	$enabled = isset( $params['enabled'] ) ? sanitize_text_field( $params['enabled'] ) : 'disable';
	
	// Update option in database
	update_option( 'ultimate_css', $enabled );
	
	return rest_ensure_response( array(
		'success' => true,
		'enabled' => $enabled,
		'message' => 'enable' === $enabled ? 'Combined CSS activated' : 'Combined CSS deactivated',
	) );
}

/**
 * Get Combined JS settings
 *
 * @return WP_REST_Response
 */
function uavc_get_combined_js_settings() {
	$ultimate_js = get_option( 'ultimate_js', 'disable' );
	
	return rest_ensure_response( array(
		'enabled' => $ultimate_js,
	) );
}

/**
 * Get Google Map API key
 *
 * @return WP_REST_Response
 */
function uavc_get_map_key() {
    $map_key = bsf_get_option( 'map_key' );

    return rest_ensure_response( array(
            'map_key' => $map_key,
    ) );
}

/**
* Update Google Map API key
*
* @param WP_REST_Request $request Request object.
* @return WP_REST_Response
*/
function uavc_update_map_key( $request ) {
    // Verify nonce for security
    $nonce_check = uavc_verify_rest_nonce( $request );
    if ( is_wp_error( $nonce_check ) ) {
        return $nonce_check;
    }

    $params  = $request->get_params();
    $map_key = isset( $params['map_key'] ) ? sanitize_text_field( $params['map_key'] ) : '';

    // Fetch the existing "bsf_options" array and update the map_key value.
    $bsf_options = get_option( 'bsf_options', array() );
    $bsf_options['map_key'] = $map_key;
    update_option( 'bsf_options', $bsf_options );

    return rest_ensure_response( array(
            'success' => true,
            'map_key' => $map_key,
    ) );
}

/**
 * Validate Google Map API key.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error
 */
function uavc_validate_map_key( $request ) {
       // Verify nonce for security
       $nonce_check = uavc_verify_rest_nonce( $request );
       if ( is_wp_error( $nonce_check ) ) {
               return $nonce_check;
       }

       $params  = $request->get_params();
       $map_key = isset( $params['map_key'] ) ? sanitize_text_field( $params['map_key'] ) : '';

       if ( empty( $map_key ) ) {
               return new WP_REST_Response( array( 'success' => false, 'message' => __( 'API key is missing.', 'ultimate_vc' ) ), 400 );
       }

       $endpoint = add_query_arg(
               array(
                       'address' => 'New York',
                       'key'     => $map_key,
               ),
               'https://maps.googleapis.com/maps/api/geocode/json'
       );

       $response = wp_remote_get( $endpoint );

       if ( is_wp_error( $response ) ) {
               return new WP_REST_Response( array( 'success' => false, 'message' => __( 'Unable to verify API key.', 'ultimate_vc' ) ), 400 );
       }

       $code = wp_remote_retrieve_response_code( $response );
       $body = json_decode( wp_remote_retrieve_body( $response ), true );

       if ( 200 !== $code || ! is_array( $body ) ) {
               return new WP_REST_Response( array( 'success' => false, 'message' => __( 'Unable to verify API key.', 'ultimate_vc' ) ), 400 );
       }

       if ( isset( $body['status'] ) && 'OK' === $body['status'] ) {
        $bsf_data = get_option('bsf_options');
        $bsf_data['map_key'] = $map_key;
        update_option('bsf_options', $bsf_data);  
        return new WP_REST_Response( array( 'success' => true, 'message' => __( 'Your API key authenticated successfully!', 'ultimate_vc' ) ), 200 );
         }

       $message = isset( $body['error_message'] ) && $body['error_message'] ? $body['error_message'] : __( 'Entered API key is invalid', 'ultimate_vc' );

       return new WP_REST_Response( array( 'success' => false, 'message' => $message ), 400 );
}

/**
 * Update Combined JS settings
 *
 * @param WP_REST_Request $request Full details about the request.
 * @return WP_REST_Response
 */
function uavc_update_combined_js_settings( $request ) {
        // Verify nonce for security
        $nonce_check = uavc_verify_rest_nonce( $request );
        if ( is_wp_error( $nonce_check ) ) {
                return $nonce_check;
        }

        $params = $request->get_params();
        $enabled = isset( $params['enabled'] ) ? sanitize_text_field( $params['enabled'] ) : 'disable';
	
	// Update option in database
	update_option( 'ultimate_js', $enabled );
	
        return rest_ensure_response( array(
                'success' => true,
                'enabled' => $enabled,
                'message' => 'enable' === $enabled ? 'Combined JS activated' : 'Combined JS deactivated',
        ) );
}

/**
 * Get analytics opt-in status.
 *
 * @return WP_REST_Response
 */
function uavc_get_analytics_optin() {
        $optin = get_option( 'uavc_analytics_optin', 'no' );

        return rest_ensure_response( array(
                'enabled' => $optin,
        ) );
}

/**
 * Update analytics opt-in status.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
function uavc_update_analytics_optin( $request ) {
        // Verify nonce for security
        $nonce_check = uavc_verify_rest_nonce( $request );
        if ( is_wp_error( $nonce_check ) ) {
                return $nonce_check;
        }

        $params  = $request->get_params();
        $enabled = isset( $params['enabled'] ) && 'yes' === $params['enabled'] ? 'yes' : 'no';

        update_option( 'uavc_analytics_optin', $enabled );

        return rest_ensure_response( array(
                'success' => true,
                'enabled' => $enabled,
        ) );
}

/**
 * Register REST API endpoint for modules
 */
function uavc_register_modules_endpoint() {
    register_rest_route(
        'uavc/v1',
        '/modules',
        array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'uavc_get_modules',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
        )
    );

      // Register endpoint for Envato activation
      register_rest_route(
        'uavc/v1',
        '/envato-activation',
        array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'uavc_envato_redirect_url',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
        )
    );

    // Register endpoint for license status check
    register_rest_route(
        'uavc/v1',
        '/license-status',
        array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'uavc_check_license_status',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
        )
    );

    // Register endpoint for license deactivation
    register_rest_route(
        'uavc/v1',
        '/license-deactivate',
        array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'uavc_deactivate_license',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
        )
    );
}
add_action( 'rest_api_init', 'uavc_register_modules_endpoint' );

/**
 * Check license status for WPBakery Page Builder
 *
 * @return WP_REST_Response
 */
function uavc_check_license_status() {
    // Check if user can manage options
    if (!current_user_can('manage_options')) {
        return new WP_Error('rest_forbidden', esc_html__('You do not have permissions to access this endpoint.', 'ultimate_vc'), array('status' => 403));
    }

    // Check the transient value first.
    $license_status = get_transient('6892199_license_status');

    // Fallback to stored option if transient is missing.
    if ( false === $license_status ) {
        $products = get_option( 'brainstrom_products', array() );
        $status   = '';
        if ( isset( $products['plugins']['6892199']['status'] ) ) {
            $status = $products['plugins']['6892199']['status'];
        } elseif ( isset( $products['themes']['6892199']['status'] ) ) {
            $status = $products['themes']['6892199']['status'];
        }

        $license_status = ( 'registered' === $status ) ? 1 : 0;
    }

    return rest_ensure_response(array(
        'success'  => true,
        'is_active' => $license_status == 1,
    ));
}
function uavc_envato_redirect_url($request) {
    // Check if user can manage options
    if (!current_user_can('manage_options')) {
        return new WP_Error('rest_forbidden', esc_html__('You do not have permissions to access this endpoint.', 'ultimate_vc'), array('status' => 403));
    }

    // Get parameters from request
    $params = $request->get_params();
    $product_id = isset($params['product_id']) ? sanitize_text_field($params['product_id']) : '';
    $url = isset($params['url']) ? esc_url_raw($params['url']) : '';
    $redirect = isset($params['redirect']) ? sanitize_text_field($params['redirect']) : '';
    $privacy_consent = isset($params['privacy_consent']) && 'true' === $params['privacy_consent'];
    $terms_conditions_consent = isset($params['terms_conditions_consent']) && 'true' === $params['terms_conditions_consent'];

    // Create form data array
    $form_data = array(
        'product_id' => $product_id,
        'url' => $url,
        'redirect' => $redirect ? rawurlencode($redirect) : '',
        'privacy_consent' => $privacy_consent,
        'terms_conditions_consent' => $terms_conditions_consent
    );

    // Check if BSF_Envato_Activate class exists
    if (class_exists('BSF_Envato_Activate')) {
        $envato_activate = new BSF_Envato_Activate();
        $activation_url = $envato_activate->envato_activation_url($form_data);

        return rest_ensure_response(array(
            'success' => true,
            'url' => esc_url_raw($activation_url)
        ));
    } else {
        return new WP_Error('class_not_found', esc_html__('Required class not found.', 'ultimate_vc'), array('status' => 500));
    }
}

/**
 * Deactivate the product license.
 *
 * @return WP_REST_Response
 */
function uavc_deactivate_license( $request ) {
    // Check if user can manage options
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to access this endpoint.', 'ultimate_vc' ), array( 'status' => 403 ) );
    }

    // Verify nonce for security
    $nonce_check = uavc_verify_rest_nonce( $request );
    if ( is_wp_error( $nonce_check ) ) {
        return $nonce_check;
    }

    $product_id = '6892199';

    // Prepare POST data as expected by BSF_License_Manager.
    $_POST['bsf_deactivate_license']          = true;
    $_POST['bsf_graupi_nonce']                = wp_create_nonce( 'bsf_license_activation_deactivation_nonce' );
    $_POST['bsf_license_manager']             = array(
        'license_key' => 'deactivate',
        'product_id'  => $product_id,
    );

    if ( class_exists( 'BSF_License_Manager' ) ) {
        $manager = BSF_License_Manager::instance();
        $manager->bsf_deactivate_license();

        $success = isset( $_POST['bsf_license_deactivation']['success'] ) ? $_POST['bsf_license_deactivation']['success'] : false;
        $message = isset( $_POST['bsf_license_deactivation']['message'] ) ? $_POST['bsf_license_deactivation']['message'] : '';

        return rest_ensure_response( array(
            'success'   => $success,
            'message'   => $message,
        ) );
    }

    return new WP_Error( 'class_not_found', esc_html__( 'Required class not found.', 'ultimate_vc' ), array( 'status' => 500 ) );
}

/**
 * Get all modules with their activation status
 *
 * @return WP_REST_Response
 */
function uavc_get_modules() {
    // Get the modules array from modules.php
    ob_start();
    include_once(dirname(dirname(__FILE__)) . '/admin/modules.php');
    ob_end_clean();
    
    // Get active modules from options
    $active_modules = get_option('ultimate_modules', array());
    $ultimate_row   = get_option( 'ultimate_row', 'enable' );
    
    // Add activation status to each module
    if (isset($modules) && is_array($modules)) {
        $result_modules = array();
        
        foreach ($modules as $key => $module) {
            // Create a copy of the module to avoid reference issues
            $module_copy = $module;
            $module_id   = strtolower( $key );

            if ( 'row_backgrounds' === $module_id ) {
                $module_copy['is_activate'] = ( 'enable' === $ultimate_row );
            } else {
                $module_copy['is_activate'] = in_array( $module_id, $active_modules );
            }
            $result_modules[$key] = $module_copy;
        }
        
        return rest_ensure_response($result_modules);
    } else {
        // Return empty array if modules not found
        return rest_ensure_response(array(
            'error' => 'Modules not found'
        ));
    }
}

/**
 * Register REST API endpoints for Icon Fonts.
 */
function uavc_register_icon_fonts_endpoint() {
    register_rest_route(
        'ultimate-vc/v1',
        '/icon-fonts',
        array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'uavc_get_icon_fonts',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'uavc_add_icon_font',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => 'uavc_remove_icon_font',
                'args'                => array(
                    'font' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
        )
    );
}
add_action( 'rest_api_init', 'uavc_register_icon_fonts_endpoint' );

/**
 * Get Icon Fonts along with icons.
 *
 * @return WP_REST_Response
 */
function uavc_get_icon_fonts() {
    if ( ! class_exists( 'Ultimate_VC_Addons_Icon_Manager' ) ) {
        require_once UAVC_DIR . 'modules/ultimate_icon_manager.php';
    }

    // Instantiate to ensure default fonts are available.
    new Ultimate_VC_Addons_Icon_Manager();

    $fonts = Ultimate_VC_Addons_Icon_Manager::load_iconfont_list();
    $upload_dir = wp_upload_dir();
    $url  = trailingslashit( $upload_dir['baseurl'] );

    $result = array();
    foreach ( $fonts as $name => $info ) {
        $charmap_file = trailingslashit( $info['include'] ) . $info['config'];
        $icons_data   = array();
        if ( file_exists( $charmap_file ) ) {
            include $charmap_file; // $icons variable.
            if ( isset( $icons[ $name ] ) ) {
                $icons_data = array_values( $icons[ $name ] );
            } elseif ( isset( $icons['Defaults'] ) ) {
                $icons_data = array_values( $icons['Defaults'] );
            }
        }

    $style = $info['style'];
    if ( false === strpos( $style, 'http' ) ) {
        $style = trailingslashit( $url ) . 'smile_fonts/' . ltrim( $style, '/' );
    }

        $result[ $name ] = array(
            'name'  => $name,
            'style' => $style,
            'icons' => $icons_data,
        );
    }

    return rest_ensure_response( $result );
}

/**
 * Upload a new icon font zip and register it.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
function uavc_add_icon_font( WP_REST_Request $request ) {
    // Verify nonce for security
    $nonce_check = uavc_verify_rest_nonce( $request );
    if ( is_wp_error( $nonce_check ) ) {
        return $nonce_check;
    }

    if ( ! class_exists( 'Ultimate_VC_Addons_Icon_Manager' ) ) {
        require_once UAVC_DIR . 'modules/ultimate_icon_manager.php';
    }

    $files        = $request->get_file_params();
    $attachment   = $request->get_param( 'attachment' );
    $uploaded_zip = '';

    if ( $attachment ) {
        $uploaded_zip = get_attached_file( (int) $attachment );
    } elseif ( ! empty( $files['file'] ) ) {
        $movefile = wp_handle_upload( $files['file'], array( 'test_form' => false ) );
        if ( empty( $movefile['file'] ) ) {
            return new WP_Error( 'upload_error', 'Unable to upload file', array( 'status' => 500 ) );
        }
        $uploaded_zip = $movefile['file'];
    } else {
        return new WP_Error( 'no_file', 'No file uploaded', array( 'status' => 400 ) );
    }

    $manager = new Ultimate_VC_Addons_Icon_Manager();
    $unzipped = $manager->zip_flatten( $uploaded_zip, array( '\.eot', '\.svg', '\.ttf', '\.woff', '\.json', '\.css' ) );
    if ( $unzipped ) {
        $manager->create_config();
    }

    if ( 'unknown' === $manager->font_name ) {
        $manager->delete_folder( $manager->paths['tempdir'] );
        return new WP_Error( 'invalid_font', 'Invalid font uploaded', array( 'status' => 400 ) );
    }

    // create_config() already handles renaming and registering the font set.
    // We only proceed if the configuration was successfully created.

    return rest_ensure_response( array(
        'success' => true,
        'font'    => $manager->font_name,
    ) );
}

/**
 * Remove an icon font set.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
function uavc_remove_icon_font( WP_REST_Request $request ) {
    // Verify nonce for security
    $nonce_check = uavc_verify_rest_nonce( $request );
    if ( is_wp_error( $nonce_check ) ) {
        return $nonce_check;
    }

    if ( ! class_exists( 'Ultimate_VC_Addons_Icon_Manager' ) ) {
        require_once UAVC_DIR . 'modules/ultimate_icon_manager.php';
    }

    $font = $request->get_param( 'font' );

    $list   = Ultimate_VC_Addons_Icon_Manager::load_iconfont_list();
    $delete = isset( $list[ $font ] ) ? $list[ $font ] : false;

    if ( $delete ) {
        $manager = new Ultimate_VC_Addons_Icon_Manager();
        $manager->delete_folder( $delete['include'] );
        $manager->remove_font( $font );

        return rest_ensure_response( array(
            'success' => true,
            'font'    => $font,
        ) );
    }

    return new WP_Error( 'not_found', 'Font not found', array( 'status' => 404 ) );
}

/**
 * Register REST API endpoints for Google Fonts.
 */
function uavc_register_google_fonts_endpoint() {
    register_rest_route(
        'ultimate-vc/v1',
        '/google-fonts',
        array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'uavc_get_google_fonts',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'args'                => array(
                    'start'  => array(
                        'sanitize_callback' => 'absint',
                        'default'           => 0,
                    ),
                    'fetch'  => array(
                        'sanitize_callback' => 'absint',
                        'default'           => 20,
                    ),
                    'search' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                        'default'           => '',
                    ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'uavc_add_google_font',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'uavc_update_google_font',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => 'uavc_delete_google_font',
                'args'                => array(
                    'font_name' => array(
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
        )
    );

    register_rest_route(
        'ultimate-vc/v1',
        '/google-fonts/refresh',
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'uavc_refresh_google_fonts',
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
        )
    );

    register_rest_route(
        'ultimate-vc/v1',
        '/google-fonts/variants',
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'uavc_get_font_variants',
            'args'                => array(
                'font_name' => array(
                    'required'          => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
            'permission_callback' => function() {
                return current_user_can( 'manage_options' );
            },
        )
    );
}
add_action( 'rest_api_init', 'uavc_register_google_fonts_endpoint' );

/**
 * Refresh Google Fonts list from Google API.
 *
 * @return WP_REST_Response
 */
function uavc_refresh_google_fonts() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'forbidden', 'Access denied', array( 'status' => 403 ) );
    }

    $fonts      = array();
    $temp_count = 0;
    $temp       = get_option( 'ultimate_google_fonts' );
    if ( ! empty( $temp ) ) {
        $temp_count = count( $temp );
    }

    $error = false;
    if ( empty( $fonts ) ) {
        try {
            $fonts = wp_remote_get( 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyD_6TR2RyX2VRf8bABDRXCcVqdMXB5FQvs' );
            $fonts = json_decode( $fonts['body'] );
        } catch ( Exception $e ) {
            $error = true;
        }
    }

    if ( true != $error && ! empty( $fonts ) ) {
        $google_fonts      = $fonts->items;
        $google_font_count = count( is_countable( $google_fonts ) ? $google_fonts : array() );
        update_option( 'ultimate_google_fonts', $google_fonts );

        $response = array(
            'count'   => ( $google_font_count - $temp_count ),
            'message' => __( ( $google_font_count - $temp_count ) . ' new fonts added. ', 'ultimate_vc' ),
        );
    } else {
        $response = array(
            'count'   => 0,
            'message' => __( 'Fonts could not be downloaded as there might be some issue with file_get_contents or wp_remote_get due to your server configuration.', 'ultimate_vc' ),
        );
    }

    return rest_ensure_response( $response );
}

/**
 * Get list of Google Fonts.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
function uavc_get_google_fonts( WP_REST_Request $request ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'forbidden', 'Access denied', array( 'status' => 403 ) );
    }

    $start  = $request->get_param( 'start' );
    $fetch  = $request->get_param( 'fetch' );
    $search = $request->get_param( 'search' );

    $google_fonts = get_option( 'ultimate_google_fonts' );
    $response     = array();
    $fonts        = array();

    if ( ! empty( $google_fonts ) ) {
        $selected_google_fonts = get_option( 'ultimate_selected_google_fonts' );
        $temp_selected         = array();
        if ( ! empty( $selected_google_fonts ) ) {
            foreach ( $selected_google_fonts as $selected_font ) {
                $temp_selected[] = $selected_font['font_name'];
            }
        }

        $font_slice_array = array();
        if ( '' !== $search ) {
            foreach ( $google_fonts as $tfont ) {
                if ( stripos( $tfont->family, $search ) !== false ) {
                    $font_slice_array[] = $tfont;
                }
            }
        } else {
            $font_slice_array = array_slice( $google_fonts, $start, $fetch );
        }

        foreach ( $font_slice_array as $tempfont ) {
            $already_selected = in_array( $tempfont->family, $temp_selected ) ? 'true' : 'false';
            $fonts[]          = array(
                'font_name' => $tempfont->family,
                'font_call' => str_replace( ' ', '+', $tempfont->family ),
                'variants'  => $tempfont->variants,
                'subsets'   => $tempfont->subsets,
                'selected'  => $already_selected,
            );
        }
    }

    $response['fonts']       = $fonts;
    $response['fonts_count'] = count( is_countable( $google_fonts ) ? $google_fonts : array() );
    $response['is_search']   = '' !== $search ? 'true' : 'false';

    return rest_ensure_response( $response );
}

/**
 * Add a Google Font to collection.
 */
function uavc_add_google_font( WP_REST_Request $request ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'forbidden', 'Access denied', array( 'status' => 403 ) );
    }

    // Verify nonce for security
    $nonce_check = uavc_verify_rest_nonce( $request );
    if ( is_wp_error( $nonce_check ) ) {
        return $nonce_check;
    }

    $font_family = $request->get_param( 'font_family' );
    $font_name   = $request->get_param( 'font_name' );
    $variants    = $request->get_param( 'variants' );
    $subsets     = $request->get_param( 'subsets' );

    $fonts = get_option( 'ultimate_selected_google_fonts' );
    if ( empty( $fonts ) ) {
        $fonts = array();
    }

    $fonts[] = array(
        'font_family' => sanitize_text_field( $font_family ),
        'font_name'   => sanitize_text_field( $font_name ),
        'variants'    => is_array( $variants ) ? $variants : array(),
        'subsets'     => is_array( $subsets ) ? $subsets : array(),
    );

    update_option( 'ultimate_selected_google_fonts', $fonts );

    return rest_ensure_response( array( 'success' => true ) );
}

/**
 * Delete a Google Font from collection.
 */
function uavc_delete_google_font( WP_REST_Request $request ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'forbidden', 'Access denied', array( 'status' => 403 ) );
    }

    // Verify nonce for security
    $nonce_check = uavc_verify_rest_nonce( $request );
    if ( is_wp_error( $nonce_check ) ) {
        return $nonce_check;
    }

    $font_name = $request->get_param( 'font_name' );
    $fonts     = get_option( 'ultimate_selected_google_fonts' );

    foreach ( $fonts as $key => $font ) {
        if ( $font['font_name'] === $font_name ) {
            unset( $fonts[ $key ] );
        }
    }

    $fonts = array_values( $fonts );
    update_option( 'ultimate_selected_google_fonts', $fonts );

    return rest_ensure_response( array( 'success' => true ) );
}

/**
 * Update selected Google Font variants and subsets.
 */
function uavc_update_google_font( WP_REST_Request $request ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'forbidden', 'Access denied', array( 'status' => 403 ) );
    }

    // Verify nonce for security
    $nonce_check = uavc_verify_rest_nonce( $request );
    if ( is_wp_error( $nonce_check ) ) {
        return $nonce_check;
    }

    $font_name = $request->get_param( 'font_name' );
    $variants  = $request->get_param( 'variants' );
    $subsets   = $request->get_param( 'subsets' );

    $fonts = get_option( 'ultimate_selected_google_fonts' );

    foreach ( $fonts as $key => $font ) {
        if ( $font['font_name'] === $font_name ) {
            $fonts[ $key ]['variants'] = is_array( $variants ) ? $variants : array();
            $fonts[ $key ]['subsets'] = is_array( $subsets ) ? $subsets : array();
        }
    }

    update_option( 'ultimate_selected_google_fonts', $fonts );

    return rest_ensure_response( array( 'success' => true ) );
}

/**
 * Get selected font variants formatted for UI.
 */
function uavc_get_font_variants( WP_REST_Request $request ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'forbidden', 'Access denied', array( 'status' => 403 ) );
    }

    $font_name     = $request->get_param( 'font_name' );
    $fonts         = get_option( 'ultimate_selected_google_fonts' );
    $font_variants = array();

    foreach ( $fonts as $font ) {
        if ( $font['font_name'] === $font_name ) {
            $font_variants = $font['variants'];
            break;
        }
    }

    $json_variants          = array();
    $default_variant_styles = array(
        array(
            'label' => 'Underline',
            'style' => 'text-decoration:underline;',
            'type'  => 'checkbox',
            'group' => 'ultimate_defaults_styles',
            'class' => 'ultimate_defaults_styles',
        ),
        array(
            'label' => 'Italic',
            'style' => 'font-style:italic;',
            'type'  => 'checkbox',
            'group' => 'ultimate_defaults_styles',
            'class' => 'ultimate_defaults_styles',
        ),
        array(
            'label' => 'Bold',
            'style' => 'font-weight:bold;',
            'type'  => 'checkbox',
            'group' => 'ultimate_defaults_styles',
            'class' => 'ultimate_defaults_styles',
        ),
    );

    if ( ! empty( $font_variants ) ) {
        $is_italic            = false;
        $is_weight            = false;
        $uniq_grp             = uniqid( '_' );
        $pre_default_variants = array();
        foreach ( $font_variants as $variant ) {
            if ( isset( $variant['variant_selected'] ) && 'true' === $variant['variant_selected'] ) {
                $temp_array  = array();
                $is_weight   = false;
                $is_italic   = false;
                $variant_val = $variant['variant_value'];
                if ( preg_match( '/italic/i', $variant_val ) && $weight = preg_replace( '/\D/', '', $variant_val ) ) {
                    $temp_array['label'] = $variant_val;
                    $temp_array['style'] = 'font-style:italic;font-weight:' . $weight . ';';
                    $is_italic           = true;
                    $is_weight           = true;
                } elseif ( preg_match( '/italic/i', $variant_val ) ) {
                    $temp_array['label'] = $variant_val;
                    $temp_array['style'] = 'font-style:italic;';
                    $is_italic           = true;
                } elseif ( $weight = preg_replace( '/\D/', '', $variant_val ) ) {
                    $temp_array['label'] = $variant_val;
                    $temp_array['style'] = 'font-weight:' . $weight . ';';
                    $is_weight           = true;
                }
                $temp_array['type']  = 'radio';
                $temp_array['group'] = 'style_by_google' . $uniq_grp;
                $temp_array['class'] = 'style_by_google';
                $json_variants[]     = $temp_array;
            }
        }

        $pre_default_variants[] = $default_variant_styles[0];
        if ( false === $is_italic ) {
            $pre_default_variants[] = $default_variant_styles[1];
        }
        if ( false === $is_weight ) {
            $pre_default_variants[] = $default_variant_styles[2];
        }
        $json_variants = array_merge( $pre_default_variants, $json_variants );
    } else {
        $json_variants = $default_variant_styles;
    }

    return rest_ensure_response( $json_variants );
}

/**
 * Register REST API endpoints for Debug settings.
 */
function uavc_register_debug_endpoint() {
    register_rest_route(
        'ultimate-vc/v1',
        '/debug-options',
        array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'uavc_get_debug_options',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'uavc_update_debug_options',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
            ),
        )
    );
}
add_action( 'rest_api_init', 'uavc_register_debug_endpoint' );

/**
 * Get Debug options.
 *
 * @return WP_REST_Response
 */
function uavc_get_debug_options() {
    $roles_option = bsf_get_option( 'ultimate_roles' );
    $editable_roles = get_editable_roles();

    $all_roles = array();
    foreach ( $editable_roles as $role_name => $role_info ) {
        $all_roles[] = array(
            'name'  => $role_name,
            'label' => $role_info['name'],
        );
    }

    $data = array(
        'theme'                      => wp_get_theme()->get( 'Name' ),
        'video_fixer'                => get_option( 'ultimate_video_fixer', 'disable' ),
        'ajax_theme'                 => get_option( 'ultimate_ajax_theme', 'disable' ),
        'theme_support'              => get_option( 'ultimate_theme_support', 'disable' ),
        'rtl_support'                => get_option( 'ultimate_rtl_support', 'disable' ),
        'custom_vc_row'              => get_option( 'ultimate_custom_vc_row', '' ),
        'dev_mode'                   => bsf_get_option( 'dev_mode' ),
        'global_scripts'             => bsf_get_option( 'ultimate_global_scripts' ),
        'modal_menu'                 => bsf_get_option( 'ultimate_modal_menu' ),
        'smooth_scroll_compatible'   => get_option( 'ultimate_smooth_scroll_compatible', 'disable' ),
        'animation_block'            => get_option( 'ultimate_animation', 'disable' ),
        'roles'                      => is_array( $roles_option ) ? $roles_option : array(),
        'all_roles'                  => $all_roles,
    );

    return rest_ensure_response( $data );
}

/**
 * Update Debug options.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
function uavc_update_debug_options( WP_REST_Request $request ) {
    // Verify nonce for security
    $nonce_check = uavc_verify_rest_nonce( $request );
    if ( is_wp_error( $nonce_check ) ) {
        return $nonce_check;
    }

    $params = $request->get_params();

    update_option( 'ultimate_video_fixer', isset( $params['video_fixer'] ) && 'enable' === $params['video_fixer'] ? 'enable' : 'disable' );
    update_option( 'ultimate_ajax_theme', isset( $params['ajax_theme'] ) && 'enable' === $params['ajax_theme'] ? 'enable' : 'disable' );
    update_option( 'ultimate_custom_vc_row', isset( $params['custom_vc_row'] ) ? sanitize_text_field( $params['custom_vc_row'] ) : '' );
    update_option( 'ultimate_theme_support', isset( $params['theme_support'] ) && 'enable' === $params['theme_support'] ? 'enable' : 'disable' );
    update_option( 'ultimate_rtl_support', isset( $params['rtl_support'] ) && 'enable' === $params['rtl_support'] ? 'enable' : 'disable' );
    update_option( 'ultimate_smooth_scroll_compatible', isset( $params['smooth_scroll_compatible'] ) && 'enable' === $params['smooth_scroll_compatible'] ? 'enable' : 'disable' );
    update_option( 'ultimate_animation', isset( $params['animation_block'] ) && 'enable' === $params['animation_block'] ? 'enable' : 'disable' );

    // Sanitize roles array properly to avoid deprecated automatic conversion warnings
    $roles_value = array();
    if ( isset( $params['roles'] ) && is_array( $params['roles'] ) && ! empty( $params['roles'] ) ) {
        $roles_value = array_map( 'sanitize_text_field', $params['roles'] );
    }

    $bsf_options = array(
        'dev_mode'               => isset( $params['dev_mode'] ) && 'enable' === $params['dev_mode'] ? 'enable' : 'disable',
        'ultimate_global_scripts'=> isset( $params['global_scripts'] ) && 'enable' === $params['global_scripts'] ? 'enable' : 'disable',
        'ultimate_modal_menu'    => isset( $params['modal_menu'] ) && 'enable' === $params['modal_menu'] ? 'enable' : 'disable',
        'ultimate_roles'         => $roles_value,
    );

    foreach ( $bsf_options as $key => $value ) {
        bsf_update_option( $key, $value );
    }

    return rest_ensure_response( array( 'success' => true ) );
}

/**
 * Create a blank page for WPBakery editor.
 *
 * @return WP_REST_Response|WP_Error
 */
function uavc_create_blank_page() {
       $post_id = wp_insert_post(
               array(
                       'post_title'  => 'New WP Bakery Page',
                       'post_type'   => 'page',
                       'post_status' => 'draft',
               )
       );

       if ( is_wp_error( $post_id ) ) {
               return $post_id;
       }

       $query_args = array(
               'vc_action'             => 'vc_inline',
               'post_id'               => $post_id,
               'post_type'             => 'page',
               'vc_post_custom_layout' => 'blank',
       );

       $url = add_query_arg( $query_args, admin_url( 'post.php' ) );

       return rest_ensure_response( array( 'url' => $url ) );
}
