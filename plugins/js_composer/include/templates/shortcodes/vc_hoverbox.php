<?php
/**
 * The template for displaying [vc_hoverbox] shortcode output of 'Hover Box' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_hoverbox.php.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 *
 * @var WPBakeryShortCode_Vc_Hoverbox $this
 * @var array $atts
 * @var string $content
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

if ( ! empty( $atts['image'] ) ) {
	$image = intval( $atts['image'] );
	$image_data = wp_get_attachment_image_src( $image, 'large' );
	$image_src = $image_data[0];
} else {
	$image_src = vc_asset_url( 'vc/no_image.png' );
}
$image_src = esc_url( $image_src );

$align = 'vc-hoverbox-align--' . esc_attr( $atts['align'] );
$shape = 'vc-hoverbox-shape--' . esc_attr( $atts['shape'] );
$width = 'vc-hoverbox-width--' . esc_attr( $atts['el_width'] );
$reverse = 'vc-hoverbox-direction--default';
if ( ! empty( $atts['reverse'] ) ) {
	$reverse = 'vc-hoverbox-direction--reverse';
}
$id = '';
if ( ! empty( $atts['el_id'] ) ) {
	$id = 'id="' . esc_attr( $atts['el_id'] ) . '"';
}

$class_to_filter = vc_shortcode_custom_css_class( $atts['css'], ' ' ) . $this->getExtraClass( $atts['el_class'] ) . $this->getCSSAnimation( $atts['css_animation'] );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );
$css_class = esc_attr( $css_class );

// Hover Background color.
if ( 'custom' !== $atts['hover_background_color'] ) {
	$hover_background_color = vc_convert_vc_color( $atts['hover_background_color'] );
} else {
	$hover_background_color = esc_attr( $atts['hover_custom_background'] );
}

$primary_title = wp_kses_post( $this->getHeading( 'primary_title', $atts, $atts['primary_align'] ) );
$hover_title = wp_kses_post( $this->getHeading( 'hover_title', $atts, $atts['hover_align'] ) );

$content = wpb_js_remove_wpautop( $content, true );
$button = '';
if ( $atts['hover_add_button'] ) {
	$button = $this->renderButton( $atts );
}
$template = <<<HTML
<div class="vc-hoverbox-wrapper $css_class $shape $align $reverse $width" $id ontouchstart="">
  <div class="vc-hoverbox">
    <div class="vc-hoverbox-inner">
      <div class="vc-hoverbox-block vc-hoverbox-front" style="background-image: url($image_src);">
        <div class="vc-hoverbox-block-inner vc-hoverbox-front-inner">
            $primary_title
        </div>
      </div>
      <div class="vc-hoverbox-block vc-hoverbox-back" style="background-color: $hover_background_color;">
        <div class="vc-hoverbox-block-inner vc-hoverbox-back-inner">
            $hover_title
            $content
            $button
        </div>
      </div>
    </div>
  </div>
</div>
HTML;

return $template;
