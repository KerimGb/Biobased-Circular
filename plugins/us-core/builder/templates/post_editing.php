<?php defined( 'ABSPATH' ) or die( 'This script cannot be accessed directly.' );
/**
 * The body of the page builder
 *
 * @var array $elms_categories
 * @var array $fieldsets
 * @var string $post_type
 */

// Checking required variables
$elms_categories = $elms_categories ?? array();
$fieldsets = $fieldsets ?? array();
$post_id = (int) usb_get_post_id();
$post_type = $post_type ?? '';
$section_included = array(
	'templates' => us_get_option( 'section_templates', 1 ),
	'favorites' => us_get_option( 'section_favorites', 1 ),
);
$classes = array(); // list of assigned classes to various sections

// Section output control by param active
$classes['tab_elements'] = $classes['add_elms'] = '';
if ( usb_get_active_panel_name() == 'add_elms' ) {
	$classes['add_elms'] = 'active';
	$classes['tab_elements'] = (
		! usb_post_editing_is_locked()
			? 'hidden'
			: ''
	);
}

// Hidden panel sections
foreach( array( 'paste_row', 'page_custom_css', 'page_settings' ) as $panel_name ) {
	$classes[ $panel_name ] = usb_get_active_panel_name() != $panel_name ? 'hidden' : '';
}

if ( get_option( 'us_license_activated' ) OR get_option( 'us_license_dev_activated' ) ) {
	$activation_message = '';
} else {
	$activation_message = '<p>';
	$activation_message .= sprintf(
		_x( '<a href="%s" target="_blank">Activate the theme</a> to unlock favorite sections.', 'Favorite Sections', 'us' ),
		admin_url( 'admin.php?page=us-home#activation' )
	);
	$activation_message .= '</p>';
}

?>
<!-- Custom Transit -->
<div class="usb-custom-transit hidden">
	<i class="fas fa-border-all"></i>
	<span class="for_name">Template section</span>
</div>

