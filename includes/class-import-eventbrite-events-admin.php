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
		add_filter( 'parent_file', array( $this, 'get_selected_tab_parent_iee' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_notices', array( $this,'iee_remove_default_notices' ), 1 );
		add_action( 'iee_display_all_notice', array( $this, 'iee_display_notices' ) );
		add_filter( 'admin_footer_text', array( $this, 'add_event_aggregator_credit' ) );
		add_action( 'admin_action_iee_view_import_history', array( $this, 'iee_view_import_history_handler' ) );
		add_action( 'admin_init', array( $this, 'iee_handle_schedule_toggle_action' ) );
		add_action( 'init', array( $this, 'sync_organizer_from_url_weekly' ) );
		add_action( 'admin_menu', array( $this, 'iee_widget_free_page' ) ); 
		
	}

	function iee_widget_free_page() {
		if ( ! post_type_exists( 'ieepro_live_feed' ) && ! defined( 'IEEPRO_VERSION' ) ) {
			add_submenu_page(
				'eventbrite_event',
				__( 'Eventbrite Widget', 'import-eventbrite-events' ),
				__( 'Eventbrite Widget', 'import-eventbrite-events' ),
				'manage_options',
				'iee_eventbrite_feed_upgrade',
				array( $this, 'iee_render_feed_upgrade_page' )
			);
		}
	}

	function iee_render_feed_upgrade_page() {
		$pro_url = 'https://xylusthemes.com/plugins/import-eventbrite-events';
		?>
		<style>
			.iee-upgrade-wrap { max-width: 900px; margin: 40px auto; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
			.iee-upgrade-hero { background: linear-gradient(135deg, #f06342 0%, #e84f2a 50%, #3d64f4 100%); border-radius: 12px; padding: 48px 40px; color: #fff; text-align: center; position: relative; overflow: hidden; margin-bottom: 32px; }
			.iee-upgrade-hero::before { content:''; position:absolute; top:-60px; right:-60px; width:220px; height:220px; background:rgba(255,255,255,0.07); border-radius:50%; }
			.iee-upgrade-hero::after { content:''; position:absolute; bottom:-40px; left:-40px; width:160px; height:160px; background:rgba(255,255,255,0.05); border-radius:50%; }
			.iee-upgrade-hero h1 { font-size: 32px; font-weight: 800; margin: 0 0 12px; position:relative; z-index:1; }
			.iee-upgrade-hero p { font-size: 16px; opacity: 0.92; margin: 0 0 28px; position:relative; z-index:1; max-width: 560px; margin-left:auto; margin-right:auto; margin-bottom:28px;}
			.iee-upgrade-hero-btn { display: inline-flex; align-items: center; gap: 8px; background: #fff; color: #f06342; font-size: 15px; font-weight: 700; padding: 14px 32px; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 16px rgba(0,0,0,0.2); position:relative; z-index:1; transition: transform 0.2s; }
			.iee-upgrade-hero-btn:hover { transform: translateY(-2px); color: #e8411a; }
			.iee-pro-badge-large { display:inline-block; background:#4CAF50; color:#fff; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; letter-spacing:1px; text-transform:uppercase; margin-bottom:16px; }

			.iee-features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px; }
			.iee-feature-card { background: #fff; border: 1px solid #e8e8e8; border-radius: 10px; padding: 24px 20px; text-align: center; transition: box-shadow 0.2s; }
			.iee-feature-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
			.iee-feature-icon { font-size: 32px; margin-bottom: 12px; display:block; }
			.iee-feature-card h3 { font-size: 14px; font-weight: 700; color: #1d2327; margin: 0 0 8px; }
			.iee-feature-card p { font-size: 12px; color: #666; margin: 0; line-height: 1.6; }

			.iee-compare-table { background:#fff; border:1px solid #e8e8e8; border-radius:10px; overflow:hidden; margin-bottom:32px; }
			.iee-compare-table table { width:100%; border-collapse:collapse; }
			.iee-compare-table th { padding:14px 20px; font-size:13px; font-weight:700; text-align:center; }
			.iee-compare-table th:first-child { text-align:left; background:#f8f9fa; }
			.iee-compare-table th.free-col { background:#f8f9fa; color:#888; }
			.iee-compare-table th.pro-col { background: linear-gradient(135deg, #f06342, #3d64f4); color:#fff; }
			.iee-compare-table td { padding:11px 20px; font-size:13px; border-top:1px solid #f0f0f0; text-align:center; }
			.iee-compare-table td:first-child { text-align:left; color:#444; font-weight:500; }
			.iee-compare-table tr:hover td { background:#fafafa; }
			.iee-check { color:#4CAF50; font-size:16px; font-weight:700; }
			.iee-cross { color:#ccc; font-size:16px; }

			.iee-bottom-cta { background:#f8f9fa; border:1px solid #e8e8e8; border-radius:10px; padding:32px; text-align:center; }
			.iee-bottom-cta h3 { font-size:20px; font-weight:700; color:#1d2327; margin:0 0 8px; }
			.iee-bottom-cta p { font-size:13px; color:#666; margin:0 0 20px; }

			@media (max-width: 782px) { .iee-features-grid { grid-template-columns: 1fr 1fr; } }
		</style>

		<div class="iee-upgrade-wrap">

			<div class="iee-upgrade-hero">
				<span class="iee-pro-badge-large"><?php esc_html_e( 'PRO Feature', 'import-eventbrite-events' ); ?></span>
				<h1 style="color:#fff;"><?php esc_html_e( 'Eventbrite Widget', 'import-eventbrite-events' ); ?></h1>
				<p style="color:#ddd;"><?php esc_html_e( 'Display Eventbrite events directly on your website no import, no authorization, no API token needed. Just paste a shortcode and go live!', 'import-eventbrite-events' ); ?></p>
				<a href="<?php echo esc_url( $pro_url ); ?>" target="_blank" class="iee-upgrade-hero-btn">
					✦ <?php esc_html_e( 'Upgrade to PRO', 'import-eventbrite-events' ); ?>
				</a>
			</div>

			<div class="iee-features-grid">
				<div class="iee-feature-card">
					<span class="iee-feature-icon">🚀</span>
					<h3><?php esc_html_e( 'No Import Needed', 'import-eventbrite-events' ); ?></h3>
					<p><?php esc_html_e( 'Show live events directly from Eventbrite, no manual importing, no syncing required.', 'import-eventbrite-events' ); ?></p>
				</div>
				<div class="iee-feature-card">
					<span class="iee-feature-icon">🔑</span>
					<h3><?php esc_html_e( 'No Auth & No Token', 'import-eventbrite-events' ); ?></h3>
					<p><?php esc_html_e( 'No API key, no OAuth setup. Just enter your Organizer ID or Collection ID and done.', 'import-eventbrite-events' ); ?></p>
				</div>
				<div class="iee-feature-card">
					<span class="iee-feature-icon">🔄</span>
					<h3><?php esc_html_e( 'Always Up-to-Date', 'import-eventbrite-events' ); ?></h3>
					<p><?php esc_html_e( 'Events auto-refresh via smart caching. Your visitors always see fresh event data.', 'import-eventbrite-events' ); ?></p>
				</div>
				<div class="iee-feature-card">
					<span class="iee-feature-icon">🎨</span>
					<h3><?php esc_html_e( '7 Layout Styles', 'import-eventbrite-events' ); ?></h3>
					<p><?php esc_html_e( 'Card Grid, List, Masonry, Timeline, Ticket, Minimal Grid, Compact List — pick what fits your site.', 'import-eventbrite-events' ); ?></p>
				</div>
				<div class="iee-feature-card">
					<span class="iee-feature-icon">🎟️</span>
					<h3><?php esc_html_e( 'Ticket Button Built-in', 'import-eventbrite-events' ); ?></h3>
					<p><?php esc_html_e( 'Show Get Tickets button with popup modal or direct Eventbrite link. Fully customizable labels.', 'import-eventbrite-events' ); ?></p>
				</div>
				<div class="iee-feature-card">
					<span class="iee-feature-icon">⚡</span>
					<h3><?php esc_html_e( 'Shortcode Builder', 'import-eventbrite-events' ); ?></h3>
					<p><?php esc_html_e( 'Visual builder generates your shortcode instantly. Paste it anywhere — pages, posts, widgets.', 'import-eventbrite-events' ); ?></p>
				</div>
			</div>

			<div class="iee-compare-table">
				<table>
					<thead>
						<tr>
							<th><?php esc_html_e( 'Feature', 'import-eventbrite-events' ); ?></th>
							<th class="free-col"><?php esc_html_e( 'Free', 'import-eventbrite-events' ); ?></th>
							<th class="pro-col"><?php esc_html_e( 'PRO', 'import-eventbrite-events' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php esc_html_e( 'Display events via Live Feed (no import)', 'import-eventbrite-events' ); ?></td>
							<td><span class="iee-cross">✕</span></td>
							<td><span class="iee-check">✔</span></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Feed by Organizer ID', 'import-eventbrite-events' ); ?></td>
							<td><span class="iee-cross">✕</span></td>
							<td><span class="iee-check">✔</span></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Feed by Collection ID', 'import-eventbrite-events' ); ?></td>
							<td><span class="iee-cross">✕</span></td>
							<td><span class="iee-check">✔</span></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Feed by Specific Event IDs', 'import-eventbrite-events' ); ?></td>
							<td><span class="iee-cross">✕</span></td>
							<td><span class="iee-check">✔</span></td>
						</tr>
						<tr>
							<td><?php esc_html_e( '7 Display Layouts (Grid, List, Masonry & more)', 'import-eventbrite-events' ); ?></td>
							<td><span class="iee-cross">✕</span></td>
							<td><span class="iee-check">✔</span></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Shortcode Builder', 'import-eventbrite-events' ); ?></td>
							<td><span class="iee-cross">✕</span></td>
							<td><span class="iee-check">✔</span></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Filter by Date & Time', 'import-eventbrite-events' ); ?></td>
							<td><span class="iee-cross">✕</span></td>
							<td><span class="iee-check">✔</span></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Ticket Button (Popup Modal or Link)', 'import-eventbrite-events' ); ?></td>
							<td><span class="iee-cross">✕</span></td>
							<td><span class="iee-check">✔</span></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Smart Cache + Auto Refresh', 'import-eventbrite-events' ); ?></td>
							<td><span class="iee-cross">✕</span></td>
							<td><span class="iee-check">✔</span></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Pagination (Load More / Infinite Scroll)', 'import-eventbrite-events' ); ?></td>
							<td><span class="iee-cross">✕</span></td>
							<td><span class="iee-check">✔</span></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Custom CSS per Feed', 'import-eventbrite-events' ); ?></td>
							<td><span class="iee-cross">✕</span></td>
							<td><span class="iee-check">✔</span></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="iee-bottom-cta">
				<h3><?php esc_html_e( 'Ready to go live with Eventbrite Widget?', 'import-eventbrite-events' ); ?></h3>
				<p><?php esc_html_e( 'Upgrade to PRO and start displaying events on your website in minutes — no technical setup needed.', 'import-eventbrite-events' ); ?></p>
				<a href="<?php echo esc_url( $pro_url ); ?>" target="_blank" 
				style="display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg,#f06342,#3d64f4); color:#fff; font-size:14px; font-weight:700; padding:13px 30px; border-radius:8px; text-decoration:none; box-shadow:0 4px 16px rgba(240,99,66,0.35);">
					✦ <?php esc_html_e( 'Get PRO Now', 'import-eventbrite-events' ); ?>
				</a>
			</div>

		</div>
		<?php
	}

	/**

	* Sync organizer from url weekly.
	*
	* This function runs weekly and fetches all published events with organizer_url set but without organizer_id.
	* It then tries to extract the organizer_id from the organizer_url and updates the organizer_id for the event.
	*
	* The function uses a transient to prevent it from running multiple times in a week.
	*
	* @since 1.8.0
	*/
	public function sync_organizer_from_url_weekly() {
		global $wpdb;

		$transient_key = 'iee_sync_organizer_from_url_last_run';

		if ( get_transient( $transient_key ) ) {
			return;
		}

		$post_type = 'eventbrite_events';
		$limit     = 2000;
		$query     = $wpdb->prepare(
			"SELECT  p.ID as post_id,  pm.meta_value as organizer_url, pm3.meta_value as start_ts
			FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm  ON p.ID = pm.post_id AND pm.meta_key = 'organizer_url'
			LEFT JOIN {$wpdb->postmeta} pm2  ON p.ID = pm2.post_id AND pm2.meta_key = 'organizer_id'
			LEFT JOIN {$wpdb->postmeta} pm3  ON p.ID = pm3.post_id AND pm3.meta_key = 'start_ts'
			WHERE p.post_type = %s
			AND p.post_status = 'publish'
			AND (pm2.meta_value IS NULL OR pm2.meta_value = '')
			ORDER BY CAST(pm3.meta_value AS UNSIGNED) DESC
			LIMIT %d",
			$post_type,
			$limit
		);

		$events = $wpdb->get_results( $query, ARRAY_A );

		if ( empty( $events ) ) {
			set_transient( $transient_key, 1, WEEK_IN_SECONDS );
			return;
		}

		foreach ( $events as $event ) {

			$post_id = (int) $event['post_id'];
			$url     = trim( $event['organizer_url'] );

			if ( empty( $url ) ) {
				continue;
			}

			if ( preg_match( '/(\d+)$/', $url, $matches ) ) {
				$organizer_id = $matches[1];

				if ( ! empty( $organizer_id ) ) {
					update_post_meta( $post_id, 'organizer_id', $organizer_id );
				}
			}
		}

		set_transient( $transient_key, 1, WEEK_IN_SECONDS );
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
		$submenu['eventbrite_event'][] = array( __( 'Dashboard', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=dashboard' ) );
		if ( post_type_exists( 'ieepro_live_feed' ) || defined( 'IEEPRO_VERSION' ) ) {
			$submenu['eventbrite_event'][] = array(
				'<span style="display:flex; justify-content:space-between; align-items:center; width:100%;">' 
					. __( 'Eventbrite Widget', 'import-eventbrite-events' ) 
					. '<span style="background:#4CAF50; margin-left:6px; flex-shrink:0;height: 22px;border-radius: 3px;color: #FFF;font-size: 12px;line-height: 18px;font-weight: 600;display: inline-flex;padding: 0 4px;align-items: center;">NEW</span>'
				. '</span>',
				'manage_options',
				'edit.php?post_type=ieepro_live_feed'
			);
		} else {
			$submenu['eventbrite_event'][] = array(
				'<span style="display:flex; justify-content:space-between; align-items:center; width:100%;">' 
					. __( 'Eventbrite Widget', 'import-eventbrite-events' ) 
					. '<span style="background:#4CAF50; margin-left:6px; flex-shrink:0;height:22px;border-radius:3px;color:#FFF;font-size:12px;line-height:18px;font-weight:600;display:inline-flex;padding:0 4px;align-items:center;">NEW</span>'
				. '</span>',
				'manage_options',
				'admin.php?page=iee_eventbrite_feed_upgrade'
			);
		}
		$submenu['eventbrite_event'][] = array( __( 'Eventbrite Import', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=eventbrite' ) );
		$submenu['eventbrite_event'][] = array( __( 'Schedule Import', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=scheduled' ) );
		$submenu['eventbrite_event'][] = array( __( 'Import History', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=history' ) );
		$submenu['eventbrite_event'][] = array( __( 'Settings', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=settings' ));
		$submenu['eventbrite_event'][] = array( __( 'Shortcodes', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=shortcodes' ));
		$submenu['eventbrite_event'][] = array( __( 'Support', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=support' ));
		$submenu['eventbrite_event'][] = array( __( 'Wizard', 'import-eventbrite-events' ), 'manage_options', admin_url( 'admin.php?page=eventbrite_event&tab=iee_setup_wizard' ));
		if( !iee_is_pro() ){
        	$submenu['eventbrite_event'][] = array( '<li class="iee_upgrade_pro current">' . __( 'Upgrade to Pro', 'import-eventbrite-events' ) . '</li>', 'manage_options', esc_url( "https://xylusthemes.com/plugins/import-eventbrite-events/") );
		}
	}

	/**
	 * Remove All Notices
	 */
	public function iee_remove_default_notices() {
		// Remove default notices display.
		remove_action( 'admin_notices', 'wp_admin_notices' );
		remove_action( 'all_admin_notices', 'wp_admin_notices' );
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

		if( isset( $_GET['tab'] ) && $_GET['tab'] == 'iee_setup_wizard' ){ // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_register_script( 'import-eventbrite-events-wizard-js', $js_dir . 'import-eventbrite-events-wizard.js',  array( 'jquery', 'jquery-ui-core' ), IEE_VERSION, false );
			wp_enqueue_script( 'import-eventbrite-events-wizard-js' );
		}

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

		$css_dir = IEE_PLUGIN_URL . 'assets/css/';
		$page    = isset( $_GET['page'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// Load styles on plugin admin page
		if ( 'eventbrite_event' === $page ) {
			wp_enqueue_style( 'import-eventbrite-events', $css_dir . 'import-eventbrite-events-admin.css', false, IEE_VERSION );
			wp_enqueue_style( 'wp-color-picker' );

			$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'iee_setup_wizard' === $tab ) {
				wp_enqueue_style( 'import-eventbrite-events-wizard-css', $css_dir . 'import-eventbrite-events-wizard.css', false, IEE_VERSION );
			}
		}

		// Load styles on widgets/post screen
		if ( in_array( $pagenow, [ 'widgets.php', 'post.php', 'post-new.php' ], true ) ) {
			wp_enqueue_style( 'jquery-ui', $css_dir . 'jquery-ui.css', false, '1.12.0' );
			wp_enqueue_style( 'import-eventbrite-events-admin-global', $css_dir . 'import-eventbrite-events-admin-global.css', false, IEE_VERSION );
			wp_enqueue_style( 'wp-color-picker' );
		}
	}

	public function admin_page() {
		global $iee_events;

			$active_tab = isset( $_GET['tab'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) )  : 'eventbrite'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$gettab     = str_replace( 'by_', '', $active_tab );
			$gettab     = ucwords( str_replace( '_', ' & ', $gettab ) );
			if( $active_tab == 'support' ){
				$page_title = 'Support & Help';
			}elseif( $active_tab == 'eventbrite' ){
				$page_title = 'Eventbrite Import';
			}elseif( $active_tab == 'ics' ){
				$page_title = 'ICS Import';
			}elseif( $active_tab == 'scheduled' ){
				$page_title = 'Scheduled Import';
			}else{
				$page_title = $gettab;
			}

			if( $active_tab == 'iee_setup_wizard' ){
				require_once IEE_PLUGIN_DIR . '/templates/admin/iee-setup-wizard.php';
				exit();
			}

			$posts_header_result = $iee_events->common->iee_render_common_header( $page_title );

			if( $active_tab != 'dashboard' ){
				?>
					<div class="iee-container" style="margin-top: 60px;">
						<div class="iee-wrap" >
							<div id="poststuff">
								<div id="post-body" class="metabox-holder columns-2">
									<?php 
										do_action( 'iee_display_all_notice' );
									?>
									<div class="delete_notice"></div>
									<div id="postbox-container-2" class="postbox-container">
										<div class="iee-app">
											<div class="iee-tabs">
												<div class="tabs-scroller">
													<div class="var-tabs var-tabs--item-horizontal var-tabs--layout-horizontal-padding">
														<div class="var-tabs__tab-wrap var-tabs--layout-horizontal">
															<a href="?page=eventbrite_event&tab=eventbrite" class="var-tab <?php echo $active_tab == 'eventbrite' ? 'var-tab--active' : 'var-tab--inactive'; ?>">
																<span class="tab-label"><?php esc_attr_e( 'Import', 'import-eventbrite-events' ); ?></span>
															</a>
															<a href="?page=eventbrite_event&tab=scheduled" class="var-tab <?php echo ( $active_tab == 'scheduled' || $active_tab == 'scheduled' )  ? 'var-tab--active' : 'var-tab--inactive'; ?>">
																<span class="tab-label"><?php esc_attr_e( 'Schedule Import', 'import-eventbrite-events' ); if( !iee_is_pro() ){ echo '<div class="iee-pro-badge"> PRO </div>'; } ?></span>
															</a>
															<a href="?page=eventbrite_event&tab=history" class="var-tab <?php echo $active_tab == 'history' ? 'var-tab--active' : 'var-tab--inactive'; ?>">
																<span class="tab-label"><?php esc_attr_e( 'History', 'import-eventbrite-events' ); ?></span>
															</a>
															<a href="?page=eventbrite_event&tab=settings" class="var-tab <?php echo $active_tab == 'settings' ? 'var-tab--active' : 'var-tab--inactive'; ?>">
																<span class="tab-label"><?php esc_attr_e( 'Setting', 'import-eventbrite-events' ); ?></span>
															</a>
															<a href="?page=eventbrite_event&tab=shortcodes" class="var-tab <?php echo $active_tab == 'shortcodes' ? 'var-tab--active' : 'var-tab--inactive'; ?>">
																<span class="tab-label"><?php esc_attr_e( 'Shortcodes', 'import-eventbrite-events' ); ?></span>
															</a>
															<a href="?page=eventbrite_event&tab=support" class="var-tab <?php echo $active_tab == 'support' ? 'var-tab--active' : 'var-tab--inactive'; ?>">
																<span class="tab-label"><?php esc_attr_e( 'Support & Help', 'import-eventbrite-events' ); ?></span>
															</a>
														</div>
													</div>
												</div>
											</div>
										</div>

										<?php
											if ( 'eventbrite' === $active_tab ) {
														require_once IEE_PLUGIN_DIR . '/templates/admin/eventbrite-import-events.php';
											} elseif ( 'settings' === $active_tab ) {
												require_once IEE_PLUGIN_DIR . '/templates/admin/import-eventbrite-events-settings.php';
											} elseif ( 'scheduled' === $active_tab ) {
												if ( iee_is_pro() ) {
													require_once IEEPRO_PLUGIN_DIR . '/templates/admin/scheduled-import-events.php';
												} else {
													?>
														<div class="iee-blur-filter" >
															<?php do_action( 'iee_render_pro_notice' ); ?>
														</div>
													<?php
												}
											} elseif ( 'history' === $active_tab ) {
												require_once IEE_PLUGIN_DIR . '/templates/admin/import-eventbrite-events-history.php';
											} elseif ( 'support' === $active_tab ) {
												require_once IEE_PLUGIN_DIR . '/templates/admin/import-eventbrite-events-support.php';
											}elseif ( 'shortcodes' === $active_tab ) {
												require_once IEE_PLUGIN_DIR . '/templates/admin/import-eventbrite-events-shortcode.php';
											}
										?>
									</div>
								</div>
								<br class="clear">
							</div>
						</div>
					</div>
				<?php
			}else{
				require_once IEE_PLUGIN_DIR . '/templates/admin/iee-dashboard.php';
			}
			$posts_footer_result = $iee_events->common->iee_render_common_footer();
	}


	/**
	 * Display notices in admin.
	 *
	 * @since    1.0.0
	 */
	public function iee_display_notices() {
		global $iee_errors, $iee_success_msg, $iee_warnings, $iee_info_msg;

		if ( ! empty( $iee_errors ) ) {
			foreach ( $iee_errors as $error ) :
				?>
				<div class="notice notice-error is-dismissible iee_notice">
					<p><?php echo $error; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
				<?php
			endforeach;
		}

		if ( ! empty( $iee_success_msg ) ) {
			foreach ( $iee_success_msg as $success ) :
				?>
				<div class="notice notice-success is-dismissible iee_notice">
					<p><?php echo $success; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
				<?php
			endforeach;
		}

		if ( ! empty( $iee_warnings ) ) {
			foreach ( $iee_warnings as $warning ) :
				?>
				<div class="notice notice-warning is-dismissible iee_notice">
					<p><?php echo $warning; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
				</div>
				<?php
			endforeach;
		}

		if ( ! empty( $iee_info_msg ) ) {
			foreach ( $iee_info_msg as $info ) :
				?>
				<div class="notice notice-info is-dismissible iee_notice">
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
					$event_data['collection_id']       = '';

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
			$allowed_tabs = array( 'dashboard', 'eventbrite', 'scheduled', 'history', 'settings', 'shortcodes', 'support' );
			$tab = isset( $_GET['tab'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) : 'eventbrite'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if( in_array( $tab, $allowed_tabs ) ){
				$submenu_file = admin_url( 'admin.php?page=eventbrite_event&tab='.$tab );
			}
		}
		
		global $post_type;
		if ( 'ieepro_live_feed' === $post_type ) {
			$submenu_file = 'edit.php?post_type=ieepro_live_feed';
		}
		
		return $submenu_file;
	}

	/**
	 * Set parent file for CPTs to keep menu open.
	 *
	 * @since 1.8.0
	 */
	public function get_selected_tab_parent_iee( $parent_file ){
		global $post_type;
		if ( 'ieepro_live_feed' === $post_type ) {
			$parent_file = 'eventbrite_event';
		}
		return $parent_file;
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


	function iee_handle_schedule_toggle_action() {
		if ( isset( $_GET['action'], $_GET['schedule_id'], $_GET['new_status'] ) && esc_attr( sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) === 'iee_toggle_status' ) {
			$schedule_id = absint( $_GET['schedule_id'] );
			$new_status  = esc_attr( sanitize_text_field( wp_unslash( $_GET['new_status'] ) ) );

			if ( ! current_user_can( 'edit_post', $schedule_id ) ) {
				wp_die( 'Permission denied' );
			}

			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( esc_attr( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) ), 'iee_toggle_schedule_' . $schedule_id ) ) {
				wp_die( 'Security check failed' );
			}

			update_post_meta( $schedule_id, '_iee_schedule_status', $new_status );
			wp_redirect( remove_query_arg( [ 'action', 'schedule_id', 'new_status', '_wpnonce' ] ) );
			exit;
		}
	}

}
