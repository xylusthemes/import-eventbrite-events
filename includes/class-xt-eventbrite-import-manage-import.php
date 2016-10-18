<?php
/**
 * Class for manage Eventbrite Import.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    XT_Eventbrite_Import
 * @subpackage XT_Eventbrite_Import/includes
 */
class XT_Eventbrite_Import_Manage_Import {

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
	 * Error generated during form submit
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $errors    Error generated during form submit
	 */
	protected $errors;

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
		add_action( 'init', array( $this, 'xtei_save_settings' ), 30 );
		add_action( 'init', array( $this, 'xtei_handle_import_form' ), 30 );
		add_action( 'admin_notices', array( $this, 'xtei_display_errors' ), 100 );
	}

	/**
	 * Eventbrite Import Settings save.
	 *
	 * @since    1.0.0
	 */
	public function xtei_save_settings() {
		if ( isset( $_POST['xtei_action'] ) && $_POST['xtei_action'] == 'xtei_save_settings' &&  check_admin_referer( 'xtei_setting_form_nonce_action', 'xtei_setting_form_nonce' ) ) {

			$xtei_eventbrite_options = array();
			// validate values.
			$xtei_eventbrite_options['eventbrite_oauth_token'] = isset( $_POST['xtei_eventbrite_oauth_token'] ) ? sanitize_text_field( $_POST['xtei_eventbrite_oauth_token'] ) : '';
			$xtei_eventbrite_options['default_status'] = isset( $_POST['xtei_default_status'] ) ? sanitize_text_field( $_POST['xtei_default_status'] ) : '';
			$xtei_eventbrite_options['enable_ticket_sec'] = isset( $_POST['enable_ticket_sec'] ) ? sanitize_text_field( $_POST['enable_ticket_sec'] ) : '';

			$is_update = update_option( 'xtei_eventbrite_options', $xtei_eventbrite_options );
			add_action( 'admin_notices', array( $this, 'xtei_display_setting_success' ), 100 );
		}
	}

	/**
	 * Handle Import Eventbrite Event.
	 *
	 * @since    1.0.0
	 */
	public function xtei_handle_import_form() {
		if ( isset( $_POST['xtei_action'] ) && $_POST['xtei_action'] == 'xtei_tec_import_submit' &&  check_admin_referer( 'xtei_import_form_nonce_action', 'xtei_import_form_nonce' ) ) {

			// validate values.
			$xtei_eventbrite_id = isset( $_POST['xtei_eventbrite_id'] ) ? sanitize_text_field( $_POST['xtei_eventbrite_id']) : '';
			if( ! is_numeric( $xtei_eventbrite_id ) ){
				$this->errors[] = __( 'Please Provide valid Eventbrite Event ID.', 'xt-eventbrite-import');
			}
			if ( ! empty( $xtei_eventbrite_id ) && empty( $this->errors ) ) {
				$this->xtei_import_tec_eventbrite_event( $xtei_eventbrite_id );
			}
		}
		// Check for Events manager's Event.
		if ( isset( $_POST['xtei_action'] ) && $_POST['xtei_action'] == 'xtei_em_import_submit' &&  check_admin_referer( 'xtei_import_form_nonce_action', 'xtei_import_form_nonce' ) ) {

			// validate values.
			$xtei_eventbrite_id = isset( $_POST['xtei_eventbrite_id'] ) ? sanitize_text_field( $_POST['xtei_eventbrite_id']) : '';
			if( ! is_numeric( $xtei_eventbrite_id ) ){
				$this->errors[] = __( 'Please Provide valid Eventbrite Event ID.', 'xt-eventbrite-import');
			}
			if ( ! empty( $xtei_eventbrite_id ) && empty( $this->errors ) ) {
				$this->xtei_import_em_eventbrite_event( $xtei_eventbrite_id );
			}
		}

	}

	/**
	 * Import Eventbrite Event to The Events Calander
	 *
	 * @since    1.0.0
	 * @param string $eventbrite_id Eventbrite Event ID.
	 */
	public function xtei_import_tec_eventbrite_event( $eventbrite_id ) {

		$xtei_importer = new XT_Eventbrite_Import_Tec_Importer( $this->plugin_name, $this->version );
		$is_exitsing_event = $xtei_importer->xtei_get_event_by_eventbrite_event_id( $eventbrite_id );
		if ( $is_exitsing_event ) {
			$this->errors[] = __( 'Eventbrite event is already exists', 'xt-eventbrite-import');
			return;
		}
		$xtei_import = $xtei_importer->xtei_run_importer( $eventbrite_id );
		if( $xtei_import && ! empty( $xtei_import ) ){
			if( isset( $xtei_import['status'] ) && $xtei_import['status'] == 0 ){
				$this->errors[] = __( $xtei_import['message'], 'xt-eventbrite-import');
				return;
			}
		}
		add_action( 'admin_notices', array( $this, 'xtei_display_import_success' ), 100 );
		return;
	}

	/**
	 * Import Eventbrite Event to Events Manager
	 *
	 * @since    1.0.0
	 * @param string $eventbrite_id Eventbrite Event ID.
	 */
	public function xtei_import_em_eventbrite_event( $eventbrite_id ) {

		$xtei_importer = new XT_Eventbrite_Import_Em_Importer( $this->plugin_name, $this->version );
		$is_exitsing_event = $xtei_importer->xtei_get_event_by_eventbrite_event_id( $eventbrite_id );
		if ( $is_exitsing_event ) {
			$this->errors[] = __( 'Eventbrite event is already exists', 'xt-eventbrite-import');
			return;
		}
		$xtei_import = $xtei_importer->xtei_run_importer( $eventbrite_id );
		if( $xtei_import && ! empty( $xtei_import ) ){
			if( isset( $xtei_import['status'] ) && $xtei_import['status'] == 0 ){
				$this->errors[] = __( $xtei_import['message'], 'xt-eventbrite-import');
				return;
			}
		}
		add_action( 'admin_notices', array( $this, 'xtei_display_import_success' ), 100 );
		return;
	}

	/**
	 * Display Errors
	 *
	 * @since    1.0.0
	 */
	public function xtei_display_errors() {
		if ( ! empty( $this->errors ) ) {
			foreach ( $this->errors as $error ) :
			    ?>
			    <div class="notice notice-error is-dismissible">
			        <p><?php echo $error; ?></p>
			    </div>
			    <?php
			endforeach;
		}
	}

	/**
	 * Display Import Success
	 *
	 * @since    1.0.0
	 */
	public function xtei_display_import_success() {
	    ?>
	    <div class="notice notice-success is-dismissible">
	        <p><?php _e( 'Eventbrite event imported successfully.', 'xt-eventbrite-import' ) ?></p>
	    </div>
	    <?php
	}

	/**
	 * Display Settings Success
	 *
	 * @since    1.0.0
	 */
	public function xtei_display_setting_success() {
	    ?>
	    <div class="notice notice-success is-dismissible">
	        <p><?php _e( 'Eventbrite import settings saved successfully.', 'xt-eventbrite-import' ) ?></p>
	    </div>
	    <?php
	}
}
