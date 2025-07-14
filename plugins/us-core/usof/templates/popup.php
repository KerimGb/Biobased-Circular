<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Popup.
 *
 * @var string $id
 * @var string $title
 * @var string $description
 * @var string $content
 * @var string $classes
 */

$_atts = array(
	'class' => 'usof-popup',
	'data-popup-id' => $id ?? us_uniqid( /* length */6 ),
);
if ( isset( $classes ) ) {
	$_atts['class'] .= sprintf( ' %s', $classes );
}

// Output popup
$output ='<div ' . us_implode_atts( $_atts ) . '>';

// Popup header
$output .= '<div class="usof-popup-header">';
$output .= '<div class="usof-popup-header-title">' . strip_tags( $title ) . '</div>';
$output .= '<button type="button" class="usof-popup-close ui-icon_close" title="' . esc_attr( us_translate( 'Close' ) ) . '"></button>';
$output .= '</div>'; // .usof-popup-header

// Popup body
$output .= '<div class="usof-popup-body">' . ( $content ?? '' ) . '</div>';

$output .= '<div class="usof-preloader"></div>';
$output .= '</div>'; // .usof-popup

echo $output;
