<?php
/**
 * Vc starts here. Manager sets mode, adds required wp hooks and loads required object of structure.
 *
 * @package WPBakery
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Manager controls and access to all modules and classes of VC.
 *
 * @since   4.2
 */
class Vc_Manager {
	/**
	 * Set status/mode for VC.
	 *
	 * It depends on what functionality is required from vc to work with current page/part of WP.
	 *
	 * Possible values:
	 *  none - current status is unknown, default mode;
	 *  page - simple wp page;
	 *  admin_page - wp dashboard;
	 *  admin_frontend_editor - WPBakery Page Builder front end editor version;
	 *  admin_settings_page - settings page
	 *  page_editable - inline version for iframe in front end editor;
	 *
	 * @since 4.2
	 * @var string
	 */
	private $mode = 'none';

	/**
	 * Enables WPBakery Page Builder to act as the theme plugin.
	 *
	 * @since 4.2
	 * @var bool
	 */
	private $is_as_theme = false;

	/**
	 * Vc is network plugin or not.
	 *
	 * @since 4.2
	 * @var bool
	 */
	private $is_network_plugin = null;

	/**
	 * List of paths.
	 *
	 * @since 4.2
	 * @var array
	 */
	private $paths = [];

	/**
	 * Default post types where to activate WPBakery Page Builder meta box settings
	 *
	 * @since 4.2
	 * @var array
	 */
	private $editor_default_post_types = [ 'page' ]; // TODO: move to Vc settings.

	/**
	 * Directory name in theme folder where composer should search for alternative templates of the shortcode.
	 *
	 * @since 4.2
	 * @var string
	 */
	private $custom_user_templates_dir = false;

	/**
	 * Set updater mode
	 *
	 * @since 4.2
	 * @var bool
	 */
	private $disable_updater = false;

	/**
	 * Modules and objects instances list
	 *
	 * @since 4.2
	 * @var array
	 */
	private $factory = [];

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	private $plugin_name = 'js_composer/js_composer.php';

	/**
	 * Core singleton class
	 *
	 * @var self - pattern realization
	 */
	private static $instance;

	/**
	 * Access control object for the current user.
	 *
	 * @var Vc_Current_User_Access|false
	 * @since 4.8
	 */
	private $current_user_access = false;

	/**
	 * Access control object for roles.
	 *
	 * @var Vc_Role_Access|false
	 * @since 4.8
	 */
	private $role_access = false;

	/**
	 * Post types where VC editors are enabled.
	 *
	 * @since 4.8
	 * @var array
	 */
	public $editor_post_types;

