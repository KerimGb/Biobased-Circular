<?php
/**
 * Backend editor template.
 *
 * @var Vc_Backend_Editor $editor
 * @var WP_Post $post
 * @var bool $wpb_vc_status
 * @var string $wpb_vc_editor_type
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// [shortcodes presets data]
if ( vc_user_access()->part( 'presets' )->can()->get() ) {
	require_once vc_path_dir( 'AUTOLOAD_DIR', 'class-vc-settings-presets.php' );
	$vc_all_presets = Vc_Settings_Preset::listAllPresets();
} else {
	$vc_all_presets = [];
}
// [/shortcodes presets data]
global $wp_version;
$custom_tag = 'script'; // TODO: Use ajax for variables.
$is_gutenberg = version_compare( $wp_version, '4.9.8', '>' ) && ! get_option( 'wpb_js_gutenberg_disable' );
$modules = vc_modules_manager()->get_settings();
if ( $is_gutenberg ) {
	$is_gutenberg = get_post_type_object( get_post_type() )->show_in_rest;
}
?>
	<<?php echo esc_attr( $custom_tag ); ?>>
		window.vc_all_presets = <?php echo wp_json_encode( $vc_all_presets ); ?>;
		window.vc_post_id = <?php echo esc_js( get_the_ID() ); ?>;
		window.wpbGutenbergEditorUrl = '<?php echo esc_js( set_url_scheme( admin_url( 'post-new.php?post_type=wpb_gutenberg_param' ) ) ); ?>';
		window.wpbGutenbergEditorSwitchUrl = '<?php echo esc_js( set_url_scheme( admin_url( 'post.php?post=' . get_the_ID() . '&action=edit&vcv-gutenberg-editor' ) ) ); ?>';
		window.wpbGutenbergEditorClassicSwitchUrl = '<?php echo esc_js( set_url_scheme( admin_url( 'post.php?post=' . get_the_ID() . '&action=edit&wpb-classic-editor' ) ) ); ?>';
		window.wpbGutenbergEditorBackendSwitchUrl = '<?php echo esc_js( set_url_scheme( admin_url( 'post.php?post=' . get_the_ID() . '&action=edit&wpb-backend-editor' ) ) ); ?>';
		window.wpbIsGutenberg = <?php echo $is_gutenberg ? 'true' : 'false'; ?>;
		window.vc_auto_save = <?php echo wp_json_encode( get_option( 'wpb_js_auto_save' ) ); ?>;
		window.vc_modules = <?php echo wp_json_encode( $modules ); ?>;
	</<?php echo esc_attr( $custom_tag ); ?>>

<?php
require_once vc_path_dir( 'EDITORS_DIR', 'navbar/class-vc-navbar.php' );
$nav_bar = new Vc_Navbar( $post );
$nav_bar->render();
$first_tag = 'style';
?>
	<style>
		#wpb_wpbakery {
			display: none;
		}
	</style>
	<div class="metabox-composer-content">
		<div id="wpbakery_content" class="wpb_main_sortable main_wrapper"></div>
		<?php
		vc_include_template(
			'editors/partials/vc_welcome_block.tpl.php',
			[ 'editor' => 'backend' ]
		);
		?>
	</div>

<?php
if ( ! vc_is_gutenberg_editor() ) {
	?>
	<input type="hidden" id="wpb_vc_js_status" name="wpb_vc_js_status" value="<?php echo esc_attr( wp_json_encode( $wpb_vc_status ) ); ?>"/>
	<input type="hidden" id="wpb_vc_editor_type" name="wpb_vc_editor_type" value="<?php echo esc_attr( $wpb_vc_editor_type ); ?>"/>
	<?php
}
?>
<input type="hidden" id="wpb_js_google_fonts_save_nonce" name="wpb_js_google_fonts_save_nonce" value="<?php echo esc_js( wp_create_nonce( 'wpb_js_google_fonts_save' ) ); ?>"/>

<input type="hidden" id="wpb_vc_loading" name="wpb_vc_loading"
		value="<?php esc_attr_e( 'Loading, please wait...', 'js_composer' ); ?>"/>
<input type="hidden" id="wpb_vc_loading_row" name="wpb_vc_loading_row"
		value="<?php esc_attr_e( 'Crunching...', 'js_composer' ); ?>"/>

<?php
vc_include_template(
	'editors/partials/vc_post_custom_meta.tpl.php',
	[ 'editor' => $editor ]
);
?>

<div id="vc_preloader" style="display: none;"></div>
<div id="vc_overlay_spinner" class="vc_ui-wp-spinner vc_ui-wp-spinner-dark vc_ui-wp-spinner-lg" style="display:none;"></div>
<?php
vc_include_template( 'editors/partials/access-manager-js.tpl.php' );
