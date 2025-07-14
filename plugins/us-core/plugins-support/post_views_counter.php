<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Post Views Counter Support
 *
 * https://wordpress.org/plugins/post-views-counter/
 */

if ( ! class_exists( 'Post_Views_Counter' ) ) {
	return;
}

if ( ! function_exists( 'us_pvc_enqueue_styles' ) ) {
	add_filter( 'pvc_enqueue_styles', 'us_pvc_enqueue_styles', 100 );
	/**
	 * Removing styles from the Post Views counter plugin
	 *
	 * @return bool
	 */
	function us_pvc_enqueue_styles() {
		if ( us_get_option( 'optimize_assets' ) AND is_plugin_active( 'post-views-counter/post-views-counter.php' ) ) {
			return FALSE;
		}
		return TRUE;
	}
}

if ( ! function_exists( 'us_pvc_get_list_orderby_params' ) ) {
	add_filter( 'us_get_list_orderby_params', 'us_pvc_get_list_orderby_params' );
	/**
	 * Append Events params to List Orderby options
	 */
	function us_pvc_get_list_orderby_params( $params ) {
		$params += array(
			'post_views' => array(
				'label' => __( 'Total views', 'us' ),
				'group' => us_translate( 'Post Attributes' ),
			),
			'post_views_today' => array(
				'label' => __( 'Views today', 'us' ),
				'group' => us_translate( 'Post Attributes' ),
			),
			'post_views_this_week' => array(
				'label' => __( 'Views this week', 'us' ),
				'group' => us_translate( 'Post Attributes' ),
			),
			'post_views_this_month' => array(
				'label' => __( 'Views this month', 'us' ),
				'group' => us_translate( 'Post Attributes' ),
			),
			'post_views_this_year' => array(
				'label' => __( 'Views this year', 'us' ),
				'group' => us_translate( 'Post Attributes' ),
			),
		);
		return $params;
	}
}

if ( ! function_exists( 'us_pvc_product_list_orderby_options' ) ) {
	add_filter( 'us_product_list_orderby_options', 'us_pvc_product_list_orderby_options', 501, 1 );
	/**
	 * Expand "orderby" options in Post List element.
	 *
	 * @param array $options The options list.
	 * @return array Returns a list of "orderby" options.
	 */
	function us_pvc_product_list_orderby_options( $options ) {
		$options += array(
			'post_views' => __( 'Total views', 'us' ),
			'post_views_today' => __( 'Views today', 'us' ),
			'post_views_this_week' => __( 'Views this week', 'us' ),
			'post_views_this_month' => __( 'Views this month', 'us' ),
			'post_views_this_year' => __( 'Views this year', 'us' ),
		);
		// Custom option is always at the end
		if ( isset( $options['custom'] ) ) {
			$custom = $options['custom'];
			unset( $options['custom'] );
			$options['custom'] = $custom;
		}
		return $options;
	}
}

if ( ! function_exists( 'us_pvc_apply_orderby_to_list_query' ) ) {
	add_filter( 'us_apply_orderby_to_list_query', 'us_pvc_apply_orderby_to_list_query', 10, 2 );
	/**
	 * Modify the database query to order by post_views
	 */
	function us_pvc_apply_orderby_to_list_query( $query_args, $orderby ) {

		// This year
		if ( $orderby == 'post_views_this_year' ) {
			$query_args['orderby'] = 'post_views';
			$query_args['views_query'] = array(
				'year' => wp_date( 'Y' ),
			);

			// This month
		} elseif ( $orderby == 'post_views_this_month' ) {
			$query_args['orderby'] = 'post_views';
			$query_args['views_query'] = array(
				'year' => wp_date( 'Y' ),
				'month' => wp_date( 'm' ),
			);

			// This week
		} elseif ( $orderby == 'post_views_this_week' ) {
			$query_args['orderby'] = 'post_views';
			$query_args['views_query'] = array(
				'year' => wp_date( 'Y' ),
				'week' => wp_date( 'W' ),
			);

			// Today
		} elseif ( $orderby == 'post_views_today' ) {
			$query_args['orderby'] = 'post_views';
			$query_args['views_query'] = array(
				'year' => wp_date( 'Y' ),
				'month' => wp_date( 'm' ),
				'day' => wp_date( 'd' ),
			);
		}

		// Prevent Post Views Counter from hiding posts with 0 views
		if ( strpos( $query_args['orderby'], 'post_views' ) !== FALSE ) {
			$query_args['views_query'] = $query_args['views_query'] ?? array();
			$query_args['views_query'] = array_merge(
				$query_args['views_query'],
				array(
					'hide_empty' => FALSE,
				)
			);
		}

		return $query_args;
	}
}
