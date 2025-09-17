<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Load Action Scheduler if not loaded
if ( ! class_exists( 'ActionScheduler' ) ) {
	require_once IEE_PLUGIN_DIR . 'includes/iee-action-scheduler/action-scheduler/action-scheduler.php';
}

// Load custom scheduler
require_once IEE_PLUGIN_DIR . 'includes/iee-action-scheduler/class-iee-event-image-scheduler.php';

// Register hook
add_action( 'iee_process_image_download', array( 'IEE_Event_Image_Scheduler', 'process_image_download' ), 10, 2 );
