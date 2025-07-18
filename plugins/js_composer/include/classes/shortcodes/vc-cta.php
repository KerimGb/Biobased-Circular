<?php
/**
 * Class that handles specific [vc_cta] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_cta.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Cta
 *
 * @since 4.5
 */
class WPBakeryShortCode_Vc_Cta extends WPBakeryShortCode {

	/**
	 * Template variables list.
	 *
	 * @var array
	 */
	protected $template_vars = [];

	/**
	 * Build element template variables.
	 *
	 * @param array $atts
	 * @param string $content
	 * @throws \Exception
	 */
	public function buildTemplate( $atts, $content ) {
		$output = [];
		$inline_css = [];

		$main_wrapper_classes = [ 'vc_cta3' ];
		$container_classes = [];
		if ( ! empty( $atts['el_class'] ) ) {
			$main_wrapper_classes[] = $atts['el_class'];
		}
		if ( ! empty( $atts['style'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-style-' . $atts['style'];
		}
		if ( ! empty( $atts['shape'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-shape-' . $atts['shape'];
		}
		if ( ! empty( $atts['txt_align'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-align-' . $atts['txt_align'];
		}
		if ( ! empty( $atts['color'] ) && ! ( isset( $atts['style'] ) && 'custom' === $atts['style'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-color-' . $atts['color'];
		}
		if ( isset( $atts['style'] ) && 'custom' === $atts['style'] ) {
			if ( ! empty( $atts['custom_background'] ) ) {
				$inline_css[] = vc_get_css_color( 'background-color', $atts['custom_background'] );
			}
		}
		if ( ! empty( $atts['i_on_border'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-icons-on-border';
		}
		if ( ! empty( $atts['i_size'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-icon-size-' . $atts['i_size'];
		}
		if ( ! empty( $atts['i_background_style'] ) ) {
			$main_wrapper_classes[] = 'vc_cta3-icons-in-box';
		}

		if ( ! empty( $atts['el_width'] ) ) {
			$container_classes[] = 'vc_cta3-size-' . $atts['el_width'];
		}

		if ( ! empty( $atts['add_icon'] ) ) {
			$output[ 'icons-' . $atts['add_icon'] ] = $this->getVcIcon( $atts );
			$main_wrapper_classes[] = 'vc_cta3-icons-' . $atts['add_icon'];
		}

		if ( ! empty( $atts['add_button'] ) ) {
			$output[ 'actions-' . $atts['add_button'] ] = $this->getButton( $atts );
			$main_wrapper_classes[] = 'vc_cta3-actions-' . $atts['add_button'];
		}

		if ( ! empty( $atts['css_animation'] ) ) {
			$main_wrapper_classes[] = $this->getCSSAnimation( $atts['css_animation'] );
		}

		if ( ! empty( $atts['css'] ) ) {
			$main_wrapper_classes[] = vc_shortcode_custom_css_class( $atts['css'] );
		}

		$output['content'] = wpb_js_remove_wpautop( $content, true );
		$output['heading1'] = $this->getHeading( 'h2', $atts );
		$output['heading2'] = $this->getHeading( 'h4', $atts );
		$output['css-class'] = $main_wrapper_classes;
		$output['container-class'] = $container_classes;
		$output['inline-css'] = $inline_css;
		$this->template_vars = $output;
	}

	/**
	 * Get element heading.
	 *
	 * @param string $tag
	 * @param array $atts
	 * @return string
	 * @throws \Exception
	 */
	public function getHeading( $tag, $atts ) {
		if ( isset( $atts[ $tag ] ) && '' !== trim( $atts[ $tag ] ) ) {
			if ( isset( $atts[ 'use_custom_fonts_' . $tag ] ) && 'true' === $atts[ 'use_custom_fonts_' . $tag ] ) {
				$custom_heading = wpbakery()->getShortCode( 'vc_custom_heading' );
				$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_custom_heading', $atts, $tag . '_' );
				$data['font_container'] = implode( '|', array_filter( [
					'tag:' . $tag,
					$data['font_container'],
				] ) );
				$data['text'] = $atts[ $tag ]; // provide text to shortcode.

				return $custom_heading->render( array_filter( $data ) );
			} else {
				$inline_css = [];
				$inline_css_string = '';
				if ( isset( $atts['style'] ) && 'custom' === $atts['style'] ) {
					if ( ! empty( $atts['custom_text'] ) ) {
						$inline_css[] = vc_get_css_color( 'color', $atts['custom_text'] );
					}
				}
				if ( ! empty( $inline_css ) ) {
					$inline_css_string = ' style="' . implode( '', $inline_css ) . '"';
				}

				return '<' . $tag . $inline_css_string . '>' . wp_kses_post( $atts[ $tag ] ) . '</' . $tag . '>';
			}
		}

		return '';
	}

	/**
	 * Render element shortcode button as independent shortcode.
	 *
	 * @param array $atts
	 * @return string
	 * @throws \Exception
	 */
	public function getButton( $atts ) {
		$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_btn', $atts, 'btn_' );
		if ( $data ) {
			$btn = wpbakery()->getShortCode( 'vc_btn' );
			if ( is_object( $btn ) ) {
				return '<div class="vc_cta3-actions">' . $btn->render( array_filter( $data ) ) . '</div>';
			}
		}

		return '';
	}

	/**
	 * Get element icon output.
	 *
	 * @param array $atts
	 * @return string
	 * @throws \Exception
	 */
	public function getVcIcon( $atts ) {

		if ( empty( $atts['i_type'] ) ) {
			$atts['i_type'] = 'fontawesome';
		}
		$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_icon', $atts, 'i_' );
		if ( $data ) {
			$icon = wpbakery()->getShortCode( 'vc_icon' );
			if ( is_object( $icon ) ) {
				return '<div class="vc_cta3-icons">' . $icon->render( array_filter( $data ) ) . '</div>';
			}
		}

		return '';
	}

	/**
	 * Get template variable list.
	 *
	 * @param string $name
	 * @return mixed|string
	 */
	public function getTemplateVariable( $name ) {
		if ( is_array( $this->template_vars ) && isset( $this->template_vars[ $name ] ) ) {

			return $this->template_vars[ $name ];
		}

		return '';
	}
}
