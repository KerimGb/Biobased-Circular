<?php
/**
 * The template for displaying [vc_gmaps] shortcode output of 'Google Maps' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_gmaps.php.
 *
 * @see https://kb.wpbakery.com/docs/developers-how-tos/change-shortcodes-html-output
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 *
 * @var $atts
 * @var $title
 * @var $link
 * @var $size
 * @var $el_class
 * @var $el_id
 * @var $css
 * @var $css_animation
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Gmaps $this
 */
$title = $link = $size = $el_class = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$zoom = 14; // deprecated 4.0.2. In 4.6 was moved outside from shortcode_atts.
$type = 'm'; // deprecated 4.0.2.
$bubble = ''; // deprecated 4.0.2.

if ( '' === $link ) {
	return null;
}
$link = trim( vc_value_from_safe( $link ) );
$bubble = ( '' !== $bubble && '0' !== $bubble ) ? '&amp;iwloc=near' : '';
$size = str_replace( [
	'px',
	' ',
], [
	'',
	'',
], $size );

if ( is_numeric( $size ) ) {
	$link = preg_replace( '/height="[0-9]*"/', 'height="' . $size . '"', $link );
}

$element_class = empty( $this->settings['element_default_class'] ) ? '' : $this->settings['element_default_class'];
$class_to_filter = 'wpb_gmaps_widget ' . esc_attr( $element_class ) . ( '' === $size ? ' vc_map_responsive' : '' );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$output = '<div class="' . esc_attr( $css_class ) . '"' . ( ! empty( $el_id ) ? ' id="' . esc_attr( $el_id ) . '"' : '' ) . '>';
$output .= wpb_widget_title( [
	'title' => $title,
	'extraclass' => 'wpb_map_heading',
] );

$output .= '<div class="wpb_wrapper"><div class="wpb_map_wraper">';
if ( preg_match( '/^\<iframe/', $link ) ) {
	$output .= $link;
} else {
	// TODO: refactor or remove outdated/deprecated attributes that is not mapped in gmaps.
	$output .= '<iframe width="100%" height="' . esc_attr( $size ) . '" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="' . esc_url( $link ) . '&amp;t=' . esc_attr( $type ) . '&amp;z=' . esc_attr( $zoom ) . '&amp;output=embed' . esc_attr( $bubble ) . '"></iframe>';
}
$output .= '</div></div></div>';

return $output;