	/**
	 * Constructor loads API functions, defines paths and adds required wp actions
	 *
	 * @since  4.8
	 */
	private function __construct() {
		$dir = WPB_PLUGIN_DIR;
		/**
		 * Define path settings for WPBakery Page Builder.
		 *
		 * APP_ROOT           - plugin directory.
		 * WP_ROOT            - WP application root directory.
		 * APP_DIR            - plugin directory name.
		 * CONFIG_DIR         - configuration directory.
		 * ASSETS_DIR         - asset directory full path.
		 * ASSETS_DIR         - modules directory.
		 * ASSETS_DIR_NAME    - directory name for assets. Used from urls creating.
		 * CORE_DIR           - classes directory for core vc files.
		 * HELPERS_DIR        - directory with helpers functions files.
		 * SHORTCODES_DIR     - shortcodes classes.
		 * SETTINGS_DIR       - main dashboard settings classes.
		 * TEMPLATES_DIR      - directory where all html templates are hold.
		 * EDITORS_DIR        - editors for the post contents
		 * PARAMS_DIR         - complex params for shortcodes editor form.
		 * UPDATERS_DIR       - automatic notifications and updating classes.
		 * MUTUAL_MODULES_DIR - common functionality for modules.
		 */
		$this->setPaths( [
			'APP_ROOT' => $dir,
			'WP_ROOT' => preg_replace( '/$\//', '', ABSPATH ),
			'APP_DIR' => basename( plugin_basename( $dir ) ),
			'CONFIG_DIR' => $dir . '/config',
			'ASSETS_DIR' => $dir . '/assets',
			'MODULES_DIR' => $dir . '/modules',
			'ASSETS_DIR_NAME' => 'assets',
			'AUTOLOAD_DIR' => $dir . '/include/autoload',
			'CORE_DIR' => $dir . '/include/classes/core',
			'HELPERS_DIR' => $dir . '/include/helpers',
			'SHORTCODES_DIR' => $dir . '/include/classes/shortcodes',
			'SETTINGS_DIR' => $dir . '/include/classes/settings',
			'TEMPLATES_DIR' => $dir . '/include/templates',
			'EDITORS_DIR' => $dir . '/include/classes/editors',
			'PARAMS_DIR' => $dir . '/include/params',
			'UPDATERS_DIR' => $dir . '/include/classes/updaters',
			'VENDORS_DIR' => $dir . '/include/classes/vendors',
			'DEPRECATED_DIR' => $dir . '/include/classes/deprecated',
			'MUTUAL_MODULES_DIR' => $dir . '/include/classes/modules',
		] );
		// Load API.
		require_once $this->path( 'HELPERS_DIR', 'helpers_factory.php' );
		require_once $this->path( 'HELPERS_DIR', 'helpers.php' );
		require_once $this->path( 'DEPRECATED_DIR', 'interfaces.php' );
		require_once $this->path( 'CORE_DIR', 'class-vc-sort.php' ); // used by wpb-map.
		require_once $this->path( 'CORE_DIR', 'class-wpb-map.php' );
		require_once $this->path( 'CORE_DIR', 'class-vc-shared-library.php' );
		require_once $this->path( 'HELPERS_DIR', 'helpers_api.php' );
		require_once $this->path( 'DEPRECATED_DIR', 'helpers_deprecated.php' );
		require_once $this->path( 'PARAMS_DIR', 'params.php' );
		require_once $this->path( 'AUTOLOAD_DIR', 'vc-shortcode-autoloader.php' );
		require_once $this->path( 'SHORTCODES_DIR', 'core/class-vc-shortcodes-manager.php' );
		require_once $this->path( 'CORE_DIR', 'class-vc-modifications.php' );
		// Add hooks.
		add_action( 'plugins_loaded', [
			$this,
			'pluginsLoaded',
		], 9 );
		add_action( 'init', [
			$this,
			'init',
		], 11 );
		$this->setPluginName( $this->path( 'APP_DIR', 'js_composer.php' ) );
		register_activation_hook( WPB_PLUGIN_FILE, [
			$this,
			'activationHook',
		] );
		register_activation_hook(WPB_PLUGIN_FILE, [
			$this,
			'activation_action',
		] );
		add_action( 'init', [
			$this,
			'load_text_domain',
		] );
		add_filter( 'plugin_row_meta', [
			$this,
			'addPluginMetaLinks',
		], 10, 2 );
	}

	/**
	 * Load textdomain for plugin
	 *
	 * @return void
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'js_composer', false, $this->path( 'APP_DIR', 'locale' ) );
	}

	/**
	 * Get the instance of VC_Manager
	 *
	 * @return self
	 */
	public static function getInstance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Callback function WP plugin_loaded action hook. Loads locale
	 *
	 * @since  4.2
	 * @access public
	 */
	public function pluginsLoaded() {
		do_action( 'vc_plugins_loaded' );
	}

