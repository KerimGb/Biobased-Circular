<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * AJAX pagination for the Post List shortcode
 */
if ( ! function_exists( 'us_ajax_post_list' ) ) {
	add_action( 'wp_ajax_nopriv_us_ajax_post_list', 'us_ajax_post_list' );
	add_action( 'wp_ajax_us_ajax_post_list', 'us_ajax_post_list' );

	function us_ajax_post_list() {
		if ( ! check_ajax_referer( 'us_post_list', '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
		}

		add_filter( 'us_get_current_id', 'us_get_current_id_from_list_ajax' );

		$template_vars = us_get_HTTP_POST_json( 'template_vars' );

		// Exclude posts of previous lists
		if ( isset( $template_vars['us_post_ids_shown_by_grid'] ) ) {
			global $us_post_ids_shown_by_grid;
			$us_post_ids_shown_by_grid = array_map( 'intval', (array) $template_vars['us_post_ids_shown_by_grid'] );
		}

		$template_vars = us_shortcode_atts( $template_vars, 'us_post_list' );

		if ( isset( $_POST['paged'] ) ) {
			$template_vars['paged'] = (int) $_POST['paged'];
		}
		if ( ! empty( $_POST['_s'] ) ) {
			$template_vars['list_search'] = (string) $_POST['_s'];
		}
		if ( ! empty( $_POST['_orderby'] ) ) {
			$template_vars['list_order'] = (string) $_POST['_orderby'];
		}
		if ( $list_filter = us_get_filter_params_from_request() ) {
			$template_vars['list_filter'] = $list_filter;
		}

		us_load_template( 'templates/elements/post_list', $template_vars );

		die;
	}
}

/**
 * AJAX pagination for the Product List shortcode
 */
if ( ! function_exists( 'us_ajax_product_list' ) ) {
	add_action( 'wp_ajax_nopriv_us_ajax_product_list', 'us_ajax_product_list' );
	add_action( 'wp_ajax_us_ajax_product_list', 'us_ajax_product_list' );

	function us_ajax_product_list() {
		if ( ! check_ajax_referer( 'us_product_list', '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
		}

		add_filter( 'us_get_current_id', 'us_get_current_id_from_list_ajax' );

		$template_vars = us_get_HTTP_POST_json( 'template_vars' );

		// Exclude posts of previous lists
		if ( isset( $template_vars['us_post_ids_shown_by_grid'] ) ) {
			global $us_post_ids_shown_by_grid;
			$us_post_ids_shown_by_grid = array_map( 'intval', (array) $template_vars['us_post_ids_shown_by_grid'] );
		}

		$template_vars = us_shortcode_atts( $template_vars, 'us_product_list' );

		if ( isset( $_POST['paged'] ) ) {
			$template_vars['paged'] = (int) $_POST['paged'];
		}
		if ( ! empty( $_POST['_s'] ) ) {
			$template_vars['list_search'] = (string) $_POST['_s'];
		}
		if ( ! empty( $_POST['_orderby'] ) ) {
			$template_vars['list_order'] = (string) $_POST['_orderby'];
		}
		if ( $list_filter = us_get_filter_params_from_request() ) {
			$template_vars['list_filter'] = $list_filter;
		}

		us_load_template( 'templates/elements/product_list', $template_vars );

		die;
	}
}

if ( ! function_exists( 'us_get_current_id_from_list_ajax' ) ) {
	/**
	 * Get current id from ajax requests of Post/Product List elements
	 */
	function us_get_current_id_from_list_ajax( $current_id ) {
		if ( $current_id < 1 AND isset( $_POST['object_id'] ) ) {
			return (int) $_POST['object_id'];
		}
		return $current_id;
	}
}

if ( ! function_exists( 'us_list_filter_post_count' ) ) {
	add_action( 'wp_ajax_nopriv_us_list_filter_post_count', 'us_list_filter_post_count' );
	add_action( 'wp_ajax_us_list_filter_post_count', 'us_list_filter_post_count' );

	/**
	 * Get post count for all List Filter values on page load
	 */
	function us_list_filter_post_count() {

		if ( ! check_ajax_referer( __FUNCTION__, '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
		}

		$query_args = us_get_HTTP_POST_json( 'query_args' );
		$list_filters = us_get_HTTP_POST_json( 'list_filters' );

		$results = us_list_filter_get_post_count( $query_args, $list_filters );
		if ( $results ) {
			wp_send_json_success( $results );
		}

		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Failed to load indexes for filters.' ),
			)
		);
	}
}
