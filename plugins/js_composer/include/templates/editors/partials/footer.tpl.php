<?php
/**
 * Editor footer template.
 *
 * @var Vc_Backend_Editor | Vc_Frontend_Editor $editor
 * @var WP_Post $post
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// [add element popup/box]
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-add-element-box.php' );
$add_element_box = new Vc_Add_Element_Box();
$add_element_box->render();
// [/add element popup/box]

// [shortcodes edit form panel render]
wpbakery()->editForm()->render();
// [/shortcodes edit form panel render]

// [templates panel editor render]
if ( vc_user_access()->part( 'templates' )->can()->get() ) {
	wpbakery()->templatesPanelEditor()->renderUITemplate();
}
// [/templates panel editor render]

// [preset panel editor render]
wpbakery()->presetPanelEditor()->renderUIPreset();
// [/preset panel editor render]

// [post settings panel render]
if ( vc_user_access()->part( 'post_settings' )->can()->get() ) {
	require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-post-settings.php' );
	$post_settings = new Vc_Post_Settings( $editor, $post );
	$post_settings->renderUITemplate();
}
// [/post settings panel render]

// [custom css/js panel render]
if ( vc_user_access()->part( 'post_settings' )->can()->get() ) {
	require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-custom-code.php' );
	$custom_code = new Vc_Custom_Code( $editor, $post );
	$custom_code->renderUITemplate();
}
// [/custom css/js panel render]

// [panel edit layout render]
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-edit-layout.php' );
$edit_layout = new Vc_Edit_Layout();
$edit_layout->renderUITemplate();
// [/panel edit layout render]

// [modal ai render]
vc_include_template( 'editors/popups/ai/modal.tpl.php' );
// [/modal ai render]

// [promo popup render]
require_once vc_path_dir( 'EDITORS_DIR', 'popups/class-vc-promo-popup.php' );
$promo_popup = new Vc_Promo_Popup();
$promo_popup->render_ui_template();
// [/promo popup render]

/**
 * Fires after editor footer.
 *
 * @param Vc_Backend_Editor | Vc_Frontend_Editor $editor
 *
 * @since 7.7
 */
do_action( 'vc_editor_footer', $editor );
