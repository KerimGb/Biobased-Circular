<?php
/**
 * UAVC Settings API.
 *
 * @package UAVC
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class UAVC_Settings_Api.
 */
class UAVC_Settings_Api {

	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.2.1
	 */
	private static $instance;

	/**
	 * Get the singleton instance of the class.
	 *
	 * @return UAVC_Settings_Api
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 2.2.1
	 * @return void
	 */
	private function __construct() {
		// Log an error message to check if the file is loading.

		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register REST API routes.
	 *
	 * @since 2.2.1
	 * @return void
	 */
	public function register_routes() { 

		register_rest_route(
			'uavc/v1',
			'/plugins',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_plugins_list' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
			]
		);

		register_rest_route(
			'uavc/v1',
			'/modules',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_uavc_modules' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
			]
		);
	}

	/**
	 * Check whether a given request has permission to read notes.
	 *
	 * @since 2.2.1
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error( 'uavc_rest_not_allowed', __( 'Sorry, you are not authorized to perform this action.', 'ultimate_vc' ), [ 'status' => 403 ] );
		}

		return true;
	}

	/**
	 * 
	 * Callback function to return widgets list.
	 * 
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_uavc_modules( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );
	
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error( 'invalid_nonce', __( 'Invalid nonce', 'ultimate_vc' ), [ 'status' => 403 ] );
		}
	
		// Get all modules from the modules directory
		$modules_list = $this->get_all_modules_list();
	
		if ( ! is_array( $modules_list ) || empty( $modules_list ) ) {
			return new WP_REST_Response( [ 'message' => __( 'Modules list not found', 'ultimate_vc' ) ], 404 );
		}
	
		return new WP_REST_Response( $modules_list, 200 );
	}

	/**
	 * Get module data from file.
	 *
	 * @param string $file Module file path.
	 * @return array|false Module data or false if not found.
	 */
	private function get_module_data( $file ) {
		if ( ! file_exists( $file ) ) {
			return false;
		}

		$filename = basename( $file );
		$module_slug = str_replace( '.php', '', $filename );
		$module_name = ucwords( str_replace( '_', ' ', str_replace( 'ultimate_', '', $module_slug ) ) );
		
		// Check if module is active
		$active_modules = array();
		if ( function_exists( 'get_option' ) ) {
			$active_modules = get_option( 'ultimate_modules', array() );
		}
		// $is_active = isset( $active_modules[ $module_slug ] ) ? $active_modules[ $module_slug ] === 'enable' : false;
		
		// Get file contents to extract description if available
		$file_content = file_get_contents( $file );
		$description = '';
		
		// Try to extract description from file header comments
		if ( preg_match( '/\*\s*Description:\s*(.+?)[\r\n]/', $file_content, $matches ) ) {
			$description = trim( $matches[1] );
		} else {
			$description = sprintf( __( '%s Module', 'ultimate_vc' ), $module_name );
		}
		
		return array(
			'id'          => $module_slug,
			'name'        => $module_name,
			'file'        => $filename,
			'description' => $description,
			'active'      => $is_active,
		);
	}

    /**
	 * Callback function to return plugins list.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_plugins_list( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );

		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error( 'invalid_nonce', __( 'Invalid nonce', 'ultimate_vc' ), [ 'status' => 403 ] );
		}

		// Fetch branding settings.
		$plugins_list = get_bsf_plugins_list();

		if ( ! is_array( $plugins_list ) ) {
			return new WP_REST_Response( [ 'message' => __( 'Plugins list not found', 'ultimate_vc' ) ], 404 );
		}

		return new WP_REST_Response( $plugins_list, 200 );
		
	}
	
}

// Initialize the UAVC_Settings_Api class.
UAVC_Settings_Api::get_instance();
