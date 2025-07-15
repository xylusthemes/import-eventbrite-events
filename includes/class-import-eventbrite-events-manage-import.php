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
		add_action( 'admin_init', array( $this, 'handle_gma_settings_submit' ), 99 );
	}

	/**
	 * Process insert group form for TEC.
	 *
	 * @since    1.0.0
	 */
	public function handle_import_form_submit() {
		global $iee_errors;
		$event_data = array();

		if ( isset( $_POST['iee_action'] ) && sanitize_text_field( wp_unslash ( $_POST['iee_action'] ) ) == 'iee_import_submit' && check_admin_referer( 'iee_import_form_nonce_action', 'iee_import_form_nonce' ) ) {

			$event_data['import_into'] = isset( $_POST['event_plugin'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['event_plugin'] ) ) ) : '';
			if ( $event_data['import_into'] == '' ) {
				$iee_errors[] = esc_html__( 'Please provide Import into plugin for Event import.', 'import-eventbrite-events' );
				return;
			}
			$event_data['import_type']      = 'onetime';
			$event_data['import_frequency'] = isset( $_POST['import_frequency'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['import_frequency'] ) ) ) : 'daily';
			$event_data['event_status']     = isset( $_POST['event_status'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['event_status'] ) ) ) : 'pending';
			$event_data['event_cats']       = isset( $_POST['event_cats'] ) ? sanitize_text_field( wp_unslash ( $_POST['event_cats'] ) ) : array();
			$event_data['event_author']     = !empty( $_POST['event_author'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['event_author'] ) ) ) : get_current_user_id();

			$this->handle_eventbrite_import_form_submit( $event_data );
		}
	}

	/**
	 * Process insert google maps api key for embed maps
	 *
	 * @since    1.7.0
	 */
	public function handle_gma_settings_submit() {
		global $iee_errors, $iee_success_msg;
		if ( isset( $_POST['iee_gma_action'] ) && 'iee_save_gma_settings' === sanitize_text_field( wp_unslash( $_POST['iee_gma_action'] ) ) && check_admin_referer( 'iee_gma_setting_form_nonce_action', 'iee_gma_setting_form_nonce' ) ) { // input var okay.
			$gma_option = array();
			$gma_option['iee_google_maps_api_key'] = isset( $_POST['iee_google_maps_api_key'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['iee_google_maps_api_key'] ) ) ) : ''; // input var okay.
			$is_update = update_option( 'iee_google_maps_api_key', $gma_option['iee_google_maps_api_key'] );
			if ( $is_update ) {
				$iee_success_msg[] = __( 'Google Maps API Key has been saved successfully.', 'import-eventbrite-events' );
			} else {
				$iee_errors[] = __( 'Something went wrong! please try again.', 'import-eventbrite-events' );
			}
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
		$event_data['eventbrite_event_id'] = isset( $_POST['iee_eventbrite_id'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['iee_eventbrite_id'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$event_data['organizer_id']        = '';
		$event_data['collection_id']       = '';

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