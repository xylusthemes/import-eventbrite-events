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