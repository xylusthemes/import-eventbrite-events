<?php
/**
 * The class responsible for import events for eventbrite.com
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    XT_Eventbrite_Import
 * @subpackage XT_Eventbrite_Import/includes
 */
class XT_Eventbrite_Import_Em_Importer {

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
	 * Run eventbrite event importer.
	 *
	 * @since    1.0.0
	 * @param int $post_id Options.
	 * @return null/void
	 */
	public function xtei_run_importer( $eventbrite_id ) {

		if ( ! $eventbrite_id || XTEI_OAUTH_TOKEN == '' ) {
			return array( 'status'=> 0, 'message'=> 'Please insert Eventbrite "Personal OAuth token".' );
		}

		$eventbrite_api_url = 'https://www.eventbriteapi.com/v3/events/' . $eventbrite_id . '/?token=' .  XTEI_OAUTH_TOKEN;
	    $eventbrite_response = wp_remote_get( $eventbrite_api_url , array( 'headers' => array( 'Content-Type' => 'application/json' ) ) );

		if ( ! is_wp_error( $eventbrite_response ) ) {
			$eventbrite_event = json_decode( $eventbrite_response['body'], true );
			if ( is_array( $eventbrite_event ) && ! isset( $eventbrite_event['error'] ) ) {
				$this->xtei_save_event( $eventbrite_event );
			}else{
				return array( 'status'=> 0, 'message'=> 'Something went wrong, please try again.' );
			}
		}else{
			return array( 'status'=> 0, 'message'=> 'Something went wrong, please try again.' );
		}
	}

