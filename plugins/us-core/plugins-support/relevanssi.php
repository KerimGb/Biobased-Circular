<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Relevanssi – A Better Search
 *
 * https://wordpress.org/plugins/relevanssi
 */

if ( ! function_exists( 'relevanssi_init' ) ) {
	return;
}

if ( ! function_exists( 'us_relevanssi_search_for_post_list' ) ) {

	add_filter( 'relevanssi_search_ok', 'us_relevanssi_search_for_post_list', 501, 2 );

	/**
	 * Relevanssi search for Post/Product List
	 *
	 * @param bool $search_ok
	 * @param WP_Query|FALSE $wp_query
	 *
	 * @return bool
	 */
	function us_relevanssi_search_for_post_list( $search_ok, $wp_query ) {

		if ( $wp_query instanceof WP_Query AND $wp_query->get( 'apply_list_url_params' ) AND $wp_query->get( 's' ) ) {
			$search_ok = TRUE;
		}

		return $search_ok;
	}
}
