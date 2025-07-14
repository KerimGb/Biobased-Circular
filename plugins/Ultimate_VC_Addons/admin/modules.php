<?php
/**
 * Elements Page
 *
 *  @package Elements Page
 */

if ( isset( $_GET['author'] ) ) { // PHPCS:ignore:WordPress.Security.NonceVerification.Recommended
	$author = true;
} else {
	$author = false;
}
	$author_extend = '';
if ( $author ) {
	$author_extend = '&author';
}
?>

<?php
	$ultimate_row = get_option( 'ultimate_row' );
if ( 'enable' == $ultimate_row ) {
	$checked_row = 'checked="checked"';
} else {
	$checked_row = '';
}

	$ultimate_modules = get_option( 'ultimate_modules' );

	$images_url = UAVC_URL . 'admin/imagesa/';

	// global $modules;
	$modules = [
		'Ultimate_Animation' => [
			'slug'        => 'ultimate-animation',
			'title'       => __( 'Animation Block', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'animation block' ],
			'icon'         => $images_url . 'animation_block.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Add entrance animations to elements', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/animation-block/',
			'category'    => 'content',
		],
		'Ultimate_Buttons' => [
			'slug'        => 'ultimate-buttons',
			'title'       => __( 'Advanced Buttons', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'advanced buttons' ],
			'icon'         => $images_url . 'advanced_button.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Create customizable call-to-action buttons', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/advanced-button/',
			'category'    => 'content',
		],
		'Ultimate_CountDown' => [
			'slug'        => 'ultimate-countdown',
			'title'       => __( 'Count Down Timer', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'count down timer' ],
			'icon'         => $images_url . 'countdown_timer.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Display countdown timers for events or offers', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/count-down-timer/',
			'category'    => 'content',
		],
		'Ultimate_Flip_Box' => [
			'slug'        => 'ultimate-flip-box',
			'title'       => __( 'Flip Boxes', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'flip boxes' ],
			'icon'         => $images_url . 'flipbox.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Show content with flip animations on hover', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/flip-box/',
			'category'    => 'content',
		],
		'Ultimate_Google_Maps' => [
			'slug'        => 'ultimate-google-maps',
			'title'       => __( 'Google Maps', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'google maps' ],
			'icon'         => $images_url . 'google_maps.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Embed interactive Google Maps with custom markers', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/google-maps/',
			'category'    => 'content',
		],
		'Ultimate_Google_Trends' => [
			'slug'        => 'ultimate-google-trends',
			'title'       => __( 'Google Trends', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'google trends' ],
			'icon'         => $images_url . 'google_trends.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Display Google Trends data on your site', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/google-trends/',
			'category'    => 'content',
		],
		'Ultimate_Headings' => [
			'slug'        => 'ultimate-headings',
			'title'       => __( 'Headings', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'headings' ],
			'icon'         => $images_url . 'heading.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Add stylized headings with advanced typography', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/heading-2/',
			'category'    => 'content',
		],
		'Ultimate_Icon_Timeline' => [
			'slug'        => 'ultimate-icon-timeline',
			'title'       => __( 'Timeline', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'timeline' ],
			'icon'         => $images_url . 'timeline.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Create vertical timelines with icons and content', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/timeline/',
			'category'    => 'content',
		],
		'Ultimate_Info_Box' => [
			'slug'        => 'ultimate-info-box',
			'title'       => __( 'Info Boxes', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'info boxes' ],
			'icon'         => $images_url . 'info_box.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Highlight information with icon, title, and description', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/info-box/',
			'category'    => 'content',
		],
		'Ultimate_Info_Circle' => [
			'slug'        => 'ultimate-info-circle',
			'title'       => __( 'Info Circle', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'info circle' ],
			'icon'         => $images_url . 'info_cirecle.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Present info in a circular layout with hover effects', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/info-circle/',
			'category'    => 'content',
		],
		'Ultimate_Info_List' => [
			'slug'        => 'ultimate-info-list',
			'title'       => __( 'Info List', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'info list' ],
			'icon'         => $images_url . 'post_info.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'List features or services with icons and text', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/info-list/',
			'category'    => 'content',
		],
		'Ultimate_Info_Tables' => [
			'slug'        => 'ultimate-info-tables',
			'title'       => __( 'Info Tables', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'info tables' ],
			'icon'         => $images_url . 'info_table.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Display information in a structured table format', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/info-table/',
			'category'    => 'content',
		],
		'Ultimate_Interactive_Banners' => [
			'slug'        => 'ultimate-interactive-banners',
			'title'       => __( 'Interactive Banners', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'interactive banners' ],
			'icon'         => $images_url . 'interactive_banners.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Create banners with interactive hover effects', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/interactive-banners/',
			'category'    => 'content',
		],
		'Ultimate_Interactive_Banner_2' => [
			'slug'        => 'ultimate-interactive-banner-2',
			'title'       => __( 'Interactive Banners - 2', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'interactive banners - 2' ],
			'icon'         => $images_url . 'interactivae_banner_2.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Design advanced interactive banners with animations.', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/interactive-banner-2/',
			'category'    => 'content',
		],
		'Ultimate_Modals' => [
			'slug'        => 'ultimate-modals',
			'title'       => __( 'Modal Popups', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'modal popups' ],
			'icon'         => $images_url . 'modal.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Add modal popups triggered by buttons or links', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/modal/',
			'category'    => 'content',
		],
		'Ultimate_Pricing_Tables' => [
			'slug'        => 'ultimate-pricing-tables',
			'title'       => __( 'Price Box', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'price box' ],
			'icon'         => $images_url . 'price_box.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Showcase pricing plans in a tabular layout', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/price-box/',
			'category'    => 'content',
		],
		'Ultimate_Spacer' => [
			'slug'        => 'ultimate-spacer',
			'title'       => __( 'Spacer / Gap', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'spacer / gap' ],
			'icon'         => $images_url . 'space_gap copy.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Insert adjustable space between elements', 'ultimate_vc' ),
			'demo_url'    => '',
			'category'    => 'content',
		],
		'Ultimate_Stats_Counter' => [
			'slug'        => 'ultimate-stats-counter',
			'title'       => __( 'Counter', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'counter' ],
			'icon'         => $images_url . 'stat_counter.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Animate numbers to display statistics or milestones', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/stats-counter/',
			'category'    => 'content',
		],
		'Ultimate_Swatch_Book' => [
			'slug'        => 'ultimate-swatch-book',
			'title'       => __( 'Swatch Book', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'swatch book' ],
			'icon'         => $images_url . 'swatch copy.svg', 
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Display content in a swatch book style layout', 'ultimate_vc' ),
			'demo_url'    => '',
			'category'    => 'content',
		],
		'Ultimate_Icons' => [
			'slug'        => 'ultimate-icons',
			'title'       => __( 'Icons', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'icons' ],
			'icon'         => $images_url . 'just_icon.svg', 
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Add and customize icons for various purposes', 'ultimate_vc' ),
			'demo_url'    => '',
			'category'    => 'content',
		],
		'Ultimate_List_Icon' => [
			'slug'        => 'ultimate-list-icon',
			'title'       => __( 'List Icons', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'list icons' ],
			'icon'         => $images_url . 'justicon copy.svg', 
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Create lists with custom icons', 'ultimate_vc' ),
			'demo_url'    => '',
			'category'    => 'content',
		],
		'Ultimate_Carousel' => [
			'slug'        => 'ultimate-carousel',
			'title'       => __( 'Advanced Carousel', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'advanced carousel' ],
			'icon'         => $images_url . 'advanced_carousel.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Build advanced carousels for images or content', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/advanced-carousel/',
			'category'    => 'content',
		],
		'Ultimate_Fancy_Text' => [
			'slug'        => 'ultimate-fancy-text',
			'title'       => __( 'Fancy Text', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'fancy text' ],
			'icon'         => $images_url . 'fancy_text.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Animate text with typing or sliding effects', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/texttype-effect/',
			'category'    => 'content',
		],
		'Ultimate_Hightlight_Box' => [
			'slug'        => 'ultimate-hightlight-box',
			'title'       => __( 'Highlight Box', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'highlight box' ],
			'icon'         => $images_url . 'highlight_box.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Emphasize content with highlighted boxes', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/highlight-box/',
			'category'    => 'content',
		],
		'Ultimate_Info_Banner' => [
			'slug'        => 'ultimate-info-banner',
			'title'       => __( 'Info Banner', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'info banner' ],
			'icon'         => $images_url . 'info_banner.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Display prominent banners with overlay text', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/info-banner/',
			'category'    => 'content',
		],
		'Ultimate_iHover' => [
			'slug'        => 'ultimate-ihover',
			'title'       => __( 'iHover', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'ihover' ],
			'icon'         => $images_url . 'mails.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Add hover effects to images or content boxes', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/ihover/',
			'category'    => 'content',
		],
		'Ultimate_Hotspot' => [
			'slug'        => 'ultimate-hotspot',
			'title'       => __( 'Hotspot', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'hotspot' ],
			'icon'         => $images_url . 'post_info.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Place interactive hotspots on images', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/hotspot/',
			'category'    => 'content',
		],
		'Ultimate_Video_Banner' => [
			'slug'        => 'ultimate-video-banner',
			'title'       => __( 'Video Banner', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'video banner' ],
			'icon'         => $images_url . 'youtube copy.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Create banners with embedded videos', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/video/',
			'category'    => 'content',
		],
		'WooComposer' => [
			'slug'        => 'woocomposer',
			'title'       => __( 'WooComposer', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'woocomposer' ],
			'icon'         => $images_url . 'woo.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Design WooCommerce pages with custom layouts', 'ultimate_vc' ),
			'demo_url'    => '',
			'category'    => 'content',
		],
		'Ultimate_Dual_Button' => [
			'slug'        => 'ultimate-dual-button',
			'title'       => __( 'Dual Button', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'dual button' ],
			'icon'         => $images_url . 'dual_button.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Add two connected buttons with individual actions', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/dual-button/',
			'category'    => 'content',
		],
		'Ultimate_link' => [
			'slug'        => 'ultimate-link',
			'title'       => __( 'Creative Link', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'creative link' ],
			'icon'         => $images_url . 'creative_link.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Create stylized and animated text links', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/creative-link/',
			'category'    => 'content',
		],
		'Ultimate_Image_Separator' => [
			'slug'        => 'ultimate-image-separator',
			'title'       => __( 'Image Separator', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'image separator' ],
			'icon'         => $images_url . 'image_separator.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Insert image-based separators between sections', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/image-separator/',
			'category'    => 'content',
		],
		'Ultimate_Content_Box' => [
			'slug'        => 'ultimate-content-box',
			'title'       => __( 'Content Box', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'content box' ],
			'icon'         => $images_url . 'content_box copy.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Display content within styled boxes', 'ultimate_vc' ),
			'demo_url'    => '',
			'category'    => 'content',
		],
		'Ultimate_Expandable_section' => [
			'slug'        => 'ultimate-expandable-section',
			'title'       => __( 'Expandable Section', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'expandable section' ],
			'icon'         => $images_url . 'expandable_section.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Add sections that expand or collapse on click', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/expandable-section/',
			'category'    => 'content',
		],
		'Ultimate_Tab' => [
			'slug'        => 'ultimate-tab',
			'title'       => __( 'Advanced Tabs', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'advanced tabs' ],
			'icon'         => $images_url . 'advanced_tab copy.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Organize content into responsive tabs', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/advanced-tab/',
			'category'    => 'content',
		],
		'Ultimate_Team' => [
			'slug'        => 'ultimate-team',
			'title'       => __( 'Ultimate Teams', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'ultimate teams' ],
			'icon'         => $images_url . 'team_element.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Showcase team members with profiles', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/team-element/',
			'category'    => 'content',
		],
		'Ultimate_Sticky_Section' => [
			'slug'        => 'ultimate-sticky-section',
			'title'       => __( 'Sticky Section', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'sticky section' ],
			'icon'         => $images_url . 'sticky_copy.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Make sections stick to the viewport on scroll', 'ultimate_vc' ),
			'demo_url'    => '',
			'category'    => 'content',
		],
		'Ultimate_Range_Slider' => [
			'slug'        => 'ultimate-range-slider',
			'title'       => __( 'Range Slider', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'range slider' ],
			'icon'         => $images_url . 'range copy.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Add sliders to select numeric ranges', 'ultimate_vc' ),
			'demo_url'    => '',
			'category'    => 'content',
		],
		'Ultimate_Videos' => [
			'slug'        => 'ultimate-videos',
			'title'       => __( 'Video', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'video' ],
			'icon'         => $images_url . 'video.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Embed responsive videos with custom settings', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/video/',
			'category'    => 'content',
		],
		'Ultimate_Ribbons' => [
			'slug'        => 'ultimate-ribbons',
			'title'       => __( 'Ribbon', 'ultimate_vc' ),
			'keywords'    => [ 'uavc', 'ribbon' ],
			'icon'         => $images_url . 'ribbon.svg',
			'title_url'   => '#',
			'default'     => true,
			'doc_url'     => '',
			'is_pro'      =>false,
			'is_new'      => true,
			'description' => __( 'Add decorative ribbons to highlight content', 'ultimate_vc' ),
			'demo_url'    => 'https://ultimate.brainstormforce.com/ribbon-widget/',
			'category'    => 'content',
		],
                'Ultimate_Dual_colors' => [
                        'slug'        => 'ultimate-dual-colors',
                        'title'       => __( 'Dual Color Heading', 'ultimate_vc' ),
                        'keywords'    => [ 'uavc', 'dual color heading' ],
                        'icon'         => $images_url . 'dual_color_heading.svg',
                        'title_url'   => '#',
                        'default'     => true,
                        'doc_url'     => '',
                        'is_pro'      =>false,
                        'is_new'      => true,
                        'description' => __( 'Create headings with dual-color text', 'ultimate_vc' ),
                        'demo_url'    => 'https://ultimate.brainstormforce.com/dual-color-heading/',
                        'category'    => 'content',
                ],
                'Row_Backgrounds' => [
                        'slug'        => 'row-backgrounds',
                        'title'       => __( 'Row Backgrounds', 'ultimate_vc' ),
                        'keywords'    => [ 'uavc', 'row backgrounds' ],
                        'icon'         => $images_url . 'row_overlay_effects.svg',
                        'title_url'   => '#',
                        'default'     => true,
                        'doc_url'     => '',
                        'is_pro'      => false,
                        'is_new'      => true,
                        'description' => __( 'Enhance rows with advanced background effects', 'ultimate_vc' ),
                        'demo_url'    => '',
                        'category'    => 'content',
                ],
        ];
	?>

