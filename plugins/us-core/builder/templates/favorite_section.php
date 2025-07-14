<?php defined( 'ABSPATH' ) or die( 'This script cannot be accessed directly.' );

/**
 * usb_skip_draggable - Skip Drag & Drop for Live Builder [optional].
 * usof_draggable - The element by which the movement occurs in the list [optional].
 */

$item_atts = array(
	'class' => 'usb-favorites-item',
	'data-search-text' => us_strtolower( $name ),
	'data-section-id' => $id,
	'data-type' => 'favorite_section',
);

?>
<div <?= us_implode_atts( $item_atts ) ?>>
	<div class="usb-favorites-item-title">
		<span><?= strip_tags( $name ) ?></span>
	</div>
	<?php if ( current_user_can( 'administrator' ) AND get_option( 'us_can_modify_favorite_sections', 1 ) ): ?>
		<div class="usb-favorites-item-actions">
			<div class="ui-icon_move usb_skip_draggable usof_draggable" title="<?= esc_attr( __( 'Drag to reorder', 'us' ) ) ?>"></div>
			<button class="ui-icon_delete usb_action_show_confirm_delete" type="button" title="<?= esc_attr( us_translate( 'Delete' ) ) ?>"></button>
		</div>
	<?php endif; ?>
</div>
