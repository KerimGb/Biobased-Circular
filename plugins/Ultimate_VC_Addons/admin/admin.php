<?php
/**
 * UAVC admin settings
 *
 * @since-----
 * @package UABB admin settings
 */

if ( ! function_exists( 'bsf_get_option' ) ) {
	/**
	 * Function to get options
	 *
	 * @since ----
	 * @param mixed $request = false set the value to false.
	 * @access public
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
if ( ! function_exists( 'bsf_update_option' ) ) {
	/**
	 * Function to update options
	 *
	 * @since ----
	 * @param mixed $request request.
	 * @param mixed $value value.
	 * @access public
	 */
	function bsf_update_option( $request, $value ) {
		$bsf_options             = get_option( 'bsf_options' );
		$bsf_options[ $request ] = $value;
		return update_option( 'bsf_options', $bsf_options );
	}
}
add_action( 'wp_ajax_bsf_dismiss_notice', 'bsf_dismiss_notice' );
if ( ! function_exists( 'bsf_dismiss_notice' ) ) {
	/**
	 * Function to dismiss notice
	 *
	 * @since ----
	 * @access public
	 */
	function bsf_dismiss_notice() {
		check_ajax_referer( 'bsf-dismiss-notice-nonce', 'security' );
		$notice = isset( $_POST['notice'] ) ? sanitize_text_field( $_POST['notice'] ) : ' ';
		$x      = bsf_update_option( $notice, true );
		echo ( $x ) ? true : false;
		die();
	}
}

add_action( 'admin_init', 'bsf_core_check', 10 );
if ( ! function_exists( 'bsf_core_check' ) ) {
	/**
	 * Function core bsf
	 *
	 * @since ----
	 * @access public
	 */
	function bsf_core_check() {
		if ( ! defined( 'BSF_CORE' ) ) {
			if ( ! bsf_get_option( 'hide-bsf-core-notice' ) ) {
				add_action( 'admin_notices', 'bsf_core_admin_notice' );
			}
		}
	}
}

if ( ! function_exists( 'bsf_core_admin_notice' ) ) {
	/**
	 * Function to admin notice
	 *
	 * @since ----
	 * @access public
	 */
	function bsf_core_admin_notice() {
		?>
		<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				$(document).on( "click", ".bsf-notice", function() {
					var bsf_notice_name = $(this).attr("data-bsf-notice");
					$.ajax({
						url: ajaxurl,
						method: 'POST',
						data: {
							action: "bsf_dismiss_notice",
							security: "<?php echo esc_attr( wp_create_nonce( 'bsf-dismiss-notice-nonce' ) ); ?>",
							notice: bsf_notice_name
						},
						success: function(response) {
							console.log(response);
						}
					})
				})
			});
		})(jQuery);
		</script>
		<div class="bsf-notice update-nag notice is-dismissible" data-bsf-notice="hide-bsf-core-notice">
			<p><?php esc_html_e( 'License registration and extensions are not part of plugin/theme anymore. Kindly download and install "BSF CORE" plugin to manage your licenses and extensins.', 'bsf' ); ?></p> 
		<?php
	}
}