<div class="wrap about-wrap bsf-page-wrapper ultimate-modules bend">
<div class="wrap-container">
	<div class="bend-heading-section ultimate-header">
	<h1><?php esc_html_e( 'Ultimate Addons Settings', 'ultimate_vc' ); ?></h1>
	<h3><?php esc_html_e( 'Ultimate Addons is designed in a very modular fashion so that most the features would be independent of each other. For any reason, should you wish to disable some features, you can do it very easily below.', 'ultimate_vc' ); ?></h3>
	<div class="bend-head-logo">
		<div class="bend-product-ver">
			<?php
			esc_html_e( 'Version', 'ultimate_vc' );
			echo ' ' . ULTIMATE_VERSION; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</div>
	</div><!-- bend-heading section -->

	<div id="msg"></div>
	<div id="bsf-message"></div>

	<div class="bend-content-wrap">
	<div class="smile-settings-wrapper">
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo admin_url( 'admin.php?page=about-ultimate' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="about-ultimate" class="nav-tab"> <?php echo esc_html__( 'About', 'ultimate_vc' ); ?> </a>
			<a href="<?php echo admin_url( 'admin.php?page=ultimate-dashboard' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="ultimate-modules" class="nav-tab nav-tab-active"> <?php echo esc_html__( 'Elements', 'ultimate_vc' ); ?> </a>
			<a href="<?php echo admin_url( 'admin.php?page=ultimate-smoothscroll' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="css-settings" class="nav-tab"> <?php echo esc_html__( 'Smooth Scroll', 'ultimate_vc' ); ?> </a>
			<a href="<?php echo admin_url( 'admin.php?page=ultimate-scripts-and-styles' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="css-settings" class="nav-tab"> <?php echo esc_html__( 'Scripts and Styles', 'ultimate_vc' ); ?> </a>
			<?php if ( $author ) : ?>
				<a href="<?php echo admin_url( 'admin.php?page=ultimate-debug-settings' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="ultimate-debug" class="nav-tab"> Debug </a>
			<?php endif; ?>
		</h2>
	</div><!-- smile-settings-wrapper -->

	</hr>

	<div class="container ultimate-content">
		<div class="col-md-12">
			<div id="ultimate-modules" class="ult-tabs active-tab">
				<br/>
				<div>
					<input type="checkbox" id="ult-all-modules-toggle" data-all="<?php echo count( $modules ); ?>" value="checkall" /> <label for="ult-all-modules-toggle"><?php echo esc_html__( 'Enable/Disable All', 'ultimate_vc' ); ?></label>
				</div>
				<form method="post" id="ultimate_modules">
					<input type="hidden" name="security" value="<?php echo esc_attr( wp_create_nonce( 'ultimate-modules-setting' ) ); ?>" />
					<input type="hidden" name="action" value="update_ultimate_modules" />
					<table class="form-table">
						<tbody>
							<?php
								$i             = 1;
								$checked_items = 0;
							foreach ( $modules as $module => $label ) {
								$checked = '';
								if ( is_array( $ultimate_modules ) && ! empty( $ultimate_modules ) ) {
									if ( in_array( strtolower( $module ), $ultimate_modules ) ) {
										$checked = 'checked="checked"';
										$checked_items++;
									} else {
										$checked = '';
									}
								}
								if ( ( $i % 2 ) == 1 ) {
									?>
									<tr valign="top" style="border-bottom: 1px solid #ddd;">
									<?php } ?>
										<th scope="row"><?php echo esc_html( $label['title']  ); ?></th>
										<td>
										<div class="onoffswitch">
											<input type="checkbox" <?php echo esc_attr( $checked ); ?> class="onoffswitch-checkbox" value="<?php echo esc_attr( strtolower( $module ) ); ?>" id="<?php echo esc_attr( strtolower( $module ) ); ?>" name="ultimate_modules[]" />

											<label class="onoffswitch-label" for="<?php echo esc_attr( strtolower( $module ) ); ?>">
												<div class="onoffswitch-inner">
													<div class="onoffswitch-active">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
													</div>
													<div class="onoffswitch-inactive">
														<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
													</div>
												</div>
											</label>
										</div>
										</td>
									<?php if ( ( $i % 2 ) == 1 ) { ?>
									<!-- </tr> -->
									<?php } ?>
							<?php $i++; } ?>
							<tr valign="top" style="border-bottom: 1px solid #ddd;">
								<th scope="row"><?php echo esc_html__( 'Row backgrounds', 'ultimate_vc' ); ?></th>
								<td> <div class="onoffswitch">
								<input type="checkbox" <?php echo esc_attr( $checked_row ); ?> id="ultimate_row" value="enable" class="onoffswitch-checkbox" name="ultimate_row" />
									<label class="onoffswitch-label" for="ultimate_row">
										<div class="onoffswitch-inner">
											<div class="onoffswitch-active">
												<div class="onoffswitch-switch"><?php echo esc_html__( 'ON', 'ultimate_vc' ); ?></div>
											</div>
											<div class="onoffswitch-inactive">
												<div class="onoffswitch-switch"><?php echo esc_html__( 'OFF', 'ultimate_vc' ); ?></div>
											</div>
										</div>
									</label>
									</div>
								</td>
								<th></th><td></td>
							</tr>
						</tbody>
					</table>
				</form>
				<p class="submit"><input type="submit" name="submit" id="submit-modules" class="button button-primary" value="<?php echo esc_attr__( 'Save Changes', 'ultimate_vc' ); ?>"></p>
			</div> <!-- #ultimate-modules -->
		</div> <!--col-md-12 -->
	</div> <!-- ultimate-content -->
	</div> <!-- bend-content-wrap -->
</div> <!-- .wrap-container -->
</div> <!-- .bend -->

<script type="text/javascript">
var submit_btn = jQuery("#submit-modules");
submit_btn.bind('click',function(e){
	e.preventDefault();
	var data = jQuery("#ultimate_modules").serialize();
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		dataType: 'html',
		type: 'post',
		success: function(result){
			result = jQuery.trim(result);
			console.log(result);
			if(result == "success"){
				jQuery("#msg").html('<div class="updated"><p><?php echo esc_html__( 'Settings updated successfully!', 'ultimate_vc' ); ?></p></div>');
				jQuery('html,body').animate({ scrollTop: 0 }, 300);
			} else {
				jQuery("#msg").html('<div class="error"><p><?php echo esc_html__( 'No settings were updated.', 'ultimate_vc' ); ?></p></div>');
				jQuery('html,body').animate({ scrollTop: 0 }, 300);
			}
		}
	});
});