	/**
	 * Callback function for WP init action hook. Sets Vc mode and loads required objects.
	 *
	 * @return void
	 * @throws \Exception
	 * @since  4.2
	 * @access public
	 */
	public function init() {
		if ( method_exists( 'LiteSpeed_Cache_API', 'esi_enabled' ) && LiteSpeed_Cache_API::esi_enabled() ) {
			LiteSpeed_Cache_API::hook_tpl_esi( 'js_composer', 'vc_hook_esi' );
		}
		ob_start();
		do_action( 'vc_before_init' );
		ob_end_clean(); // FIX for whitespace issues (#76147).
		$this->setMode();
		do_action( 'vc_after_set_mode' );
		/**
		 * Set version of VC if required.
		 */
		$this->setVersion();
		// Load required.
		! vc_is_updater_disabled() && vc_updater()->init();
		/**
		 * Load plugin modules.
		 */
		vc_modules_manager()->load();
		/**
		 * Init default hooks and options to load.
		 */
		$this->vc()->init();
		/**
		 * If is admin and not front end editor.
		 */
		is_admin() && ! vc_is_frontend_editor() && $this->asAdmin();
		/**
		 * If frontend editor is enabled init editor.
		 */
		vc_enabled_frontend() && vc_frontend_editor()->init();
		do_action( 'vc_before_mapping' ); // VC ACTION.
		// Include default shortcodes.
		$this->mapper()->init(); // execute all required.
		do_action( 'vc_after_mapping' ); // VC ACTION.
		new Vc_Modifications();
		if ( vc_user_access()->wpAny( 'manage_options' )->part( 'settings' )->can( 'vc-updater-tab' )->get() ) {
			vc_license()->setupReminder();
		}
		do_action( 'vc_after_init' );
	}

	/**
	 * Retrieves the current user's access control object.
	 *
	 * @return Vc_Current_User_Access
	 * @since 4.8
	 */
	public function getCurrentUserAccess() {
		if ( ! $this->current_user_access ) {
			require_once vc_path_dir( 'CORE_DIR', 'access/class-vc-current-user-access.php' );
			$this->current_user_access = new Vc_Current_User_Access();
		}

		return $this->current_user_access;
	}

	/**
	 * Sets the current user access control object.
	 *
	 * @param false|Vc_Current_User_Access $current_user_access
	 */
	public function setCurrentUserAccess( $current_user_access ) {
		$this->current_user_access = $current_user_access;
	}

	/**
	 * Retrieves the role access control object.
	 *
	 * @return Vc_Role_Access
	 * @since 4.8
	 */
	public function getRoleAccess() {
		if ( ! $this->role_access ) {
			require_once vc_path_dir( 'CORE_DIR', 'access/class-vc-role-access.php' );
			$this->role_access = new Vc_Role_Access();
		}

		return $this->role_access;
	}

	/**
	 * Sets the role access control object.
	 *
	 * @param false|Vc_Role_Access $role_access
	 */
	public function setRoleAccess( $role_access ) {
		$this->role_access = $role_access;
	}

	/**
	 * Enables to add hooks in activation process.
	 *
	 * @param bool $networkWide
	 * @since 4.5
	 */
	public function activationHook( $networkWide = false ) {
		do_action( 'vc_activation_hook', $networkWide );
	}

	/**
	 * Load required components to enable useful functionality.
	 * We load here autoload plugin functionality that mostly works with wp hooks system.
	 *
	 * @access public
	 * @since 4.4
	 */
	public function loadComponents() {
		vc_autoload_manager()->load();
	}

	/**
	 * Load required logic for operating in Wp Admin dashboard.
	 *
	 * @return void
	 * @since  4.2
	 * @access protected
	 */
	protected function asAdmin() {
		vc_license()->init();
		vc_backend_editor()->addHooksSettings();
	}

