<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Activation for Advanced Custom Fields PRO
 *
 * include plugin_dir_path( __FILE__ ) . 'acf-pro-activation.php';
 */

if ( ! function_exists( '_option_acf_pro_license' ) ) {
	add_filter( 'option_acf_pro_license', '_option_acf_pro_license', 501 );
	function _option_acf_pro_license() {
		return base64_encode(
			serialize(
				array(
					'key' => 'unlimited_license',
					'url' => site_url(),
				)
			)
		);
	}
}

if ( ! function_exists( '_option_acf_pro_license_status' ) ) {
	add_filter( 'option_acf_pro_license_status', '_option_acf_pro_license_status', 501 );
	function _option_acf_pro_license_status() {
		return array(
			'status' => 'active',
			'created' => time(),
			'expiry' => time() + 10*YEAR_IN_SECONDS,
			'name' => '', // 'Agency',
			'lifetime' => FALSE,
			'refunded' => FALSE,
			'view_licenses_url' => site_url(),
			'manage_subscription_url' => site_url(),
			'error_msg' => '',
			'next_check' => time() + 10*YEAR_IN_SECONDS,
			'legacy_multisite' => TRUE,
		);
	}
}

add_action( 'init', static function() {
	if ( get_option( 'acf_pro_license' ) === FALSE ) {
		update_option( 'acf_pro_license', '' );
	}
	if (
		get_option( 'acf_pro_license_status' ) === FALSE
		OR get_option( 'acf_pro_license_status' ) === ''
	) {
		update_option( 'acf_pro_license_status', array() ); // must be array to avoid fatal error
	}

	remove_action( 'admin_init', 'acf_pro_check_defined_license', 20 );
	remove_action( 'admin_init', 'acf_pro_maybe_reactivate_license', 25 );
	remove_action( 'current_screen', 'acf_pro_display_activation_error', 30 );
}, 501 );

add_action( 'admin_menu', static function() {
	remove_submenu_page( 'edit.php?post_type=acf-field-group', 'acf-settings-updates' );
}, 501 );
