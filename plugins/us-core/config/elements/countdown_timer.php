<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: us_countdown_timer
 */

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );

$acf_custom_fields = array();
if (
	function_exists( 'us_acf_get_fields' )
	AND us_is_elm_editing_page()
) {
	$acf_custom_fields = us_acf_get_fields( array( 'date_picker', 'date_time_picker', 'time_picker' ), TRUE );
}

$_months = us_arr_path( $conditional_params, 'conditions.params.time_month.options' );
$_days = us_arr_path( $conditional_params, 'conditions.params.time_day.options' );
$_years = us_arr_path( $conditional_params, 'conditions.params.time_year.options' );
$_hours = us_arr_path( $conditional_params, 'conditions.params.time_hour.options' );
$_minutes = us_arr_path( $conditional_params, 'conditions.params.time_minute.options' );

unset( $_months['any'], $_days['any'], $_years['any'], $_hours['any'], $_minutes['any'] );

/**
 * @return array
 */
return array(
	'title' => __( 'Countdown Timer', 'us' ),
	'category' => __( 'Interactive', 'us' ),
	'icon' => 'fas fa-stopwatch',
	'params' => us_set_params_weight(

		// General section
		array(
			'date_source' => array(
				'title' => __( 'Final Date', 'us' ),
				'description' => sprintf( us_translate( 'Local time is %s.' ), '<strong>' . wp_date( 'M d Y, H:i' ) . '</strong>' ),
				'type' => 'select',
				'options' => array_merge(
					array(
						'custom' => __( 'Custom', 'us' ),
					),
					$acf_custom_fields,
				),
				'std' => 'custom',
				'usb_preview' => TRUE,
			),
			'time_month' => array(
				'title' => us_translate( 'Month' ),
				'type' => 'select',
				'options' => $_months,
				'std' => wp_date( 'm' ),
				'cols' => 4,
				'show_if' => array( 'date_source', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'time_day' => array(
				'title' => us_translate( 'Day' ),
				'type' => 'select',
				'options' => $_days,
				'std' => zeroise( wp_date( 'd' ) + 1, 2 ),
				'cols' => 6,
				'show_if' => array( 'date_source', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'time_year' => array(
				'title' => us_translate( 'Year' ),
				'type' => 'select',
				'options' => $_years,
				'std' => wp_date( 'Y' ),
				'cols' => 4,
				'show_if' => array( 'date_source', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'time_hour' => array(
				'title' => us_translate( 'Hour' ),
				'type' => 'select',
				'options' => $_hours,
				'std' => '00',
				'cols' => 6,
				'show_if' => array( 'date_source', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'time_minute' => array(
				'title' => us_translate( 'Minute' ),
				'type' => 'select',
				'options' => $_minutes,
				'std' => '00',
				'cols' => 6,
				'show_if' => array( 'date_source', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'action_after_end' => array(
				'title' => __( 'Action after the countdown ends', 'us' ),
				'type' => 'select',
				'options' => array(
					'hide' => __( 'Hide this element', 'us' ),
					'show_message' => __( 'Show the message', 'us' ),
				),
				'std' => 'hide',
				'usb_preview' => TRUE,
			),
			'expired_message' => array(
				'description' => __( 'HTML tags are allowed.', 'us' ),
				'type' => 'html',
				'encoded' => TRUE,
				'std' => base64_encode( __( 'Message', 'us' ) ),
				'classes' => 'for_above',
				'show_if' => array( 'action_after_end', '=', 'show_message' ),
				'usb_preview' => TRUE,
			),
			'animation' => array(
				'title' => __( 'Animation', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'slide' => __( 'Slide', 'us' ),
					'zoom' => __( 'Zoom', 'us' ),
					'flip' => __( 'Flip', 'us' ),
				),
				'std' => 'none',
				'usb_preview' => array(
					'mod' => 'animation',
				),
			),
		),

		// Appearance section
		array(
			'days_label' => array(
				'title' => __( 'Titles', 'us' ),
				'type' => 'text',
				'placeholder' => __( 'days', 'us' ),
				'std' => __( 'days', 'us' ),
				'group' => __( 'Titles', 'us' ),
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-countdown-item-label:eq(0)',
				),
			),
			'hours_label' => array(
				'type' => 'text',
				'placeholder' => __( 'hours', 'us' ),
				'std' => __( 'hours', 'us' ),
				'classes' => 'for_above',
				'group' => __( 'Titles', 'us' ),
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-countdown-item-label:eq(1)',
				),
			),
			'minutes_label' => array(
				'type' => 'text',
				'placeholder' => __( 'minutes', 'us' ),
				'std' => __( 'minutes', 'us' ),
				'classes' => 'for_above',
				'group' => __( 'Titles', 'us' ),
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-countdown-item-label:eq(2)',
				),
			),
			'seconds_label' => array(
				'type' => 'text',
				'placeholder' => __( 'seconds', 'us' ),
				'std' => __( 'seconds', 'us' ),
				'classes' => 'for_above',
				'description' => __( 'Leave empty to hide value', 'us' ),
				'group' => __( 'Titles', 'us' ),
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-countdown-item-label:eq(3)',
				),
			),
			'label_pos' => array(
				'title' => __( 'Title Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'bottom' => us_translate( 'Bottom' ),
					'aside' => us_translate( 'Right' ),
				),
				'std' => 'bottom',
				'group' => __( 'Titles', 'us' ),
				'usb_preview' => array(
					'mod' => 'labelpos',
				),
			),
			'label_size' => array(
				'title' => __( 'Title Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '1rem',
				'group' => __( 'Titles', 'us' ),
				'usb_preview' => array(
					'css' => '--label-size',
				),
			),
			'label_weight' => array(
				'title' => __( 'Title Weight', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => us_translate( 'Default' ),
					'100' => '100 ' . __( 'thin', 'us' ),
					'200' => '200 ' . __( 'extra-light', 'us' ),
					'300' => '300 ' . __( 'light', 'us' ),
					'400' => '400 ' . __( 'normal', 'us' ),
					'500' => '500 ' . __( 'medium', 'us' ),
					'600' => '600 ' . __( 'semi-bold', 'us' ),
					'700' => '700 ' . __( 'bold', 'us' ),
					'800' => '800 ' . __( 'extra-bold', 'us' ),
					'900' => '900 ' . __( 'ultra-bold', 'us' ),
				),
				'std' => '',
				'group' => __( 'Titles', 'us' ),
				'usb_preview' => array(
					'css' => '--label-weight',
				),
			),
			'label_color' => array(
				'title' => __( 'Title Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => '',
				'group' => __( 'Titles', 'us' ),
				'usb_preview' => array(
					'css' => '--label-color',
				),
			),
		),

		$conditional_params,
		$design_options_params,
	),
	'usb_init_js' => '$elm.wCountdown();',
);
