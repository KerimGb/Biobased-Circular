<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output html element
 *
 * @var $content        string
 * @var $design_options array
 * @var $classes        string
 * @var $id             string
 */

$_atts['class'] = 'w-html';
$_atts['class'] .= $classes ?? '';

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

$content = rawurldecode( base64_decode( $content ) );
$content = us_replace_dynamic_value( $content );

echo '<div' . us_implode_atts( $_atts ) . '>';
echo do_shortcode( $content );
echo '</div>';
