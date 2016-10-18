<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    XT_Eventbrite_Import
 * @subpackage XT_Eventbrite_Import/admin
 */
class XT_Eventbrite_Import_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/xt-eventbrite-import-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Add submenu for import page.
	 *
	 * @since    1.0.0
	 */
	public function xtei_add_import_menu() {
		add_menu_page(
			esc_html__( 'Eventbrite Event Import', 'xt-eventbrite-import' ),
			esc_html__( 'Eventbrite Import', 'xt-eventbrite-import' ),
			'manage_options',
			'xt-eventbrite-import',
			array( $this, 'xtei_event_import_page' ),
			'dashicons-calendar-alt',
			26
		);
	}

	/**
	 * Render page for eventbrite import.
	 *
	 * @since    1.0.0
	 */
	public function xtei_event_import_page() {
		$xtei_options = get_option( XTEI_OPTIONS, array() );
		include XTEI_ADMIN_PATH . 'partials/xt-eventbrite-import-page.php';
	}
}
