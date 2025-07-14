<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: cform
 */

$btn_styles = us_get_btn_styles();
$field_styles = us_get_field_styles();

// Get Reusable Blocks
$us_page_blocks_list = us_is_elm_editing_page() ? us_get_posts_titles_for( 'us_page_block' ) : array();

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );
$effect_options_params = us_config( 'elements_effect_options' );

$file_accept_description = __( 'Examples:', 'us' );
$file_accept_description .= ' <span class="usof-example">.pdf</span>,';
$file_accept_description .= ' <span class="usof-example">.jpg, .jpeg</span>,';
$file_accept_description .= ' <span class="usof-example">image/*</span>.';
$file_accept_description .= ' <a href="https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/accept" target="_blank">';
$file_accept_description .= __( 'Learn more', 'us' );
$file_accept_description .= '</a>';

$file_max_size_description = __( 'Examples:', 'us' );
$file_max_size_description .= ' <span class="usof-example">1MB</span>,';
$file_max_size_description .= ' <span class="usof-example">5MB</span>,';
$file_max_size_description .= ' <span class="usof-example">10MB</span>';

// Default Form Fields
$default_fields = array(
	array(
		'type' => 'text',
		'label' => '',
		'placeholder' => us_translate( 'Name' ),
	),
	array(
		'type' => 'email',
		'label' => '',
		'placeholder' => us_translate( 'Email' ),
	),
	array(
		'type' => 'textarea',
		'label' => '',
		'placeholder' => us_translate( 'Text' ),
	),
);

/**
 * @return array
 */