	/**
	 * Set VC mode.
	 *
	 * Mode depends on which page is requested by client from server and request parameters like vc_action.
	 *
	 * @return void
	 * @throws \Exception
	 * @since  4.2
	 * @access protected
	 */
	protected function setMode() {
		/**
		 * TODO: Create another system (When ajax rebuild).
		 * Use vc_action param to define mode.
		 * 1. admin_frontend_editor - set by editor or request param
		 * 2. admin_backend_editor - set by editor or request param
		 * 3. admin_frontend_editor_ajax - set by request param
		 * 4. admin_backend_editor_ajax - set by request param
		 * 5. admin_updater - by vc_action
		 * 6. page_editable - by vc_action or transient with vc_action param
		 */
		if ( is_admin() ) {
			if ( 'vc_inline' === vc_action() ) {
				vc_user_access()->wpAny( [
					'edit_post',
					(int) vc_request_param( 'post_id' ),
				] )->validateDie()->part( 'frontend_editor' )->can()->validateDie();
				$this->mode = 'admin_frontend_editor';
			} elseif ( ( vc_user_access()->wpAny( 'edit_posts', 'edit_pages' )->get() ) && ( 'vc_upgrade' === vc_action() || ( 'update-selected' === vc_get_param( 'action' ) && $this->pluginName() === vc_get_param( 'plugins' ) ) ) ) {
				$this->mode = 'admin_updater';
			} elseif ( vc_user_access()->wpAny( 'manage_options' )->get() && array_key_exists( vc_get_param( 'page' ), vc_settings()->getTabs() ) ) {
				$this->mode = 'admin_settings_page';
			} else {
				$this->mode = 'admin_page';
			}
		} elseif ( 'true' === vc_get_param( 'vc_editable' ) ) {
				vc_user_access()->checkAdminNonce()->validateDie()->wpAny([
					'edit_post',
					(int) vc_request_param( 'vc_post_id' ),
				])->validateDie()->part( 'frontend_editor' )->can()->validateDie();
				$this->mode = 'page_editable';
		} elseif (
				get_transient( 'vc_action' ) === 'vc_editable'
				&& isset( $_SERVER['HTTP_SEC_FETCH_DEST'] )
				&& 'iframe' === $_SERVER['HTTP_SEC_FETCH_DEST'] ) {

			delete_transient( 'vc_action' );
			$this->mode = 'page_editable';
		} else {
			$this->mode = 'page';
		}
	}

	/**
	 * Sets version of the VC in DB as option `vc_version`
	 *
	 * @return void
	 * @since 4.3.2
	 * @access protected
	 */
	protected function setVersion() {
		$version = get_option( 'vc_version' );
		if ( ! is_string( $version ) || version_compare( $version, WPB_VC_VERSION ) !== 0 ) {
			add_action( 'vc_after_init', [
				vc_settings(),
				'rebuild',
			] );
			update_option( 'vc_version', WPB_VC_VERSION );
		}
	}

	/**
	 * Get current mode for VC.
	 *
	 * @return string
	 * @since  4.2
	 * @access public
	 */
	public function mode() {
		return $this->mode;
	}

	/**
	 * Setter for paths
	 *
	 * @param string $paths
	 * @since  4.2
	 * @access protected
	 */
	protected function setPaths( $paths ) {
		$this->paths = $paths;
	}

	/**
	 * Gets absolute path for file/directory in filesystem.
	 *
	 * @param string $name - name of path dir.
	 * @param string $file - file name or directory inside path.
	 *
	 * @return string
	 * @since  4.2
	 * @access public
	 */
	public function path( $name, $file = '' ) {
		$path = $this->paths[ $name ] . ( strlen( $file ) > 0 ? '/' . preg_replace( '/^\//', '', $file ) : '' );

		return apply_filters( 'vc_path_filter', $path );
	}

	/**
	 * Set default post types. Vc editors are enabled for such kind of posts.
	 *
	 * @param array $type - list of default post types.
	 */
	public function setEditorDefaultPostTypes( array $type ) {
		$this->editor_default_post_types = $type;
	}

	/**
	 * Returns list of default post types where user can use WPBakery Page Builder editors.
	 *
	 * @return array
	 * @since  4.2
	 * @access public
	 */
	public function editorDefaultPostTypes() {
		return $this->editor_default_post_types;
	}

	/**
	 * Get post types where WPBakery Page Builder editors are enabled.
	 *
	 * @return array
	 * @throws \Exception
	 * @since  4.2
	 * @access public
	 */
	public function editorPostTypes() {
		if ( null === $this->editor_post_types ) {
			$post_types = array_keys( vc_user_access()->part( 'post_types' )->getAllCaps() );
			$this->editor_post_types = $post_types ? $post_types : $this->editorDefaultPostTypes();
		}

		return $this->editor_post_types;
	}