	/**
	 * Save (Create or update) Eventbrite imported to The Event Calendar Events from a Eventbrite.com event.
	 *
	 * @since  1.0.0
	 * @param array  $eventbrite_event Event array get from Eventbrite.com.
	 * @param string $eventbrite_group_id Eventbrite group slug.
	 * @param int    $post_id Eventbrite Url id.
	 * @return void
	 */
	public function xtei_save_event( $eventbrite_event = array() ) {

		if ( ! empty( $eventbrite_event ) && is_array( $eventbrite_event ) && array_key_exists( 'id', $eventbrite_event ) ) {
			$xtei_options = get_option( XTEI_OPTIONS, array() );
			$is_exitsing_event = $this->xtei_get_event_by_eventbrite_event_id( $eventbrite_event['id'] );

			if ( $is_exitsing_event ) {
				// Check weather update existing events or not.
				$update_events = isset( $xtei_options['update_events'] ) ? $xtei_options['update_events'] : 'no';
				if ( 'yes' != $update_events ) {
					return;
				}
			}
			global $wpdb;
			$default_status = isset( $xtei_options['default_status'] ) ? $xtei_options['default_status'] : 'pending';

			$eventdata = array(
				'post_title'  => array_key_exists( 'name', $eventbrite_event ) ? sanitize_text_field( $eventbrite_event['name']['text'] ) : '',
				'post_content' => array_key_exists( 'description', $eventbrite_event ) ? $eventbrite_event['description']['html'] : '',
				'post_type'   => XTEI_EM_POSTTYPE,
				'post_status' => $default_status,
			);
			if ( $is_exitsing_event ) {
				$eventdata['ID'] = $is_exitsing_event;
			}

			$event_id = wp_insert_post( $eventdata, true );

			if ( ! is_wp_error( $event_id ) ) {
				$event = get_post( $event_id );
				if ( empty( $event ) ) { return '';}

				// Assign category.
				$xtei_cats = array();
				$xtei_cats = isset( $_POST['xtei_event_cats'] ) ? (array) $_POST['xtei_event_cats'] : array();
				if ( ! empty( $xtei_cats ) ) {
					foreach ( $xtei_cats as $xtei_catk => $xtei_catv ) {
						$xtei_cats[ $xtei_catk ] = (int) $xtei_catv;
					}
				}
				if ( ! empty( $xtei_cats ) ) {
					wp_set_object_terms( $event_id, $xtei_cats, XTEI_EM_TAXONOMY );
				}

				// Setup Featred Image.
				$event_featured_image  = array_key_exists( 'logo', $eventbrite_event ) ? urldecode( $eventbrite_event['logo']['original']['url'] ) : '';
				if( $event_featured_image != '' ){
					$this->xt_setup_featured_image_to_event( $event_id, $event_featured_image );
				}

				if ( $is_exitsing_event ) {
					$location_id = $this->xt_get_location_args( $eventbrite_event, $event_id );
				}else{
					$location_id = $this->xt_get_location_args( $eventbrite_event, false );
				}


				// Get event data.
				if ( array_key_exists( 'start', $eventbrite_event ) ) {
					$start = str_replace( 'T',' ', str_replace( 'Z', ' ', $eventbrite_event['start']['local'] ) );
					$start_utc = str_replace( 'T',' ', str_replace( 'Z', ' ', $eventbrite_event['start']['utc'] ) );
					$event_start_time = strtotime( $start );
					$event_start_time_utc = strtotime( $start_utc );
				} else {
					$event_start_time = time();
					$event_start_time_utc = time();
				}
				if ( array_key_exists( 'end', $eventbrite_event ) ) {
					$end = str_replace( 'T',' ', str_replace( 'Z', ' ', $eventbrite_event['end']['local'] ) );
					$end_utc = str_replace( 'T',' ', str_replace( 'Z', ' ', $eventbrite_event['end']['utc'] ) );
					$event_end_time = strtotime( $end );
					$event_end_time_utc = strtotime( $end_utc );
				} else {
					$event_end_time = time();
					$event_end_time_utc = time();
				}

				$event_timezone = isset( $eventbrite_event['start']['timezone'] ) ? $eventbrite_event['start']['timezone'] : '';

				$event_status = null;
				if ( $event->post_status == 'publish' ) { $event_status = 1;}
				if ( $event->post_status == 'pending' ) { $event_status = 0;}
				// Save Meta.
				//update_post_meta( $event_id, '_event_id', 0 );
				update_post_meta( $event_id, '_event_start_time', date( 'H:i:s', $event_start_time ) );
				update_post_meta( $event_id, '_event_end_time', date( 'H:i:s', $event_end_time ) );
				update_post_meta( $event_id, '_event_all_day', 0 );
				update_post_meta( $event_id, '_event_start_date', date( 'Y-m-d', $event_start_time ) );
				update_post_meta( $event_id, '_event_end_date', date( 'Y-m-d', $event_end_time ) );
				update_post_meta( $event_id, '_location_id', $location_id );
				update_post_meta( $event_id, '_event_status', $event_status );
				update_post_meta( $event_id, '_event_private', 0 );
				update_post_meta( $event_id, '_start_ts', str_pad( $event_start_time, 10, 0, STR_PAD_LEFT));
				update_post_meta( $event_id, '_end_ts', str_pad( $event_end_time, 10, 0, STR_PAD_LEFT));
				update_post_meta( $event_id, '_xt_eventbrite_event_id', $eventbrite_event['id'] );
				update_post_meta( $event_id, '_xt_eventbrite_event_link', esc_url( $eventbrite_event['url'] ) );
				update_post_meta( $event_id, '_xt_eventbrite_response_raw_data', wp_json_encode( $eventbrite_event ) );

				// Custom table Details
				$event_array = array(
					'post_id' => $event_id,
					'event_slug' => $event->post_name,
					'event_owner' => $event->post_author,
					'event_name' => $event->post_title,
					'event_start_time' => date( 'H:i:s', $event_start_time ),
					'event_end_time' => date( 'H:i:s', $event_end_time ),
					'event_all_day' => 0,
					'event_start_date' => date( 'Y-m-d', $event_start_time ),
					'event_end_date' => date( 'Y-m-d', $event_end_time ),
					'post_content' => $event->post_content,
					'location_id' => $location_id,
					'event_status' => $event_status,
					'event_date_created' => $event->post_date,
				);

				$event_table = ( defined( 'EM_EVENTS_TABLE' ) ? EM_EVENTS_TABLE : $wpdb->prefix . 'em_events' );
				if ( $is_exitsing_event ) {
					$eve_id = get_post_meta( $event_id, '_event_id', true );
					$where = array( 'event_id' => $eve_id );
					$wpdb->update( $event_table , $event_array, $where );
				}else{
					if ( $wpdb->insert( $event_table , $event_array ) ) {
						update_post_meta( $event_id, '_event_id', $wpdb->insert_id );
					}
				}
			}else{
				return array( 'status'=> 0, 'message'=> 'Something went wrong, please try again.' );
			}
		}else{
			return array( 'status'=> 0, 'message'=> 'Something went wrong, please try again.' );
		}
	}