if ( isset( $_GET['hide-bsf-core-notice'] ) && 're-enable' === $_GET['hide-bsf-core-notice'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$x = bsf_update_option( 'hide-bsf-core-notice', false );
}

// end of common functions.

if ( ! class_exists( 'Ultimate_Admin_Area' ) ) {
	/**
	 * Function that initializes Admin Area
	 *
	 * @class Ultimate_Admin_Area
	 */
	class Ultimate_Admin_Area {

		/**
		 * Instance
		 * z
		 *
		 * @access private
		 * @var string Class object.
		 * @since 1.0.0
		 */
		private $menu_slug = 'uavc';

		/**
		 * Constructor function that constructs default values for the Ultimate Animation module.
		 *
		 * @method __construct
		 */
		public function __construct() {

			/* add admin menu */
			add_action( 'admin_menu', array( $this, 'register_brainstorm_menu' ), 99 );

			// Hide admin notices on UAVC pages
			add_action( 'admin_notices', array( $this, 'hide_admin_notices' ), 1 );
			add_action( 'all_admin_notices', array( $this, 'hide_admin_notices' ), 1 );

			add_action( 'admin_enqueue_scripts', array( $this, 'bsf_admin_scripts_updater' ), 1 );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
			add_action( 'wp_ajax_update_ultimate_options', array( $this, 'update_settings' ) );
			add_action( 'wp_ajax_update_ultimate_debug_options', array( $this, 'update_debug_settings' ) );
			add_action( 'wp_ajax_update_ultimate_modules', array( $this, 'update_modules' ) );
			add_action( 'wp_ajax_update_css_options', array( $this, 'update_css_options' ) );

			add_action( 'wp_ajax_uavc_bulk_activate_modules', array( $this, 'bulk_activate_modules' ) );
			add_action( 'wp_ajax_uavc_bulk_deactivate_modules', array( $this, 'bulk_deactivate_modules' ) );
			add_action( 'wp_ajax_uavc_activate_module', array( $this, 'activate_module' ) );
			add_action( 'wp_ajax_uavc_deactivate_module', array( $this, 'deactivate_module' ) );

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}
			add_action( 'wp_ajax_update_dev_notes', array( $this, 'update_dev_notes' ) );
			add_filter( 'update_footer', array( $this, 'debug_link' ), 999 );

			/* Flow content view */
			add_action( 'uavc_render_admin_page_content', [ $this, 'render_content' ], 10, 2 );

			// Settings page - register with higher priority to ensure it runs after other plugins
			add_action( 'admin_menu', [ $this, 'uavc_register_settings_page' ], 99 );
		}

		/**
		 * For debug link
		 *
		 * @since ----
		 * @param string $text text.
		 * @access public
		 */
		public function debug_link( $text ) {
			$screen = get_current_screen();
			$array  = array(
				'ultimate_page_ultimate-scripts-and-styles',
				'ultimate_page_ultimate-smoothscroll',
				'ultimate_page_ultimate-dashboard',
				'toplevel_page_about-ultimate',
				'ultimate_page_ultimate-product-license',
				'settings_page_ultimate-product-license',
				'admin_page_ultimate-debug-settings',
				'settings_page_ultimate-product-license-network',
			);

			if ( ! in_array( $screen->id, $array ) ) {
				return $text;
			}

			$author_extend = '';

			$link         = '';
			$debug_url    = admin_url( 'admin.php?page=ultimate-debug-settings' );
			$license_link = bsf_registration_page_url( "&activation_method=license-key$author_extend", '6892199' );

			if ( isset( $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$author_extend = '&author';
				$license_link  = add_query_arg( 'author', '', $license_link );
			}

			$link .= '<a href="' . $debug_url . ' ">Ultimate Addons debug settings</a>';

			return $link;
		}
		/**
		 * For bsf admin scripts.
		 *
		 * @since ----
		 * @param string $hook hooks.
		 * @access public
		 */
		public function bsf_admin_scripts_updater( $hook ) {
			if ( defined( 'OPN_VERSION' ) ) {
				// @codingStandardsIgnoreStart.
				$custom_css = "
					@font-face {
						font-family: 'opn';
						src:url('" . plugins_url( 'fonts/opn.eot', __FILE__ ) . "');
						src:url('" . plugins_url( 'fonts/opn.eot', __FILE__ ) . "') format('embedded-opentype'),
							url('" . plugins_url( 'fonts/opn.woff', __FILE__ ) . "') format('woff'),
							url('" . plugins_url( 'fonts/opn.ttf', __FILE__ ) . "') format('truetype'),
							url('" . plugins_url( 'fonts/opn.svg', __FILE__ ) . "') format('svg');
						font-weight: normal;
						font-style: normal;
					}
                                        .toplevel_page_about-ultimate > div.wp-menu-image:before,

					.toplevel_page_opn-settings > div.wp-menu-image:before {
						content: \"\\e600\" !important;
						font-family: 'opn' !important;
					}
				";
				wp_add_inline_style( 'wp-admin', $custom_css );
			}

                       $custom_css_styles = "
                                        @font-face {
                                                font-family: 'ultimate';
                                                src:url('" . plugins_url( 'fonts/ultimate.eot', __FILE__ ) . "');
                                                src:url('" . plugins_url( 'fonts/ultimate.eot', __FILE__ ) . "') format('embedded-opentype'),
                                                        url('" . plugins_url( 'fonts/ultimate.woff', __FILE__ ) . "') format('woff'),
                                                        url('" . plugins_url( 'fonts/ultimate.ttf', __FILE__ ) . "') format('truetype'),
                                                        url('" . plugins_url( 'fonts/ultimate.svg', __FILE__ ) . "') format('svg');
                                                font-weight: normal;
                                                font-style: normal;
                                        }
                                        .toplevel_page_about-ultimate > div.wp-menu-image:before,
                                        .toplevel_page_uavc > div.wp-menu-image:before {
                                                content: \"\\e600\" !important;
                                                font-family: 'ultimate' !important;
                                                speak: none;
                                                font-style: normal;
                                                font-weight: normal;
                                                font-variant: normal;
                                                text-transform: none;
                                                line-height: 1;
                                                -webkit-font-smoothing: antialiased;
                                                -moz-osx-font-smoothing: grayscale;
                                                font-size:20px;
                                        }
                                        .toplevel_page_about-ultimate a[href=\"admin.php?page=font-icon-Manager\"] {
                                            display: none !important;
                                        }
                                        .toplevel_page_about-ultimate a[href=\"admin.php?page=ultimate-font-manager\"] {
                                            display: none !important;
                                        }
                                        .wp-customizer .customize-control-ast-responsive-spacing .ast-spacing-responsive-btns button[type=\"button\"] {
                                                height: inherit;
                                        }
			";// @codingStandardsIgnoreEnd.
			wp_add_inline_style( 'wp-admin', $custom_css_styles );


			if ( 'post.php' == $hook ||
			'post-new.php' == $hook ||
				'ultimate_page_about-ultimate' == $hook ||
				'visual-composer_page_vc-roles' == $hook ||
				'toplevel_page_about-ultimate' == $hook ||
				'ultimate_page_ultimate-dashboard' == $hook ||
				'ultimate_page_ultimate-smoothscroll' == $hook ||
				'ultimate_page_ultimate-scripts-and-styles' == $hook ||
				'ultimate_page_ultimate-product-license' == $hook ||
				'admin_page_ultimate-debug-settings' == $hook ||
				'ultimate_page_bsf-google-maps' == $hook ||
				'settings_page_ultimate-product-license' == $hook ) {

				$css_ext = '.min.css';
				if ( is_rtl() ) {
					$css_ext = '.min-rtl.css';
				}

				$bsf_dev_mode = bsf_get_option( 'dev_mode' );
				wp_enqueue_script( 'jquery-migrate' );
				wp_register_style( 'ultimate-vc-addons-admin-style', UAVC_URL . 'admin/css/style.css', null, ULTIMATE_VERSION );

				wp_register_style( 'ultimate-vc-addons-chosen-style', UAVC_URL . 'admin/vc_extend/css/chosen.css', null, ULTIMATE_VERSION );
				wp_register_script( 'ultimate-vc-addons-chosen-script', UAVC_URL . 'admin/vc_extend/js/chosen.js', null, ULTIMATE_VERSION, true );

				wp_register_script( 'ultimate-vc-addons-backend-script', UAVC_URL . 'admin/js/ultimate-vc-backend.min.js', array( 'jquery' ), ULTIMATE_VERSION, true );
				wp_register_style( 'ultimate-vc-addons-backend-style', UAVC_URL . 'admin/css/ultimate-vc-backend' . $css_ext, null, ULTIMATE_VERSION );

				if ( 'enable' === $bsf_dev_mode ) {
					wp_enqueue_style( 'ultimate-vc-addons-admin-style' );
				} else {
					wp_enqueue_style( 'wp-color-picker' );
					wp_enqueue_script( 'ultimate-vc-addons-backend-script' );
					wp_enqueue_style( 'ultimate-vc-addons-backend-style' );
				}
			}

			wp_register_script( 'ultimate-vc-addons-admin-media', UAVC_URL . 'admin/js/admin-media.js', array( 'jquery' ), ULTIMATE_VERSION, false );
			wp_enqueue_script( 'ultimate-vc-addons-admin-media' );

			wp_localize_script(
				'ultimate-vc-addons-admin-media',
				'uavc',
				array(
					'add_zipped_font'        => wp_create_nonce( 'smile-add-zipped-fonts-nonce' ),
					'remove_zipped_font'     => wp_create_nonce( 'smile-remove-zipped-fonts-nonce' ),
					'get_font_variants'      => wp_create_nonce( 'uavc-get-font-variants-nonce' ),
					'ult_get_attachment_url' => wp_create_nonce( 'uavc-get-attachment-url-nonce' ),
				)
			);
		}//end bsf_admin_scripts_updater()

		/**
		 * For regester menu.
		 *
		 * @since ----
		 * @access public
		 */
		public function register_brainstorm_menu() {

			if ( is_multisite() && ! current_user_can( 'manage_network_options' ) ) {
				return;
			} else {
				$role = 'manage_network_options';
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			} else {
				$role = 'manage_options';
			}

			global $submenu;

			if ( defined( 'BSF_MENU_POS' ) ) {
				$required_place = BSF_MENU_POS;
			} else {
				$required_place = 200;
			}

			if ( function_exists( 'bsf_get_free_menu_position' ) ) {
				$place = bsf_get_free_menu_position( $required_place, 1 );
			} else {
				$place = null;
			}

             
			// Add sub-menu for OPN if OPN in installed - {One Page Navigator}.
			if ( defined( 'OPN_VERSION' ) ) {
				if ( defined( 'BSF_MENU_POS' ) ) {
					$required_place = BSF_MENU_POS;
				} else {
					$required_place = 200;
				}

				if ( function_exists( 'bsf_get_free_menu_position' ) ) {
					$place = bsf_get_free_menu_position( $required_place, 1 );
				} else {
					$place = null;
				}

				$page = add_menu_page(
					'OPN',
					'OPN',
					'administrator',
					'opn-settings',
					array( $this, 'load_opn' ),
					'dashicons-admin-generic',
					$place
				);
			}

			// section wise menu.
			global $bsf_section_menu;
			$section_menu       = array(
				'menu'          => 'ultimate-resources',
				'is_down_arrow' => true,
			);
			$bsf_section_menu[] = $section_menu;

			$icon_manager_page = add_submenu_page(
				'about-ultimate',
				__( 'Icon Manager', 'ultimate_vc' ),
				__( 'Icon Manager', 'ultimate_vc' ),
				$role,
				'bsf-font-icon-manager',
				array( $this, 'ultimate_icon_manager_menu' )
			);

			$AIO_Icon_Manager = new Ultimate_VC_Addons_Icon_Manager();// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			add_action( 'admin_print_scripts-' . $icon_manager_page, array( $AIO_Icon_Manager, 'admin_scripts' ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			$Ultimate_Google_Font_Manager = new Ultimate_VC_Addons_Google_Font_Manager(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			$google_font_manager_page     = add_submenu_page(
				'about-ultimate',
				__( 'Google Font Manager', 'ultimate_vc' ),
				__( 'Google Fonts', 'ultimate_vc' ),
				$role,
				'bsf-google-font-manager',
				array( $Ultimate_Google_Font_Manager, 'ultimate_font_manager_dashboard' ) // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			);
			add_action( 'admin_print_scripts-' . $google_font_manager_page, array( $Ultimate_Google_Font_Manager, 'admin_google_font_scripts' ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			$google_font_manager_page = add_submenu_page(
				'about-ultimate',
				__( 'Google Maps', 'ultimate_vc' ),
				__( 'Google Maps', 'ultimate_vc' ),
				$role,
				'bsf-google-maps',
				array( $this, 'ultimate_google_maps_dashboard' )
			);

			// must be at end of all sub menu.

			$submenu['about-ultimate'][0][0] = __( 'About', 'ultimate_vc' );// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		}

		/**
 * Get all UAVC modules list.
 *
 * @return array List of all UAVC modules.
 */
public static function get_all_modules_list() {
    $modules = [];
    $modules_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'modules/';
    
    if ( is_dir( $modules_dir ) ) {
        $module_files = glob( $modules_dir . '*.php' );
        
        foreach ( $module_files as $file ) {
            // Skip index.php and utility files
            $filename = basename( $file );
            if ( 'index.php' === $filename || 'ultimate_functions.php' === $filename ) {
                continue;
            }
            
            // Get module information
            $module_data = self::get_module_data( $file );
            
            if ( $module_data ) {
                $modules[] = $module_data;
            }
        }
    }
    
    return $modules;
}

/**
 * Extract module data from file.
 *
 * @param string $file Path to module file.
 * @return array|false Module data or false if not a valid module.
 */
private static function get_module_data( $file ) {
    // Get file contents
    $file_content = file_get_contents( $file );
    
    if ( ! $file_content ) {
        return false;
    }
    
    $filename = basename( $file );
    $module_name = str_replace( ['ultimate_', '.php'], ['', ''], $filename );
    $module_name = str_replace( '_', ' ', $module_name );
    $module_name = ucwords( $module_name );
    
    // Try to extract class name if exists
    $class_name = '';
    if ( preg_match( '/class\s+([a-zA-Z0-9_]+)/', $file_content, $matches ) ) {
        $class_name = $matches[1];
    }
    
    return [
        'id' => sanitize_title( $module_name ),
        'name' => $module_name,
        'file' => $filename,
        'path' => $file,
        'class' => $class_name,
        'active' => self::is_module_active( sanitize_title( $module_name ) ),
    ];
}

/**
 * Check if a module is active.
 *
 * @param string $module_id Module ID.
 * @return boolean True if active, false otherwise.
 */
private static function is_module_active( $module_id ) {
    $modules = get_option( '_uavc_modules', [] );
    return isset( $modules[$module_id] ) && $modules[$module_id] !== 'disabled';
}

/**
 * Bulk activate all modules
 */
public function bulk_activate_modules() {
    check_ajax_referer( 'wp_rest', 'nonce' );

    // Get all module keys from the modules array in modules.php
    ob_start();
    include_once(dirname(__FILE__) . '/modules.php');
    ob_end_clean();
    
    $new_modules = array();
    
    if (isset($modules) && is_array($modules)) {
        foreach ($modules as $key => $module) {
            $new_modules[] = strtolower($key);
        }
    }

    // Update modules settings - make sure we're storing an array of strings
    $new_modules = array_map('strval', $new_modules);
    update_option( 'ultimate_modules', $new_modules );
    update_option( 'ultimate_row', 'enable' );

    // Send a JSON response
    wp_send_json_success(array(
        'message' => 'Modules activated successfully.',
        'modules' => $new_modules
    ));
}

/**
 * Deactivate all modules
 */
public function bulk_deactivate_modules() {
    check_ajax_referer( 'wp_rest', 'nonce' );

    // Empty the modules array to deactivate all modules
    update_option( 'ultimate_modules', array() );
    update_option( 'ultimate_row', 'disable' );

    // Send a JSON response
    wp_send_json_success(array(
        'message' => 'Modules deactivated successfully.'
    ));
}

/**
 * Deactivate a specific module
 */
public function deactivate_module() {
    check_ajax_referer( 'wp_rest', 'nonce' );

    $module_id = isset( $_POST['module_id'] ) ? sanitize_text_field( $_POST['module_id'] ) : '';
    $modules = get_option( 'ultimate_modules', array() );
    
    // Ensure $modules is an array of strings
    $modules = array_map('strval', $modules);

    if ( ! empty( $module_id ) ) {
        $module_id_lower = strtolower( $module_id );

        if ( 'row_backgrounds' === $module_id_lower ) {
            update_option( 'ultimate_row', 'disable' );
        } else {
            // Remove the module from the active modules list
            $key = array_search( $module_id_lower, $modules );
            if ( false !== $key ) {
                unset( $modules[ $key ] );
                // Re-index the array
                $modules = array_values( $modules );
            }

            // Update modules
            update_option( 'ultimate_modules', $modules );
        }
    }

    wp_send_json_success(array(
        'module_id' => $module_id,
        'status' => 'deactivated'
    ));
}

/**
 * Activate a specific module
 */
public function activate_module() {
    check_ajax_referer( 'wp_rest', 'nonce' );

    $module_id = isset( $_POST['module_id'] ) ? sanitize_text_field( $_POST['module_id'] ) : '';
    $modules = get_option( 'ultimate_modules', array() );
    
    // Ensure $modules is an array of strings
    $modules = array_map('strval', $modules);

    if ( ! empty( $module_id ) ) {
        // Add the module to the active modules list if it's not already there
        $module_id_lower = strtolower( $module_id );
        if ( 'row_backgrounds' === $module_id_lower ) {
            update_option( 'ultimate_row', 'enable' );
        } else {
            if ( ! in_array( $module_id_lower, $modules, true ) ) {
                $modules[] = $module_id_lower;
            }

            // Update modules
            update_option( 'ultimate_modules', $modules );
        }
    }

    wp_send_json_success(array(
        'module_id' => $module_id,
        'status' => 'activated'
    ));
}

			/**
	 * Hide admin notices on the custom settings page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function hide_admin_notices() {
		$screen = get_current_screen();
		
		// Define pages where notices should be hidden
		$pages_to_hide_notices = array(
			'toplevel_page_uavc',     // Top level UAVC page
			'toplevel_page_about-ultimate', // About Ultimate page
			'ultimate_page_ultimate-dashboard', // Ultimate dashboard
			'ultimate_page_ultimate-smoothscroll', // Smooth scroll settings
			'ultimate_page_ultimate-scripts-and-styles', // Scripts and styles
			'ultimate_page_bsf-font-icon-manager', // Icon manager
			'ultimate_page_bsf-google-font-manager', // Google font manager
			'ultimate_page_bsf-google-maps', // Google maps
		);

		if ( in_array( $screen->id, $pages_to_hide_notices ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * CHeck if it is current page by parameters
	 *
	 * @param string $page_slug Menu name.
	 * @param string $action Menu name.
	 *
	 * @return  string page url
	 */
	public static function is_current_page( $page_slug = '', $action = '' ) {

		$page_matched = false;

		if ( empty( $page_slug ) ) {
			return false;
		}

		$current_page_slug = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_action    = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! is_array( $action ) ) {
			$action = explode( ' ', $action );
		}

		if ( $page_slug === $current_page_slug && in_array( $current_action, $action, true ) ) {
			$page_matched = true;
		}

		return $page_matched;
	}

		/**
		 * Renders the admin settings content.
		 *
		 * @since 1.0.0
		 * @param string $menu_page_slug current page name.
		 * @param string $page_action current page action.
		 *
		 * @return void
		 */
		public function render_content() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( self::is_current_page( 'uavc' ) ) {
				include_once UAVC_DIR . 'inc/settings/settings-app.php';
			}
		}

			/**
	 * Show a settings page incase of unsupported theme.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function uavc_register_settings_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Determine the appropriate capability based on multisite settings
		if ( is_multisite() && ! current_user_can( 'manage_network_options' ) ) {
			return;
		} else {
			$role = 'manage_network_options';
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		} else {
			$role = 'manage_options';
		}

		global $submenu;

		// Set the desired menu position - after WP Bakery and other similar plugins
		if ( defined( 'BSF_MENU_POS' ) ) {
			$required_place = BSF_MENU_POS;
		} else {
			$required_place = 200; // Default position after most plugins
		}

		// Try to get a free menu position using BSF helper function if available
		if ( function_exists( 'bsf_get_free_menu_position' ) ) {
			$place = bsf_get_free_menu_position( $required_place, 1 );
		} else {
			$place = $required_place;
		}

		$menu_slug  = 'uavc';
		$capability = 'manage_options';

		add_menu_page(
			__( 'Ultimate', 'ultimate_vc' ),
			__( 'Ultimate', 'ultimate_vc' ),
			$capability,
			$menu_slug,
			array($this, 'render'),
                       '',
                       $place
       );

	// Add the Dashboard Submenu.
	add_submenu_page(
			$menu_slug,
			__( 'UAVC', 'ultimate_vc' ),
			__( 'Dashboard', 'ultimate_vc' ),
			$capability,
			$menu_slug . '#dashboard',
			array($this, 'render'),
			1
	);

	// Remove the default submenu added by add_menu_page.
	remove_submenu_page( $menu_slug, $menu_slug );

			// Add the Settings Submenu.
			add_submenu_page(
				$menu_slug,
				__( 'UAVC', 'ultimate_vc' ),
				__( 'Elements', 'ultimate_vc' ),
				$capability,
				$menu_slug . '#elements',
				array($this, 'render'),
				99
			);

			// Add the Settings Submenu.
			add_submenu_page(
				$menu_slug,
				__( 'UAVC', 'ultimate_vc' ),
				__( 'Settings', 'ultimate_vc' ),
				$capability,
				$menu_slug . '#settings',
				array($this, 'render'),
				99
			);
	}

	/**
	 * Settings page.
	 *
	 * Call back function for add submenu page function.
	 *
	 * @since 2.2.1
	 * @return void
	 */
	public function render() {

		$menu_page_slug = ( ! empty( $_GET['page'] ) ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : $this->menu_slug; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page_action    = '';
   
		if ( isset( $_GET['action'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page_action = sanitize_text_field( wp_unslash( $_GET['action'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page_action = str_replace( '_', '-', $page_action );
		}
   
		include_once UAVC_DIR . 'inc/settings/admin-base.php';
	}

	/**
	 * Get WP Bakery Page edit link
	 */
	public static function get_uavc_new_page_url() {
		if ( class_exists( 'WPBakeryVisualComposerAbstract' ) && current_user_can( 'edit_pages' ) ) {
			// First create a new draft page to get an ID
			$post_id = wp_insert_post(
				array(
					'post_title'    => 'New WP Bakery Page',
					'post_type'     => 'page',
					'post_status'   => 'draft',
				)
			);
			
			if ( $post_id && !is_wp_error( $post_id ) ) {
				// Now create the URL with the new post ID
				$query_args = array(
					'vc_action'             => 'vc_inline',
					'post_id'               => $post_id,
					'post_type'             => 'page',
					'vc_post_custom_layout' => 'blank'
				);
				
				$new_post_url = add_query_arg( $query_args, admin_url( 'post.php' ) );
				
				return $new_post_url;
			}
		}
		return '';
	}

	/**
	 * Load admin styles on UAVC  screen.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts() {

		global $pagenow, $post_type;
		
		wp_enqueue_script(
			'uavc-react-app',
			ULTIMATE_URL . 'build/main.js',
			array( 'wp-element', 'wp-dom-ready', 'wp-api-fetch' ),
			ULTIMATE_VERSION,
			true
		);

		wp_enqueue_style(
			'uavc-react-styles',
			ULTIMATE_URL . 'build/main.css',
			array(),
			ULTIMATE_VERSION
		);

		wp_localize_script(
			'uavc-react-app',
			'uavcSettingsData',
			array(
				'uavc_nonce_action' => wp_create_nonce( 'wp_rest' ),
				'installer_nonce'   => wp_create_nonce( 'updates' ),
				'nonce'             => wp_create_nonce('wp_rest'),
				'uavc_current_version'  => defined( 'ULTIMATE_VERSION' ) ? ULTIMATE_VERSION : '',
				'logo_url' => ULTIMATE_URL . 'admin/imagesa/logo.svg',
				'video_control'                       => ULTIMATE_URL . 'admin/imagesa/video-control.svg',
				'license_url' => ULTIMATE_URL . 'admin/imagesa/license.png',
				'explore_banner' => ULTIMATE_URL . 'admin/imagesa/Explore_banner.png',
				'welcome_banner' => ULTIMATE_URL . 'admin/imagesa/Welcome_banner.png',
				'success_banner' => ULTIMATE_URL . 'admin/imagesa/frame.png',
				'confetti_banner' => ULTIMATE_URL . 'admin/imagesa/confetti.png',
				'user_url'                 => ULTIMATE_URL . 'admin/imagesa/editor.svg',
				'user__selected_url'       => ULTIMATE_URL . 'admin/imagesa/editor_selected.svg',
				'advanced_url'                 => ULTIMATE_URL . 'admin/imagesa/advanced.svg',
				'advanced__selected_url'       => ULTIMATE_URL . 'admin/imagesa/advanced_selected.svg',
				'editor_url'                 => ULTIMATE_URL . 'admin/imagesa/user.svg',
				'editor__selected_url'       => ULTIMATE_URL . 'admin/imagesa/user_selected.svg',
                'font_banner' => ULTIMATE_URL . 'admin/imagesa/Font.png',
                'icon_manager_url'       => ULTIMATE_URL . 'admin/imagesa/icon_manager.svg',
                'icon_manager_selected_url' => ULTIMATE_URL . 'admin/imagesa/icon_manager_selected.svg',
                'google_fonts_url'       => ULTIMATE_URL . 'admin/imagesa/google_fonts.svg',
                'google_fonts_selected_url' => ULTIMATE_URL . 'admin/imagesa/google_fonts_selected.svg',
                'google_map_icon' => ULTIMATE_URL . 'admin/imagesa/google_maps.svg',
                'google_map_selected' => ULTIMATE_URL . 'admin/imagesa/google_maps_selected.svg',
                'debug_url' => ULTIMATE_URL . 'admin/imagesa/info_box.svg',
                'debug_selected_url' => ULTIMATE_URL . 'admin/imagesa/info_box.svg',
				'highlight_url' => ULTIMATE_URL . 'admin/imagesa/highlight_box.png',
				'fancy_text_url' => ULTIMATE_URL . 'admin/imagesa/fancy_text.png',
				'info_banner_url' => ULTIMATE_URL . 'admin/imagesa/info_banner.png',
				'ihover_url' => ULTIMATE_URL . 'admin/imagesa/ihover.png',
				'row_separator_effect_url' => ULTIMATE_URL . 'admin/imagesa/row_separator_effect.png',
				'parallax_fade_effect_url' => ULTIMATE_URL . 'admin/imagesa/parallax_fade_effect.png',
				'overlay_effects_url' => ULTIMATE_URL . 'admin/imagesa/overlay_effects.png',
				'advanced_carousel_url' => ULTIMATE_URL . 'admin/imagesa/advanced_carousel.png',
				'interactive_banner_2_url' => ULTIMATE_URL . 'admin/imagesa/interactive_banner_2.png',
				'countdown_url' => ULTIMATE_URL . 'admin/imagesa/countdown.png',
				'counter_url' => ULTIMATE_URL . 'admin/imagesa/counter.png',
				'google_trends_url' => ULTIMATE_URL . 'admin/imagesa/google_trends.png',
				'flip_box_url' => ULTIMATE_URL . 'admin/imagesa/flip_box.png',
                'ajax_url'                 => admin_url( 'admin-ajax.php' ),
                                'ajax_nonce'               => wp_create_nonce( 'uavc-widget-nonce' ),
			)
		);

		// Add license data for Envato activation
		$product_id      = '6892199';

		// Build redirect URL similar to BSF_Envato_Activate::get_redirect_url().
		if ( is_ssl() ) {
				$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} else {
				$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}

		$redirect = esc_url(
				remove_query_arg(
						array( 'license_action', 'token', 'product_id', 'purchase_key', 'success', 'status', 'message' ),
						$redirect
				)
		);

	   $redirect = add_query_arg( array( 'bsf-inline-license-form' => $product_id ), $redirect );

	   wp_localize_script(
			   'uavc-react-app',
			   'uavcLicense',
			   array(
					   'product_id'              => $product_id,
					   'url'                     => admin_url( 'admin.php?page=ultimate-dashboard' ),
					   'redirect'                => $redirect,
					   'hash'                    => '#settings',
					   'envato_activation_nonce' => wp_create_nonce( 'envato_activation_nonce' ),
			   )
	   );

		wp_enqueue_script( 'uavc-admin-script', ULTIMATE_URL . 'admin/js/admin-media.js', array( 'jquery', 'updates' ), ULTIMATE_VERSION, true );
		wp_enqueue_script( 'uavc-admin-script', ULTIMATE_URL . 'admin/js/admin-update.js', array( 'jquery', 'updates' ), ULTIMATE_VERSION, true );
		wp_enqueue_script( 'uavc-admin-script', ULTIMATE_URL . 'admin/js/dualbtnbackend.js', array( 'jquery', 'updates' ), ULTIMATE_VERSION, true );
		wp_enqueue_script( 'uavc-admin-script', ULTIMATE_URL . 'admin/js/dualbnfront.js', array( 'jquery', 'updates' ), ULTIMATE_VERSION, true );
		wp_enqueue_script( 'uavc-admin-script', ULTIMATE_URL . 'admin/js/google-fonts-admin.js', array( 'jquery', 'updates' ), ULTIMATE_VERSION, true );
		wp_enqueue_script( 'uavc-admin-script', ULTIMATE_URL . 'admin/js/jquery_creative_link.js', array( 'jquery', 'updates' ), ULTIMATE_VERSION, true );
		wp_enqueue_script( 'uavc-admin-script', ULTIMATE_URL . 'admin/jquery-colorpicker.js', array( 'jquery', 'updates' ), ULTIMATE_VERSION, true );
		wp_enqueue_script( 'uavc-admin-script', ULTIMATE_URL . 'admin/team-admin.js', array( 'jquery', 'updates' ), ULTIMATE_VERSION, true );

		wp_enqueue_style( 'uavc-admin-style', ULTIMATE_URL . 'admin/css/style.css', [], ULTIMATE_VERSION );
		
		$strings = [
			'addon_activate'        => esc_html__( 'Activate', 'ultimate_vc' ),
			'addon_activated'       => esc_html__( 'Activated', 'ultimate_vc' ),
			'addon_active'          => esc_html__( 'Active', 'ultimate_vc' ),
			'addon_deactivate'      => esc_html__( 'Deactivate', 'ultimate_vc' ),
			'addon_inactive'        => esc_html__( 'Inactive', 'ultimate_vc' ),
			'addon_install'         => esc_html__( 'Install', 'ultimate_vc' ),
			'theme_installed'       => esc_html__( 'Theme Installed', 'ultimate_vc' ),
			'plugin_installed'      => esc_html__( 'Plugin Installed', 'ultimate_vc' ),
			'addon_download'        => esc_html__( 'Download', 'ultimate_vc' ),
			'addon_exists'          => esc_html__( 'Already Exists.', 'ultimate_vc' ),
			'visit_site'            => esc_html__( 'Visit Website', 'ultimate_vc' ),
			'nonce'                 => wp_create_nonce( 'uavc-admin-nonce' ),
			'installer_nonce'       => wp_create_nonce( 'updates' ),
			'ajax_url'                 => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('wp_rest'),
			
		];

		$strings = apply_filters( 'uavc_admin_strings', $strings );
	
		wp_localize_script(
			'uavc-admin-script',
			'uavc_admin_data',
			$strings
		);
	}

		/**
		 * For loading options.
		 *
		 * @since ----
		 * @access public
		 */
		public function load_opn() {
			if ( class_exists( 'OPN_Navigator' ) ) {
				$OPN_Navigator = new OPN_Navigator(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
				$OPN_Navigator->opn_settings(); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			}
		}
		/**
		 * For icon manager.
		 *
		 * @since ----
		 * @access public
		 */
		public function ultimate_icon_manager_menu() {
			$AIO_Icon_Manager = new Ultimate_VC_Addons_Icon_Manager();// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			$AIO_Icon_Manager->icon_manager_dashboard();// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function load_modules() {
			require_once plugin_dir_path( __FILE__ ) . '/modules.php';
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function load_dashboard() {
			require_once plugin_dir_path( __FILE__ ) . '/dashboard.php';
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function load_about() {
			require_once plugin_dir_path( __FILE__ ) . '/about.php';
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function load_smoothscroll() {
			require_once plugin_dir_path( __FILE__ ) . '/smooth-scroll-setting.php';
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function load_scripts_styles() {
			require_once plugin_dir_path( __FILE__ ) . '/script-styles.php';
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function product_license() {
			require_once plugin_dir_path( __FILE__ ) . '/product-license.php';
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function load_debug_settings() {
			require_once plugin_dir_path( __FILE__ ) . '/debug.php';
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function ultimate_resources() {
			$connects = false;
			require_once plugin_dir_path( __FILE__ ) . '/resources.php';
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function ultimate_google_maps_dashboard() {
			require_once plugin_dir_path( __FILE__ ) . '/map-settings.php';
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function update_modules() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			check_ajax_referer( 'ultimate-modules-setting', 'security' );

			if ( isset( $_POST['ultimate_row'] ) ) {
				$ultimate_row = sanitize_text_field( $_POST['ultimate_row'] );
			} else {
				$ultimate_row = 'disable';
			}
			$result1 = update_option( 'ultimate_row', $ultimate_row );

			$ultimate_modules = array();
			if ( isset( $_POST['ultimate_modules'] ) ) {
				$ultimate_modules = array_map( 'sanitize_text_field', $_POST['ultimate_modules'] );
			}
			$result2 = update_option( 'ultimate_modules', $ultimate_modules );

			if ( $result1 || $result2 ) {
				echo 'success';
			} else {
				echo 'failed';
			}
			die();
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function can_access_admin() {
			$bsf_ultimate_roles = bsf_get_option( 'ultimate_roles' );
			if ( false == $bsf_ultimate_roles || empty( $bsf_ultimate_roles ) ) {
				$bsf_ultimate_roles = array( 'administrator' );
			}

			if ( ! in_array( 'administrator', $bsf_ultimate_roles ) ) {
				array_push( $bsf_ultimate_roles, 'administrator' );
			}

			$user      = wp_get_current_user();
			$user_role = $user->roles[0];

			if ( in_array( $user_role, $bsf_ultimate_roles ) ) {
				return $user_role;
			}
			return false;
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function update_debug_settings() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			check_ajax_referer( 'ultimate-debug-settings', 'security' );

			if ( isset( $_POST['ultimate_video_fixer'] ) ) {
				$ultimate_video_fixer = sanitize_text_field( $_POST['ultimate_video_fixer'] );
			} else {
				$ultimate_video_fixer = 'disable';
			}
			$result1 = update_option( 'ultimate_video_fixer', $ultimate_video_fixer );

			if ( isset( $_POST['ultimate_ajax_theme'] ) ) {
				$ultimate_ajax_theme = sanitize_text_field( $_POST['ultimate_ajax_theme'] );
			} else {
				$ultimate_ajax_theme = 'disable';
			}
			$result2 = update_option( 'ultimate_ajax_theme', $ultimate_ajax_theme );

			if ( isset( $_POST['ultimate_custom_vc_row'] ) ) {
				$ultimate_custom_vc_row = sanitize_text_field( $_POST['ultimate_custom_vc_row'] );
			} else {
				$ultimate_custom_vc_row = '';
			}
			$result3 = update_option( 'ultimate_custom_vc_row', $ultimate_custom_vc_row );

			if ( isset( $_POST['ultimate_theme_support'] ) ) {
				$ultimate_theme_support = sanitize_text_field( $_POST['ultimate_theme_support'] );
			} else {
				$ultimate_theme_support = 'disable';
			}
			$result4 = update_option( 'ultimate_theme_support', $ultimate_theme_support );

			if ( isset( $_POST['ultimate_rtl_support'] ) ) {
				$ultimate_rtl_support = sanitize_text_field( $_POST['ultimate_rtl_support'] );
			} else {
				$ultimate_rtl_support = 'disable';
			}
			$result5 = update_option( 'ultimate_rtl_support', $ultimate_rtl_support );

			if ( isset( $_POST['ultimate_modal_fixer'] ) ) {
				$ultimate_modal_fixer = sanitize_text_field( $_POST['ultimate_modal_fixer'] );
			} else {
				$ultimate_modal_fixer = 'disable';
			}
			$result6 = update_option( 'ultimate_modal_fixer', $ultimate_modal_fixer );

			$result7 = false;
			$result8 = false;

			$bsf_options_array     = array( 'dev_mode', 'ultimate_global_scripts', 'ultimate_roles', 'ultimate_modal_menu' );
			$check_update_option_7 = false;
			$check_update_option_8 = false;

			if ( isset( $_POST['bsf_options'] ) ) {
				$bsf_options_keys = array_keys( array_map( 'sanitize_text_field', $_POST['bsf_options'] ) );

				$bsf_options_array = array_diff( $bsf_options_array, $bsf_options_keys );
				foreach ( $_POST['bsf_options'] as $key => $value ) { // PHPCS:Ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$key = sanitize_text_field( $key );
					if ( is_array( $value ) ) {
						$value = array_map( 'sanitize_text_field', $value );
					} else {
						$value = sanitize_text_field( $value );
					}

					$result7 = bsf_update_option( $key, $value );
					if ( $result7 ) {
						$check_update_option_7 = true;
					}
				}
			}

			foreach ( $bsf_options_array as $key => $key_value ) {
				$key_value = sanitize_text_field( $key_value );
				$result8   = bsf_update_option( $key_value, '' );
				if ( $result8 ) {
					$check_update_option_8 = true;
				}
				$result8 = true;
			}

			if ( isset( $_POST['ultimate_smooth_scroll_compatible'] ) ) {
				$ultimate_smooth_scroll_compatible = sanitize_text_field( $_POST['ultimate_smooth_scroll_compatible'] );
			} else {
				$ultimate_smooth_scroll_compatible = 'disable';
			}
			$result9 = update_option( 'ultimate_smooth_scroll_compatible', $ultimate_smooth_scroll_compatible );

			if ( isset( $_POST['ultimate_animation'] ) ) {
				$ultimate_animation = sanitize_text_field( $_POST['ultimate_animation'] );
			} else {
				$ultimate_animation = 'disable';
			}
			$result10 = update_option( 'ultimate_animation', $ultimate_animation );

			if ( $result1 || $result2 || $result3 || $result4 || $result5 || $result6 || $result7 || $result8 || $result9 || $result10 ) {
				echo 'success';
			} else {
				echo 'failed';
			}

			die();
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function update_settings() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			check_ajax_referer( 'smooth-scroll-setting', 'security' );

			if ( isset( $_POST['ultimate_smooth_scroll'] ) ) {
				$ultimate_smooth_scroll = sanitize_text_field( $_POST['ultimate_smooth_scroll'] );
			} else {
				$ultimate_smooth_scroll = 'disable';
			}
			$result1 = update_option( 'ultimate_smooth_scroll', $ultimate_smooth_scroll );

			if ( isset( $_POST['ultimate_smooth_scroll_options'] ) ) {
				$ultimate_smooth_scroll_options['step']  = isset( $_POST['ultimate_smooth_scroll_options']['step'] ) ? (int) $_POST['ultimate_smooth_scroll_options']['step'] : '';
				$ultimate_smooth_scroll_options['speed'] = isset( $_POST['ultimate_smooth_scroll_options']['speed'] ) ? (int) $_POST['ultimate_smooth_scroll_options']['speed'] : '';
			} else {
				$ultimate_smooth_scroll_options = '';
			}
			$result2 = update_option( 'ultimate_smooth_scroll_options', $ultimate_smooth_scroll_options );

			if ( $result1 || $result2 ) {
				echo 'success';
			} else {
				echo 'failed';
			}
			die();
		}
		/**
		 * For loading modules.
		 *
		 * @since ----
		 * @access public
		 */
		public function update_css_options() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			check_ajax_referer( 'css-settings-setting', 'security' );

			if ( isset( $_POST['ultimate_css'] ) ) {
				$ultimate_css = sanitize_text_field( $_POST['ultimate_css'] );
			} else {
				$ultimate_css = 'disable';
			}
			$result1 = update_option( 'ultimate_css', $ultimate_css );
			if ( isset( $_POST['ultimate_js'] ) ) {
				$ultimate_js = sanitize_text_field( $_POST['ultimate_js'] );
			} else {
				$ultimate_js = 'disable';
			}
			$result2 = update_option( 'ultimate_js', $ultimate_js );
			if ( $result1 || $result2 ) {
				echo 'success';
			} else {
				echo 'failed';
			}
			die();
		}

		/**
		 * Display admin notices for plugin activation
		 *
		 * @since ----
		 * @access public
		 */
		public function display_notice() {
			global $hook_suffix;
			$status        = 'not-activated';
			$ultimate_keys = get_option( 'ultimate_keys' );
			$username      = $ultimate_keys['envato_username'];
			$api_key       = $ultimate_keys['envato_api_key'];
			$purchase_code = $ultimate_keys['ultimate_purchase_code'];
			$user_email    = ( isset( $ultimate_keys['ultimate_user_email'] ) ) ? $ultimate_keys['ultimate_user_email'] : '';

			$activation_check = get_option( 'ultimate_license_activation' );

			if ( false === ( get_transient( 'ultimate_license_activation' ) ) ) {
				if ( ! empty( $activation_check ) ) {
					$get_activation_data   = check_license_activation( $purchase_code, $username, $user_email );
					$activation_check_temp = json_decode( $get_activation_data );
					$val                   = array(
						'response' => $activation_check_temp->response,
						'status'   => $activation_check_temp->status,
						'code'     => $activation_check_temp->code,
					);
					$val                   = array_map( 'sanitize_text_field', $val );
					update_option( 'ultimate_license_activation', $val );
					delete_transient( 'ultimate_license_activation' );
					set_transient( 'ultimate_license_activation', true, 60 * 60 * 12 );
				}
			}

			$activation_check   = get_option( 'ultimate_license_activation' );
			$ultimate_constants = get_option( 'ultimate_constants' );
			$builtin            = get_option( 'ultimate_updater' );

			if ( '' !== $activation_check ) {
				$status = isset( $activation_check['status'] ) ? $activation_check['status'] : 'not-activated';
				$code   = $activation_check['code'];
			}

			if ( 'Deactivated' == $status || 'not-activated' == $status || 'not-verified' == $status ) {
				if ( 'plugins.php' == $hook_suffix ) {
					if ( 'disabled' === $builtin || true === $ultimate_constants['ULTIMATE_NO_PLUGIN_PAGE_NOTICE'] || ( is_multisite() == true && is_main_site() == false ) ) {
						$hide_notice = true;
					} else {
						$hide_notice = false;
					}
					$reg_link = ( is_multisite() ) ? network_admin_url( 'index.php?page=bsf-dashboard' ) : admin_url( 'index.php?page=bsf-dashboard' );

					if ( ! $hide_notice ) :
						?>
						<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
							<style type="text/css">
						.ult_activate{min-width:825px;background: #FFF;border:1px solid #0096A3;padding:5px;margin:15px 0;border-radius:3px;-webkit-border-radius:3px;position:relative;overflow:hidden}
						.ult_activate .ult_a{position:absolute;top:5px;right:10px;font-size:48px;}
						.ult_activate .ult_button{font-weight:bold;border:1px solid #029DD6;border-top:1px solid #06B9FD;font-size:15px;text-align:center;padding:9px 0 8px 0;color:#FFF;background:#029DD6;-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px}
						.ult_activate .ult_button:hover{text-decoration:none !important;border:1px solid #029DD6;border-bottom:1px solid #00A8EF;font-size:15px;text-align:center;padding:9px 0 8px 0;color:#F0F8FB;background:#0079B1;-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px}
						.ult_activate .ult_button_border{border:1px solid #0096A3;-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px;background:#029DD6;}
						.ult_activate .ult_button_container{cursor:pointer;display:inline-block; padding:5px;-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px;width:215px}
						.ult_activate .ult_description{position:absolute;top:8px;left:230px;margin-left:25px;color:#0096A3;font-size:15px;z-index:1000}
						.ult_activate .ult_description strong{color:#0096A3;font-weight:normal}
							</style>
								<div class="ult_activate">
									<div class="ult_a"><img style="width:1em;" src="<?php echo UAVC_URL . 'img/logo-icon.png'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" alt=""></div>
																		<div class="ult_button_container" onclick="document.location='<?php echo $reg_link; ?>'"> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<div class="ult_button_border">
											<div class="ult_button"><span class="dashicons-before dashicons-admin-network" style="padding-right: 6px;"></span><?php __( 'Activate your license', 'ultimate_vc' ); ?></div>
										</div>
									</div>
									<div class="ult_description"><h3 style="margin:0;padding: 2px 0px;"><strong><?php esc_html_e( 'Almost done!', 'ultimate_vc' ); ?></strong></h3><p style="margin: 0;"><?php rsc_attr_e( 'Please activate your copy of the Ultimate Addons for WPBakery Page Builder to receive automatic updates & get premium support', 'ultimate_vc' ); ?></p></div> 
								</div>
						</div>
						<?php
					endif;
				} elseif ( 'post-new.php' == $hook_suffix || 'edit.php' == $hook_suffix || 'post.php' == $hook_suffix ) {
					if ( 'disabled' === $builtin || true === $ultimate_constants['ULTIMATE_NO_EDIT_PAGE_NOTICE'] || ( is_multisite() == true && is_main_site() == false ) ) {
						$hide_notice = true;
					} else {
						$hide_notice = false;
					}
					if ( ! $hide_notice ) :
						?>

						<div class="updated fade">
							<p><?php echo esc_html__( 'Howdy! Please', 'ultimate_vc' ) . ' <a href="' . $reg_link . '">' . esc_html__( 'activate your copy', 'ultimate_vc' ) . ' </a> ' . esc_html__( 'of the Ultimate Addons for WPBakery Page Builder to receive automatic updates & get premium support.', 'ultimate_vc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span style="float: right; padding: 0px 4px; cursor: pointer;" class="uavc-activation-notice">X</span>
							</p>
						</div>
						<script type="text/javascript">
						jQuery(".uavc-activation-notice").click(function(){
							jQuery(this).parents(".updated").fadeOut(800);
						});
						</script>

						<?php
					endif;
				}
			}
		}

	}
	new Ultimate_Admin_Area();
}

/**
 * Generate 32 characters
 *
 * @since ----
 * @access public
 */
function ult_generate_rand_id() {
// @codingStandardsIgnoreStart.
	$validCharacters = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$myKeeper        = '';
	$length          = 32;
	for ( $n = 1; $n < $length; $n++ ) {
		$whichCharacter = rand( 0, strlen( $validCharacters ) - 1 );
		$myKeeper      .= $validCharacters[ $whichCharacter ];
	}
	return $myKeeper;
// @codingStandardsIgnoreEnd.
}
/**
 * Alternative function for wp_remote_get
 *
 * @since ----
 * @param mixed $path path.
 * @access public
 */
function ultimate_remote_get( $path ) {
// @codingStandardsIgnoreStart.
	if ( function_exists( 'curl_init' ) ) {
		// create curl resource.
		$ch = curl_init();

		// set url.
		curl_setopt( $ch, CURLOPT_URL, $path );

		// return the transfer as a string.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		// $output contains the output string.
		$output = curl_exec( $ch );

		// close curl resource to free up system resources.
		curl_close( $ch );

		if ( '' !== $output ) {
			return $output;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
// @codingStandardsIgnoreEnd.
// hooks to add bsf-core stylesheet.
add_filter( 'bsf_core_style_screens', 'ultimate_bsf_core_style_hooks' );
/**
 * Alternative function for wp_remote_get
 *
 * @since ----
 * @param mixed $hooks hooks.
 * @access public
 */
function ultimate_bsf_core_style_hooks( $hooks ) {
	$array = array(
		'ultimate_page_ultimate-resources',
		'ultimate_page_about-ultimate',
		'toplevel_page_about-ultimate',
		'ultimate_page_ultimate-dashboard',
		'ultimate_page_ultimate-smoothscroll',
		'ultimate_page_ultimate-scripts-and-styles',
		'admin_page_ultimate-debug-settings',
		'ultimate_page_bsf-google-maps',
		'ultimate_page_ultimate-product-license',
		'settings_page_ultimate-product-license',
	);
	foreach ( $array as $hook ) {
		array_push( $hooks, $hook );
	}
	return $hooks;
}
// hooks to add frosty script.
add_filter( 'bsf_core_frosty_screens', 'ultimate_bsf_core_frosty_hooks' );
/**
 * Hooks to add frosty script
 *
 * @since ----
 * @param mixed $hooks hooks.
 * @access public
 */
function ultimate_bsf_core_frosty_hooks( $hooks ) {
	$array = array(
		'ultimate_page_ultimate-smoothscroll',
	);
	foreach ( $array as $hook ) {
		array_push( $hooks, $hook );
	}
	return $hooks;
}

/**
 * Hooks to add frosty script
 *
 * @since ----
 * @param mixed $url url.
 * @param mixed $original_url orignal_url.
 * @param mixed $_context context.
 * @access public
 */
function UAVC_product_license_redirect_to_modal_popup( $url, $original_url, $_context ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	if ( 'admin.php?page=ultimate-product-license' == $url ) {
		remove_filter( 'clean_url', 'UAVC_product_license_redirect_to_modal_popup', 10 );
		return bsf_registration_page_url( false, '6892199' );
	}
	return $url;
}
if ( is_admin() ) {
	add_filter( 'clean_url', 'UAVC_product_license_redirect_to_modal_popup', 10, 3 );
}
