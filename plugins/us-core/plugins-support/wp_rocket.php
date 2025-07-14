<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'rocket_init' ) ) {
	return FALSE;
}

if ( ! function_exists( 'us_exclude_delayed_assets' ) ) {

	add_filter( 'rocket_delay_js_exclusions', 'us_exclude_delayed_assets' );

	/**
	 * Exclude theme assets from "Delay JavaScript execution"
	 */
	function us_exclude_delayed_assets( $excluded ) {
		$exclude = array(
			'/jquery-?[0-9.](.*)(.min|.slim|.slim.min)?.js',
			'maps.googleapis.com',
			'us_add_no_touch',
			'us-core-js',
		);

		if ( us_get_option( 'optimize_assets' ) ) {
			$exclude[] = us_get_asset_file( 'js', TRUE );
		} else {
			$exclude[] = 'us.core.min.js';
		}

		return array_merge( $excluded, $exclude );
	}
}

if ( ! function_exists( 'us_skip_cache_until_conditions' ) ) {

	add_filter( 'us_conditional_param_result', 'us_skip_cache_until_conditions', 501, 1 );

	/**
	 * Skip cache until conditions are met
	 *
	 * @return bool Returns true if conditions are met
	 */
	function us_skip_cache_until_conditions( $conditions_are_met ) {

		if ( ! $conditions_are_met AND ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', TRUE );
		}

		return $conditions_are_met;
	}
}
