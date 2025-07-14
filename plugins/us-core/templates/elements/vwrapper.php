<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Vertical Wrapper
 */

$_atts['class'] = 'w-vwrapper';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' align_' . $alignment;
$_atts['class'] .= ' valign_' . $valign;

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}
if ( trim( (string) $inner_items_gap ) != '0.7rem' ) {
	$_atts['style'] = '--vwrapper-gap:' . $inner_items_gap;
}

// Link
$link_html = '';
$link_atts = us_generate_link_atts( $link );
if ( ! empty( $link_atts['href'] ) AND ! usb_is_post_preview() ) {
	$_atts['class'] .= ' has-link';
	$link_atts['class'] = 'w-vwrapper-link smooth-scroll';

	// Add aria-label, if title is empty to avoid accessibility issues
	if ( empty( $link_atts['title'] ) ) {
		$link_atts['aria-label'] = us_translate( 'Link' );
	}
	$link_html = '<a' . us_implode_atts( $link_atts ) . '></a>';
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= do_shortcode( $content );
$output .= $link_html;
$output .= '</div>';

echo $output;