	/**
	 * Set Location for event
	 *
	 * @since    1.0.0
	 * @param array $meetup_event Meetup event.
	 * @return array
	 */
	public function xt_get_location_args( $eventbrite_event, $event_id = false ) {
		global $wpdb;

		if ( ! array_key_exists( 'venue_id', $eventbrite_event ) ) {
			return null;
		}
		$event_venue_id = $eventbrite_event['venue_id'];
		$existing_venue = get_posts( array(
			'posts_per_page' => 1,
			'post_type' => XTEI_LOCATION_POSTTYPE,
			'meta_key' => '_xt_eventbrite_event_location_id',
			'meta_value' => $event_venue_id,
			'suppress_filters' => false,
		) );


		if ( is_array( $existing_venue ) && ! empty( $existing_venue ) && ! $event_id ) {
			return get_post_meta( $existing_venue[0]->ID, '_location_id', true );
		}

		$get_venue = wp_remote_get( 'https://www.eventbriteapi.com/v3/venues/' . $event_venue_id .'/?token=' . XTEI_OAUTH_TOKEN, array( 'headers' => array( 'Content-Type' => 'application/json' ) ) );

		if ( ! is_wp_error( $get_venue ) ) {
			$venue = json_decode( $get_venue['body'], true );
			if ( is_array( $venue ) && ! isset( $venue['errors'] ) ) {
				if ( ! empty( $venue ) && array_key_exists( 'id', $venue ) ) {

					$locationdata = array(
						'post_title'  => ( $venue['name'] ) ? $venue['name'] : esc_html__( 'Unnamed Location', 'xt-eventbrite-import' ),
						'post_content' => '',
						'post_type'   => XTEI_LOCATION_POSTTYPE,
						'post_status' => 'publish',
					);
					if ( is_array( $existing_venue ) && ! empty( $existing_venue ) ) {
						$locationdata['ID'] = $existing_venue[0]->ID;
					}
					$location_id = wp_insert_post( $locationdata, true );

					if ( ! is_wp_error( $location_id ) ) {

						$blog_id = 0;
						if ( is_multisite() ) {
							$blog_id = get_current_blog_id();
						}
						$location = get_post( $location_id );
						if ( empty( $location ) ) { return null;}
						// Location information.
						$address = isset( $venue['address']['localized_address_display'] ) ? $venue['address']['localized_address_display'] : $venue['address']['address_1'];
						$city = isset( $venue['address']['city'] ) ? $venue['address']['city'] : '';

						$state = isset( $venue['address']['region'] ) ? $venue['address']['region'] : '';
						$country = isset( $venue['address']['country'] ) ? strtoupper( $venue['address']['country'] ) : '';
						$zip = isset( $venue['address']['postal_code'] ) ? $venue['address']['postal_code'] : '';
						$lat = isset( $venue['latitude'] ) ? $venue['latitude'] : '';
						$lon = isset( $venue['longitude'] ) ? $venue['longitude'] : '';

						// Save metas.
						//update_post_meta( $location_id, '_location_id', 0 );
						update_post_meta( $location_id, '_blog_id', $blog_id );
						update_post_meta( $location_id, '_location_address', $address );
						update_post_meta( $location_id, '_location_town', $city );
						update_post_meta( $location_id, '_location_state', $state );
						update_post_meta( $location_id, '_location_postcode', $zip );
						update_post_meta( $location_id, '_location_region','' );
						update_post_meta( $location_id, '_location_country', $country );
						update_post_meta( $location_id, '_location_latitude', $lat );
						update_post_meta( $location_id, '_location_longitude', $lon );
						update_post_meta( $location_id, '_location_status', 1 );
						update_post_meta( $location_id, '_xt_eventbrite_event_location_id', (int) $venue['id'] );

						$location_array = array(
							'post_id' => $location_id,
							'blog_id' => $blog_id,
							'location_slug' => $location->post_name,
							'location_name' => $location->post_title,
							'location_owner' => $location->post_author,
							'location_address' => $address,
							'location_town' => $city,
							'location_state' => $state,
							'location_postcode' => $zip,
							'location_region' => '',
							'location_country' => $country,
							'location_latitude' => $lat,
							'location_longitude' => $lon,
							'post_content' => $location->post_content,
							'location_status' => 1,
							'location_private' => 0,
						);

						if( defined( 'EM_LOCATIONS_TABLE' ) ){
							$event_location_table = EM_LOCATIONS_TABLE;
						}else{
							$event_location_table = $wpdb->prefix . 'em_locations';
						}


						if( $event_id && is_numeric( $event_id ) ){
							$loc_id = get_post_meta( $event_id, '_location_id', true );
							$where = array( 'location_id' => $loc_id );
							$wpdb->update( $event_location_table , $location_array , $where );
							return $loc_id;
						}else{
							if ( $wpdb->insert( $event_location_table , $location_array ) ) {
								$insert_loc_id = $wpdb->insert_id;
								update_post_meta( $location_id, '_location_id', $insert_loc_id );
								return $insert_loc_id;
							}
						}
					}
					return null;
				}
			}
		}
	}

