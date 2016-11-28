<?php
/**
 * The core plugin class.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    XT_Eventbrite_Import
 * @subpackage XT_Eventbrite_Import/includes
 */
class XT_Eventbrite_Import {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      XT_Eventbrite_Import_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'eventbrite-import-for-the-events-calendar';
		$this->version = '1.0.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - XT_Eventbrite_Import_Loader. Orchestrates the hooks of the plugin.
	 * - XT_Eventbrite_Import_i18n. Defines internationalization functionality.
	 * - XT_Eventbrite_Import_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xt-eventbrite-import-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xt-eventbrite-import-i18n.php';

		/**
		 * The class responsible for Manage Insert/Delete operation on eventbrite url.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xt-eventbrite-import-manage-import.php';

		/**
		 * The class responsible for import and save TEC Eventbrite events.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xt-eventbrite-import-tec-importer.php';

		/**
		 * The class responsible for import and save EM Eventbrite events.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xt-eventbrite-import-em-importer.php';

		/**
		 * The class responsible for Display Ticket section of Eventbrite on Event page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-xt-eventbrite-import-display.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-xt-eventbrite-import-admin.php';

		$this->loader = new XT_Eventbrite_Import_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the XT_Eventbrite_Import_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new XT_Eventbrite_Import_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new XT_Eventbrite_Import_Admin( $this->get_plugin_name(), $this->get_version() );
		$xtei_manage_imports = new XT_Eventbrite_Import_Manage_Import( $this->get_plugin_name(), $this->get_version() );
		$xtei_display = new XT_Eventbrite_Import_Display( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu',$plugin_admin, 'xtei_add_import_menu', 30 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    XT_Eventbrite_Import_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Check for dependencies to work this plugin and deactive plugin if requirements not met.
	 *
	 * @since    1.0.0
	 * @param string $plugin_basename Plugin basename.
	 */
	public function xtei_check_requirements( $plugin_basename ) {
		if ( ! $this->xtei_is_meets_requirements() ) {
			deactivate_plugins( $plugin_basename );
			add_action( 'admin_notices',array( $this, 'xtei_deactivate_notice' ) );
			return false;
		}
		return true;
	}
	/**
	 * Check meets dependencies requirements
	 *
	 * @since  1.0.0
	 * @return boolean true if met requirements.
	 */
	public function xtei_is_meets_requirements() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) || is_plugin_active( 'events-manager/events-manager.php' ) ) {
			$xtei_options = get_option( XTEI_OPTIONS, array() );
			if( ! isset( $xtei_options['eventbrite_oauth_token'] ) || $xtei_options['eventbrite_oauth_token'] == "" ){
				add_action( 'admin_notices', array( $this, 'xtei_eventbrite_key_warning') );
			}
			return true;
		}
		return false;
	}

	/**
	 * Display an error message when the plugin deactivates itself.
	 */
	public function xtei_deactivate_notice() {
		?>
		<div class="error">
		    <p>
				<?php _e( 'Import Eventbrite Events requires <a href="https://wordpress.org/plugins/the-events-calendar/" target="_blank" >The Events Calendar</a> or <a href="https://wordpress.org/plugins/events-manager/" target="_blank" >Events Manager</a> to be installed and activated. Import Eventbrite Events has been deactivated itself.', 'xt-eventbrite-import' ); ?>
		    </p>
		</div>
		<?php
	}

	/**
	 * Display an warning message if eventbrite key is not there.
	 */
	public function xtei_eventbrite_key_warning() {
		?>
	    <div class="notice notice-warning is-dismissible">
	        <p><?php esc_html_e( 'Please insert Eventbrite "Personal OAuth token" in order to work eventbrite import.', 'xt-eventbrite-import' ) ?></p>
	    </div>
	    <?php
	}
}
