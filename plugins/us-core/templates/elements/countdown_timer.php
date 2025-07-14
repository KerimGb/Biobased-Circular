<?php defined( 'ABSPATH' ) or die( 'This script cannot be accessed directly.' );
/**
 * Template for shortcode: us_countdown_timer
 *
 * @var string $date_source
 * @var string $time_year
 * @var string $days_label
 * @var string $hours_label
 * @var string $minutes_label
 * @var string $seconds_label
 * @var string $time_month
 * @var string $time_day
 * @var string $time_hour
 * @var string $time_minute
 * @var string $action_after_end
 * @var string $expired_message
 */

$_atts = array(
	'class' => 'w-countdown labelpos_' . $label_pos,
	'role' => 'timer',
	'style' => '',
);

if ( $label_size ) {
	$_atts['style'] .= '--label-size:' . $label_size . ';';
}
if ( $label_weight ) {
	$_atts['style'] .= '--label-weight:' . $label_weight . ';';
}
if ( $label_color ) {
	$_atts['style'] .= '--label-color:' . us_get_color( $label_color ) . ';';
}

$_atts['class'] .= $classes ?? '';
$_atts['class'] .= ' animation_' . $animation;
$_atts['class'] .= ' after_end_' . $action_after_end;

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

if ( $date_source === 'custom' ) {
	$target_time_str = sprintf(
		'%04d-%02d-%02d %02d:%02d:00',
		(int) $time_year, (int) $time_month, (int) $time_day, (int) $time_hour, (int) $time_minute
	);

} else {
	$target_time_str = us_get_custom_field( $date_source, FALSE );

	if ( ! $target_time_str AND ! usb_is_preview() ) {
		return;
	}

	// Improve UX when live edit Page Templates
	if ( usb_is_template_preview() ) {
		$target_time_str = '+ 1day';
	}
}

$site_timezone = wp_timezone_string();
$target_time = strtotime( "$target_time_str $site_timezone" );
$current_time = wp_date( 'U' );

$remaining_time = (int) $target_time - (int) $current_time;

if ( $expired_message = base64_decode( $expired_message, TRUE ) ) {
	$expired_message = rawurldecode( $expired_message );
}

if ( $remaining_time <= 0 ) {
	$_atts['class'] .= ' expired';

	if ( $action_after_end == 'hide' AND ! usb_is_preview() ) {
		return;
	}
}

$days = floor( $remaining_time / DAY_IN_SECONDS );
$hours = floor( ( $remaining_time % DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
$minutes = floor( ( $remaining_time % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );
$seconds = $remaining_time % 60;

$js_data = array(
	'remainingTime' => $remaining_time,
);

$output = '<div' . us_implode_atts( $_atts ) . us_pass_data_to_js( $js_data ) . '>';

foreach ( array( 'days', 'hours', 'minutes', 'seconds' ) as $type ) {
	$output .= '<div class="w-countdown-item" data-type="' . $type . '">';
	$output .= '<span class="w-countdown-item-number"><span>' .  zeroise( $$type, 2 ) . '</span></span>';
	$output .= '<span class="w-countdown-item-label">' . esc_html( ${ $type . '_label' } ) . '</span>';
	$output .= '</div>';
}
if ( $action_after_end === 'show_message' ) {
	$output .= '<div class="w-countdown-message">' . do_shortcode( $expired_message ) . '</div>';
}
$output .= '</div>';

echo $output;
