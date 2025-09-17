<?php
/**
 * Class for eventbrite Imports.
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

class Import_Eventbrite_Events_Eventbrite {

	public $oauth_token;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		global $iee_events;
		$options           = iee_get_import_options( 'eventbrite' );
		$this->oauth_token = isset( $options['eventbrite_oauth_token'] ) ? $options['eventbrite_oauth_token'] : '';
	}

	/**
	 * import Eventbrite event by ID.
	 *
	 * @since    1.0.0
	 * @param array $eventdata  import event data.
	 * @return /boolean
	 */
	public function import_event_by_event_id( $event_data = array() ) {
		global $iee_errors, $iee_events;
		$options                = iee_get_import_options( 'eventbrite' );
		$eventbrite_oauth_token = isset( $options['eventbrite_oauth_token'] ) ? $options['eventbrite_oauth_token'] : '';
		$eventbrite_id          = isset( $event_data['eventbrite_event_id'] ) ? $event_data['eventbrite_event_id'] : 0;

		if ( ! $eventbrite_id || $this->oauth_token == '' ) {
			$iee_errors[] = __( 'Please insert Eventbrite "Personal OAuth token".', 'import-eventbrite-events' );
			return;
		}

		$eventbrite_api_url  = 'https://www.eventbriteapi.com/v3/events/' . $eventbrite_id . '/?expand=venue,ticket_availability,organizer,organizer.logo&token=' . $this->oauth_token;
		$eventbrite_response = wp_remote_get( $eventbrite_api_url, array( 'headers' => array( 'Content-Type' => 'application/json' ) ) );

		if ( is_wp_error( $eventbrite_response ) ) {
			$iee_errors[] = __( 'Something went wrong, please try again.', 'import-eventbrite-events' );
			return;
		}

		$eventbrite_event = json_decode( $eventbrite_response['body'], true );
		if ( is_array( $eventbrite_event ) && ! isset( $eventbrite_event['error'] ) ) {

			$series_id = isset( $eventbrite_event['series_id'] ) ? $eventbrite_event['series_id'] : ''; 
			$description = $this->get_eventbrite_event_description( $eventbrite_id, $series_id );
			if(!empty($description)){
				$eventbrite_event['description']['html'] = $description;
			}
			return $this->save_eventbrite_event( $eventbrite_event, $event_data );

		} else {
			if( $eventbrite_event['error'] == 'INVALID_AUTH' ){
				$error_description =  str_replace( 'OAuth token', 'Private token', $eventbrite_event['error_description'] );
				$iee_errors[] = $error_description;
				return;
			}
			$iee_errors[] = __( 'Something went wrong, please try again.', 'import-eventbrite-events' );
			return;
		}
	}

	/**
	 * Get Event description from eventbrite.
	 *
	 * @param $eventbrite_id
	 * @return string description
	 */
	function get_eventbrite_event_description( $eventbrite_id, $series_id = '' ) {
		$description = '';

		if ( ! empty( $series_id ) ) {
			$desc_transient_key = 'iee_series_description_' . $series_id;
			$cached_desc        = get_transient( $desc_transient_key );

			if ( ! empty( $cached_desc ) ) {
				return $cached_desc;
			}
		}

		$description = '';
		$eventbrite_desc_url  = 'https://www.eventbriteapi.com/v3/events/' . $eventbrite_id . '/description/?token=' . $this->oauth_token;
		$eventbrite_response = wp_remote_get( $eventbrite_desc_url, array( 'headers' => array( 'Content-Type' => 'application/json' ) ) );
		if ( !is_wp_error( $eventbrite_response ) ) {
			$event_desc = json_decode( wp_remote_retrieve_body($eventbrite_response) );
			$description = isset( $event_desc->description ) ? $event_desc->description : '';
			
			if ( ! empty( $description ) && ! empty( $series_id ) ) {
				set_transient( 'iee_series_description_' . $series_id, $description, HOUR_IN_SECONDS );
			}
		}
		return $description;
	}

	/**
	 * Save (Create or update) Eventbrite imported to The Event Calendar Events from a Eventbrite.com event.
	 *
	 * @since  1.0.0
	 * @param array $eventbrite_event Event array get from Eventbrite.com.
	 * @param int   $post_id Eventbrite Url id.
	 * @return void
	 */
	public function save_eventbrite_event( $eventbrite_event = array(), $event_args = array() ) {

		global $iee_events;
		if ( ! empty( $eventbrite_event ) && is_array( $eventbrite_event ) && array_key_exists( 'id', $eventbrite_event ) ) {
			$centralize_array = $this->generate_centralize_array( $eventbrite_event );
			return $iee_events->common->import_events_into( $centralize_array, $event_args );
		}
	}


	/**
	 * Format events arguments as per TEC
	 *
	 * @since    1.0.0
	 * @param array $eventbrite_event Eventbrite event.
	 * @return array
	 */
	public function generate_centralize_array( $eventbrite_event ) {
		global $iee_events;

		if ( ! isset( $eventbrite_event['id'] ) ) {
			return false;
		}

		$start_time = $start_time_utc = time();
		$end_time   = $end_time_utc = time();
		$utc_offset = '';

		if ( array_key_exists( 'start', $eventbrite_event ) ) {
			$start_time     = isset( $eventbrite_event['start']['local'] ) ? strtotime( $iee_events->common->convert_datetime_to_db_datetime( $eventbrite_event['start']['local'] ) ) : strtotime( gmdate( 'Y-m-d H:i:s' ) );
			$start_time_utc = isset( $eventbrite_event['start']['utc'] ) ? strtotime( $iee_events->common->convert_datetime_to_db_datetime( $eventbrite_event['start']['utc'] ) ) : '';
			$utc_offset     = $iee_events->common->get_utc_offset( $eventbrite_event['start']['local'] );
		}

		if ( array_key_exists( 'end', $eventbrite_event ) ) {
			$end_time     = isset( $eventbrite_event['end']['local'] ) ? strtotime( $iee_events->common->convert_datetime_to_db_datetime( $eventbrite_event['end']['local'] ) ) : $start_time;
			$end_time_utc = isset( $eventbrite_event['end']['utc'] ) ? strtotime( $iee_events->common->convert_datetime_to_db_datetime( $eventbrite_event['end']['utc'] ) ) : $start_time_utc;

		}
		
		$event_image       = '';
		$iee_options       = get_option( IEE_OPTIONS );
		$small_thumbnail = isset( $iee_options['small_thumbnail'] ) ? $iee_options['small_thumbnail'] : 'no';
		$timezone          = isset( $eventbrite_event['start']['timezone'] ) ? $eventbrite_event['start']['timezone'] : '';
		$event_name        = isset( $eventbrite_event['name']['text'] ) ? sanitize_text_field( $eventbrite_event['name']['text'] ) : '';
		$event_description = isset( $eventbrite_event['description']['html'] ) ? $eventbrite_event['description']['html'] : '';
		$event_url         = array_key_exists( 'url', $eventbrite_event ) ? esc_url( $eventbrite_event['url'] ) : '';
		if( $small_thumbnail == 'yes'){
			$event_image       = array_key_exists( 'logo', $eventbrite_event ) ? urldecode( $eventbrite_event['logo']['url'] ) : '';
		}else{
			$original_url      = isset( $eventbrite_event['logo']['original']['url'] ) ? $eventbrite_event['logo']['original']['url'] : '';
			if( !empty( $original_url ) ){
				$event_image       = array_key_exists( 'logo', $eventbrite_event ) ? urldecode( $original_url ) : '';
			}
		}
		$image             = explode( '?s=', $event_image );
		$image_url         = esc_url( urldecode( str_replace( 'https://img.evbuc.com/', '', $image[0] ) ) );
		$series_id         = isset( $eventbrite_event['series_id'] ) ? $eventbrite_event['series_id'] : '';
		$ticket_price      = isset( $eventbrite_event['ticket_availability']['minimum_ticket_price']['major_value'] ) ? $eventbrite_event['ticket_availability']['minimum_ticket_price']['major_value'] : '';	
		$ticket_currency   = isset( $eventbrite_event['ticket_availability']['minimum_ticket_price']['currency'] ) ? $eventbrite_event['ticket_availability']['minimum_ticket_price']['currency'] : '';	

		$xt_event = array(
			'origin'          => 'eventbrite',
			'ID'              => isset( $eventbrite_event['id'] ) ? $eventbrite_event['id'] : '',
			'name'            => $event_name,
			'description'     => $event_description,
			'starttime_local' => $start_time,
			'endtime_local'   => $end_time,
			'startime_utc'    => $start_time_utc,
			'endtime_utc'     => $end_time_utc,
			'timezone'        => $timezone,
			'utc_offset'      => $utc_offset,
			'event_duration'  => '',
			'is_all_day'      => '',
			'url'             => $event_url,
			'image_url'       => $image_url,
			'series_id'		  => $series_id,
			'ticket_price'    => $ticket_price,
			'ticket_currency' => $ticket_currency,
		);

		if ( array_key_exists( 'organizer', $eventbrite_event ) ) {
			$organizer_details = $eventbrite_event['organizer'];
			$xt_event['organizer'] = $this->get_organizer( $organizer_details );
		}

		if ( array_key_exists( 'venue', $eventbrite_event ) ) {
			$location_details = $eventbrite_event['venue'];
			$online_event     = $eventbrite_event['online_event'] ?? false;
			if( $online_event ){
				$location_details['name'] = 'Online Event';
			}
			$xt_event['location'] = $this->get_location( $location_details );
		}

		$xt_event = apply_filters( 'iee_eventbrite_centralize_array', $xt_event, $eventbrite_event );

		return $xt_event;
	}

	/**
	 * Get organizer args for event.
	 *
	 * @since    1.0.0
	 * @param array $eventbrite_event Eventbrite event.
	 * @return array
	 */
	public function get_organizer( $organizer_details ) {
		if ( array_key_exists( 'id', $organizer_details ) && isset( $organizer_details['name'] ) && ! empty( $organizer_details['name'] ) ) {
			$org_image = isset( $organizer_details['logo']['original']['url'] ) ? urldecode( $organizer_details['logo']['original']['url'] ) : '';
			$image     = explode( '?s=', $org_image );
			$image_url = esc_url( urldecode( str_replace( 'https://img.evbuc.com/', '', $image[0] ) ) );

			$event_organizer = array(
				'ID'          => isset( $organizer_details['id'] ) ? $organizer_details['id'] : '',
				'name'        => isset( $organizer_details['name'] ) ? $organizer_details['name'] : '',
				'description' => isset( $organizer_details['description']['text'] ) ? $organizer_details['description']['text'] : '',
				'email'       => '',
				'phone'       => '',
				'url'         => isset( $organizer_details['url'] ) ? $organizer_details['url'] : '',
				'image_url'   => $image_url,
			);
			return $event_organizer;
		}
		return null;
	}

	/**
	 * Get location args for event
	 *
	 * @since    1.0.0
	 * @param array $eventbrite_event Eventbrite event.
	 * @return array
	 */
	public function get_location( $location_details ) {

		if ( isset( $location_details['name'] ) && ! empty( $location_details['name'] ) ) {
			$event_location = array(
				'ID'           => isset( $location_details['id'] ) ? $location_details['id'] : '',
				'name'         => isset( $location_details['name'] ) ? $location_details['name'] : '',
				'description'  => '',
				'address_1'    => isset( $location_details['address']['address_1'] ) ? $location_details['address']['address_1'] : '',
				'address_2'    => isset( $location_details['address']['address_2'] ) ? $location_details['address']['address_2'] : '',
				'city'         => isset( $location_details['address']['city'] ) ? $location_details['address']['city'] : '',
				'state'        => isset( $location_details['address']['region'] ) ? $location_details['address']['region'] : '',
				'country'      => isset( $location_details['address']['country'] ) ? $location_details['address']['country'] : '',
				'zip'          => isset( $location_details['address']['postal_code'] ) ? $location_details['address']['postal_code'] : '',
				'lat'          => isset( $location_details['address']['latitude'] ) ? $location_details['address']['latitude'] : '',
				'long'         => isset( $location_details['address']['longitude'] ) ? $location_details['address']['longitude'] : '',
				'full_address' => isset( $location_details['address']['localized_address_display'] ) ? $location_details['address']['localized_address_display'] : '',
				'url'          => '',
				'image_url'    => '',
			);
			return $event_location;
		}
		return null;
	}

	/**
	 * Get organizer Name based on Organiser ID.
	 *
	 * @since    1.0.0
	 * @param array $eventbrite_event Eventbrite event.
	 * @return array
	 */
	public function get_organizer_name_by_id( $organizer_id ) {

		if ( ! $organizer_id || $organizer_id == '' ) {
			return;
		}

		$get_oraganizer = wp_remote_get(
			'https://www.eventbriteapi.com/v3/organizers/' . $organizer_id . '/?token=' . $this->oauth_token,
			array(
				'headers' => array(
					'Content-Type' => 'application/json'
				),
				'timeout' => 20,
			)
		);

		if ( ! is_wp_error( $get_oraganizer ) ) {
			$oraganizer = json_decode( $get_oraganizer['body'], true );
			if ( is_array( $oraganizer ) && ! isset( $oraganizer['errors'] ) ) {
				if ( ! empty( $oraganizer ) && array_key_exists( 'id', $oraganizer ) ) {

					$oraganizer_name = isset( $oraganizer['name'] ) ? $oraganizer['name'] : '';
					return $oraganizer_name;
				}
			}
		}
		return '';
	}
}