	/**
	 * Set post types where VC editors are enabled.
	 *
	 * @param array $post_types
	 * @throws \Exception
	 * @since  4.4
	 * @access public
	 */
	public function setEditorPostTypes( array $post_types ) {
		$this->editor_post_types = ! empty( $post_types ) ? $post_types : $this->editorDefaultPostTypes();

		require_once ABSPATH . 'wp-admin/includes/user.php';

		$editable_roles = get_editable_roles();
		foreach ( $editable_roles as $role => $settings ) {
			$part = vc_role_access()->who( $role )->part( 'post_types' );
			$all_post_types = $part->getAllCaps();

			foreach ( $all_post_types as $post_type => $value ) {
				$part->getRole()->remove_cap( $part->getStateKey() . '/' . $post_type );
			}
			$part->setState( 'custom' );

			foreach ( $this->editor_post_types as $post_type ) {
				$part->setCapRule( $post_type );
			}
		}
	}

	/**
	 * Setter for as-theme-plugin status for VC.
	 *
	 * @param bool $value
	 * @since  4.2
	 * @access public
	 */
	public function setIsAsTheme( $value = true ) {
		$this->is_as_theme = (bool) $value;
	}

	/**
	 * Get as-theme-plugin status
	 *
	 * As theme plugin status used by theme developers. It disables settings
	 *
	 * @return bool
	 * @since  4.2
	 * @access public
	 */
	public function isAsTheme() {
		return (bool) $this->is_as_theme;
	}

	/**
	 * Setter for as network plugin for MultiWP.
	 *
	 * @param bool $value
	 * @since  4.2
	 * @access public
	 */
	public function setAsNetworkPlugin( $value = true ) {
		$this->is_network_plugin = $value;
	}

	/**
	 * Gets VC is activated as network plugin.
	 *
	 * @return bool
	 * @since  4.2
	 * @access public
	 */
	public function isNetworkPlugin() {
		if ( is_null( $this->is_network_plugin ) ) {
			// Check is VC as network plugin.
			if ( is_multisite() && ( is_plugin_active_for_network( $this->pluginName() ) || is_network_only_plugin( $this->pluginName() ) ) ) {
				$this->setAsNetworkPlugin( true );
			}
		}

		return $this->is_network_plugin ? true : false;
	}

	/**
	 * Setter for disable updater variable.
	 *
	 * @param bool $value
	 *
	 * @since 4.2
	 */
	public function disableUpdater( $value = true ) {
		$this->disable_updater = $value;
	}

	/**
	 * Get is vc updater is disabled;
	 *
	 * @return bool
	 * @see to where updater will be
	 *
	 * @since 4.2
	 */
	public function isUpdaterDisabled() {
		return is_admin() && $this->disable_updater;
	}

	/**
	 * Set user directory name.
	 *
	 * Directory name is the directory name vc should scan for custom shortcodes template.
	 *
	 * @param string $dir - path to shortcodes templates inside developers theme.
	 * @since    4.2
	 * @access   public
	 */
	public function setCustomUserShortcodesTemplateDir( $dir ) {
		preg_replace( '/\/$/', '', $dir );
		$this->custom_user_templates_dir = $dir;
	}

	/**
	 * Get default directory where shortcodes templates area placed.
	 *
	 * @return string - path to default shortcodes
	 * @since  4.2
	 * @access public
	 */
	public function getDefaultShortcodesTemplatesDir() {
		return vc_path_dir( 'TEMPLATES_DIR', 'shortcodes' );
	}

	/**
	 * Get shortcodes template dir.
	 *
	 * @param string $template
	 *
	 * @return string
	 * @since  4.2
	 * @access public
	 */
	public function getShortcodesTemplateDir( $template ) {
		return false !== $this->custom_user_templates_dir ? $this->custom_user_templates_dir . '/' . $template : locate_template( 'vc_templates/' . $template );
	}

	/**
	 * Directory name where template files will be stored.
	 *
	 * @return string
	 * @since  4.2
	 * @access public
	 */
	public function uploadDir() {
		return 'js_composer';
	}

	/**
	 * Getter for VC_Mapper instance
	 *
	 * @return Vc_Mapper
	 * @since  4.2
	 * @access public
	 */
	public function mapper() {
		if ( ! isset( $this->factory['mapper'] ) ) {
			require_once $this->path( 'CORE_DIR', 'class-vc-mapper.php' );
			$this->factory['mapper'] = new Vc_Mapper();
		}

		return $this->factory['mapper'];
	}

