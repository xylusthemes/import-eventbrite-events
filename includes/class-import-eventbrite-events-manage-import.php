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
if ( ! defined( 'ABSPATH' ) ) exit;

class Import_Eventbrite_Events_Manage_Import {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'setup_success_messages' ) );
		add_action( 'admin_init', array( $this, 'handle_import_form_submit' ) , 99);
		add_action( 'admin_init', array( $this, 'handle_import_settings_submit' ), 99 );
		add_action( 'admin_init', array( $this, 'handle_listtable_oprations' ), 99 );
	}

	/**
	 * Process insert group form for TEC.
	 *
	 * @since    1.0.0
	 */
	public function handle_import_form_submit() {
		global $iee_errors; 
		$event_data = array();

		if ( isset( $_POST['iee_action'] ) && $_POST['iee_action'] == 'iee_import_submit' &&  check_admin_referer( 'iee_import_form_nonce_action', 'iee_import_form_nonce' ) ) {
			
			$event_data['import_into'] = isset( $_POST['event_plugin'] ) ? sanitize_text_field( $_POST['event_plugin']) : '';
			if( $event_data['import_into'] == '' ){
				$iee_errors[] = esc_html__( 'Please provide Import into plugin for Event import.', 'import-eventbrite-events' );
				return;
			}
			$event_data['import_type'] = 'onetime';
			$event_data['import_frequency'] = isset( $_POST['import_frequency'] ) ? sanitize_text_field( $_POST['import_frequency']) : 'daily';
			$event_data['event_status'] = isset( $_POST['event_status'] ) ? sanitize_text_field( $_POST['event_status']) : 'pending';
			$event_data['event_cats'] = isset( $_POST['event_cats'] ) ? $_POST['event_cats'] : array();

			$this->handle_eventbrite_import_form_submit( $event_data );
		}
	}

	/**
	 * Process insert group form for TEC.
	 *
	 * @since    1.0.0
	 */
	public function handle_import_settings_submit() {
		global $iee_errors, $iee_success_msg;
		if ( isset( $_POST['iee_action'] ) && $_POST['iee_action'] == 'iee_save_settings' &&  check_admin_referer( 'iee_setting_form_nonce_action', 'iee_setting_form_nonce' ) ) {
				
			$iee_options = array();
			$iee_options = isset( $_POST['eventbrite'] ) ? $_POST['eventbrite'] : array();
			
			$is_update = update_option( IEE_OPTIONS, $iee_options );
			if( $is_update ){
				$iee_success_msg[] = __( 'Import settings has been saved successfully.', 'import-eventbrite-events' );
			}else{
				$iee_errors[] = __( 'Something went wrong! please try again.', 'import-eventbrite-events' );
			}
		}
	}

	/**
	 * Delete scheduled import from list table.
	 *
	 * @since    1.0.0
	 */
	public function handle_listtable_oprations() {

		global $iee_success_msg;
		if ( isset( $_GET['iee_action'] ) && $_GET['iee_action'] == 'iee_simport_delete' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'iee_delete_import_nonce') ) {
			$import_id = $_GET['import_id'];
			$page = isset($_GET['page'] ) ? $_GET['page'] : 'eventbrite_event';
			$tab = isset($_GET['tab'] ) ? $_GET['tab'] : 'scheduled';
			$wp_redirect = admin_url( 'admin.php?page='.$page );
			if ( $import_id > 0 ) {
				$post_type = get_post_type( $import_id );
				if ( $post_type == 'iee_scheduled_import' ) {
					wp_delete_post( $import_id, true );
					$query_args = array( 'iee_msg' => 'import_del', 'tab' => $tab );
        			wp_redirect(  add_query_arg( $query_args, $wp_redirect ) );
					exit;
				}
			}
		}

		if ( isset( $_GET['iee_action'] ) && $_GET['iee_action'] == 'iee_history_delete' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'iee_delete_history_nonce' ) ) {
			$history_id = (int)$_GET['history_id'];
			$page = isset($_GET['page'] ) ? $_GET['page'] : 'eventbrite_event';
			$tab = isset($_GET['tab'] ) ? $_GET['tab'] : 'history';
			$wp_redirect = admin_url( 'admin.php?page='.$page );
			if ( $history_id > 0 ) {
				wp_delete_post( $history_id, true );
				$query_args = array( 'iee_msg' => 'history_del', 'tab' => $tab );
        		wp_redirect(  add_query_arg( $query_args, $wp_redirect ) );
				exit;
			}
		}

		if ( isset( $_GET['iee_action'] ) && $_GET['iee_action'] == 'iee_run_import' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'iee_run_import_nonce') ) {
			$import_id = (int)$_GET['import_id'];
			$page = isset($_GET['page'] ) ? $_GET['page'] : 'eventbrite_event';
			$tab = isset($_GET['tab'] ) ? $_GET['tab'] : 'scheduled';
			$wp_redirect = admin_url( 'admin.php?page='.$page );
			if ( $import_id > 0 ) {
				do_action( 'iee_run_scheduled_import', $import_id );
				$query_args = array( 'iee_msg' => 'import_success', 'tab' => $tab );
        		wp_redirect(  add_query_arg( $query_args, $wp_redirect ) );
				exit;
			}
		}

		if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'bulk-iee_scheduled_import') ) {
			$tab = isset($_GET['tab'] ) ? $_GET['tab'] : 'scheduled';
			$wp_redirect = get_site_url() . urldecode( $_REQUEST['_wp_http_referer'] );
        	$delete_ids = $_REQUEST['iee_scheduled_import'];
        	if( !empty( $delete_ids ) ){
        		foreach ($delete_ids as $delete_id ) {
        			wp_delete_post( $delete_id, true );
        		}            		
        	}
        	$query_args = array( 'iee_msg' => 'import_dels', 'tab' => $tab );
        	wp_redirect(  add_query_arg( $query_args, $wp_redirect ) );
			exit;
		}

		if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'bulk-iee_import_histories') ) {
			$tab = isset($_GET['tab'] ) ? $_GET['tab'] : 'history';
			$wp_redirect = get_site_url() . urldecode( $_REQUEST['_wp_http_referer'] );
        	$delete_ids = $_REQUEST['import_history'];
        	if( !empty( $delete_ids ) ){
        		foreach ($delete_ids as $delete_id ) {
        			wp_delete_post( $delete_id, true );
        		}            		
        	}	
        	$query_args = array( 'iee_msg' => 'history_dels', 'tab' => $tab );
        	wp_redirect(  add_query_arg( $query_args, $wp_redirect ) );
			exit;
		}
	}

	/**
	 * Handle Eventbrite import form submit.
	 *
	 * @since    1.0.0
	 */
	public function handle_eventbrite_import_form_submit( $event_data ){
		global $iee_errors, $iee_success_msg, $iee_events;
		$import_events = array();
		$eventbrite_options = iee_get_import_options('eventbrite');
		if( !isset( $eventbrite_options['eventbrite_oauth_token'] ) || $eventbrite_options['eventbrite_oauth_token'] == '' ){
			$iee_errors[] = esc_html__( 'Please insert Eventbrite "Personal OAuth token" in settings.', 'import-eventbrite-events' );
			return;
		}

		$event_data['import_origin'] = 'eventbrite';
		$event_data['import_by'] = 'event_id';
		$event_data['eventbrite_event_id'] = isset( $_POST['iee_eventbrite_id'] ) ? sanitize_text_field( $_POST['iee_eventbrite_id']) : '';
		$event_data['organizer_id'] = '';
		
		if( !is_numeric( $event_data['eventbrite_event_id'] ) ){
			$iee_errors[] = esc_html__( 'Please provide valid Eventbrite event ID.', 'import-eventbrite-events' );
			return;
		}
		$import_events[] = $iee_events->eventbrite->import_event_by_event_id( $event_data );
		if( $import_events && !empty( $import_events ) ){
			$iee_events->common->display_import_success_message( $import_events, $event_data );
		}
	}


	/**
	 * Setup Success messages.
	 *
	 * @since    1.0.0
	 */
	public function setup_success_messages(){
		global $iee_success_msg;
		if( isset( $_GET['iee_msg'] ) && $_GET['iee_msg'] != '' ){
			switch ( $_GET['iee_msg'] ) {
				case 'import_del':
					$iee_success_msg[] = esc_html__( 'Scheduled import deleted successfully.', 'import-eventbrite-events' );
					break;

				case 'import_dels':
					$iee_success_msg[] = esc_html__( 'Scheduled imports are deleted successfully.', 'import-eventbrite-events' );
					break;

				case 'import_success':
					$iee_success_msg[] = esc_html__( 'Scheduled import has been run successfully.', 'import-eventbrite-events' );
					break;

				case 'history_del':
					$iee_success_msg[] = esc_html__( 'Import history deleted successfully.', 'import-eventbrite-events' );
					break;

				case 'history_dels':
					$iee_success_msg[] = esc_html__( 'Import histories are deleted successfully.', 'import-eventbrite-events' );
					break;

				case 'upgrade_finish':
					$iee_success_msg[] = esc_html__( 'Update has been finish successfully.', 'import-eventbrite-events' );
					break;				

				default:
					$iee_success_msg[] = esc_html__( 'Scheduled imports are deleted successfully.', 'import-eventbrite-events' );
					break;
			}
		}
	}
}
