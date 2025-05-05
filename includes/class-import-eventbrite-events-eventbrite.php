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

		$eventbrite_api_url  = 'https://www.eventbriteapi.com/v3/events/' . $eventbrite_id . '/?token=' . $this->oauth_token;
		$eventbrite_response = wp_remote_get( $eventbrite_api_url, array( 'headers' => array( 'Content-Type' => 'application/json' ) ) );

		if ( is_wp_error( $eventbrite_response ) ) {
			$iee_errors[] = __( 'Something went wrong, please try again.', 'import-eventbrite-events' );
			return;
		}

		$eventbrite_event = json_decode( $eventbrite_response['body'], true );
		if ( is_array( $eventbrite_event ) && ! isset( $eventbrite_event['error'] ) ) {
			$description = $this->get_eventbrite_event_description($eventbrite_id);
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
	function get_eventbrite_event_description($eventbrite_id){
		$description = '';
		$eventbrite_desc_url  = 'https://www.eventbriteapi.com/v3/events/' . $eventbrite_id . '/description/?token=' . $this->oauth_token;
		$eventbrite_response = wp_remote_get( $eventbrite_desc_url, array( 'headers' => array( 'Content-Type' => 'application/json' ) ) );
		if ( !is_wp_error( $eventbrite_response ) ) {
			$event_desc = json_decode( wp_remote_retrieve_body($eventbrite_response) );
			$description = isset( $event_desc->description ) ? $event_desc->description : '';
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

		$iee_options       = get_option( IEE_OPTIONS );
		$small_thumbnail = isset( $iee_options['small_thumbnail'] ) ? $iee_options['small_thumbnail'] : 'no';
		$timezone          = isset( $eventbrite_event['start']['timezone'] ) ? $eventbrite_event['start']['timezone'] : '';
		$event_name        = isset( $eventbrite_event['name']['text'] ) ? sanitize_text_field( $eventbrite_event['name']['text'] ) : '';
		$event_description = isset( $eventbrite_event['description']['html'] ) ? $eventbrite_event['description']['html'] : '';
		$event_url         = array_key_exists( 'url', $eventbrite_event ) ? esc_url( $eventbrite_event['url'] ) : '';
		if( $small_thumbnail == 'yes'){
			$event_image       = array_key_exists( 'logo', $eventbrite_event ) ? urldecode( $eventbrite_event['logo']['url'] ) : '';
		}else{
			$event_image       = array_key_exists( 'logo', $eventbrite_event ) ? urldecode( $eventbrite_event['logo']['original']['url'] ) : '';
		}
		$image             = explode( '?s=', $event_image );
		$image_url         = esc_url( urldecode( str_replace( 'https://img.evbuc.com/', '', $image[0] ) ) );
		$series_id         = isset( $eventbrite_event['series_id'] ) ? $eventbrite_event['series_id'] : '';

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
		);

		if ( array_key_exists( 'organizer_id', $eventbrite_event ) ) {
			$xt_event['organizer'] = $this->get_organizer( $eventbrite_event );
		}

		if ( array_key_exists( 'venue_id', $eventbrite_event ) ) {
			$xt_event['location'] = $this->get_location( $eventbrite_event );
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
	public function get_organizer( $eventbrite_event ) {
		if ( ! array_key_exists( 'organizer_id', $eventbrite_event ) ) {
			return null;
		}
		$event_organizer = $eventbrite_event['organizer_id'];
		$get_oraganizer  = wp_remote_get(
			'https://www.eventbriteapi.com/v3/organizers/' . $event_organizer . '/?token=' . $this->oauth_token,
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

					$org_image = isset( $oraganizer['logo']['original']['url'] ) ? urldecode( $oraganizer['logo']['original']['url'] ) : '';
					$image     = explode( '?s=', $org_image );
					$image_url = esc_url( urldecode( str_replace( 'https://img.evbuc.com/', '', $image[0] ) ) );

					$event_organizer = array(
						'ID'          => isset( $oraganizer['id'] ) ? $oraganizer['id'] : '',
						'name'        => isset( $oraganizer['name'] ) ? $oraganizer['name'] : '',
						'description' => isset( $oraganizer['description']['text'] ) ? $oraganizer['description']['text'] : '',
						'email'       => '',
						'phone'       => '',
						'url'         => isset( $oraganizer['url'] ) ? $oraganizer['url'] : '',
						'image_url'   => $image_url,
					);
					return $event_organizer;
				}
			}
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
	public function get_location( $eventbrite_event ) {
		if ( ! array_key_exists( 'venue_id', $eventbrite_event ) ) {
			return null;
		}
		$event_venue_id = $eventbrite_event['venue_id'];
		$is_online      = $eventbrite_event['online_event'];
		if( $is_online === true ){
			$event_location = array(
				'name'         => 'Online Event',
			);
			return $event_location;
		}
		$get_venue      = wp_remote_get( 'https://www.eventbriteapi.com/v3/venues/' . $event_venue_id . '/?token=' . $this->oauth_token, array( 'headers' => array( 'Content-Type' => 'application/json' ) ) );

		if ( ! is_wp_error( $get_venue ) ) {
			$venue = json_decode( $get_venue['body'], true );
			if ( is_array( $venue ) && ! isset( $venue['errors'] ) ) {
				if ( ! empty( $venue ) && array_key_exists( 'id', $venue ) ) {

					$event_location = array(
						'ID'           => isset( $venue['id'] ) ? $venue['id'] : '',
						'name'         => isset( $venue['name'] ) ? $venue['name'] : '',
						'description'  => '',
						'address_1'    => isset( $venue['address']['address_1'] ) ? $venue['address']['address_1'] : '',
						'address_2'    => isset( $venue['address']['address_2'] ) ? $venue['address']['address_2'] : '',
						'city'         => isset( $venue['address']['city'] ) ? $venue['address']['city'] : '',
						'state'        => isset( $venue['address']['region'] ) ? $venue['address']['region'] : '',
						'country'      => isset( $venue['address']['country'] ) ? $venue['address']['country'] : '',
						'zip'          => isset( $venue['address']['postal_code'] ) ? $venue['address']['postal_code'] : '',
						'lat'          => isset( $venue['address']['latitude'] ) ? $venue['address']['latitude'] : '',
						'long'         => isset( $venue['address']['longitude'] ) ? $venue['address']['longitude'] : '',
						'full_address' => isset( $venue['address']['localized_address_display'] ) ? $venue['address']['localized_address_display'] : $venue['address']['address_1'],
						'url'          => '',
						'image_url'    => '',
					);
					return $event_location;
				}
			}
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
