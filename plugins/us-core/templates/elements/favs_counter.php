<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Favorites Counter element
 *
 * @var $icon                string
 * @var $hide_empty          bool
 * @var $quantity_color_bg   string
 * @var $quantity_color_text string
 * @var $classes             string
 */

$_atts['class'] = 'w-favs-counter';
$_atts['class'] .= $classes ?? '';

if ( $hide_empty ) {
	$_atts['class'] .= ' hide_empty';
}
if ( $vstretch ) {
	$_atts['class'] .= ' height_full';
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

$quantity_inline_css = us_prepare_inline_css(
	array(
		'background' => us_get_color( $quantity_color_bg, /* Gradient */ TRUE ),
		'color' => us_get_color( $quantity_color_text ),
	)
);

$user_favs_quantity = count( us_get_user_favorite_post_ids() );

if ( $user_favs_quantity < 1 ) {
	$_atts['class'] .= ' empty';
}

$link_atts = us_generate_link_atts( $link );

if ( ! empty( $link_atts['href'] ) AND empty( $link_atts['title'] ) ) {
	$link_atts['aria-label'] = __( 'Link to Favorites list', 'us' );
}

$output = '<div' . us_implode_atts( $_atts ) . '>';

if ( ! empty( $link_atts['href'] ) ) {
	$output .= '<a class="w-favs-counter-link"' . us_implode_atts( $link_atts ) . ' >';
}

$output .= '<span class="w-favs-counter-icon">';

if ( ! empty( $icon ) ) {
	$output .= us_prepare_icon_tag( $icon );
}

$output .= '<span class="w-favs-counter-quantity"' . $quantity_inline_css . '>' . $user_favs_quantity . '</span>';

$output .= '</span>'; // w-favs-counter-icon

if ( ! empty( $link_atts['href'] ) ) {
	$output .= '</a>'; // w-favs-counter-link
}

$output .= '</div>'; // w-favs-counter

echo $output;
