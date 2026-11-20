<?php
/**
 * Ajax functions class for WP Event aggregator.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    Import_Eventbrite_Events
 * @subpackage Import_Eventbrite_Events/includes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Import_Eventbrite_Events_Ajax {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_iee_load_paged_events',  array( $this, 'iee_load_paged_events_callback' ) );
        add_action( 'wp_ajax_nopriv_iee_load_paged_events',  array( $this, 'iee_load_paged_events_callback' ) );
		add_action( 'wp_ajax_iee_check_live_sold_out',  array( $this, 'iee_check_live_sold_out_callback' ) );
        add_action( 'wp_ajax_nopriv_iee_check_live_sold_out',  array( $this, 'iee_check_live_sold_out_callback' ) );
	}

	public function iee_check_live_sold_out_callback() {
		if ( empty( $_POST['event_ids'] ) || ! is_array( $_POST['event_ids'] ) ) {
			wp_send_json_error( 'No event IDs provided' );
		}

		$event_ids = array_slice( array_map( 'intval', $_POST['event_ids'] ), 0, 50 );
		$sold_out_ids = array();

		if ( function_exists( 'iee_live_check_sold_out_status' ) ) {
			foreach ( $event_ids as $post_id ) {
				iee_live_check_sold_out_status( $post_id );
				$is_sold_out = get_post_meta( $post_id, 'iee_is_sold_out', true );
				if ( 'yes' === $is_sold_out ) {
					$sold_out_ids[] = $post_id;
				}
			}
		}

		wp_send_json_success( array( 'sold_out_ids' => $sold_out_ids ) );
	}

	public function iee_load_paged_events_callback() {
		if ( empty( $_POST['atts'] ) || empty( $_POST['page'] ) ) {
			wp_send_json_error( 'Missing params' );
		}

		$atts          = json_decode( stripslashes( $_POST['atts'] ), true );
		$atts['paged'] = intval( $_POST['page'] );
		$html          = do_shortcode( '[eventbrite_events ' . http_build_query( $atts, '', ' ' ) . ']' );

		wp_send_json_success( $html );
	}
}