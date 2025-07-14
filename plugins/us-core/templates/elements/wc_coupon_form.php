<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Coupon Form
 * Note: This element can be used for both Cart and Checkout pages
 *
 * @var string $placeholder - Placeholder
 * @var string $btn_label - Button Label
 * @var string $btn_style - Button Style
 *
 * @see https://github.com/woocommerce/woocommerce/blob/trunk/templates/cart/cart.php
 * @version 7.9.0
 * @see https://github.com/woocommerce/woocommerce/blob/trunk/templates/checkout/form-coupon.php
 * @version 7.0.1
 */

if ( ! class_exists( 'woocommerce' ) ) {
	return;
}

if ( ! usb_is_post_preview() ) {
	if (
		is_null( WC()->cart )
		OR WC()->cart->is_empty()
		OR ! wc_coupons_enabled()
	) {
		return;
	}
}

$_atts['class'] = 'w-wc-coupon-form';
$_atts['class'] .= $classes ?? '';

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Define Checkout or Cart page
if ( is_checkout() ) {
	$_atts['class'] .= ' is_checkout';

} elseif ( is_cart() ) {
	$_atts['class'] .= ' is_cart';
}

if ( ! empty( WC()->cart->get_coupons() ) ) {
	$_atts['class'] .= ' coupon_applied';
}

$input_atts = array(
	'class' => 'input-text',
	'type' => 'text',
	'value' => '',
	'placeholder' => $placeholder,
);
?>
<div<?php echo us_implode_atts( $_atts ) ?>>
	<div class="woocommerce-form-coupon coupon">
		<label class="screen-reader-text"><?= esc_html( us_translate( 'Coupon:', 'woocommerce' ) ) ?></label>
		<input<?= us_implode_atts( $input_atts ) ?>/>
		<button class="w-btn <?= us_get_btn_class( $btn_style ) ?>" type="submit" name="apply_coupon">
			<span class="w-btn-label"><?= esc_html( $btn_label ) ?></span>
		</button>
		<?php is_cart() ? do_action( 'woocommerce_cart_coupon' ) : '' ?>
	</div>
</div>
