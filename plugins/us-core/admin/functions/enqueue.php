<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'us_code_editor_enqueue_scripts' ) ) {
	add_action( 'admin_enqueue_scripts', 'us_code_editor_enqueue_scripts' );

	function us_code_editor_enqueue_scripts() {
		global $pagenow;
		if (
			$pagenow == 'post.php'
			AND isset( $_GET['post'] )
			AND in_array( get_post_type( $_GET['post'] ), array( 'us_header', 'us_grid_layout' ) )
			AND function_exists( 'wp_enqueue_code_editor' )
		) {
			wp_enqueue_code_editor( array(
				'type' => 'text/html',
				// https://codemirror.net/doc/manual.html#config
				'codemirror' => array(
					'viewportMargin' => 100,
					'lineWrapping' => TRUE,
				),
			) );
		}
	}
}

if ( ! function_exists( 'us_admin_inline_js' ) ) {
	add_action( 'admin_print_scripts', 'us_admin_inline_js' );

	function us_admin_inline_js() {
		global $pagenow;

		// Outputs the current identifier for JS scripts
		if ( $pagenow == 'post.php' AND isset( $_GET['post'] ) ) {
			$post_id = (int) $_GET['post'];
			echo "<script>window.usCurrentPostId = $post_id;</script>";
		}
	}
}

if ( ! function_exists( 'us_hide_gutenberg_switch_button' ) ) {
	add_action( 'admin_head', 'us_hide_gutenberg_switch_button' );

	function us_hide_gutenberg_switch_button() {
		if ( ! us_get_option( 'block_editor' ) ) {
			echo '<style>.wpb_switch-to-gutenberg{display:none!important;}</style>';
		}

	}
}