return array(
	'title' => __( 'Contact Form', 'us' ),
	'icon' => 'fas fa-envelope',
	'params' => us_set_params_weight(

		// Fields
		array(
			'items' => array(
				'type' => 'group',
				'group' => __( 'Fields', 'us' ),
				'show_controls' => TRUE,
				'is_sortable' => TRUE,
				'is_accordion' => TRUE,
				'accordion_title' => 'type',
				'usb_preview' => TRUE,
				'params' => array(
					'type' => array(
						'title' => us_translate( 'Type' ),
						'type' => 'select',
						'options' => array(
							'text' => us_translate( 'Text' ) . ' ' . __( '(single line)', 'us' ),
							'textarea' => us_translate( 'Text' ) . ' ' . __( '(multiple lines)', 'us' ),
							'email' => us_translate( 'Email' ),
							'date' => __( 'Date Picker', 'us' ),
							'select' => __( 'Dropdown', 'us' ),
							'checkboxes' => __( 'Checkboxes', 'us' ),
							'radio' => __( 'Radio buttons', 'us' ),
							'file' => __( 'Upload File', 'us' ),
							'info' => __( 'Text Block', 'us' ),
							'agreement' => __( 'Agreement checkbox', 'us' ),
							'captcha' => __( 'Math Captcha', 'us' ),
							'reCAPTCHA' => 'reCAPTCHA',
						),
						'std' => 'text',
						'admin_label' => TRUE,
					),
					'inputmode' => array(
						'title' => __( 'Input mode', 'us' ),
						'type' => 'select',
						'options' => array(
							'text' => 'text',
							'decimal' => 'decimal',
							'numeric' => 'numeric',
							'tel' => 'tel',
							'url' => 'url',
						),
						'std' => 'text',
						'show_if' => array( 'type', '=', 'text' ),
					),
					'date_format' => array(
						'title' => us_translate( 'Date Format' ),
						'type' => 'text',
						'std' => 'd MM yy',
						'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">yy-mm-dd</span>, <span class="usof-example">dd/mm/y</span>, <span class="usof-example">d MM, D</span>. <a href="https://api.jqueryui.com/datepicker/#utility-formatDate" target="_blank">' . __( 'Learn more', 'us' ) . '</a>',
						'show_if' => array( 'type', '=', 'date' ),
					),
					'label' => array(
						'title' => us_translate( 'Title' ),
						'description' => __( 'Shown above the field', 'us' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'type', '!=', array( 'info', 'reCAPTCHA' ) ),
						'admin_label' => TRUE,
					),
					'description' => array(
						'title' => us_translate( 'Description' ),
						'description' => __( 'Shown below the field', 'us' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'type', '!=', array( 'info', 'reCAPTCHA' ) ),
					),
					'placeholder' => array(
						'title' => __( 'Placeholder', 'us' ),
						'description' => __( 'Shown inside the field', 'us' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'type', '=', array( 'text', 'email', 'date', 'textarea' ) ),
						'admin_label' => TRUE,
					),
					'values' => array(
						'title' => __( 'Values', 'us' ),
						'description' => __( 'Each value on a new line', 'us' ),
						'type' => 'textarea',
						'encoded' => TRUE,
						'std' => '',
						'show_if' => array( 'type', '=', array( 'select', 'checkboxes', 'radio' ) ),
					),
					'value' => array(
						'title' => us_translate( 'Text' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'type', '=', array( 'info', 'agreement' ) ),
					),
					'required' => array(
						'switch_text' => __( 'Required field', 'us' ),
						'type' => 'switch',
						'std' => 0,
						'show_if' => array( 'type', '=', array( 'text', 'email', 'date', 'textarea', 'checkboxes', 'file', 'radio', 'select' ), ),
					),
					'is_used_as_from_email' => array(
						'switch_text' => __( 'Use the value of this field as sender\' address of emails', 'us' ),
						'type' => 'switch',
						'std' => 0,
						'show_if' => array( 'type', '=', array( 'email' ), ),
					),
					'is_used_as_from_name' => array(
						'switch_text' => __( 'Use the value of this field as sender\' name of emails', 'us' ),
						'type' => 'switch',
						'std' => 0,
						'show_if' => array( 'type', '=', array( 'text' ), ),
					),
					'move_label' => array(
						'switch_text' => __( 'Move title on focus', 'us' ),
						'type' => 'switch',
						'std' => 0,
						'show_if' => array( 'type', '=', array( 'text', 'email', 'date', 'textarea', 'captcha' ) ),
					),
					'accept' => array(
						'title' => __( 'Accepted file types', 'us' ),
						'description' => $file_accept_description,
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'type', '=', 'file' ),
					),
					'file_max_size' => array(
						'title' => __( 'File size limit', 'us' ),
						'description' => $file_max_size_description,
						'placeholder' => '10MB', // Default if not set
						'type' => 'text',
						'std' => '10MB',
						'show_if' => array( 'type', '=', 'file' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'us' ),
						'type' => 'icon',
						'std' => '',
						'show_if' => array( 'type', '=', array( 'text', 'email', 'date', 'textarea', 'select', 'captcha' ), ),
					),
					'cols' => array(
						'title' => us_translate( 'Width' ),
						'type' => 'radio',
						'options' => array(
							'1' => us_translate( 'Full' ),
							'2' => '1/2',
							'3' => '1/3',
							'4' => '1/4',
						),
						'std' => '1',
						'show_if' => array( 'type', '=', array( 'text', 'email', 'date', 'textarea', 'select', 'checkboxes', 'radio', 'file', 'captcha' ), ),
					),
					'reCAPTCHA_warn' => array(
						'type' => 'hidden',
						'std' => '',
						'description' => '<a href="' . admin_url( 'admin.php?page=us-theme-options#advanced' ) . '" target="_blank">' . strip_tags( __( 'Manage reCAPTCHA options', 'us' ) ) . '</a>',
						'show_if' => array( 'type', '=', 'reCAPTCHA' ),
						'classes' => 'type_message',
					),
				),
				'std' => $default_fields,
			),
		),

		// Button
		array(
			'button_text' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => us_translate( 'Submit' ),
				'dynamic_values' => TRUE,
				'group' => __( 'Button', 'us' ),
				'usb_preview' => array(
					'attr' => 'text',
					'elm' => 'button[type=submit].w-btn .w-btn-label',
				),
			),
			'button_style' => array(
				'title' => us_translate( 'Style' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => $btn_styles,
				'std' => '1',
				'group' => __( 'Button', 'us' ),
				'usb_preview' => array(
					'mod' => 'us-btn-style',
					'elm' => 'button[type=submit].w-btn',
				),
			),
			'button_size' => array(
				'title' => us_translate( 'Size' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Button', 'us' ),
				'usb_preview' => array(
					'css' => 'font-size',
					'elm' => 'button[type=submit].w-btn',
				),
			),
			'button_size_mobiles' => array(
				'title' => __( 'Size on Mobiles', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Button', 'us' ),
				'usb_preview' => array(
					'css' => '--btn-size-mobiles',
					'elm' => '.w-form-row.for_submit',
				),
			),
			'button_align' => array(
				'title' => __( 'Button Alignment', 'us' ),
				'type' => 'radio',
				'labels_as_icons' => 'fas fa-align-*',
				'options' => array(
					'none' => us_translate( 'Default' ),
					'left' => us_translate( 'Left' ),
					'center' => us_translate( 'Center' ),
					'right' => us_translate( 'Right' ),
					'justify' => __( 'Stretch to the full width', 'us' ),
				),
				'std' => 'none',
				'group' => __( 'Button', 'us' ),
				'is_responsive' => TRUE,
				'usb_preview' => array(
					'mod' => 'align',
					'elm' => '.w-form-row.for_submit',
				),
			),
			'icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'group' => __( 'Button', 'us' ),
				'usb_preview' => TRUE,
			),
			'iconpos' => array(
				'title' => __( 'Icon Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'left',
				'group' => __( 'Button', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		// Appearance
		array(
			'us_field_style' => array(
				'title' => __( 'Field Style', 'us' ),
				'description' => $misc['desc_field_styles'],
				'type' => 'select',
				'options' => $field_styles,
				'std' => 'default',
				'usb_preview' => array(
					'mod' => 'us-field-style',
				),
				'group' => us_translate( 'Appearance' ),
			),
			'fields_layout' => array(
				'title' => __( 'Fields Layout', 'us' ),
				'type' => 'radio',
				'options' => array(
					'ver' => __( 'Vertical', 'us' ),
					'hor' => __( 'Horizontal', 'us' ),
				),
				'std' => 'ver',
				'cols' => 2,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'layout',
				),
			),
			'fields_gap' => array(
				'title' => __( 'Gap between Fields', 'us' ),
				'type' => 'slider',
				'std' => '1rem',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
					'rem' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
					'vh' => array(
						'min' => 0.0,
						'max' => 9.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'css' => '--fields-gap',
				),
			),
			'action_after_sending' => array(
				'title' => __( 'Action after sending', 'us' ),
				'type' => 'select',
				'options' => array(
					'show_message' => __( 'Show the message', 'us' ),
					'show_reusable_block' => __( 'Show the Reusable Block', 'us' ),
					'redirect' => __( 'Redirect to URL', 'us' ),
					'open_popup' => __( 'Open a popup', 'us' ),
					'close_popup' => __( 'Close the current popup', 'us' ),
				),
				'std' => 'show_message',
				'group' => us_translate( 'Appearance' ),
			),
			'success_message' => array(
				'description' => __( 'HTML tags are allowed.', 'us' ),
				'type' => 'html',
				'encoded' => TRUE,
				'std' => base64_encode( __( 'Thank you! Your message was sent.', 'us' ) ),
				'classes' => 'for_above',
				'show_if' => array( 'action_after_sending', '=', 'show_message' ),
				'group' => us_translate( 'Appearance' ),
			),
			'reusable_block' => array(
				'type' => 'select',
				'hints_for' => 'us_page_block',
				'options' => us_array_merge(
					array( 'none' => '– ' . us_translate( 'None' ) . ' –' ),
					$us_page_blocks_list
				),
				'std' => 'none',
				'classes' => 'for_above',
				'show_if' => array( 'action_after_sending', '=', 'show_reusable_block' ),
				'group' => us_translate( 'Appearance' ),
			),
			'redirect_url' => array(
				'placeholder' => us_translate( 'Link' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'for_above',
				'show_if' => array( 'action_after_sending', '=', 'redirect' ),
				'group' => us_translate( 'Appearance' ),
			),
			'popup_selector' => array(
				'description' => __( 'Use class or ID.', 'us' ) . ' ' . __( 'Examples:', 'us' ) . ' <span class="usof-example">.my-popup</span>, <span class="usof-example">#my-popup</span>',
				'type' => 'text',
				'std' => '',
				'classes' => 'for_above',
				'show_if' => array( 'action_after_sending', '=', 'open_popup' ),
				'group' => us_translate( 'Appearance' ),
			),
			'hide_form_after_sending' => array(
				'switch_text' => __( 'Hide this form after sending', 'us' ),
				'type' => 'switch',
				'std' => 0,
				'show_if' => array( 'action_after_sending', '=', array( 'show_message', 'show_reusable_block' ) ),
				'group' => us_translate( 'Appearance' ),
			),
		),

		// Mail
		array(
			'email_subject' => array(
				'title' => __( 'Email Subject', 'us' ),
				'type' => 'text',
				'std' => sprintf( __( 'Message from %s', 'us' ), '[page_title]' ),
				'group' => __( 'Mail', 'us' ),
			),
			'email_message' => array(
				'title' => __( 'Email Message', 'us' ),
				'description' => __( 'HTML tags are allowed.', 'us' ),
				'type' => 'html',
				'encoded' => TRUE,
				'std' => base64_encode( '<p>' . sprintf( __( 'This message was sent from the %s page.', 'us' ), '<a href="[page_url]">[page_title]</a>' ) . '</p> [field_list]' ),
				'group' => __( 'Mail', 'us' ),
			),
			'receiver_email' => array(
				'title' => __( 'Recipient Email', 'us' ),
				'description' => __( 'This form submissions will be sent to this email.', 'us' ) . ' ' . __( 'For several values use commas', 'us' ),
				'type' => 'text',
				'std' => get_option( 'admin_email' ),
				'dynamic_values' => TRUE,
				'admin_label' => TRUE,
				'group' => __( 'Mail', 'us' ),
			),
			'bcc_email' => array(
				'title' => __( 'BCC Email', 'us' ),
				'description' => __( 'Blind copy will be sent to this email.', 'us' ),
				'type' => 'text',
				'std' => '',
				'dynamic_values' => TRUE,
				'group' => __( 'Mail', 'us' ),
			),
			'reply_to' => array(
				'title' => __( 'Reply To', 'us' ),
				'description' => __( 'If empty, the filled email field will be used.', 'us' ),
				'type' => 'text',
				'std' => '',
				'dynamic_values' => TRUE,
				'group' => __( 'Mail', 'us' ),
			),
		),

		$effect_options_params,
		$conditional_params,
		$design_options_params
	),
	'fallback_params' => array(
		'button_fullwidth',
	),
	'usb_init_js' => 'jQuery( $elm ).wForm()',
);
