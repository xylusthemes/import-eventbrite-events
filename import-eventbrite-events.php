<?php
/**
 * Plugin Name:       Import Eventbrite Events
 * Plugin URI:        http://xylusthemes.com/plugins/import-eventbrite-events/
 * Description:       Import Eventbrite Events allows you to import Eventbrite (eventbrite.com) events into your WordPress site.
 * Version:           1.7.8
 * Author:            Xylus Themes
 * Author URI:        https://xylusthemes.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       import-eventbrite-events
 * Domain Path:       /languages
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 * @package    Import_Eventbrite_Events
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Import_Eventbrite_Events' ) ) :

	/**
	 * Main Import Eventbrite Events class
	 */
	class Import_Eventbrite_Events {

		/** Singleton *************************************************************/
		/**
		 * Import_Eventbrite_Events The one true Import_Eventbrite_Events.
		 */
		private static $instance;
		public $common, $cpt, $eventbrite, $admin, $manage_import, $iee, $tec, $em, $eventon, $event_organizer, $aioec, $my_calendar, $ee4, $common_pro, $cron, $eventbrite_pro, $eventprime, $elementor_widget;

		/**
		 * Main Import Eventbrite Events Instance.
		 *
		 * Insure that only one instance of Import_Eventbrite_Events exists in memory at any one time.
		 * Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @static object $instance
		 * @uses Import_Eventbrite_Events::setup_constants() Setup the constants needed.
		 * @uses Import_Eventbrite_Events::includes() Include the required files.
		 * @uses Import_Eventbrite_Events::laod_textdomain() load the language files.
		 * @see run_import_eventbrite_events()
		 * @return object| Import Eventbrite Events the one true Import Eventbrite Events.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Import_Eventbrite_Events ) ) {
				self::$instance = new Import_Eventbrite_Events();
				self::$instance->setup_constants();

				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
				add_action( 'wp_enqueue_scripts', array( self::$instance, 'iee_enqueue_style' ) );
				add_action( 'wp_enqueue_scripts', array( self::$instance, 'iee_enqueue_script' ) );
				add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( self::$instance, 'iee_setting_doc_links' ) );

				self::$instance->includes();
				self::$instance->common     = new Import_Eventbrite_Events_Common();
				self::$instance->cpt        = new Import_Eventbrite_Events_Cpt();
				self::$instance->eventbrite = new Import_Eventbrite_Events_Eventbrite();
				self::$instance->admin      = new Import_Eventbrite_Events_Admin();
				if ( iee_is_pro() ) {
					self::$instance->manage_import = new Import_Eventbrite_Events_Pro_Manage_Import();
				} else {
					self::$instance->manage_import = new Import_Eventbrite_Events_Manage_Import();
				}
				self::$instance->iee             = new Import_Eventbrite_Events_IEE();
				self::$instance->tec             = new Import_Eventbrite_Events_TEC();
				self::$instance->em              = new Import_Eventbrite_Events_EM();
				self::$instance->eventon         = new Import_Eventbrite_Events_EventON();
				self::$instance->eventprime      = new Import_Eventbrite_Events_EventPrime();
				self::$instance->event_organizer = new Import_Eventbrite_Events_Event_Organizer();
				self::$instance->aioec           = new Import_Eventbrite_Events_Aioec();
				self::$instance->my_calendar     = new Import_Eventbrite_Events_My_Calendar();
				self::$instance->ee4             = new Import_Eventbrite_Events_EE4();

			}
			return self::$instance;
		}

		/** Magic Methods *********************************************************/

		/**
		 * A dummy constructor to prevent Import_Eventbrite_Events from being loaded more than once.
		 *
		 * @since 1.0.0
		 * @see Import_Eventbrite_Events::instance()
		 * @see run_import_eventbrite_events()
		 */
		private function __construct() {
			/* Do nothing here */
		}

		/**
		 * A dummy magic method to prevent Import_Eventbrite_Events from being cloned.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'import-eventbrite-events' ), '1.7.8' );
		}

		/**
		 * A dummy magic method to prevent Import_Eventbrite_Events from being unserialized.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'import-eventbrite-events' ), '1.7.8' );
		}


		/**
		 * Setup plugins constants.
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin version.
			if ( ! defined( 'IEE_VERSION' ) ) {
				define( 'IEE_VERSION', '1.7.8' );
			}

			// Minimum Pro plugin version.
			if ( ! defined( 'IEE_MIN_PRO_VERSION' ) ) {
				define( 'IEE_MIN_PRO_VERSION', '1.7.4' );
			}

			// Plugin folder Path.
			if ( ! defined( 'IEE_PLUGIN_DIR' ) ) {
				define( 'IEE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin folder URL.
			if ( ! defined( 'IEE_PLUGIN_URL' ) ) {
				define( 'IEE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin root file.
			if ( ! defined( 'IEE_PLUGIN_FILE' ) ) {
				define( 'IEE_PLUGIN_FILE', __FILE__ );
			}

			// Options.
			if ( ! defined( 'IEE_OPTIONS' ) ) {
				define( 'IEE_OPTIONS', 'xtei_eventbrite_options' );
			}

			// Pro plugin Buy now Link.
			if ( ! defined( 'IEE_PLUGIN_BUY_NOW_URL' ) ) {
				define( 'IEE_PLUGIN_BUY_NOW_URL', 'http://xylusthemes.com/plugins/import-eventbrite-events/?utm_source=insideplugin&utm_medium=web&utm_content=sidebar&utm_campaign=freeplugin' );
			}
		}

		/**
		 * Include required files.
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function includes() {

			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-common.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-list-table.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-admin.php';
			if ( iee_is_pro() ) {
				$pro_dir = plugin_dir_path( __DIR__ ) . 'import-eventbrite-events-pro/';
				if ( defined( 'IEEPRO_PLUGIN_DIR' ) ) {
					$pro_dir = IEEPRO_PLUGIN_DIR;
				}
				require_once $pro_dir . 'includes/class-import-eventbrite-events-manage-import.php';
			} else {
				require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-manage-import.php';
			}
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-cpt.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-eventbrite.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-iee.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-tec.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-em.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-eventon.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-eventprime.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-event_organizer.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-aioec.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-my-calendar.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-import-eventbrite-events-ee4.php';
			require_once IEE_PLUGIN_DIR . 'includes/class-iee-plugin-deactivation.php';
			// Gutenberg Block.
			require_once IEE_PLUGIN_DIR . 'blocks/eventbrite-events/index.php';
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {

			load_plugin_textdomain(
				'import-eventbrite-events',
				false,
				basename( dirname( __FILE__ ) ) . '/languages'
			);

		}

		/**
		 * IEE setting And docs link add in plugin page.
		 *
		 * @since 1.0
		 * @return void
		 */
		public function iee_setting_doc_links( $links ) {
			$iee_setting_doc_link = array(
				'iee-event-setting' => sprintf(
					'<a href="%s">%s</a>',
					esc_url( admin_url( 'admin.php?page=eventbrite_event&tab=settings' ) ),
					esc_html__( 'Setting', 'import-eventbrite-events' )
				),
				'iee-event-docs' => sprintf(
					'<a target="_blank" href="%s">%s</a>',
					esc_url( 'https://docs.xylusthemes.com/docs/import-eventbrite-events-plugin/' ),
					esc_html__( 'Docs', 'import-eventbrite-events' )
				),
			);
			
			$upgrate_to_pro = array();
			if( !iee_is_pro() ){
				$upgrate_to_pro = array( 'iee-event-pro-link' => sprintf(
                    '<a href="%s" target="_blank" style="color:#1da867;font-weight: 900;">%s</a>',
                    esc_url( 'https://xylusthemes.com/plugins/import-eventbrite-events/' ),
                    esc_html__( 'Upgrade to Pro', 'import-eventbrite-events' )
                ) ) ;
			}

			return array_merge( $links, $iee_setting_doc_link, $upgrate_to_pro );
		}

		/**
		 * Enqueue style front-end
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function iee_enqueue_style() {

			$css_dir = IEE_PLUGIN_URL . 'assets/css/';
			wp_enqueue_style( 'font-awesome', $css_dir . 'font-awesome.min.css', false, IEE_VERSION );
			wp_enqueue_style( 'import-eventbrite-events-front', $css_dir . 'import-eventbrite-events.css', false, IEE_VERSION );
			wp_enqueue_style( 'import-eventbrite-events-front-style2', $css_dir . 'grid-style2.css', false, IEE_VERSION );
		}

		/**
		 * Enqueue script front-end
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function iee_enqueue_script() {
			// enqueue script here.
		}

	}

endif; // End If class exists check.

/**
 * The main function for that returns Import_Eventbrite_Events
 *
 * The main function responsible for returning the one true Import_Eventbrite_Events
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $iee_events = run_import_eventbrite_events(); ?>
 *
 * @since 1.0.0
 * @return object|Import_Eventbrite_Events The one true Import_Eventbrite_Events Instance.
 */
function run_import_eventbrite_events() {
	return Import_Eventbrite_Events::instance();
}

/**
 * Get Import events setting options
 *
 * @since 1.0
 * @param string $type Option type.
 * @return array|bool Options.
 */
function iee_get_import_options( $type = '' ) {
	$iee_options = get_option( IEE_OPTIONS );
	return $iee_options;
}

// Get Import_Eventbrite_Events Running.
global $iee_events, $iee_errors, $iee_success_msg, $iee_warnings, $iee_info_msg;
$iee_events = run_import_eventbrite_events();
$iee_errors = $iee_warnings = $iee_success_msg = $iee_info_msg = array();

/**
 * The code that runs during plugin activation.
 *
 * @since 1.0
 */
function iee_activate_import_eventbrite_events() {
	global $iee_events;
	$iee_events->cpt->register_event_post_type();
	flush_rewrite_rules();
	add_option( 'iee_plugin_activated', true );
}
register_activation_hook( __FILE__, 'iee_activate_import_eventbrite_events' );
