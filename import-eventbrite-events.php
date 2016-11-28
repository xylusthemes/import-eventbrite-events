<?php
/**
 * Plugin Name:       Import Eventbrite Events
 * Plugin URI:        https://xylusthemes.com/plugins/import-eventbrite-events/
 * Description:       Import Eventbrite Events allows you to import Eventbrite ( eventbrite.com ) events into The Events Calendar or Events Manager.
 * Version:           1.0.1
 * Author:            xylus
 * Author URI:        http://xylusthemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       xt-eventbrite-import
 * Domain Path:       /languages
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 * @package    XT_Eventbrite_Import
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Global variables.
 */
define( 'XTEI_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'XTEI_ADMIN_PATH', plugin_dir_path( __FILE__ ) . 'admin/' );
define( 'XTEI_INCLUDES_PATH', plugin_dir_path( __FILE__ ) . 'includes/' );
define( 'XTEI_OPTIONS', 'xtei_eventbrite_options' );
define( 'XTEI_AUTO_OPTIONS', 'xtei_auto_import_options' );
define( 'XTEI_PLUGIN_BUY_NOW_URL', 'https://xylusthemes.com/plugins/import-eventbrite-events/' );

define( 'XTEI_TEC_TAXONOMY', 'tribe_events_cat' );
if ( class_exists( 'Tribe__Events__Main' ) ) {
	define( 'XTEI_TEC_POSTTYPE', Tribe__Events__Main::POSTTYPE );
}else{
	define( 'XTEI_TEC_POSTTYPE', 'tribe_events' );
}

if ( class_exists( 'Tribe__Events__Organizer' ) ) {
	define( 'XTEI_TEC_ORGANIZER_POSTTYPE', Tribe__Events__Organizer::POSTTYPE );
}else{
	define( 'XTEI_TEC_ORGANIZER_POSTTYPE', 'tribe_organizer' );
}

if ( class_exists( 'Tribe__Events__Venue' ) ) {
	define( 'XTEI_TEC_VENUE_POSTTYPE', Tribe__Events__Venue::POSTTYPE );
}else{
	define( 'XTEI_TEC_VENUE_POSTTYPE', 'tribe_venue' );
}

if ( defined( 'EM_POST_TYPE_EVENT' ) ) {
	define( 'XTEI_EM_POSTTYPE', EM_POST_TYPE_EVENT );
} else {
	define( 'XTEI_EM_POSTTYPE', 'event' );
}
if ( defined( 'EM_TAXONOMY_CATEGORY' ) ) {
	define( 'XTEI_EM_TAXONOMY',EM_TAXONOMY_CATEGORY );
} else {
	define( 'XTEI_EM_TAXONOMY','event-categories' );
}
if ( defined( 'EM_POST_TYPE_LOCATION' ) ) {
	define( 'XTEI_LOCATION_POSTTYPE',EM_POST_TYPE_LOCATION );
} else {
	define( 'XTEI_LOCATION_POSTTYPE','location' );
}

$xtei_options = get_option( XTEI_OPTIONS, array() );
$xtei_oauth_token = isset( $xtei_options['eventbrite_oauth_token'] ) ? $xtei_options['eventbrite_oauth_token'] : '';
define( 'XTEI_OAUTH_TOKEN', $xtei_oauth_token );
/**
 * Runs during plugin activation.
 */
function activate_xt_eventbrite_import() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-xt-eventbrite-import-activator.php';
	XT_Eventbrite_Import_Activator::activate();
}

/**
 * Runs during plugin deactivation.
 */
function deactivate_xt_eventbrite_import() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-xt-eventbrite-import-deactivator.php';
	XT_Eventbrite_Import_Deactivator::deactivate();
}

/**
* Register Plugin activation and deactivation hooks
*/
register_activation_hook( __FILE__, 'activate_xt_eventbrite_import' );
register_deactivation_hook( __FILE__, 'deactivate_xt_eventbrite_import' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-xt-eventbrite-import.php';


/**
 * is plugin active.
 *
 * @since    1.0.0
 */
function xt_is_plugin_active( $plugin = '' ) {

	if( $plugin != ''){

	}
	return false;
}

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_xt_eventbrite_import() {

	$plugin = new XT_Eventbrite_Import();
	$plugin->run();
	$plugin->xtei_check_requirements( plugin_basename( __FILE__ ) );

}
run_xt_eventbrite_import();

