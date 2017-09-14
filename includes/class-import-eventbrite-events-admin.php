<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package     Import_Eventbrite_Events
 * @subpackage  Import_Eventbrite_Events/admin
 * @copyright   Copyright (c) 2016, Dharmesh Patel
 * @since       1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * @package     Import_Eventbrite_Events
 * @subpackage  Import_Eventbrite_Events/admin
 * @author     Dharmesh Patel <dspatel44@gmail.com>
 */
class Import_Eventbrite_Events_Admin {


	public $adminpage_url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->adminpage_url = admin_url('admin.php?page=eventbrite_event' );

		add_action( 'init', array( $this, 'register_scheduled_import_cpt' ) );
		add_action( 'init', array( $this, 'register_history_cpt' ) );
		add_action( 'admin_init', array( $this, 'database_upgrade_notice' ) );
		add_action( 'admin_init', array( $this, 'maybe_proceed_database_upgrade' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_pages') );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts') );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles') );
		add_action( 'admin_notices', array( $this, 'display_notices') );
		add_filter( 'admin_footer_text', array( $this, 'add_event_aggregator_credit' ) );
	}

	/**
	 * Create the Admin menu and submenu and assign their links to global varibles.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function add_menu_pages(){

		add_menu_page( __( 'Import Eventbrite Events', 'import-eventbrite-events' ), __( 'Eventbrite Import', 'import-eventbrite-events' ), 'manage_options', 'eventbrite_event', array( $this, 'admin_page' ), 'dashicons-calendar-alt', '30' );
	}

	/**
	 * Load Admin Scripts
	 *
	 * Enqueues the required admin scripts.
	 *
	 * @since 1.0
	 * @param string $hook Page hook
	 * @return void
	 */
	function enqueue_admin_scripts( $hook ) {

		$js_dir  = IEE_PLUGIN_URL . 'assets/js/';
		wp_register_script( 'import-eventbrite-events', $js_dir . 'import-eventbrite-events-admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), IEE_VERSION );
		wp_enqueue_script( 'import-eventbrite-events' );

	}

	/**
	 * Load Admin Styles.
	 *
	 * Enqueues the required admin styles.
	 *
	 * @since 1.0
	 * @param string $hook Page hook
	 * @return void
	 */
	function enqueue_admin_styles( $hook ) {

	  	$css_dir = IEE_PLUGIN_URL . 'assets/css/';
	 	wp_enqueue_style('jquery-ui', $css_dir . 'jquery-ui.css', false, "1.12.0" );
	 	wp_enqueue_style('import-eventbrite-events', $css_dir . 'import-eventbrite-events-admin.css', false, "" );
	}

	/**
	 * Load Admin page.
	 *
	 * @since 1.0
	 * @return void
	 */
	function admin_page() {
		
		?>
		<div class="wrap">
		    <h2><?php esc_html_e( 'Import Eventbrite Events', 'import-eventbrite-events' ); ?></h2>
		    <?php
		    // Set Default Tab to Import.
		    $tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'eventbrite';
		    $ntab = isset( $_GET[ 'ntab' ] ) ? $_GET[ 'ntab' ] : 'import';
		    ?>
		    <div id="poststuff">
		        <div id="post-body" class="metabox-holder columns-2">

		            <div id="postbox-container-1" class="postbox-container">
		            	<?php require_once IEE_PLUGIN_DIR . '/templates/admin-sidebar.php'; ?>
		            </div>
		            <div id="postbox-container-2" class="postbox-container">

		                <h1 class="nav-tab-wrapper">

		                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'eventbrite', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( $tab == 'eventbrite' ) { echo 'nav-tab-active'; } ?>">
		                        <?php esc_html_e( 'Eventbrite', 'import-eventbrite-events' ); ?>
		                    </a>

		                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'scheduled', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( $tab == 'scheduled' ) { echo 'nav-tab-active'; } ?>">
		                        <?php esc_html_e( 'Scheduled Imports', 'import-eventbrite-events' ); ?>
		                    </a>

		                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'history', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( $tab == 'history' ) { echo 'nav-tab-active'; } ?>">
		                        <?php esc_html_e( 'Import History', 'import-eventbrite-events' ); ?>
		                    </a>

		                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'settings', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( $tab == 'settings' ) { echo 'nav-tab-active'; } ?>">
		                        <?php esc_html_e( 'Settings', 'import-eventbrite-events' ); ?>
		                    </a>

				            <a href="<?php echo esc_url( add_query_arg( 'tab', 'support', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( $tab == 'support' ) { echo 'nav-tab-active'; } ?>">
		                        <?php esc_html_e( 'Support & Help', 'import-eventbrite-events' ); ?>
		                    </a>
		                </h1>

		                <div class="import-eventbrite-events-page">

		                	<?php
		                	if ( $tab == 'eventbrite' ) {

		                		require_once IEE_PLUGIN_DIR . '/templates/eventbrite-import-events.php';

		                	} elseif ( $tab == 'settings' ) {
		                		
		                		require_once IEE_PLUGIN_DIR . '/templates/import-eventbrite-events-settings.php';

		                	} elseif ( $tab == 'scheduled' ) {

		                		require_once IEE_PLUGIN_DIR . '/templates/scheduled-import-events.php';

		                	}elseif ( $tab == 'history' ) {
		                		
		                		require_once IEE_PLUGIN_DIR . '/templates/import-eventbrite-events-history.php';

		                	} elseif ( $tab == 'support' ) {

		                		require_once IEE_PLUGIN_DIR . '/templates/import-eventbrite-events-support.php';

		                	}
			                ?>
		                	<div style="clear: both"></div>
		                </div>

		        </div>
		        
		    </div>
		</div>
		<?php
	}


	/**
	 * Display notices in admin.
	 *
	 * @since    1.0.0
	 */
	public function display_notices() {
		global $iee_errors, $iee_success_msg, $iee_warnings, $iee_info_msg;
		
		if ( ! empty( $iee_errors ) ) {
			foreach ( $iee_errors as $error ) :
			    ?>
			    <div class="notice notice-error is-dismissible">
			        <p><?php echo $error; ?></p>
			    </div>
			    <?php
			endforeach;
		}

		if ( ! empty( $iee_success_msg ) ) {
			foreach ( $iee_success_msg as $success ) :
			    ?>
			    <div class="notice notice-success is-dismissible">
			        <p><?php echo $success; ?></p>
			    </div>
			    <?php
			endforeach;
		}

		if ( ! empty( $iee_warnings ) ) {
			foreach ( $iee_warnings as $warning ) :
			    ?>
			    <div class="notice notice-warning is-dismissible">
			        <p><?php echo $warning; ?></p>
			    </div>
			    <?php
			endforeach;
		}

		if ( ! empty( $iee_info_msg ) ) {
			foreach ( $iee_info_msg as $info ) :
			    ?>
			    <div class="notice notice-info is-dismissible">
			        <p><?php echo $info; ?></p>
			    </div>
			    <?php
			endforeach;
		}

	}

	/**
	 * Register custom post type for scheduled imports.
	 *
	 * @since    1.0.0
	 */
	public function register_scheduled_import_cpt() {
		$labels = array(
			'name'               => _x( 'Scheduled Import', 'post type general name', 'import-eventbrite-events' ),
			'singular_name'      => _x( 'Scheduled Import', 'post type singular name', 'import-eventbrite-events' ),
			'menu_name'          => _x( 'Scheduled Imports', 'admin menu', 'import-eventbrite-events' ),
			'name_admin_bar'     => _x( 'Scheduled Import', 'add new on admin bar', 'import-eventbrite-events' ),
			'add_new'            => _x( 'Add New', 'book', 'import-eventbrite-events' ),
			'add_new_item'       => __( 'Add New Import', 'import-eventbrite-events' ),
			'new_item'           => __( 'New Import', 'import-eventbrite-events' ),
			'edit_item'          => __( 'Edit Import', 'import-eventbrite-events' ),
			'view_item'          => __( 'View Import', 'import-eventbrite-events' ),
			'all_items'          => __( 'All Scheduled Imports', 'import-eventbrite-events' ),
			'search_items'       => __( 'Search Scheduled Imports', 'import-eventbrite-events' ),
			'parent_item_colon'  => __( 'Parent Imports:', 'import-eventbrite-events' ),
			'not_found'          => __( 'No Imports found.', 'import-eventbrite-events' ),
			'not_found_in_trash' => __( 'No Imports found in Trash.', 'import-eventbrite-events' ),
		);

		$args = array(
			'labels'             => $labels,
	        'description'        => __( 'Scheduled Imports.', 'import-eventbrite-events' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'show_in_admin_bar'  => false,
			'show_in_nav_menus'  => false,
			'can_export'         => false,
			'rewrite'            => false,
			'capability_type'    => 'page',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'title' ),
			'menu_position'		=> 5,
		);

		register_post_type( 'iee_scheduled_import', $args );
	}

	/**
	 * Register custom post type for Save import history.
	 *
	 * @since    1.0.0
	 */
	public function register_history_cpt() {
		$labels = array(
			'name'               => _x( 'Import History', 'post type general name', 'import-eventbrite-events' ),
			'singular_name'      => _x( 'Import History', 'post type singular name', 'import-eventbrite-events' ),
			'menu_name'          => _x( 'Import History', 'admin menu', 'import-eventbrite-events' ),
			'name_admin_bar'     => _x( 'Import History', 'add new on admin bar', 'import-eventbrite-events' ),
			'add_new'            => _x( 'Add New', 'book', 'import-eventbrite-events' ),
			'add_new_item'       => __( 'Add New', 'import-eventbrite-events' ),
			'new_item'           => __( 'New History', 'import-eventbrite-events' ),
			'edit_item'          => __( 'Edit History', 'import-eventbrite-events' ),
			'view_item'          => __( 'View History', 'import-eventbrite-events' ),
			'all_items'          => __( 'All Import History', 'import-eventbrite-events' ),
			'search_items'       => __( 'Search History', 'import-eventbrite-events' ),
			'parent_item_colon'  => __( 'Parent History:', 'import-eventbrite-events' ),
			'not_found'          => __( 'No History found.', 'import-eventbrite-events' ),
			'not_found_in_trash' => __( 'No History found in Trash.', 'import-eventbrite-events' ),
		);

		$args = array(
			'labels'             => $labels,
	        'description'        => __( 'Import History', 'import-eventbrite-events' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'show_in_admin_bar'  => false,
			'show_in_nav_menus'  => false,
			'can_export'         => false,
			'rewrite'            => false,
			'capability_type'    => 'page',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'title' ),
			'menu_position'		=> 5,
		);

		register_post_type( 'iee_import_history', $args );
	}


	/**
	 * Add Import Eventbrite Events ratting text
	 *
	 * @since 1.0
	 * @return void
	 */
	public function add_event_aggregator_credit( $footer_text ){
		$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		if ( $page != '' && $page == 'eventbrite_event' ) {
			$rate_url = 'https://wordpress.org/support/plugin/import-eventbrite-events/reviews/?rate=5#new-post';

			$footer_text .= sprintf(
				esc_html__( ' Rate %1$sImport Eventbrite Events%2$s %3$s', 'import-eventbrite-events' ),
				'<strong>',
				'</strong>',
				'<a href="' . $rate_url . '" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}
		return $footer_text;
	}
	
	/**
	 * Render database upgrade notice. if older version is installed.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function database_upgrade_notice( $footer_text ){
		global $iee_info_msg;
		$auto_import_options = get_option( 'xtei_auto_import_options', array() );
		if( !empty( $auto_import_options ) ){
			$auto_import = isset( $auto_import_options['enable_auto_import'] ) ? $auto_import_options['enable_auto_import'] : array();
			if( !empty( $auto_import ) ){
				$upgrade_args = array(
					'iee_upgrade_action' => 'database',
				);
				$update_button = sprintf( '<a class="button-primary" href="%1$s">%2$s</a>',
					esc_url( wp_nonce_url( add_query_arg( $upgrade_args ), 'iee_upgrade_action_nonce' ) ),
					esc_html__( 'Update', 'import-eventbrite-events' )
				);
			    $iee_info_msg[] = esc_html__( 'Please click update for finish update of Import Eventbrite Events. ', 'import-eventbrite-events' ) . $update_button;
			}
		}
	}

	/**
	 * database upgrade Proceed.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function maybe_proceed_database_upgrade(){
		if ( isset( $_GET['iee_upgrade_action'] ) && $_GET['iee_upgrade_action'] == 'database' && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'iee_upgrade_action_nonce' ) ) {
				
			$auto_import_options = get_option( 'xtei_auto_import_options', array() );
			$auto_import = isset( $auto_import_options['enable_auto_import'] ) ? $auto_import_options['enable_auto_import'] : array();
			if( !empty( $auto_import ) ){

				$xtei_options = get_option( 'xtei_eventbrite_options', array() );
				foreach ($auto_import as $import_into ) {
					$event_data = $event_cats = array();

					if( $import_into == 'tec'){
						$event_cats = isset( $auto_import_options['xtei_event_cats'] ) ? $auto_import_options['xtei_event_cats'] : array();
					}
					if( $import_into == 'em'){
						$event_cats = isset( $auto_import_options['xtei_event_em_cats'] ) ? $auto_import_options['xtei_event_em_cats'] : array();
					}
					$event_data['import_into'] = $import_into;
					$event_data['import_type'] = 'scheduled';
					$event_data['import_frequency'] = isset( $auto_import_options['cron_interval'] ) ? $auto_import_options['cron_interval'] : 'twicedaily';
					$event_data['event_cats'] = $event_cats;
					$event_data['event_status'] = isset( $xtei_options['default_status'] ) ? $xtei_options['default_status'] : 'pending';
					$event_data['import_origin'] = 'eventbrite';
					$event_data['import_by'] = 'your_events';
					$event_data['eventbrite_event_id'] = '';
					$event_data['organizer_id'] = '';

					$insert_args = array(
						'post_type' => 'iee_scheduled_import',
						'post_status' => 'publish',
						'post_title' => 'Your profile Events',
					);
					$insert = wp_insert_post( $insert_args, true );
					if ( is_wp_error( $insert ) ) {
						$iee_errors[] = esc_html__( 'Something went wrong when insert url.', 'import-eventbrite-events' ) . $insert->get_error_message();
						return;
					}
					$import_frequency = isset( $event_data['import_frequency']) ? $event_data['import_frequency'] : 'twicedaily';
					update_post_meta( $insert, 'import_origin', 'eventbrite' );
					update_post_meta( $insert, 'import_eventdata', $event_data );
					wp_schedule_event( time(), $import_frequency, 'iee_run_scheduled_import', array( 'post_id' => $insert ) );
				}
				delete_option( 'xtei_auto_import_options' );
				$page = isset($_GET['page'] ) ? $_GET['page'] : 'eventbrite_event';
				$tab = isset($_GET['tab'] ) ? $_GET['tab'] : 'scheduled';
				$wp_redirect = admin_url( 'admin.php' );
	        	$query_args = array( 'page' => $page, 'iee_msg' => 'upgrade_finish', 'tab' => $tab );
	        	wp_redirect(  add_query_arg( $query_args, $wp_redirect ) );
				exit;
			}
		}
	}


	/**
	 * Get Plugin array
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_xyuls_themes_plugins(){
		return array(
			'wp-event-aggregator' => esc_html__( 'WP Event Aggregator', 'import-facebook-events' ),
			'import-facebook-events' => esc_html__( 'Import Facebook Events', 'import-facebook-events' ),
			'import-meetup-events' => esc_html__( 'Import Meetup Events', 'import-facebook-events' ),
			'wp-bulk-delete' => esc_html__( 'WP Bulk Delete', 'import-facebook-events' ),
		);
	}

	/**
	 * Get Plugin Details.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_wporg_plugin( $slug ){

		if( $slug == '' ){
			return false;
		}

		$transient_name = 'support_plugin_box'.$slug;
		$plugin_data = get_transient( $transient_name );
		if( false === $plugin_data ){
			if ( ! function_exists( 'plugins_api' ) ) {
				include_once ABSPATH . '/wp-admin/includes/plugin-install.php';
			}

			$plugin_data = plugins_api( 'plugin_information', array(
				'slug' => $slug,
				'is_ssl' => is_ssl(),
				'fields' => array(
					'banners' => true,
					'active_installs' => true,
				),
			) );

			if ( ! is_wp_error( $plugin_data ) ) {
				set_transient( $transient_name, $plugin_data, 24 * HOUR_IN_SECONDS );
			} else {
				// If there was a bug on the Current Request just leave
				return false;
			}			
		}
		return $plugin_data;
	}
}