<!-- Add Items -->
<div class="usb-panel-tab-elements usof-tabs <?= esc_attr( $classes['tab_elements'] )?>">
	<?php if ( $section_included['templates'] OR $section_included['favorites'] ): ?>
	<div class="usof-tabs-list">
		<div class="usof-tabs-item usb_action_tab_add_elms <?= esc_attr( $classes['add_elms'] ) ?>"><?= strip_tags( __( 'Elements', 'us' ) ) ?></div>
		<?php if ( $section_included['templates'] ): ?>
		<div class="usof-tabs-item usb_action_show_templates"><?= strip_tags( us_translate( 'Templates' ) ) ?></div>
		<?php endif; ?>
		<?php if ( $section_included['favorites'] ): ?>
			<div class="usof-tabs-item usb_action_show_favorites"><?= strip_tags( _x( 'Favorites', 'Favorite Sections', 'us' ) ) ?></div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	<div class="usof-tabs-sections">
		<div class="usof-tabs-section for_elements <?= esc_attr( $classes['add_elms'] ) ?>">
			<!-- Begin Add Element List -->
			<div class="usb-panel-elms">
				<div class="usb-panel-search">
					<input type="text" name="search" autocomplete="off" placeholder="<?= esc_attr( us_translate( 'Search' ) ) ?>">
					<i class="ui-icon_close usb_action_reset_search_in_panel hidden" title="<?= esc_attr( __( 'Reset', 'us' ) ) ?>"></i>
				</div>
				<div class="usb-panel-search-noresult hidden"><?= strip_tags( us_translate( 'No results found.' ) ) ?></div>
				<?php foreach ( $elms_categories as $category => $elms ): ?>
					<?php
					// Category title
					$title = ! empty( $category ) ? $category : us_translate( 'General' );
					echo '<h2 class="usb-panel-elms-header">' . strip_tags( $title ) . '</h2>';

					// Category elements
					$output = '<div class="usb-panel-elms-list">';
					foreach ( $elms as $type => $elm ) {
						$elm_atts = array(
							'class' => 'usb-panel-elms-item usb-elm-has-icon',
							'data-title' => $elm['title'],
							'data-type' => (string) $type,
						);
						if ( ! empty( $elm['is_container'] ) ) {
							$elm_atts['data-isContainer'] = TRUE;
						}

						// Hide specific elements
						if (
							! empty( $elm['hide_on_adding_list'] )
							OR (
								! empty( $elm['show_for_post_types'] )
								AND ! in_array( $post_type, (array) $elm['show_for_post_types'] )
							)
							OR (
								! empty( $elm[ 'hide_for_post_ids' ] )
								AND in_array( $post_id, (array) $elm['hide_for_post_ids'] )
							)
						) {
							$elm_atts['class'] .= ' hidden';

						} elseif( ! empty( $elm_atts['data-title'] ) ) {
							$elm_atts['data-search-text'] = us_strtolower( $elm_atts['data-title'] );
						}
						$output .= '<div' . us_implode_atts( $elm_atts ) . '>';
						$output .= '<i class="' . $elm['icon'] . '"></i>';
						$output .= '</div>';
					}
					$output .= '</div>';
					echo $output;
					?>
				<?php endforeach; ?>
			</div>
			<!-- End Add Element List -->
		</div>
		<?php if ( $section_included['templates'] ): ?>
		<div class="usof-tabs-section for_templates">
			<!-- Begin Templates List -->
			<div id="usb-templates" class="usb-templates">
				<div class="usb-templates-error"><?= strip_tags( us_translate( 'No results found.' ) ) ?></div>
			</div>
			<!-- End Templates List -->
		</div>
		<?php endif; ?>
		<?php if ( $section_included['favorites'] ): ?>
			<div class="usof-tabs-section for_favorites">
				<!-- Begin Favorites -->
				<div id="usb-favorites" class="usb-favorites">
					<div class="usb-panel-search hidden">
						<input type="text" name="search" autocomplete="off" placeholder="<?= esc_attr( us_translate( 'Search' ) ) ?>">
						<i class="ui-icon_close usb_action_reset_search_in_panel hidden" title="<?= esc_attr( __( 'Reset', 'us' ) ) ?>"></i>
					</div>
					<div class="usb-panel-search-noresult hidden"><?= strip_tags( us_translate( 'No results found.' ) ) ?></div>
					<div class="usb-favorites-empty-list hidden">
						<i class="fas fa-heart"></i>
						<p><?= _x( 'Save your favorite sections to make them quickly available on all of your websites.', 'Favorite Sections', 'us' ) ?></p>
						<?= $activation_message ?>
					</div>
					<div class="usb-favorites-list hidden"></div>
					<div class="usb-favorites-confirm-deletion hidden">
						<p><?= sprintf( _x( 'You want to delete the %s section, this action cannot be undone.', 'Favorite Sections', 'us' ), '<strong class="for_section_name"></strong>' ) ?></p>
						<div class="usof-buttons">
							<button class="usof-button usb_action_delete_from_favorites"><?= strip_tags( us_translate( 'Delete' ) ) ?></button>
							<button class="usof-button usb_action_cancel_deletion_from_favorites"><?= strip_tags( us_translate( 'Cancel' ) ) ?></button>
						</div>
					</div>
				</div>
				<!-- End Favorites -->
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Begin popups -->
<?php
	$input_atts = array(
		'type' => 'text',
		'name' => 'section_name',
		'placeholder' => _x( 'Section Name', 'Favorite Sections', 'us' ),
		'autocomplete' => 'on',
	);
	$popup_content = '<p>' . _x( 'Save your favorite sections to make them quickly available on all of your websites.', 'Favorite Sections', 'us' ) . '</p>';
	$popup_content .= '<div class="usof-form-row desc_1">';
	$popup_content .= '<input' . us_implode_atts( $input_atts ) . '>';
	$popup_content .= '<div class="usof-form-row-desc-text">';
	$popup_content .= __( 'Use emoji for better visualization:', 'us' ) . ' <span class="usof-example">💜</span>, <span class="usof-example">✅</span>, <span class="usof-example">💎</span>, <span class="usof-example">🟡</span>. ';
	$popup_content .= '<a href="https://getemoji.com/#objects" target="_blank">' . __( 'Get more emoji', 'us' ) . '</a>';
	$popup_content .= '</div>'; // .usof-form-row-desc-text
	$popup_content .= '</div>'; // .usof-form-row

	if ( $activation_message ) {
		$popup_content .= $activation_message;
	} else {
		$popup_content .= '<div class="usof-buttons">';
		$popup_content .= '<button class="usof-button button-primary usb_action_save_to_favorites">';
		$popup_content .= '<span>' . strip_tags( us_translate( 'Save' ) ) . '</span>';
		$popup_content .= '<span class="usof-preloader"></span>';
		$popup_content .= '</button>';
		$popup_content .= '<div class="usof-message status_error hidden"></div>';
		$popup_content .= '</div>'; // .usof-buttons
	}
	us_load_template( 'usof/templates/popup', array(
		'id' => 'popup_save_to_favorites',
		'classes' => 'usof-container',
		'title' => _x( 'Save to Favorites', 'Favorite Sections', 'us' ),
		'content' => $popup_content,
	) )
