<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class IEE_Event_Image_Scheduler {

	public static function schedule_image_download( $event_id, $image_url, $event_args ) {
		if ( ! empty( $event_id ) && ! empty( $image_url ) ) {
			$ac_run_time = ( !empty($event_args['import_type']) && $event_args['import_type'] === 'onetime' ) ? 30 : 60;
			as_schedule_single_action( time() + $ac_run_time, 'iee_process_image_download', array( $event_id, $image_url ), 'iee_image_group' );
		}
	}

	public static function process_image_download( $event_id, $image_url ) {
        global $iee_events;
		if ( empty( $event_id ) || empty( $image_url ) ) return;
        
		if ( method_exists( $iee_events->common, 'setup_featured_image_to_event' ) ) {
			$iee_events->common->setup_featured_image_to_event( $event_id, $image_url );
		}
	}
}
