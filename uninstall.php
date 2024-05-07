<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       http://xylusthemes.com
 * @since      1.0.0
 *
 * @package    Import_Eventbrite_Events
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$iee_options    = get_option( 'xtei_eventbrite_options' );
$delete_ieedata = isset( $iee_options['delete_ieedata'] ) ? $iee_options['delete_ieedata'] : 'no';
if ( $delete_ieedata == 'yes' ) {

	// Remove options
	delete_option( 'xtei_eventbrite_options' );

	// Remove schduled Imports
	$scheduled_import_args = array(
		'post_type'      => 'iee_scheduled_import',
		'posts_per_page' => -1,
	);
	$scheduled_imports     = get_posts( $scheduled_import_args );
	if ( ! empty( $scheduled_imports ) ) {
		foreach ( $scheduled_imports as $import ) {
			if ( $import->ID != '' ) {
				wp_delete_post( $import->ID, true );
			}
		}
	}

	// Remove Import history
	$import_history_args = array(
		'post_type'      => 'iee_import_history',
		'posts_per_page' => -1,
	);
	$import_histories    = get_posts( $import_history_args );
	if ( ! empty( $import_histories ) ) {
		foreach ( $import_histories as $import_history ) {
			if ( $import_history->ID != '' ) {
				wp_delete_post( $import_history->ID, true );
			}
		}
	}
}
