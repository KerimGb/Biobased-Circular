<?php
/**
 * Admin Base HTML.
 *
 * @package ultimate_vc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Process license activation when redirected from Envato.
if ( isset( $_GET['license_action'] ) && 'activate_license' === $_GET['license_action'] && class_exists( 'BSF_Envato_Activate' ) ) {
	new BSF_Envato_Activate();
}

?>
<div class="uavc-menu-page-wrapper">
	<div id="uavc-menu-page">
		<div class="uavc-menu-page-content uavc-clear">
			<?php
				do_action( 'uavc_render_admin_page_content', $menu_page_slug, $page_action );
			?>
		</div>
	</div>
</div>
