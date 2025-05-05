<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package     Import_Eventbrite_Events
 * @subpackage  Import_Eventbrite_Events/admin
 * @copyright   Copyright (c) 2016, Dharmesh Patel
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @package     Import_Eventbrite_Events
 * @subpackage  Import_Eventbrite_Events/admin
 * @author     Dharmesh Patel <dspatel44@gmail.com>
 */
class Import_Eventbrite_Events_Admin {

	/**
	 * Admin page URL
	 *
	 * @var string
	 */
	public $adminpage_url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->adminpage_url = admin_url( 'admin.php?page=eventbrite_event' );

		add_action( 'init', array( $this, 'register_scheduled_import_cpt' ) );
		add_action( 'init', array( $this, 'register_history_cpt' ) );
		add_action( 'admin_init', array( $this, 'iee_check_delete_pst_event_cron_status' ) );
		add_action( 'iee_delete_past_events_cron', array( $this, 'iee_delete_past_events' ) );
		add_action( 'admin_init', array( $this, 'database_upgrade_notice' ) );
		add_action( 'admin_init', array( $this, 'maybe_proceed_database_upgrade' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
		add_filter( 'submenu_file', array( $this, 'get_selected_tab_submenu_iee' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_notices', array( $this, 'display_notices' ) );
		add_filter( 'admin_footer_text', array( $this, 'add_event_aggregator_credit' ) );
		add_action( 'admin_action_iee_view_import_history', array( $this, 'iee_view_import_history_handler' ) );
	}

	/**
	 * Create the Admin menu and submenu and assign their links to global varibles.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function add_menu_pages() {

		add_menu_page( __( 'Import Eventbrite Events', 'import-eventbrite-events' ), __( 'Eventbrite Import', 'import-eventbrite-events' ), 'manage_options', 'eventbrite_event', array( $this, 'admin_page' ), 'dashicons-calendar-alt', '30' );
		global $submenu;	
		$submenu['eventbrite_event'][] = array( __( 'Eventbrite Import', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=eventbrite' ) );
		$submenu['eventbrite_event'][] = array( __( 'Schedule Import', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=scheduled' ) );
		$submenu['eventbrite_event'][] = array( __( 'Import History', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=history' ) );
		$submenu['eventbrite_event'][] = array( __( 'Settings', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=settings' ));
		$submenu['eventbrite_event'][] = array( __( 'Shortcodes', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=shortcodes' ));
		$submenu['eventbrite_event'][] = array( __( 'Support & help', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=support' ));
		if( !iee_is_pro() ){
        	$submenu['eventbrite_event'][] = array( '<li class="iee_upgrade_pro current">' . __( 'Upgrade to Pro', 'import-eventbrite-events' ) . '</li>', 'manage_options', esc_url( "https://xylusthemes.com/plugins/import-eventbrite-events/") );
		}
	}

	/**
	 * Load Admin Scripts
	 *
	 * Enqueues the required admin scripts.
	 *
	 * @since 1.0
	 * @param string $hook Page hook.
	 * @return void
	 */
	public function enqueue_admin_scripts( $hook ) {

		$js_dir = IEE_PLUGIN_URL . 'assets/js/';
		wp_register_script( 'import-eventbrite-events', $js_dir . 'import-eventbrite-events-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'wp-color-picker' ), IEE_VERSION ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		wp_enqueue_script( 'import-eventbrite-events' );

	}

	/**
	 * Load Admin Styles.
	 *
	 * Enqueues the required admin styles.
	 *
	 * @since 1.0
	 * @param string $hook Page hook.
	 * @return void
	 */
	public function enqueue_admin_styles( $hook ) {

		global $pagenow;
		$page = isset( $_GET['page'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'eventbrite_event' === $page || 'widgets.php' === $pagenow || 'post.php' === $pagenow || 'post-new.php' === $pagenow ) {
			$css_dir = IEE_PLUGIN_URL . 'assets/css/';
			wp_enqueue_style( 'jquery-ui', $css_dir . 'jquery-ui.css', false, '1.12.0' );
			wp_enqueue_style( 'import-eventbrite-events', $css_dir . 'import-eventbrite-events-admin.css', false, IEE_VERSION );
			wp_enqueue_style('wp-color-picker');
		}
	}

	/**
	 * Load Admin page.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function admin_page() {

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Import Eventbrite Events', 'import-eventbrite-events' ); ?></h2>
			<?php
			// Set Default Tab to Import.
			$tab  = isset( $_GET['tab'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) : 'eventbrite'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$ntab = isset( $_GET['ntab'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['ntab'] ) ) ) : 'import'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">

					<div id="postbox-container-1" class="postbox-container">
						<?php
						if ( ! iee_is_pro() ) {
							require_once IEE_PLUGIN_DIR . '/templates/admin/admin-sidebar.php';
						}
						?>
					</div>
					<div id="postbox-container-2" class="postbox-container">

						<h1 class="nav-tab-wrapper">
							<a href="<?php echo esc_url( add_query_arg( 'tab', 'eventbrite', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( 'eventbrite' === $tab ) { echo 'nav-tab-active'; } ?> ">
								<?php esc_html_e( 'Eventbrite', 'import-eventbrite-events' ); ?>
							</a>

							<a href="<?php echo esc_url( add_query_arg( 'tab', 'scheduled', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( 'scheduled' === $tab ) { echo 'nav-tab-active'; } ?>">
								<?php esc_html_e( 'Scheduled Imports', 'import-eventbrite-events' ); ?>
							</a>

							<a href="<?php echo esc_url( add_query_arg( 'tab', 'history', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( 'history' === $tab ) { echo 'nav-tab-active'; } ?>">
								<?php esc_html_e( 'Import History', 'import-eventbrite-events' ); ?>
							</a>

							<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( 'settings' === $tab ) { echo 'nav-tab-active'; } ?>">
								<?php esc_html_e( 'Settings', 'import-eventbrite-events' ); ?>
							</a>

							<a href="<?php echo esc_url( add_query_arg( 'tab', 'shortcodes', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( 'shortcodes' == $tab) { echo 'nav-tab-active'; } ?>">
								<?php esc_html_e( 'Shortcodes', 'import-eventbrite-events' ); ?>
							</a>

							<a href="<?php echo esc_url( add_query_arg( 'tab', 'support', $this->adminpage_url ) ); ?>" class="nav-tab <?php if ( 'support' === $tab ) { echo 'nav-tab-active'; } ?>">
								<?php esc_html_e( 'Support & Help', 'import-eventbrite-events' ); ?>
							</a>
						</h1>

						<div class="import-eventbrite-events-page">
							<?php
							if ( 'eventbrite' === $tab ) {
								require_once IEE_PLUGIN_DIR . '/templates/admin/eventbrite-import-events.php';
							} elseif ( 'settings' === $tab ) {
								require_once IEE_PLUGIN_DIR . '/templates/admin/import-eventbrite-events-settings.php';
							} elseif ( 'scheduled' === $tab ) {
								if ( iee_is_pro() ) {
									require_once IEEPRO_PLUGIN_DIR . '/templates/admin/scheduled-import-events.php';
								} else {
									do_action( 'iee_render_pro_notice' );
								}
							} elseif ( 'history' === $tab ) {
								require_once IEE_PLUGIN_DIR . '/templates/admin/import-eventbrite-events-history.php';
							} elseif ( 'support' === $tab ) {
								require_once IEE_PLUGIN_DIR . '/templates/admin/import-eventbrite-events-support.php';
							}elseif ( 'shortcodes' === $tab ) {
								require_once IEE_PLUGIN_DIR . '/templates/admin/import-eventbrite-events-shortcode.php';
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
					<p><?php echo $error; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
				<?php
			endforeach;
		}

		if ( ! empty( $iee_success_msg ) ) {
			foreach ( $iee_success_msg as $success ) :
				?>
				<div class="notice notice-success is-dismissible">
					<p><?php echo $success; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
				<?php
			endforeach;
		}

		if ( ! empty( $iee_warnings ) ) {
			foreach ( $iee_warnings as $warning ) :
				?>
				<div class="notice notice-warning is-dismissible">
					<p><?php echo $warning; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
				<?php
			endforeach;
		}

		if ( ! empty( $iee_info_msg ) ) {
			foreach ( $iee_info_msg as $info ) :
				?>
				<div class="notice notice-info is-dismissible">
					<p><?php echo $info; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
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
			'menu_position'      => 5,
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
			'menu_position'      => 5,
		);

		register_post_type( 'iee_import_history', $args );
	}


	/**
	 * Add Import Eventbrite Events ratting text
	 *
	 * @since  1.0
	 * @param  string $footer_text WP Admin Footer text.
	 * @return string $footer_text Altered WP Admin Footer text.
	 */
	public function add_event_aggregator_credit( $footer_text ) {
		$page = isset( $_GET['page'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $page ) && 'eventbrite_event' === $page ) {
			$rate_url = 'https://wordpress.org/support/plugin/import-eventbrite-events/reviews/?rate=5#new-post';

			$footer_text .= sprintf(
				/* translators: leave %1$s, %2$s and %3$s */
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
	public function database_upgrade_notice() {
		global $iee_info_msg;
		$auto_import_options = get_option( 'xtei_auto_import_options', array() );
		if ( ! empty( $auto_import_options ) ) {
			$auto_import = isset( $auto_import_options['enable_auto_import'] ) ? $auto_import_options['enable_auto_import'] : array();
			if ( ! empty( $auto_import ) ) {
				$upgrade_args   = array(
					'iee_upgrade_action' => 'database',
				);
				$update_button  = sprintf(
					'<a class="button-primary" href="%1$s">%2$s</a>',
					esc_url( wp_nonce_url( add_query_arg( $upgrade_args ), 'iee_upgrade_action_nonce' ) ),
					esc_html__( 'Update', 'import-eventbrite-events' )
				);
				$iee_info_msg[] = esc_html__( 'Please click update for finish update of Import Eventbrite Events. ', 'import-eventbrite-events' ) . $update_button;
			}
		}
	}

	/**
	 * Database upgrade Proceed.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function maybe_proceed_database_upgrade() {
		if ( isset( $_GET['iee_upgrade_action'] ) && 'database' === sanitize_text_field( wp_unslash( $_GET['iee_upgrade_action'] ) ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'iee_upgrade_action_nonce' ) ) {  // input var okey.

			$auto_import_options = get_option( 'xtei_auto_import_options', array() );
			$auto_import         = isset( $auto_import_options['enable_auto_import'] ) ? $auto_import_options['enable_auto_import'] : array();
			if ( ! empty( $auto_import ) ) {

				$xtei_options = get_option( 'xtei_eventbrite_options', array() );
				foreach ( $auto_import as $import_into ) {
					$event_data = $event_cats = array();

					if ( 'tec' === $import_into ) {
						$event_cats = isset( $auto_import_options['xtei_event_cats'] ) ? $auto_import_options['xtei_event_cats'] : array();
					}
					if ( 'em' === $import_into ) {
						$event_cats = isset( $auto_import_options['xtei_event_em_cats'] ) ? $auto_import_options['xtei_event_em_cats'] : array();
					}
					$event_data['import_into']         = $import_into;
					$event_data['import_type']         = 'scheduled';
					$event_data['import_frequency']    = isset( $auto_import_options['cron_interval'] ) ? $auto_import_options['cron_interval'] : 'twicedaily';
					$event_data['event_cats']          = $event_cats;
					$event_data['event_status']        = isset( $xtei_options['default_status'] ) ? $xtei_options['default_status'] : 'pending';
					$event_data['import_origin']       = 'eventbrite';
					$event_data['import_by']           = 'your_events';
					$event_data['eventbrite_event_id'] = '';
					$event_data['organizer_id']        = '';

					$insert_args = array(
						'post_type'   => 'iee_scheduled_import',
						'post_status' => 'publish',
						'post_title'  => 'Your profile Events',
					);
					$insert      = wp_insert_post( $insert_args, true );
					if ( is_wp_error( $insert ) ) {
						$iee_errors[] = esc_html__( 'Something went wrong when insert url.', 'import-eventbrite-events' ) . $insert->get_error_message();
						return;
					}
					$import_frequency = isset( $event_data['import_frequency'] ) ? $event_data['import_frequency'] : 'twicedaily';
					update_post_meta( $insert, 'import_origin', 'eventbrite' );
					update_post_meta( $insert, 'import_eventdata', $event_data );
					wp_schedule_event( time(), $import_frequency, 'iee_run_scheduled_import', array( 'post_id' => $insert ) );
				}
				delete_option( 'xtei_auto_import_options' );
				$page        = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : 'eventbrite_event'; // input var okey.
				$tab         = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'scheduled'; // input var okey.
				$wp_redirect = admin_url( 'admin.php' );
				$query_args  = array(
					'page'    => $page,
					'iee_msg' => 'upgrade_finish',
					'tab'     => $tab,
				);
				wp_redirect( add_query_arg( $query_args, $wp_redirect ) );
				exit;
			}
		}
	}

	/**
	 * Tab Submenu got selected.
	 *
	 * @since 1.6.7
	 * @return void
	 */
	public function get_selected_tab_submenu_iee( $submenu_file ){
		if( !empty( $_GET['page'] ) && esc_attr( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) == 'eventbrite_event' ){ // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$allowed_tabs = array( 'eventbrite', 'scheduled', 'history', 'settings', 'shortcodes', 'support' );
			$tab = isset( $_GET['tab'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) : 'eventbrite'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if( in_array( $tab, $allowed_tabs ) ){
				$submenu_file = admin_url( 'admin.php?page=eventbrite_event&tab='.$tab );
			}
		}
		return $submenu_file;
	}

	/**
	 * Render imported Events in history Page.
	 *
	 * @return void
	 */
	public function iee_view_import_history_handler() {
	    define( 'IFRAME_REQUEST', true );
	    iframe_header();
	    $history_id = isset($_GET['history']) ? absint($_GET['history']) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	    if( $history_id > 0){
	    	$imported_data = get_post_meta($history_id, 'imported_data', true);
	    	if(!empty($imported_data)){
	    		?>
			    <table class="widefat fixed striped">
				<thead>
					<tr>
						<th id="title" class="column-title column-primary"><?php esc_html_e( 'Event', 'import-eventbrite-events' ); ?></th>
						<th id="action" class="column-operation"><?php esc_html_e( 'Created/Updated', 'import-eventbrite-events' ); ?></th>
						<th id="action" class="column-date"><?php esc_html_e( 'Action', 'import-eventbrite-events' ); ?></th>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php
					foreach ($imported_data as $event) {
						?>
						<tr>
							<td class="title column-title">
								<?php 
								printf(
									'<a href="%1$s" target="_blank">%2$s</a>',
									esc_url( get_the_permalink( $event['id'] ) ),
									esc_attr( get_the_title($event['id'] ) )
								);
								?>
							</td>
							<td class="title column-title">
								<?php echo esc_attr( ucfirst($event['status']) ); ?>
							</td>
							<td class="title column-action">
								<?php 
								printf(
									'<a href="%1$s" target="_blank">%2$s</a>',
									esc_url( get_edit_post_link($event['id'] ) ),
									esc_attr__( 'Edit', 'import-eventbrite-events' )
								);
								?>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
				<?php
	    		?>
	    		<?php
	    	}else{
	    		?>
	    		<div class="iee_no_import_events">
		    		<?php esc_html_e( 'No data found', 'import-eventbrite-events' ); ?>
		    	</div>
	    		<?php
	    	}
	    }else{
	    	?>
    		<div class="iee_no_import_events">
	    		<?php esc_html_e( 'No data found', 'import-eventbrite-events' ); ?>
	    	</div>
    		<?php
	    }
	    ?>
	    <style>
	    	.iee_no_import_events{
				text-align: center;
				margin-top: 60px;
				font-size: 1.4em;
			}
	    </style>
	    <?php
	    iframe_footer();
	    exit;
	}

	/**
	 * Render Delete Past Event in the eventbrite_events post type
	 * @return void
	 */
	public function iee_delete_past_events() {
    
		$current_time = current_time('timestamp');
		$args         = array(
			'post_type'       => 'eventbrite_events',
			'posts_per_page'  => 100,
			'post_status'     => 'publish',
			'fields'          => 'ids',
			'meta_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => 'end_ts',
					'value'   => current_time( 'timestamp' ) - ( 24 * 3600 ),
					'compare' => '<',      
					'type'    => 'NUMERIC',
				),
			),
		);
		$events = get_posts( $args );
	
		if ( empty( $events ) ) {
			return;
		}

		foreach ( $events as $event_id ) {
			wp_trash_post( $event_id );
		}
	}
	
	/**
	 * re-create if the past event cron is delete
	 */
	public function iee_check_delete_pst_event_cron_status(){
		
		$iee_options        = get_option( IEE_OPTIONS );
		$move_peit_ieevents = isset( $iee_options['move_peit'] ) ? $iee_options['move_peit'] : 'no';
		if ( $move_peit_ieevents == 'yes' ) {
			if ( !wp_next_scheduled( 'iee_delete_past_events_cron' ) ) {
				wp_schedule_event( time(), 'daily', 'iee_delete_past_events_cron' );
			}
		}else{
			if ( wp_next_scheduled( 'iee_delete_past_events_cron' ) ) {
				wp_clear_scheduled_hook( 'iee_delete_past_events_cron' );
			}
		}

	}
}
