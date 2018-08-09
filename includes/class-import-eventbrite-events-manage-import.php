<?php
/**
 * Class for manane Imports submissions.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    Import_Eventbrite_Events
 * @subpackage Import_Eventbrite_Events/includes
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Import_Eventbrite_Events_Manage_Import {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'handle_import_form_submit' ), 99 );
	}

	/**
	 * Process insert group form for TEC.
	 *
	 * @since    1.0.0
	 */
	public function handle_import_form_submit() {
		global $iee_errors;
		$event_data = array();

		if ( isset( $_POST['iee_action'] ) && $_POST['iee_action'] == 'iee_import_submit' && check_admin_referer( 'iee_import_form_nonce_action', 'iee_import_form_nonce' ) ) {

			$event_data['import_into'] = isset( $_POST['event_plugin'] ) ? sanitize_text_field( $_POST['event_plugin'] ) : '';
			if ( $event_data['import_into'] == '' ) {
				$iee_errors[] = esc_html__( 'Please provide Import into plugin for Event import.', 'import-eventbrite-events' );
				return;
			}
			$event_data['import_type']      = 'onetime';
			$event_data['import_frequency'] = isset( $_POST['import_frequency'] ) ? sanitize_text_field( $_POST['import_frequency'] ) : 'daily';
			$event_data['event_status']     = isset( $_POST['event_status'] ) ? sanitize_text_field( $_POST['event_status'] ) : 'pending';
			$event_data['event_cats']       = isset( $_POST['event_cats'] ) ? $_POST['event_cats'] : array();

			$this->handle_eventbrite_import_form_submit( $event_data );
		}
	}

	/**
	 * Handle Eventbrite import form submit.
	 *
	 * @since    1.0.0
	 */
	public function handle_eventbrite_import_form_submit( $event_data ) {
		global $iee_errors, $iee_success_msg, $iee_events;
		$import_events      = array();
		$eventbrite_options = iee_get_import_options( 'eventbrite' );
		if ( ! isset( $eventbrite_options['eventbrite_oauth_token'] ) || $eventbrite_options['eventbrite_oauth_token'] == '' ) {
			$iee_errors[] = esc_html__( 'Please insert Eventbrite "Personal OAuth token" in settings.', 'import-eventbrite-events' );
			return;
		}

		$event_data['import_origin']       = 'eventbrite';
		$event_data['import_by']           = 'event_id';
		$event_data['eventbrite_event_id'] = isset( $_POST['iee_eventbrite_id'] ) ? sanitize_text_field( $_POST['iee_eventbrite_id'] ) : '';
		$event_data['organizer_id']        = '';

		if ( ! is_numeric( $event_data['eventbrite_event_id'] ) ) {
			$iee_errors[] = esc_html__( 'Please provide valid Eventbrite event ID.', 'import-eventbrite-events' );
			return;
		}
		$import_events[] = $iee_events->eventbrite->import_event_by_event_id( $event_data );
		if ( $import_events && ! empty( $import_events ) ) {
			$iee_events->common->display_import_success_message( $import_events, $event_data );
		}
	}

}
