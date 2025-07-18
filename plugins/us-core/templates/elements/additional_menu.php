<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Simple Menu element
 */

if ( empty( $source ) ) {
	$source = key( us_get_nav_menus() );
}

if (
	! is_nav_menu( $source )
	AND ! usb_is_post_preview()
) {
	return;
}

$_atts['class'] = 'w-menu';
$_atts['class'] .= $classes ?? '';

// Force horizontal layout for element in header
if ( $us_elm_context == 'header' ) {
	$layout = 'hor';
}

$_atts['class'] .= ' layout_' . $layout;
$_atts['class'] .= ( $spread ) ? ' spread' : '';

$_atts['style'] = '';

$css_styles = '';
$depth = 1;
if ( $us_elm_context == 'shortcode' ) {
	$responsive_width = trim( (string) $responsive_width );

	$_atts['class'] .= ' style_' . $main_style;
	$_atts['class'] .= empty( $responsive_width ) ? ' not_responsive' : '';

	// Fallback since version 7.1
	if ( ! empty( $align ) ) {
		$_atts['class'] .= ' align_' . $align;
	}

	// Needs to override alignment on mobiles
	if ( in_array( 'mobiles', us_design_options_has_property( $css, 'text-align' ) ) ) {
		$_atts['class'] .= ' has_text_align_on_mobiles';
	}

	// Generate unique ID for US builder preview
	if ( usb_is_post_preview() ) {
		$us_menu_id = us_uniqid();
	} else {
		global $us_menu_id;
		$us_menu_id = isset( $us_menu_id ) ? ( $us_menu_id + 1 ) : 1;
	}

	$_atts['class'] .= ' us_menu_' . $us_menu_id;

	// Add inline CSS vars
	if ( ! in_array( $main_gap, array( '', '0', '0em', '0px' ) ) ) {
		$_atts['style'] .= '--main-gap:' . $main_gap . ';';
	}
	if ( ! in_array( $main_ver_indent, array( '', '0', '0em', '0px' ) ) ) {
		$_atts['style'] .= '--main-ver-indent:' . $main_ver_indent . ';';
	}
	if ( ! in_array( $main_hor_indent, array( '', '0', '0em', '0px' ) ) ) {
		$_atts['style'] .= '--main-hor-indent:' . $main_hor_indent . ';';
	}

	// Main Items colors
	if ( $main_color_bg = us_get_color( $main_color_bg, /* Gradient */ TRUE ) AND $main_style == 'blocks' ) {
		$_atts['style'] .= '--main-bg-color:' . $main_color_bg . ';';
	}
	if ( $main_color_text = us_get_color( $main_color_text ) ) {
		$_atts['style'] .= '--main-color:' . $main_color_text . ';';
	}
	if ( $main_color_bg_hover = us_get_color( $main_color_bg_hover, /* Gradient */ TRUE ) AND $main_style == 'blocks' ) {
		$_atts['style'] .= '--main-hover-bg-color:' . $main_color_bg_hover . ';';
	}
	if ( $main_color_text_hover = us_get_color( $main_color_text_hover ) ) {
		$_atts['style'] .= '--main-hover-color:' . $main_color_text_hover . ';';
	}
	if ( $main_color_bg_active = us_get_color( $main_color_bg_active, /* Gradient */ TRUE ) AND $main_style == 'blocks' ) {
		$_atts['style'] .= '--main-active-bg-color:' . $main_color_bg_active . ';';
	}
	if ( $main_color_text_active = us_get_color( $main_color_text_active ) ) {
		$_atts['style'] .= '--main-active-color:' . $main_color_text_active . ';';
	}

	// Show Sub items
	if ( $sub_items ) {
		$depth = 0;
		$_atts['class'] .= ' with_children';

		// Gap between Sub items
		if ( ! in_array( $sub_gap, array( '', '0', '0em', '0px' ) ) ) {
			$_atts['style'] .= '--sub-gap:' . $sub_gap . ';';
		}
	}

	// Switch horizontal to vertical at screens below defined width
	if ( ! empty( $responsive_width ) ) {
		$css_styles .= '@media ( max-width:' . $responsive_width . ' ) {';
		$css_styles .= '.us_menu_' . $us_menu_id . ' .menu { display: block !important; }';
		$css_styles .= '.us_menu_' . $us_menu_id . ' .menu > li { margin: 0 0 var(--main-gap,' . $main_gap . ') !important; }';
		$css_styles .= '}';
	}
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Output the element
$output = '<nav' . us_implode_atts( $_atts ) . '>';
$output .= wp_nav_menu(
	array(
		'menu' => $source,
		'container' => FALSE,
		'depth' => $depth,
		'item_spacing' => 'discard',
		'echo' => FALSE,
	)
);
if ( ! empty( $css_styles ) ) {
	$output .= '<style>' . us_minify_css( $css_styles ) . '</style>';
}
$output .= '</nav>';

echo $output;