?>
<!-- End popups -->

<!-- Elements Fieldsets -->
<div id="usb-tmpl-fieldsets" class="hidden">
	<?php foreach ( $fieldsets as $fieldset_name => $fieldset ): ?>
		<form class="usb-panel-fieldset" data-name="<?= esc_attr( $fieldset_name ) ?>">
			<?php us_load_template(
				'usof/templates/edit_form', array(
					'type' => $fieldset_name,
					'params' => $fieldset['params'] ?? array(),
					'context' => 'shortcode'
				)
			) ?>
		</form>
	<?php endforeach; ?>
</div>

<!-- Paste Row/Section -->
<div class="usb-panel-import-content usof-container inited <?= esc_attr( $classes['paste_row'] ) ?>">
	<textarea placeholder="[vc_row][vc_column] ... [/vc_column][/vc_row]"></textarea>
	<button class="usof-button usb_action_save_import_content disabled" disabled>
		<span><?= strip_tags( __( 'Append Section', 'us' ) ) ?></span>
		<span class="usof-preloader"></span>
	</button>
</div>

<!-- Page Custom CSS -->
<div class="usb-panel-page-custom-css usof-container inited <?= esc_attr( $classes['page_custom_css'] ) ?>">
	<div class="type_css" data-name="usb_post_custom_css">
		<?php us_load_template(
			'usof/templates/fields/css', array(
				'name' => 'usb_post_custom_css',
				'value' => '', // NOTE: The value is empty because the data should be loaded from the preview frame.
			)
		) ?>
	</div>
</div>

<!-- Page Settings -->
<div class="usb-panel-page-settings usof-container inited <?= esc_attr( $classes['page_settings'] ) ?>">
	<!-- Begin page fields -->
	<?php us_load_template(
		'usof/templates/edit_form', array(
			'context' => 'us_builder',
			'params' => us_config( 'us-builder.page_fields.params', array() ),
			'type' => 'page_fields',
			'values' => array(), // Values will be set on the JS side after loading the iframe.
		)
	) ?>
	<!-- End page fields -->
	<!-- Begin page metadata -->
	<div class="usb-panel-page-meta">
		<?php foreach ( (array) us_config( 'meta-boxes', array() ) as $metabox_config ): ?>
			<?php
			if (
				! us_arr_path( $metabox_config, 'usb_context' )
				OR ! in_array( $post_type, (array) us_arr_path( $metabox_config, 'post_types', array() ) )
			) {
				continue;
			}
			?>
			<div class="usb-panel-page-meta-title"><?php esc_html_e( $metabox_config['title'] ) ?></div>
			<?php us_load_template(
				'usof/templates/edit_form', array(
					'params' => us_arr_path( $metabox_config, 'fields', array() ),
					'type' => us_arr_path( $metabox_config, 'id', '' ),
					'values' => array(), // Values will be set on the JS side after loading the iframe.
				)
			) ?>
		<?php endforeach; ?>
	</div>
	<!-- End page metadata -->
</div>
