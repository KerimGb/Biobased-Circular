<?php
/**
 * The template for displaying [vc_line_chart] shortcode output of 'Line Chart' element.
 *
 * This template can be overridden by copying it to yourtheme/vc_templates/vc_line_chart.php.
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
 * @var $el_class
 * @var $el_id
 * @var $type
 * @var $style
 * @var $legend
 * @var $animation
 * @var $tooltips
 * @var $x_values
 * @var $values
 * @var $css
 * @var $css_animation
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Line_Chart $this
 */
$el_class = $el_id = $title = $type = $legend = $style = $tooltips = $animation = $x_values = $values = $css = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$base_colors = [
	'normal' => [
		'blue' => '#5472d2',
		'turquoise' => '#00c1cf',
		'pink' => '#fe6c61',
		'violet' => '#8d6dc4',
		'peacoc' => '#4cadc9',
		'chino' => '#cec2ab',
		'mulled-wine' => '#50485b',
		'vista-blue' => '#75d69c',
		'orange' => '#f7be68',
		'sky' => '#5aa1e3',
		'green' => '#6dab3c',
		'juicy-pink' => '#f4524d',
		'sandy-brown' => '#f79468',
		'purple' => '#b97ebb',
		'black' => '#2a2a2a',
		'grey' => '#ebebeb',
		'white' => '#ffffff',
		'default' => '#f7f7f7',
		'primary' => '#0088cc',
		'info' => '#58b9da',
		'success' => '#6ab165',
		'warning' => '#ff9900',
		'danger' => '#ff675b',
		'inverse' => '#555555',
	],
	'active' => [
		'blue' => '#3c5ecc',
		'turquoise' => '#00a4b0',
		'pink' => '#fe5043',
		'violet' => '#7c57bb',
		'peacoc' => '#39a0bd',
		'chino' => '#c3b498',
		'mulled-wine' => '#413a4a',
		'vista-blue' => '#5dcf8b',
		'orange' => '#f5b14b',
		'sky' => '#4092df',
		'green' => '#5f9434',
		'juicy-pink' => '#f23630',
		'sandy-brown' => '#f57f4b',
		'purple' => '#ae6ab0',
		'black' => '#1b1b1b',
		'grey' => '#dcdcdc',
		'white' => '#f0f0f0',
		'default' => '#e8e8e8',
		'primary' => '#0074ad',
		'info' => '#3fafd4',
		'success' => '#59a453',
		'warning' => '#e08700',
		'danger' => '#ff4b3c',
		'inverse' => '#464646',
	],
];
$colors = [
	'flat' => [
		'normal' => $base_colors['normal'],
		'active' => $base_colors['active'],
	],
];
foreach ( $base_colors['normal'] as $name => $color ) {
	$colors['modern']['normal'][ $name ] = [ vc_colorCreator( $color, 7 ), $color ];
}
foreach ( $base_colors['active'] as $name => $color ) {
	$colors['modern']['active'][ $name ] = [ vc_colorCreator( $color, 7 ), $color ];
}

wp_enqueue_script( 'vc_line_chart' );

$element_class = empty( $this->settings['element_default_class'] ) ? '' : $this->settings['element_default_class'];
$class_to_filter = 'vc_chart vc_line-chart ' . esc_attr( $element_class );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$options = [];

if ( ! empty( $legend ) ) {
	$options[] = 'data-vc-legend="1"';
}

if ( ! empty( $tooltips ) ) {
	$options[] = 'data-vc-tooltips="1"';
}

if ( ! empty( $animation ) ) {
	$options[] = 'data-vc-animation="' . esc_attr( str_replace( 'easein', 'easeIn', $animation ) ) . '"';
}

$values = (array) vc_param_group_parse_atts( $values );
$data = [
	'labels' => explode( ';', trim( $x_values, ';' ) ),
	'datasets' => [],
];

foreach ( $values as $k => $v ) {

	if ( 'custom' === $style ) {
		if ( ! empty( $v['custom_color'] ) ) {
			$color = $v['custom_color'];
			$highlight = vc_colorCreator( $v['custom_color'], - 10 ); // 10% darker
		} else {
			$color = 'grey';
			$highlight = 'grey';
		}
	} else {
		$color = isset( $colors[ $style ]['normal'][ $v['color'] ] ) ? $colors[ $style ]['normal'][ $v['color'] ] : $v['normal']['color'];
		$highlight = isset( $colors[ $style ]['active'][ $v['color'] ] ) ? $colors[ $style ]['active'][ $v['color'] ] : $v['active']['color'];
	}

	// don't use gradients for lines.
	if ( 'line' === $type ) {
		$color = is_array( $color ) ? end( $color ) : $color;
		$highlight = is_array( $highlight ) ? end( $highlight ) : $highlight;
		$rgb = vc_hex2rgb( $color );
		$fill_color = 'rgba(' . $rgb[0] . ', ' . $rgb[1] . ', ' . $rgb[2] . ', 0.1)';
	} else {
		$fill_color = $color;
	}

	if ( 'modern' === $style ) {
		$stroke_color = is_array( $color ) ? end( $color ) : $color;
		$highlight_stroke_color = vc_colorCreator( $stroke_color, - 7 );
	} else {
		$stroke_color = $color;
		$highlight_stroke_color = $highlight;
	}

	$data['datasets'][] = [
		'label' => isset( $v['title'] ) ? $v['title'] : '',
		'borderColor' => $stroke_color,
		'backgroundColor' => ( 'modern' === $style ? [
			$stroke_color,
			$highlight_stroke_color,
		] : $stroke_color ),
		'data' => explode( ';', isset( $v['y_values'] ) ? trim( $v['y_values'], ';' ) : '' ),
	];
}

$options[] = 'data-vc-type="' . esc_attr( $type ) . '"';
$options[] = 'data-vc-values="' . htmlentities( wp_json_encode( $data ) ) . '"';

if ( '' !== $title ) {
	$title = '<h2 class="wpb_heading">' . $title . '</h4>';
}

$canvas_html = '<canvas class="vc_line-chart-canvas" width="1" height="1"></canvas>';

if ( ! empty( $el_id ) ) {
	$options[] = 'id="' . esc_attr( $el_id ) . '"';
}
$output = '
<div class="' . esc_attr( $css_class ) . '" ' . implode( ' ', $options ) . '>
	' . wp_kses_post( $title ) . '
	<div class="wpb_wrapper">
		' . $canvas_html . '
	</div>' . '
</div>' . '
';

return $output;