	/**
	 * WPBakery Page Builder.
	 *
	 * @return Vc_Base
	 * @since  4.2
	 * @access public
	 */
	public function vc() {
		if ( ! isset( $this->factory['vc'] ) ) {
			do_action( 'vc_before_init_vc' );
			require_once $this->path( 'CORE_DIR', 'class-vc-base.php' );
			$vc = new Vc_Base();
			// DI Set template new modal editor.
			require_once $this->path( 'EDITORS_DIR', 'popups/class-vc-templates-panel-editor.php' );
			require_once $this->path( 'CORE_DIR', 'shared-templates/class-vc-shared-templates.php' );
			$vc->setTemplatesPanelEditor( new Vc_Templates_Panel_Editor() );
			// Create shared templates.
			$vc->shared_templates = new Vc_Shared_Templates();

			// DI Set edit form.
			require_once $this->path( 'EDITORS_DIR', 'popups/class-vc-shortcode-edit-form.php' );
			$vc->setEditForm( new Vc_Shortcode_Edit_Form() );

			// DI Set preset new modal editor.
			require_once $this->path( 'EDITORS_DIR', 'popups/class-vc-preset-panel-editor.php' );
			$vc->setPresetPanelEditor( new Vc_Preset_Panel_Editor() );

			$this->factory['vc'] = $vc;
			do_action( 'vc_after_init_vc' );
		}

		return $this->factory['vc'];
	}

	/**
	 * Vc options.
	 *
	 * @return Vc_Settings
	 * @since  4.2
	 * @access public
	 */
	public function settings() {
		if ( ! isset( $this->factory['settings'] ) ) {
			do_action( 'vc_before_init_settings' );
			require_once $this->path( 'SETTINGS_DIR', 'class-vc-settings.php' );
			$this->factory['settings'] = new Vc_Settings();
			do_action( 'vc_after_init_settings' );
		}

		return $this->factory['settings'];
	}

	/**
	 * Vc license settings.
	 *
	 * @return Vc_License
	 * @since  4.2
	 * @access public
	 */
	public function license() {
		if ( ! isset( $this->factory['license'] ) ) {
			do_action( 'vc_before_init_license' );
			require_once $this->path( 'SETTINGS_DIR', 'class-vc-license.php' );
			$this->factory['license'] = new Vc_License();
			do_action( 'vc_after_init_license' );
		}

		return $this->factory['license'];
	}

	/**
	 * Get frontend VC editor.
	 *
	 * @return Vc_Frontend_Editor
	 * @since  4.2
	 * @access public
	 */
	public function frontendEditor() {
		if ( ! isset( $this->factory['frontend_editor'] ) ) {
			do_action( 'vc_before_init_frontend_editor' );
			require_once $this->path( 'EDITORS_DIR', 'class-vc-frontend-editor.php' );
			$this->factory['frontend_editor'] = new Vc_Frontend_Editor();
		}

		return $this->factory['frontend_editor'];
	}

	/**
	 * Get backend VC editor. Edit page version.
	 *
	 * @return Vc_Backend_Editor
	 * @since 4.2
	 */
	public function backendEditor() {
		if ( ! isset( $this->factory['backend_editor'] ) ) {
			do_action( 'vc_before_init_backend_editor' );
			require_once $this->path( 'EDITORS_DIR', 'class-vc-backend-editor.php' );
			$this->factory['backend_editor'] = new Vc_Backend_Editor();
		}

		return $this->factory['backend_editor'];
	}

	/**
	 * Gets automapper instance.
	 *
	 * @return Vc_Automapper
	 * @since  4.2
	 */
	public function automapper() {
		if ( ! isset( $this->factory['automapper'] ) ) {
			do_action( 'vc_before_init_automapper' );
			require_once $this->path( 'MODULES_DIR', 'automapper/module.php' );
			$this->factory['automapper'] = new Vc_Automapper();
			do_action( 'vc_after_init_automapper' );
		}

		return $this->factory['automapper'];
	}

