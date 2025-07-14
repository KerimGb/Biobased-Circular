<?php
/**
 * BSF CORE common functions.
 *
 * @package BSF CORE commom functions.
 */

if ( ! function_exists( 'bsf_get_option' ) ) {
	/**
	 * Bsf_get_option.
	 *
	 * @param bool $request Request.
	 */
	function bsf_get_option( $request = false ) {
		$bsf_options = get_option( 'bsf_options' );
		if ( ! $request ) {
			return $bsf_options;
		} else {
			return ( isset( $bsf_options[ $request ] ) ) ? $bsf_options[ $request ] : false;
		}
	}
}

// Include the necessary file to use get_plugins() function.
if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! function_exists( 'bsf_update_option' ) ) {
	/**
	 * Bsf_update_option.
	 *
	 * @param bool $request Request.
	 * @param bool $value Value.
	 */
	function bsf_update_option( $request, $value ) {
		$bsf_options             = get_option( 'bsf_options' );
		$bsf_options[ $request ] = $value;
		return update_option( 'bsf_options', $bsf_options );
	}
}
if ( ! function_exists( 'uavc_hex2rgb' ) ) {
	/**
	 * Ultimate_hex2rgb.
	 *
	 * @param string $hex Hex.
	 * @param string $opacity Opacity.
	 */
	function uavc_hex2rgb( $hex, $opacity = 1 ) {
		$hex = str_replace( '#', '', $hex );
		if ( 33 == strlen( $hex ) ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgba = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $opacity . ')';
		return $rgba; // returns an array with the rgb values.
	}
}
/**
 * Get_ultimate_vc_responsive_media_css.
 *
 * @param array $args Arguments.
 */
function get_ultimate_vc_responsive_media_css( $args ) {
	$content = '';
	if ( isset( $args ) && is_array( $args ) ) {
		// get targeted css class/id from array.
		if ( array_key_exists( 'target', $args ) ) {
			if ( ! empty( $args['target'] ) ) {
				$content .= " data-ultimate-target='" . esc_attr( $args['target'] ) . "' ";
			}
		}

		// get media sizes.
		if ( array_key_exists( 'media_sizes', $args ) ) {
			if ( ! empty( $args['media_sizes'] ) ) {
				$content .= " data-responsive-json-new='" . wp_json_encode( $args['media_sizes'] ) . "' ";
			}
		}
	}
	return $content;
}

if ( ! function_exists( 'uavc_img_single_init' ) ) {
	/**
	 * Ult_img_single_init.
	 *
	 * @param string $content Content.
	 * @param string $data Data.
	 * @param string $size Size.
	 */
	function uavc_img_single_init( $content = null, $data = '', $size = 'full' ) {

		$final = '';

		if ( '' != $content && 'null|null' != $content ) {

			// Create an array.
			$mainstr = explode( '|', (string) $content );
			$string  = '';
			$mainarr = array();

			$temp_id  = $mainstr[0];
			$temp_url = ( isset( $mainstr[1] ) ) ? $mainstr[1] : 'null';

			if ( ! empty( $mainstr ) && is_array( $mainstr ) ) {
				foreach ( $mainstr as $key => $value ) {
					if ( ! empty( $value ) ) {
						if ( stripos( $value, '^' ) !== false ) {
							$tmvav_array = explode( '^', $value );
							if ( is_array( $tmvav_array ) && ! empty( $tmvav_array ) ) {
								if ( ! empty( $tmvav_array ) ) {
									if ( isset( $tmvav_array[0] ) ) {
										$mainarr[ $tmvav_array[0] ] = ( isset( $tmvav_array[1] ) ) ? $tmvav_array[1] : '';
									}
								}
							}
						} else {
							$mainarr['id']  = $temp_id;
							$mainarr['url'] = $temp_url;
						}
					}
				}
			}

			if ( '' != $data ) {
				switch ( $data ) {
					case 'url':     // First  - Priority for ID.
						if ( ! empty( $mainarr['id'] ) && 'null' != $mainarr['id'] ) {

							$image_url = '';
							// Get image URL, If input is number - e.g. 100x48 / 140x40 / 350x53.
							if ( 1 === preg_match( '/^\d/', $size ) ) {
								$size = explode( 'x', $size );

								// resize image using vc helper function - wpb_resize.
								$img = wpb_resize( $mainarr['id'], null, $size[0], $size[1], true );
								if ( $img ) {
									$image_url = $img['url'];
								}
							} else {

								// Get image URL, If input is string - [thumbnail, medium, large, full].
								$hasimage  = wp_get_attachment_image_src( $mainarr['id'], $size ); // returns an array.
								$image_url = isset( $hasimage[0] ) ? $hasimage[0] : '';
							}

							if ( isset( $image_url ) && ! empty( $image_url ) ) {
								$final = $image_url;
							} else {

								// Second - Priority for URL - get {image from url}.
								if ( isset( $mainarr['url'] ) ) {
									$final = uavc_get_url( $mainarr['url'] );
								}
							}
						} else {
							// Second - Priority for URL - get {image from url}.
							if ( isset( $mainarr['url'] ) ) {
								$final = uavc_get_url( $mainarr['url'] );
							}
						}
						break;
					case 'title':
						$final = isset( $mainarr['title'] ) ? $mainarr['title'] : get_post_meta( $mainarr['id'], '_wp_attachment_image_title', true );
						break;
					case 'caption':
						$final = isset( $mainarr['caption'] ) ? $mainarr['caption'] : get_post_meta( $mainarr['id'], '_wp_attachment_image_caption', true );
						break;
					case 'alt':
						$final = isset( $mainarr['alt'] ) ? $mainarr['alt'] : get_post_meta( $mainarr['id'], '_wp_attachment_image_alt', true );
						break;
					case 'description':
						$final = isset( $mainarr['description'] ) ? $mainarr['description'] : get_post_meta( $mainarr['id'], '_wp_attachment_image_description', true );
						break;
					case 'json':
						$final = wp_json_encode( $mainarr );
						break;

					case 'sizes':
						$img_size = uavc_get_image_squere_size( $img_id, $img_size );

						$img   = wpb_getImageBySize(
							array(
								'attach_id'  => $img_id,
								'thumb_size' => $img_size,
								'class'      => 'vc_single_image-img',
							)
						);
						$final = $img;
						break;

					case 'array':
					default:
						$final = $mainarr;
						break;

				}
			}
		}

		return $final;
	}
	add_filter( 'ult_get_img_single', 'uavc_img_single_init', 10, 3 );
}

if ( ! function_exists( 'uavc_get_url' ) ) {
	/**
	 * Ult_get_url.
	 *
	 * @param string $img Img.
	 */
	function uavc_get_url( $img ) {
		if ( isset( $img ) && ! empty( $img ) ) {
			return $img;
		}
	}
}

// USE THIS CODE TO SUPPORT CUSTOM SIZE OPTION.
if ( ! function_exists( 'uavc_get_image_squere_size' ) ) {
	/**
	 * GetImageSquereSize.
	 *
	 * @param string $img_id Image ID.
	 * @param string $img_size Image Size.
	 */
	function uavc_get_image_squere_size( $img_id, $img_size ) {
		if ( preg_match_all( '/(\d+)x(\d+)/', $img_size, $sizes ) ) {
			$exact_size = array(
				'width'  => isset( $sizes[1][0] ) ? $sizes[1][0] : '0',
				'height' => isset( $sizes[2][0] ) ? $sizes[2][0] : '0',
			);
		} else {
			$image_downsize = image_downsize( $img_id, $img_size );
			$exact_size     = array(
				'width'  => $image_downsize[1],
				'height' => $image_downsize[2],
			);
		}

		if ( isset( $exact_size['width'] ) && (int) $exact_size['width'] !== (int) $exact_size['height'] ) {
			$img_size = (int) $exact_size['width'] > (int) $exact_size['height']
				? $exact_size['height'] . 'x' . $exact_size['height']
				: $exact_size['width'] . 'x' . $exact_size['width'];
		}

		return $img_size;
	}
}

/**
	 * Get the status of a theme.
	 *
	 * @param string $theme_slug The slug of the theme.
	 * @return string The theme status: 'Activated', 'Installed', or 'Install'.
	 *
	 * @since 0.0.1
	 */
	 function get_theme_status( $theme_slug ) {
		$installed_themes = wp_get_themes();
	
		// Check if the theme is installed.
		if ( isset( $installed_themes[ $theme_slug ] ) ) {
			$current_theme = wp_get_theme();
		
			// Check if the current theme slug matches the provided theme slug.
			if ( $current_theme->get_stylesheet() === $theme_slug ) {
				return 'Activated'; // Theme is active.
			} else {
				return 'Installed'; // Theme is installed but not active.
			}
		} else {
			return 'Install'; // Theme is not installed at all.
		}
	}

	/**
	 * Get plugin status
	 *
	 * @since 0.0.1
	 *
	 * @param  string $plugin_init_file Plugin init file.
	 * @return string
	 */
	 function get_plugin_status( $plugin_init_file ) {

		$installed_plugins = get_plugins();

		if ( ! isset( $installed_plugins[ $plugin_init_file ] ) ) {
			return 'Install';
		} elseif ( is_plugin_active( $plugin_init_file ) ) {
			return 'Activated';
		} else {
			return 'Installed';
		}
	}

	/**
	 * Provide General settings array().
	 *
	 * @since 2.2.1
	 * @return array()
	 */
	 function get_bsf_plugins_list() {

		if ( ! isset( $get_bsf_plugins_list ) ) {
			 $get_bsf_plugins_list = get_bsf_plugins();
		}

		return apply_filters( 'uavc_plugins_list', $get_bsf_plugins_list );
	}

	/**
	 * List of plugins that we propose to install.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	function get_bsf_plugins() {

		$images_url = UAVC_URL . 'admin/imagesa/';

		$plugins = [

			'astra'                                        => [
				'icon'         => $images_url . 'astra.svg',
				'type'         => 'theme',
				'name'         => esc_html__( 'Astra', 'ultimate_vc' ),
				'desc'         => esc_html__( 'Fast and customizable theme for your website.', 'ultimate_vc' ),
				'wporg'        => 'https://wordpress.org/themes/astra/',
				'url'          => 'https://downloads.wordpress.org/theme/astra.zip',
				'siteurl'      => ['author_url'],
				'slug'         => 'astra',
				'isFree'       => true,
				'status'       => get_theme_status( 'astra' ),
				'settings_url' => admin_url( 'admin.php?page=astra' ),
			],
				'surecart/surecart.php'                        => [
				'icon'         => $images_url . 'surecart.svg',
				'type'         => 'plugin',
				'name'         => esc_html__( 'SureCart', 'ultimate_vc' ),
				'desc'         => esc_html__( 'Sell your products easily on WordPress.', 'ultimate_vc' ),
				'wporg'        => 'https://wordpress.org/plugins/surecart/',
				'url'          => 'https://downloads.wordpress.org/plugin/surecart.zip',
				'siteurl'      => 'https://surecart.com/',
				'isFree'       => true,
				'slug'         => 'surecart',
				'status'       => get_plugin_status( 'surecart/surecart.php' ),
				'settings_url' => admin_url( 'admin.php?page=sc-getting-started' ),
			],

			'presto-player/presto-player.php'              => [
				'icon'         => $images_url . 'pplayer.svg',
				'type'         => 'plugin',
				'name'         => esc_html__( 'Presto Player', 'ultimate_vc' ),
				'desc'         => html_entity_decode( esc_html__( 'Display seamless & interactive videos.', 'ultimate_vc' ) ),
				'wporg'        => 'https://wordpress.org/plugins/presto-player/',
				'url'          => 'https://downloads.wordpress.org/plugin/presto-player.zip',
				'siteurl'      => 'https://prestoplayer.com/',
				'slug'         => 'presto-player',
				'isFree'       => true,
				'status'       => get_plugin_status( 'presto-player/presto-player.php' ),
				'settings_url' => admin_url( 'edit.php?post_type=pp_video_block' ),
			],

			'sureforms/sureforms.php'                      => [
				'icon'         => $images_url . 'sureforms.svg',
				'type'         => 'plugin',
				'name'         => esc_html__( 'SureForms', 'ultimate_vc' ),
				'desc'         => esc_html__( 'Create high-converting forms with ease.', 'ultimate_vc' ),
				'wporg'        => 'https://wordpress.org/plugins/sureforms/',
				'url'          => 'https://downloads.wordpress.org/plugin/sureforms.zip',
				'siteurl'      => 'https://sureforms.com/',
				'slug'         => 'sureforms',
				'isFree'       => true,
				'status'       => get_plugin_status( 'sureforms/sureforms.php' ),
				'settings_url' => admin_url( 'admin.php?page=sureforms_menu' ),
			],

			'suretriggers/suretriggers.php'                => [
				'icon'         => $images_url . 'OttoKit-Symbol-Primary.svg',
				'type'         => 'plugin',
				'name'         => esc_html__( 'OttoKit (Formerly SureTriggers)', 'ultimate_vc' ),
				'desc'         => esc_html__( 'Automate WordPress tasks effortlessly.', 'ultimate_vc' ),
				'wporg'        => 'https://wordpress.org/plugins/suretriggers/',
				'url'          => 'https://downloads.wordpress.org/plugin/suretriggers.zip',
				'siteurl'      => 'https://ottokit.com/',
				'slug'         => 'suretriggers',
				'isFree'       => true,
				'status'       => get_plugin_status( 'suretriggers/suretriggers.php' ),
				'settings_url' => admin_url( 'admin.php?page=suretriggers' ),
			],

			'all-in-one-schemaorg-rich-snippets/index.php' => [
				'icon'         => $images_url . 'aiosrs.svg',
				'type'         => 'plugin',
				'name'         => html_entity_decode( esc_html__( 'Schema – All In One Schema Rich Snippets', 'ultimate_vc' ) ),
				'desc'         => html_entity_decode( esc_html__( 'Boost SEO with rich results & structured data.', 'ultimate_vc' ) ),
				'wporg'        => 'https://wordpress.org/plugins/all-in-one-schemaorg-rich-snippets/',
				'url'          => 'https://downloads.wordpress.org/plugin/all-in-one-schemaorg-rich-snippets.zip',
				'siteurl'      => 'https://wordpress.org/plugins/all-in-one-schemaorg-rich-snippets/',
				'slug'         => 'all-in-one-schemaorg-rich-snippets',
				'isFree'       => true,
				'status'       => get_plugin_status( 'all-in-one-schemaorg-rich-snippets/index.php' ),
				'settings_url' => admin_url( 'admin.php?page=rich_snippet_dashboard' ),
			],

		];

		foreach ( $plugins as $key => $plugin ) {
			// Check if it's a plugin and is active.
			if ( 'plugin' === $plugin['type'] && is_plugin_active( $key ) ) {
				unset( $plugins[ $key ] );
			}

			if ( 'plugin' === $plugin['type'] && 'astra-sites/astra-sites.php' === $key ) {
				$st_pro_status = get_plugin_status( 'astra-pro-sites/astra-pro-sites.php' );
				if ( 'Installed' === $st_pro_status || 'Activated' === $st_pro_status ) {
					unset( $plugins[ $key ] );
				}
			}

			if ( 'theme' === $plugin['type'] ) {
				$current_theme = wp_get_theme();
				if ( $current_theme->get_stylesheet() === $plugin['slug'] ) {
					unset( $plugins[ $key ] );
				}
			}
		}

		return $plugins;
	}

	


/* Ultimate Box Shadow */
if ( ! function_exists( 'uavc_get_box_shadow' ) ) {
	/**
	 * GetImageSquereSize.
	 *
	 * @param string $content Content.
	 * @param string $data Image Data.
	 */
	function uavc_get_box_shadow( $content = null, $data = '' ) {
		// e.g.    horizontal:14px|vertical:20px|blur:30px|spread:40px|color:#81d742|style:inset|.
		$final = '';

		if ( '' != $content ) {

			// Create an array.
			$mainstr = explode( '|', $content );
			$string  = '';
			$mainarr = array();
			if ( ! empty( $mainstr ) && is_array( $mainstr ) ) {
				foreach ( $mainstr as $key => $value ) {
					if ( ! empty( $value ) ) {
						$string = explode( ':', $value );
						if ( is_array( $string ) ) {
							if ( ! empty( $string[1] ) && 'outset' != $string[1] ) {
								$mainarr[ $string[0] ] = $string[1];
							}
						}
					}
				}
			}

			$rm_bar   = str_replace( '|', '', $content );
			$rm_colon = str_replace( ':', ' ', $rm_bar );
			$rmkeys   = str_replace( 'horizontal', '', $rm_colon );
			$rmkeys   = str_replace( 'vertical', '', $rmkeys );
			$rmkeys   = str_replace( 'blur', '', $rmkeys );
			$rmkeys   = str_replace( 'spread', '', $rmkeys );
			$rmkeys   = str_replace( 'color', '', $rmkeys );
			$rmkeys   = str_replace( 'style', '', $rmkeys );
			$rmkeys   = str_replace( 'outset', '', $rmkeys );     // Remove outset from style - To apply {outset} box. shadow.

			if ( '' != $data ) {
				switch ( $data ) {
					case 'data':
						$final = $rmkeys;
						break;
					case 'array':
						$final = $mainarr;
						break;
					case 'css':
					default:
						$final = 'box-shadow:' . $rmkeys . ';';
						break;
				}
			} else {
				$final = 'box-shadow:' . $rmkeys . ';';
			}
		}

		return $final;
	}

	add_filter( 'ultimate_getboxshadow', 'uavc_get_box_shadow', 10, 3 );
}
