<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Proreview
 * @subpackage Proreview/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Proreview
 * @subpackage Proreview/includes
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Proreview {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Proreview_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $p_onboard    To initializsed the object of class onboard.
	 */
	protected $p_onboard;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area,
	 * the public-facing side of the site and common side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'PROREVIEW_VERSION' ) ) {

			$this->version = PROREVIEW_VERSION;
		} else {

			$this->version = '1.0.0';
		}

		$this->plugin_name = 'proreview';

		$this->proreview_dependencies();
		$this->proreview_locale();
		if ( is_admin() ) {
			$this->proreview_admin_hooks();
		} else {
			$this->proreview_public_hooks();
		}
		$this->proreview_common_hooks();

		$this->proreview_api_hooks();


	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Proreview_Loader. Orchestrates the hooks of the plugin.
	 * - Proreview_i18n. Defines internationalization functionality.
	 * - Proreview_Admin. Defines all hooks for the admin area.
	 * - Proreview_Common. Defines all hooks for the common area.
	 * - Proreview_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function proreview_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-proreview-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-proreview-i18n.php';

		if ( is_admin() ) {

			// The class responsible for defining all actions that occur in the admin area.
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-proreview-admin.php';

			// The class responsible for on-boarding steps for plugin.
			if ( is_dir(  plugin_dir_path( dirname( __FILE__ ) ) . 'onboarding' ) && ! class_exists( 'Proreview_Onboarding_Steps' ) ) {
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-proreview-onboarding-steps.php';
			}

			if ( class_exists( 'Proreview_Onboarding_Steps' ) ) {
				$p_onboard_steps = new Proreview_Onboarding_Steps();
			}
		} else {

			// The class responsible for defining all actions that occur in the public-facing side of the site.
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-proreview-public.php';

		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'package/rest-api/class-proreview-rest-api.php';

		/**
		 * This class responsible for defining common functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'common/class-proreview-common.php';

		$this->loader = new Proreview_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Proreview_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function proreview_locale() {

		$plugin_i18n = new Proreview_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function proreview_admin_hooks() {

		$p_plugin_admin = new Proreview_Admin( $this->p_get_plugin_name(), $this->p_get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $p_plugin_admin, 'p_admin_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $p_plugin_admin, 'p_admin_enqueue_scripts' );

		// Add settings menu for proreview.
		$this->loader->add_action( 'admin_menu', $p_plugin_admin, 'p_options_page' );
		$this->loader->add_action( 'admin_menu', $p_plugin_admin, 'mwb_p_remove_default_submenu', 50 );

		// All admin actions and filters after License Validation goes here.
		$this->loader->add_filter( 'mwb_add_plugins_menus_array', $p_plugin_admin, 'p_admin_submenu_page', 15 );
		$this->loader->add_filter( 'p_template_settings_array', $p_plugin_admin, 'p_admin_template_settings_page', 10 );
		$this->loader->add_filter( 'p_general_settings_array', $p_plugin_admin, 'p_admin_general_settings_page', 10 );

		// Saving tab settings.
		$this->loader->add_action( 'admin_init', $p_plugin_admin, 'p_admin_save_tab_settings' );

		$this->loader->add_action( 'pre_get_comments', $p_plugin_admin, 'get_comment_data' );
		// $this->loader->add_filter( 'comment_row_actions', $p_plugin_admin, 'add_pin_action', 20, 2 );

		
		$this->loader->add_action( 'wp_ajax_mwb_prfw_pin_review', $p_plugin_admin, 'mwb_prfw_pin_review' );

		$this->loader->add_action( 'wp_ajax_mwb_prfw_unpin_review', $p_plugin_admin, 'mwb_prfw_unpin_review' );

		

	}

	/**
	 * Register all of the hooks related to the common functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function proreview_common_hooks() {

		$p_plugin_common = new Proreview_Common( $this->p_get_plugin_name(), $this->p_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $p_plugin_common, 'p_common_enqueue_styles' );

		$this->loader->add_action( 'wp_enqueue_scripts', $p_plugin_common, 'p_common_enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function proreview_public_hooks() {

		$p_plugin_public = new Proreview_Public( $this->p_get_plugin_name(), $this->p_get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $p_plugin_public, 'p_public_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $p_plugin_public, 'p_public_enqueue_scripts' );
		$this->loader->add_action( 'mwb_prfw_pro_pinned_comments', $p_plugin_public, 'show_pin' );

		$this->loader->add_action( 'comment_form_top', $p_plugin_public, 'add_attributes' );

		$this->loader->add_action( 'comment_post', $p_plugin_public, 'mwb_save_data' );

		$this->loader->add_action( 'mwb_filter_action', $p_plugin_public, 'show_attr_filter' );
		$this->loader->add_action( 'mwb_show_filtered_attr', $p_plugin_public, 'apply_filter_action' );

		$this->loader->add_filter( 'woocommerce_reviews_title', $p_plugin_public, 'mwb_change_text_filter', 20, 3 );
		$this->loader->add_filter( 'mwb_prfw_pro_upload_review', $p_plugin_public, 'add_video_review', 20, 3 );
		
		

	}

	/**
	 * Register all of the hooks related to the api functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function proreview_api_hooks() {

		$p_plugin_api = new Proreview_Rest_Api( $this->p_get_plugin_name(), $this->p_get_version() );

		$this->loader->add_action( 'rest_api_init', $p_plugin_api, 'mwb_p_add_endpoint' );

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function p_run() {
		$this->loader->p_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function p_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Proreview_Loader    Orchestrates the hooks of the plugin.
	 */
	public function p_get_loader() {
		return $this->loader;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Proreview_Onboard    Orchestrates the hooks of the plugin.
	 */
	public function p_get_onboard() {
		return $this->p_onboard;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function p_get_version() {
		return $this->version;
	}

	/**
	 * Predefined default mwb_p_plug tabs.
	 *
	 * @return  Array       An key=>value pair of proreview tabs.
	 */
	public function mwb_p_plug_default_tabs() {

		$p_default_tabs = array();

		$p_default_tabs['proreview-general'] = array(
			'title'       => esc_html__( 'General Setting', 'proreview' ),
			'name'        => 'proreview-general',
		);
		$p_default_tabs = apply_filters( 'mwb_p_plugin_standard_admin_settings_tabs', $p_default_tabs );

		$p_default_tabs['proreview-system-status'] = array(
			'title'       => esc_html__( 'System Status', 'proreview' ),
			'name'        => 'proreview-system-status',
		);
		$p_default_tabs['proreview-template'] = array(
			'title'       => esc_html__( 'Templates', 'proreview' ),
			'name'        => 'proreview-template',
		);
		$p_default_tabs['proreview-overview'] = array(
			'title'       => esc_html__( 'Overview', 'proreview' ),
			'name'        => 'proreview-overview',
		);

		return $p_default_tabs;
	}

	/**
	 * Locate and load appropriate tempate.
	 *
	 * @since   1.0.0
	 * @param string $path path file for inclusion.
	 * @param array  $params parameters to pass to the file for access.
	 */
	public function mwb_p_plug_load_template( $path, $params = array() ) {

		$p_file_path = PROREVIEW_DIR_PATH . $path;

		if ( file_exists( $p_file_path ) ) {

			include $p_file_path;
		} else {

			/* translators: %s: file path */
			$p_notice = sprintf( esc_html__( 'Unable to locate file at location "%s". Some features may not work properly in this plugin. Please contact us!', 'proreview' ), $p_file_path );
			$this->mwb_p_plug_admin_notice( $p_notice, 'error' );
		}
	}

	/**
	 * Show admin notices.
	 *
	 * @param  string $p_message    Message to display.
	 * @param  string $type       notice type, accepted values - error/update/update-nag.
	 * @since  1.0.0
	 */
	public static function mwb_p_plug_admin_notice( $p_message, $type = 'error' ) {

		$p_classes = 'notice ';

		switch ( $type ) {

			case 'update':
			$p_classes .= 'updated is-dismissible';
			break;

			case 'update-nag':
			$p_classes .= 'update-nag is-dismissible';
			break;

			case 'success':
			$p_classes .= 'notice-success is-dismissible';
			break;

			default:
			$p_classes .= 'notice-error is-dismissible';
		}

		$p_notice  = '<div class="' . esc_attr( $p_classes ) . ' mwb-errorr-8">';
		$p_notice .= '<p>' . esc_html( $p_message ) . '</p>';
		$p_notice .= '</div>';

		echo wp_kses_post( $p_notice );
	}


	/**
	 * Show wordpress and server info.
	 *
	 * @return  Array $p_system_data       returns array of all wordpress and server related information.
	 * @since  1.0.0
	 */
	public function mwb_p_plug_system_status() {
		global $wpdb;
		$p_system_status = array();
		$p_wordpress_status = array();
		$p_system_data = array();

		// Get the web server.
		$p_system_status['web_server'] = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

		// Get PHP version.
		$p_system_status['php_version'] = function_exists( 'phpversion' ) ? phpversion() : __( 'N/A (phpversion function does not exist)', 'proreview' );

		// Get the server's IP address.
		$p_system_status['server_ip'] = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';

		// Get the server's port.
		$p_system_status['server_port'] = isset( $_SERVER['SERVER_PORT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_PORT'] ) ) : '';

		// Get the uptime.
		$p_system_status['uptime'] = function_exists( 'exec' ) ? @exec( 'uptime -p' ) : __( 'N/A (make sure exec function is enabled)', 'proreview' );

		// Get the server path.
		$p_system_status['server_path'] = defined( 'ABSPATH' ) ? ABSPATH : __( 'N/A (ABSPATH constant not defined)', 'proreview' );

		// Get the OS.
		$p_system_status['os'] = function_exists( 'php_uname' ) ? php_uname( 's' ) : __( 'N/A (php_uname function does not exist)', 'proreview' );

		// Get WordPress version.
		$p_wordpress_status['wp_version'] = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : __( 'N/A (get_bloginfo function does not exist)', 'proreview' );

		// Get and count active WordPress plugins.
		$p_wordpress_status['wp_active_plugins'] = function_exists( 'get_option' ) ? count( get_option( 'active_plugins' ) ) : __( 'N/A (get_option function does not exist)', 'proreview' );

		// See if this site is multisite or not.
		$p_wordpress_status['wp_multisite'] = function_exists( 'is_multisite' ) && is_multisite() ? __( 'Yes', 'proreview' ) : __( 'No', 'proreview' );

		// See if WP Debug is enabled.
		$p_wordpress_status['wp_debug_enabled'] = defined( 'WP_DEBUG' ) ? __( 'Yes', 'proreview' ) : __( 'No', 'proreview' );

		// See if WP Cache is enabled.
		$p_wordpress_status['wp_cache_enabled'] = defined( 'WP_CACHE' ) ? __( 'Yes', 'proreview' ) : __( 'No', 'proreview' );

		// Get the total number of WordPress users on the site.
		$p_wordpress_status['wp_users'] = function_exists( 'count_users' ) ? count_users() : __( 'N/A (count_users function does not exist)', 'proreview' );

		// Get the number of published WordPress posts.
		$p_wordpress_status['wp_posts'] = wp_count_posts()->publish >= 1 ? wp_count_posts()->publish : __( '0', 'proreview' );

		// Get PHP memory limit.
		$p_system_status['php_memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'proreview' );

		// Get the PHP error log path.
		$p_system_status['php_error_log_path'] = ! ini_get( 'error_log' ) ? __( 'N/A', 'proreview' ) : ini_get( 'error_log' );

		// Get PHP max upload size.
		$p_system_status['php_max_upload'] = function_exists( 'ini_get' ) ? (int) ini_get( 'upload_max_filesize' ) : __( 'N/A (ini_get function does not exist)', 'proreview' );

		// Get PHP max post size.
		$p_system_status['php_max_post'] = function_exists( 'ini_get' ) ? (int) ini_get( 'post_max_size' ) : __( 'N/A (ini_get function does not exist)', 'proreview' );

		// Get the PHP architecture.
		if ( PHP_INT_SIZE == 4 ) {
			$p_system_status['php_architecture'] = '32-bit';
		} elseif ( PHP_INT_SIZE == 8 ) {
			$p_system_status['php_architecture'] = '64-bit';
		} else {
			$p_system_status['php_architecture'] = 'N/A';
		}

		// Get server host name.
		$p_system_status['server_hostname'] = function_exists( 'gethostname' ) ? gethostname() : __( 'N/A (gethostname function does not exist)', 'proreview' );

		// Show the number of processes currently running on the server.
		$p_system_status['processes'] = function_exists( 'exec' ) ? @exec( 'ps aux | wc -l' ) : __( 'N/A (make sure exec is enabled)', 'proreview' );

		// Get the memory usage.
		$p_system_status['memory_usage'] = function_exists( 'memory_get_peak_usage' ) ? round( memory_get_peak_usage( true ) / 1024 / 1024, 2 ) : 0;

		// Get CPU usage.
		// Check to see if system is Windows, if so then use an alternative since sys_getloadavg() won't work.
		if ( stristr( PHP_OS, 'win' ) ) {
			$p_system_status['is_windows'] = true;
			$p_system_status['windows_cpu_usage'] = function_exists( 'exec' ) ? @exec( 'wmic cpu get loadpercentage /all' ) : __( 'N/A (make sure exec is enabled)', 'proreview' );
		}

		// Get the memory limit.
		$p_system_status['memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'proreview' );

		// Get the PHP maximum execution time.
		$p_system_status['php_max_execution_time'] = function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : __( 'N/A (ini_get function does not exist)', 'proreview' );

		// Get outgoing IP address.
		$p_system_status['outgoing_ip'] = function_exists( 'file_get_contents' ) ? file_get_contents( 'http://ipecho.net/plain' ) : __( 'N/A (file_get_contents function does not exist)', 'proreview' );

		$p_system_data['php'] = $p_system_status;
		$p_system_data['wp'] = $p_wordpress_status;

		return $p_system_data;
	}

	/**
	 * Generate html components.
	 *
	 * @param  string $p_components    html to display.
	 * @since  1.0.0
	 */
	public function mwb_p_plug_generate_html( $p_components = array() ) {
		if ( is_array( $p_components ) && ! empty( $p_components ) ) {
			foreach ( $p_components as $p_component ) {
				if ( ! empty( $p_component['type'] ) &&  ! empty( $p_component['id'] ) ) {
					switch ( $p_component['type'] ) {

						case 'hidden':
						case 'number':
						case 'email':
						case 'text':
						?>
						<div class="mwb-form-group mwb-p-<?php echo esc_attr($p_component['type']); ?>">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $p_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $p_component['title'] ) ? esc_html( $p_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<?php if ( 'number' != $p_component['type'] ) { ?>
												<span class="mdc-floating-label" id="my-label-id" style=""><?php echo ( isset( $p_component['placeholder'] ) ? esc_attr( $p_component['placeholder'] ) : '' ); ?></span>
											<?php } ?>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input
									class="mdc-text-field__input <?php echo ( isset( $p_component['class'] ) ? esc_attr( $p_component['class'] ) : '' ); ?>" 
									name="<?php echo ( isset( $p_component['name'] ) ? esc_html( $p_component['name'] ) : esc_html( $p_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $p_component['id'] ); ?>"
									type="<?php echo esc_attr( $p_component['type'] ); ?>"
									value="<?php echo ( isset( $p_component['value'] ) ? esc_attr( $p_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $p_component['placeholder'] ) ? esc_attr( $p_component['placeholder'] ) : '' ); ?>"
									>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $p_component['description'] ) ? esc_attr( $p_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
						<?php
						break;

						case 'password':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $p_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $p_component['title'] ) ? esc_html( $p_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<input 
									class="mdc-text-field__input <?php echo ( isset( $p_component['class'] ) ? esc_attr( $p_component['class'] ) : '' ); ?> mwb-form__password" 
									name="<?php echo ( isset( $p_component['name'] ) ? esc_html( $p_component['name'] ) : esc_html( $p_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $p_component['id'] ); ?>"
									type="<?php echo esc_attr( $p_component['type'] ); ?>"
									value="<?php echo ( isset( $p_component['value'] ) ? esc_attr( $p_component['value'] ) : '' ); ?>"
									placeholder="<?php echo ( isset( $p_component['placeholder'] ) ? esc_attr( $p_component['placeholder'] ) : '' ); ?>"
									>
									<i class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing mwb-password-hidden" tabindex="0" role="button">visibility</i>
								</label>
								<div class="mdc-text-field-helper-line">
									<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $p_component['description'] ) ? esc_attr( $p_component['description'] ) : '' ); ?></div>
								</div>
							</div>
						</div>
						<?php
						break;

						case 'textarea':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label class="mwb-form-label" for="<?php echo esc_attr( $p_component['id'] ); ?>"><?php echo ( isset( $p_component['title'] ) ? esc_html( $p_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
								<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--textarea"  	for="text-field-hero-input">
									<span class="mdc-notched-outline">
										<span class="mdc-notched-outline__leading"></span>
										<span class="mdc-notched-outline__notch">
											<span class="mdc-floating-label"><?php echo ( isset( $p_component['placeholder'] ) ? esc_attr( $p_component['placeholder'] ) : '' ); ?></span>
										</span>
										<span class="mdc-notched-outline__trailing"></span>
									</span>
									<span class="mdc-text-field__resizer">
										<textarea class="mdc-text-field__input <?php echo ( isset( $p_component['class'] ) ? esc_attr( $p_component['class'] ) : '' ); ?>" rows="2" cols="25" aria-label="Label" name="<?php echo ( isset( $p_component['name'] ) ? esc_html( $p_component['name'] ) : esc_html( $p_component['id'] ) ); ?>" id="<?php echo esc_attr( $p_component['id'] ); ?>" placeholder="<?php echo ( isset( $p_component['placeholder'] ) ? esc_attr( $p_component['placeholder'] ) : '' ); ?>"><?php echo ( isset( $p_component['value'] ) ? esc_textarea( $p_component['value'] ) : '' ); // WPCS: XSS ok. ?></textarea>
									</span>
								</label>

							</div>
						</div>

						<?php
						break;

						case 'select':
						case 'multiselect':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label class="mwb-form-label" for="<?php echo esc_attr( $p_component['id'] ); ?>"><?php echo ( isset( $p_component['title'] ) ? esc_html( $p_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
								<div class="mwb-form-select">
									<select id="<?php echo esc_attr( $p_component['id'] ); ?>" name="<?php echo ( isset( $p_component['name'] ) ? esc_html( $p_component['name'] ) : '' ); ?><?php echo ( 'multiselect' === $p_component['type'] ) ? '[]' : ''; ?>" id="<?php echo esc_attr( $p_component['id'] ); ?>" class="mdl-textfield__input <?php echo ( isset( $p_component['class'] ) ? esc_attr( $p_component['class'] ) : '' ); ?>" <?php echo 'multiselect' === $p_component['type'] ? 'multiple="multiple"' : ''; ?> >
										<?php
										foreach ( $p_component['options'] as $p_key => $p_val ) {
											?>
											<option value="<?php echo esc_attr( $p_key ); ?>"
												<?php
												if ( is_array( $p_component['value'] ) ) {
													selected( in_array( (string) $p_key, $p_component['value'], true ), true );
												} else {
													selected( $p_component['value'], (string) $p_key );
												}
												?>
												>
												<?php echo esc_html( $p_val ); ?>
											</option>
											<?php
										}
										?>
									</select>
									<label class="mdl-textfield__label" for="octane"><?php echo esc_html( $p_component['description'] ); ?><?php echo ( isset( $p_component['description'] ) ? esc_attr( $p_component['description'] ) : '' ); ?></label>
								</div>
							</div>
						</div>

						<?php
						break;

						case 'checkbox':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $p_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $p_component['title'] ) ? esc_html( $p_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control mwb-pl-4">
								<div class="mdc-form-field">
									<div class="mdc-checkbox">
										<input 
										name="<?php echo ( isset( $p_component['name'] ) ? esc_html( $p_component['name'] ) : esc_html( $p_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $p_component['id'] ); ?>"
										type="checkbox"
										class="mdc-checkbox__native-control <?php echo ( isset( $p_component['class'] ) ? esc_attr( $p_component['class'] ) : '' ); ?>"
										value="<?php echo ( isset( $p_component['value'] ) ? esc_attr( $p_component['value'] ) : '' ); ?>"
										<?php checked( $p_component['value'], '1' ); ?>
										/>
										<div class="mdc-checkbox__background">
											<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
												<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
											</svg>
											<div class="mdc-checkbox__mixedmark"></div>
										</div>
										<div class="mdc-checkbox__ripple"></div>
									</div>
									<label for="checkbox-1"><?php echo ( isset( $p_component['description'] ) ? esc_attr( $p_component['description'] ) : '' ); ?></label>
								</div>
							</div>
						</div>
						<?php
						break;

						case 'radio':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="<?php echo esc_attr( $p_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $p_component['title'] ) ? esc_html( $p_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control mwb-pl-4">
								<div class="mwb-flex-col">
									<?php
									foreach ( $p_component['options'] as $p_radio_key => $p_radio_val ) {
										?>
										<div class="mdc-form-field">
											<div class="mdc-radio">
												<input
												name="<?php echo ( isset( $p_component['name'] ) ? esc_html( $p_component['name'] ) : esc_html( $p_component['id'] ) ); ?>"
												value="<?php echo esc_attr( $p_radio_key ); ?>"
												type="radio"
												class="mdc-radio__native-control <?php echo ( isset( $p_component['class'] ) ? esc_attr( $p_component['class'] ) : '' ); ?>"
												<?php checked( $p_radio_key, $p_component['value'] ); ?>
												>
												<div class="mdc-radio__background">
													<div class="mdc-radio__outer-circle"></div>
													<div class="mdc-radio__inner-circle"></div>
												</div>
												<div class="mdc-radio__ripple"></div>
											</div>
											<label for="radio-1"><?php echo esc_html( $p_radio_val ); ?></label>
										</div>	
										<?php
									}
									?>
								</div>
							</div>
						</div>
						<?php
						break;

						case 'radio-switch':
						?>

						<div class="mwb-form-group">
							<div class="mwb-form-group__label">
								<label for="" class="mwb-form-label"><?php echo ( isset( $p_component['title'] ) ? esc_html( $p_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
							</div>
							<div class="mwb-form-group__control">
								<div>
									<div class="mdc-switch">
										<div class="mdc-switch__track"></div>
										<div class="mdc-switch__thumb-underlay">
											<div class="mdc-switch__thumb"></div>
											<input name="<?php echo ( isset( $p_component['name'] ) ? esc_html( $p_component['name'] ) : esc_html( $p_component['id'] ) ); ?>" type="checkbox" id="<?php echo esc_html( $p_component['id'] ); ?>" value="on" class="mdc-switch__native-control <?php echo ( isset( $p_component['class'] ) ? esc_attr( $p_component['class'] ) : '' ); ?>" role="switch" aria-checked="<?php if ( 'on' == $p_component['value'] ) echo 'true'; else echo 'false'; ?>"
											<?php checked( $p_component['value'], 'on' ); ?>
											>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
						break;

						case 'button':
						?>
						<div class="mwb-form-group">
							<div class="mwb-form-group__label"></div>
							<div class="mwb-form-group__control">
								<button class="mdc-button mdc-button--raised" name= "<?php echo ( isset( $p_component['name'] ) ? esc_html( $p_component['name'] ) : esc_html( $p_component['id'] ) ); ?>"
									id="<?php echo esc_attr( $p_component['id'] ); ?>"> <span class="mdc-button__ripple"></span>
									<span class="mdc-button__label <?php echo ( isset( $p_component['class'] ) ? esc_attr( $p_component['class'] ) : '' ); ?>"><?php echo ( isset( $p_component['button_text'] ) ? esc_html( $p_component['button_text'] ) : '' ); ?></span>
								</button>
							</div>
						</div>

						<?php
						break;

						case 'multi':
							?>
							<div class="mwb-form-group mwb-isfw-<?php echo esc_attr( $p_component['type'] ); ?>">
								<div class="mwb-form-group__label">
									<label for="<?php echo esc_attr( $p_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $p_component['title'] ) ? esc_html( $p_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
									</div>
									<div class="mwb-form-group__control">
									<?php
									foreach ( $p_component['value'] as $component ) {
										?>
											<label class="mdc-text-field mdc-text-field--outlined">
												<span class="mdc-notched-outline">
													<span class="mdc-notched-outline__leading"></span>
													<span class="mdc-notched-outline__notch">
														<?php if ( 'number' != $component['type'] ) { ?>
															<span class="mdc-floating-label" id="my-label-id" style=""><?php echo ( isset( $p_component['placeholder'] ) ? esc_attr( $p_component['placeholder'] ) : '' ); ?></span>
														<?php } ?>
													</span>
													<span class="mdc-notched-outline__trailing"></span>
												</span>
												<input 
												class="mdc-text-field__input <?php echo ( isset( $p_component['class'] ) ? esc_attr( $p_component['class'] ) : '' ); ?>" 
												name="<?php echo ( isset( $p_component['name'] ) ? esc_html( $p_component['name'] ) : esc_html( $p_component['id'] ) ); ?>"
												id="<?php echo esc_attr( $component['id'] ); ?>"
												type="<?php echo esc_attr( $component['type'] ); ?>"
												value="<?php echo ( isset( $p_component['value'] ) ? esc_attr( $p_component['value'] ) : '' ); ?>"
												placeholder="<?php echo ( isset( $p_component['placeholder'] ) ? esc_attr( $p_component['placeholder'] ) : '' ); ?>"
												<?php echo esc_attr( ( 'number' === $component['type'] ) ? 'max=10 min=0' : '' ); ?>
												>
											</label>
								<?php } ?>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $p_component['description'] ) ? esc_attr( $p_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
								<?php
							break;
						case 'color':
						case 'date':
						case 'file':
							?>
							<div class="mwb-form-group mwb-isfw-<?php echo esc_attr( $p_component['type'] ); ?>">
								<div class="mwb-form-group__label">
									<label for="<?php echo esc_attr( $p_component['id'] ); ?>" class="mwb-form-label"><?php echo ( isset( $p_component['title'] ) ? esc_html( $p_component['title'] ) : '' ); // WPCS: XSS ok. ?></label>
								</div>
								<div class="mwb-form-group__control">
									<label class="mdc-text-field mdc-text-field--outlined">
										<input 
										class="<?php echo ( isset( $p_component['class'] ) ? esc_attr( $p_component['class'] ) : '' ); ?>" 
										name="<?php echo ( isset( $p_component['name'] ) ? esc_html( $p_component['name'] ) : esc_html( $p_component['id'] ) ); ?>"
										id="<?php echo esc_attr( $p_component['id'] ); ?>"
										type="<?php echo esc_attr( $p_component['type'] ); ?>"
										value="<?php echo ( isset( $p_component['value'] ) ? esc_attr( $p_component['value'] ) : '' ); ?>"
										<?php echo esc_html( ( 'date' === $p_component['type'] ) ? 'max='. date( 'Y-m-d', strtotime( date( "Y-m-d", mktime() ) . " + 365 day" ) ) .' ' . 'min=' . date( "Y-m-d" ) . '' : '' ); ?>
										>
									</label>
									<div class="mdc-text-field-helper-line">
										<div class="mdc-text-field-helper-text--persistent mwb-helper-text" id="" aria-hidden="true"><?php echo ( isset( $p_component['description'] ) ? esc_attr( $p_component['description'] ) : '' ); ?></div>
									</div>
								</div>
							</div>
							<?php
						break;

						case 'submit':
						?>
						<tr valign="top">
							<td scope="row">
								<input type="submit" class="button button-primary" 
								name="<?php echo ( isset( $p_component['name'] ) ? esc_html( $p_component['name'] ) : esc_html( $p_component['id'] ) ); ?>"
								id="<?php echo esc_attr( $p_component['id'] ); ?>"
								class="<?php echo ( isset( $p_component['class'] ) ? esc_attr( $p_component['class'] ) : '' ); ?>"
								value="<?php echo esc_attr( $p_component['button_text'] ); ?>"
								/>
							</td>
						</tr>
						<?php
						break;

						default:
						break;
					}
				}
			}
		}
	}
}