jQuery(document).ready(function(e) {

	jQuery('.onoffswitch').click(function(){
		$switch = jQuery(this);
		setTimeout(function(){
			if($switch.find('.onoffswitch-checkbox').is(':checked'))
				$switch.find('.onoffswitch-checkbox').attr('checked',false);
			else
				$switch.find('.onoffswitch-checkbox').attr('checked',true);
			$switch.trigger('onUltimateSwitchClick');
		},300);

	});

	var checked_items = <?php echo esc_attr( $checked_items ); ?>;
	var all_modules = parseInt(jQuery('#ult-all-modules-toggle').data('all'));
	if(checked_items === all_modules) {
		jQuery('#ult-all-modules-toggle').attr('checked',true);
	}

	jQuery('#ult-all-modules-toggle').click(function(){
		var is_check = (jQuery(this).is(':checked')) ? true : false;
		jQuery('.onoffswitch').find('.onoffswitch-checkbox').attr('checked',is_check);
	});
});
</script>
<style type="text/css">
/*On Off Checkbox Switch*/
.onoffswitch {
	position: relative;
	width: 95px;
	display: inline-block;
	float: left;
	margin-right: 15px;
	-webkit-user-select:none;
	-moz-user-select:none;
	-ms-user-select: none;
}
.onoffswitch-checkbox {
	display: none !important;
}
.onoffswitch-label {
	display: block;
	overflow: hidden;
	cursor: pointer;
	border: 0px solid #999999;
	border-radius: 0px;
}
.onoffswitch-inner {
	width: 200%;
	margin-left: -100%;
	-moz-transition: margin 0.3s ease-in 0s;
	-webkit-transition: margin 0.3s ease-in 0s;
	-o-transition: margin 0.3s ease-in 0s;
	transition: margin 0.3s ease-in 0s;
}
.rtl .onoffswitch-inner{
	margin: 0;
}
.rtl .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
	margin-right: -100%;
	margin-left:auto;
}
.onoffswitch-inner > div {
	float: left;
	position: relative;
	width: 50%;
	height: 24px;
	padding: 0;
	line-height: 24px;
	font-size: 12px;
	color: white;
	font-weight: bold;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	box-sizing: border-box;
}
.onoffswitch-inner .onoffswitch-active {
	padding-left: 15px;
	background-color: #CCCCCC;
	color: #FFFFFF;
}
.onoffswitch-inner .onoffswitch-inactive {
	padding-right: 15px;
	background-color: #CCCCCC;
	color: #FFFFFF;
	text-align: right;
}
.onoffswitch-switch {
	/*width: 50px;*/
	width:35px;
	margin: 0px;
	text-align: center;
	border: 0px solid #999999;
	border-radius: 0px;
	position: absolute;
	top: 0;
	bottom: 0;
}
.onoffswitch-active .onoffswitch-switch {
	background: #3F9CC7;
	left: 0;
}
.onoffswitch-inactive .onoffswitch-switch {
	background: #7D7D7D;
	right: 0;
}
.onoffswitch-active .onoffswitch-switch:before {
	content: " ";
	position: absolute;
	top: 0;
	/*left: 50px;*/
	left:35px;
	border-style: solid;
	border-color: #3F9CC7 transparent transparent #3F9CC7;
	/*border-width: 12px 8px;*/
	border-width: 15px;
}
.onoffswitch-inactive .onoffswitch-switch:before {
	content: " ";
	position: absolute;
	top: 0;
	/*right: 50px;*/
	right:35px;
	border-style: solid;
	border-color: transparent #7D7D7D #7D7D7D transparent;
	/*border-width: 12px 8px;*/
	border-width: 50px;
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
	margin-left: 0;
}
#ultimate-settings, #ultimate-modules, .ult-tabs{ display:none; }
#ultimate-settings.active-tab, #ultimate-modules.active-tab, .ult-tabs.active-tab{ display:block; }
.ult-badge {
	padding-bottom: 10px;
	height: 170px;
	width: 150px;
	position: absolute;
	border-radius: 3px;
	top: 0;
	right: 0;
}
div#msg > .updated, div#msg > .error { display:block !important;}
div#msg {
	position: absolute;
	left: 20px;
	top: 140px;
	max-width: 30%;
}
.onoffswitch-inner:before,
.onoffswitch-inner:after {
	display:none
}
.onoffswitch-switch {
	height: initial !important;
	color: white !important;
}
</style>