	/**
	 * Check for existing event.
	 *
	 * @since    1.0.0
	 * @param int $eventbrite_event_id Eventbrite event id.
	 * @return /boolean
	 */
	public function xtei_get_event_by_eventbrite_event_id( $eventbrite_event_id ) {
		$event_args = array(
			'post_type' => XTEI_EM_POSTTYPE,
			'post_status' => array( 'pending', 'draft', 'publish' ),
			'posts_per_page' => -1,
			'meta_key'   => '_xt_eventbrite_event_id',
			'meta_value' => $eventbrite_event_id,
		);

		$events = new WP_Query( $event_args );
		if ( $events->have_posts() ) {
			while ( $events->have_posts() ) {
				$events->the_post();
				return get_the_ID();
			}
		}
		wp_reset_postdata();
		return false;
	}

	/**
	 * Setup Featured image to events
	 *
	 * @since    1.0.0
	 * @param int $event_id event id.
	 * @param int $image_url Image URL
	 * @return void
	 */
	public function xt_setup_featured_image_to_event( $event_id, $image_url = '' ) {
		if ( $image_url == '' ) {
			return;
		}
		$event = get_post( $event_id );
		if( Empty ( $event ) ){
			return;
		}
		$image = explode( '?s=', $image_url );
		$image_url = str_replace('https://img.evbuc.com/', '', $image[0] );
		// Add Featured Image to Post
		$image_name       = $event->post_name . '_image.png';
		$upload_dir       = wp_upload_dir(); // Set upload folder
		$image_data       = file_get_contents( $image_url ); // Get image data
		$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
		$filename         = basename( $unique_file_name ); // Create image file name

		// Check folder permission and define file location
		if( wp_mkdir_p( $upload_dir['path'] ) ) {
		    $file = $upload_dir['path'] . '/' . $filename;
		} else {
		    $file = $upload_dir['basedir'] . '/' . $filename;
		}

		// Create the image  file on the server
		file_put_contents( $file, $image_data );

		// Check image file type
		$wp_filetype = wp_check_filetype( $filename, null );

		// Set attachment data
		$attachment = array(
		    'post_mime_type' => $wp_filetype['type'],
		    'post_title'     => sanitize_file_name( $filename ),
		    'post_content'   => '',
		    'post_status'    => 'inherit'
		);

		// Create the attachment
		$attach_id = wp_insert_attachment( $attachment, $file, $event_id );

		// Include image.php
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		// Define attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

		// Assign metadata to attachment
		wp_update_attachment_metadata( $attach_id, $attach_data );

		// And finally assign featured image to post
		set_post_thumbnail( $event_id, $attach_id );

	}
}