	/**
	 * Gets autoload manager.
	 *
	 * @return Vc_Autoload_Manager
	 * @since  7.7
	 */
	public function autoload() {
		if ( ! isset( $this->factory['autoload'] ) ) {
			do_action( 'vc_before_init_autoload' );
			require_once $this->path( 'CORE_DIR', 'class-autoload-manager.php' );
			$this->factory['autoload'] = new Vc_Autoload_Manager();
			do_action( 'vc_after_init_autoload' );
		}

		return $this->factory['autoload'];
	}

	/**
	 * Gets modules manager instance.
	 *
	 * @return Vc_Modules_Manager
	 * @since  7.7
	 */
	public function modules() {
		if ( ! isset( $this->factory['modules'] ) ) {
			do_action( 'vc_before_init_modules' );
			require_once $this->path( 'MUTUAL_MODULES_DIR', '/class-modules-manager.php' );
			$this->factory['modules'] = new Vc_Modules_Manager();
			do_action( 'vc_after_init_modules' );
		}

		return $this->factory['modules'];
	}

	/**
	 * Gets updater instance.
	 *
	 * @return Vc_Updater
	 * @since 4.2
	 */
	public function updater() {
		if ( ! isset( $this->factory['updater'] ) ) {
			do_action( 'vc_before_init_updater' );
			require_once $this->path( 'UPDATERS_DIR', 'class-vc-updater.php' );
			$updater = new Vc_Updater();
			require_once vc_path_dir( 'UPDATERS_DIR', 'class-vc-updating-manager.php' );
			$updater->setUpdateManager( new Vc_Updating_Manager( WPB_VC_VERSION, $updater->versionUrl(), $this->pluginName() ) );
			$this->factory['updater'] = $updater;
			do_action( 'vc_after_init_updater' );
		}

		return $this->factory['updater'];
	}

	/**
	 * Getter for plugin name variable.
	 *
	 * @return string
	 * @since 4.2
	 */
	public function pluginName() {
		return $this->plugin_name;
	}

	/**
	 * Sen plugin name.
	 *
	 * @param string $name
	 * @since 4.8.1
	 */
	public function setPluginName( $name ) {
		$this->plugin_name = $name;
	}

	/**
	 * Get absolute url for VC asset file.
	 *
	 * Assets are css, javascript, less files and images.
	 *
	 * @param string $file
	 *
	 * @return string
	 * @since 4.2
	 */
	public function assetUrl( $file ) {
		return preg_replace( '/\s/', '%20', plugins_url( $this->path( 'ASSETS_DIR_NAME', $file ), WPB_PLUGIN_FILE ) );
	}

	/**
	 * Add custom links to plugin meta in plugin page list.
	 *
	 * @param array  $links
	 * @param string $plugin_file
	 * @return array
	 * @since 8.3
	 */
	public function addPluginMetaLinks( $links, $plugin_file ) {
		if ( plugin_basename( WPB_PLUGIN_FILE ) !== $plugin_file ) {
			return $links;
		}

		// Remove last on which can be "Visit plugin site" or "View details".
		array_pop( $links );

		// Add "View details" expicitly.
		$links = array_merge( $links, [
			sprintf(
				'<a href="%s" class="thickbox open-plugin-details-modal">%s</a>',
				esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=js_composer&TB_iframe=true&width=772&height=500' ) ),
				esc_html__( 'View details', 'text-domain' )
			),
			sprintf(
				'<a href="%s" target="%s">%s</a>',
				esc_url( 'https://support.wpbakery.com/?utm_source=wpdashboard&utm_medium=wp-plugins&utm_campaign=info&utm_content=text' ),
				esc_attr( '_blank' ),
				esc_html__( 'Customer Center', 'text-domain' )
			),
		] );

		return $links;
	}

	/**
	 * Action after plugin activate.
	 *
	 * @note  We should keep it in vc-manager
	 * @see https://wordpress.org/support/topic/register_activation_hook-does-not-work
	 *
	 * @since 8.4
	 */
	public function activation_action() {
		delete_site_transient( 'update_plugins' );
		wp_update_plugins();
	}
}
