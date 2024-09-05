<?php
// If this file is called directly, abort.
// Icon Credit: Icon made by Freepik and Vectors Market from www.flaticon.com
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $iee_events;
$open_source_support_url = 'https://wordpress.org/support/plugin/import-eventbrite-events/';
$support_url             = 'https://xylusthemes.com/support/?utm_source=insideplugin&utm_medium=web&utm_content=sidebar&utm_campaign=freeplugin';

$review_url   = 'https://wordpress.org/support/plugin/import-eventbrite-events/reviews/?rate=5#new-post';
$facebook_url = 'https://www.facebook.com/xylusinfo/';
$twitter_url  = 'https://twitter.com/XylusThemes/';

?>
<div class="wpea_container">
	<div class="wpea_row">
		<div class="wpea-column support_well">
			<h3><?php esc_attr_e( 'Getting Support', 'import-eventbrite-events' ); ?></h3>

			<div class="iee-support-features">
				<div class="iee-support-features-card">
					<div class="iee-support-features-img">
						<img class="iee-support-features-icon" src="<?php echo esc_url( IEE_PLUGIN_URL.'assets/images/document.svg' ); ?>" alt="<?php esc_attr_e( 'Looking for Something?', 'import-eventbrite-events' ); ?>">
					</div>
					<div class="iee-support-features-text">
						<h3 class="iee-support-features-title"><?php esc_attr_e( 'Looking for Something?', 'import-eventbrite-events' ); ?></h3>
						<p><?php esc_attr_e( 'We have documentation of how to import eventbrite events.', 'import-eventbrite-events' ); ?></p>
						<a target="_blank" class="button button-primary" href="http://docs.xylusthemes.com/docs/import-eventbrite-events-plugin/"><?php esc_attr_e( 'Plugin Documentation', 'import-eventbrite-events' ); ?></a>
					</div>
				</div>
				<div class="iee-support-features-card">
					<div class="iee-support-features-img">
						<img class="iee-support-features-icon" src="<?php echo esc_url( IEE_PLUGIN_URL.'assets/images/call-center.svg' ); ?>" alt="<?php esc_attr_e( 'Need Any Assistance?', 'import-eventbrite-events' ); ?>">
					</div>
					<div class="iee-support-features-text">
						<h3 class="iee-support-features-title"><?php esc_attr_e( 'Need Any Assistance?', 'import-eventbrite-events' ); ?></h3>
						<p><?php esc_attr_e( 'Our EXPERT Support Team is always ready to Help you out.', 'import-eventbrite-events' ); ?></p>
						<a target="_blank" class="button button-primary" href="https://xylusthemes.com/support/"><?php esc_attr_e( 'Contact Support', 'import-eventbrite-events' ); ?></a>
					</div>
				</div>
				<div class="iee-support-features-card">
					<div class="iee-support-features-img">
						<img class="iee-support-features-icon"  src="<?php echo esc_url( IEE_PLUGIN_URL.'assets/images/bug.svg' ); ?>" alt="<?php esc_attr_e( 'Found Any Bugs?', 'import-eventbrite-events' ); ?>" />
					</div>
					<div class="iee-support-features-text">
						<h3 class="iee-support-features-title"><?php esc_attr_e( 'Found Any Bugs?', 'import-eventbrite-events' ); ?></h3>
						<p><?php esc_attr_e( 'Report any Bug that you Discovered, Get Instant Solutions.', 'import-eventbrite-events' ); ?></p>
						<a target="_blank" class="button button-primary" href="https://github.com/xylusthemes/import-eventbrite-events"><?php esc_attr_e( 'Report to GitHub', 'import-eventbrite-events' ); ?></a>
					</div>
				</div>
				<div class="iee-support-features-card">
					<div class="iee-support-features-img">
						<img class="iee-support-features-icon" src="<?php echo esc_url( IEE_PLUGIN_URL.'assets/images/tools.svg' ); ?>" alt="<?php esc_attr_e( 'Require Customization?', 'import-eventbrite-events' ); ?>" />
					</div>
					<div class="iee-support-features-text">
						<h3 class="iee-support-features-title"><?php esc_attr_e( 'Require Customization?', 'import-eventbrite-events' ); ?></h3>
						<p><?php esc_attr_e( 'We would Love to hear your Integration and Customization Ideas.', 'import-eventbrite-events' ); ?></p>
						<a target="_blank" class="button button-primary" href="https://xylusthemes.com/what-we-do/"><?php esc_attr_e( 'Connect Our Service', 'import-eventbrite-events' ); ?></a>
					</div>
				</div>
				<div class="iee-support-features-card">
					<div class="iee-support-features-img">
						<img class="iee-support-features-icon" src="<?php echo esc_url( IEE_PLUGIN_URL.'assets/images/like.svg' ); ?>" alt="<?php esc_attr_e( 'Like The Plugin?', 'import-eventbrite-events' ); ?>" />
					</div>
					<div class="iee-support-features-text">
						<h3 class="iee-support-features-title"><?php esc_attr_e( 'Like The Plugin?', 'import-eventbrite-events' ); ?></h3>
						<p><?php esc_attr_e( 'Your Review is very important to us as it helps us to grow more.', 'import-eventbrite-events' ); ?></p>
						<a target="_blank" class="button button-primary" href="https://wordpress.org/support/plugin/import-eventbrite-events/reviews/?rate=5#new-post"><?php esc_attr_e( 'Review Us on WP.org', 'import-eventbrite-events' ); ?></a>
					</div>
				</div>
			</div>
		</div>

		<?php
		$plugins     = array();
		$plugin_list = $iee_events->admin->get_xyuls_themes_plugins();
		if ( ! empty( $plugin_list ) ) {
			foreach ( $plugin_list as $key => $value ) {
				$plugins[] = $iee_events->admin->get_wporg_plugin( $key );
			}
		}
		?>
		<h3><?php _e( 'Plugins you should try', 'import-eventbrite-events' ); ?></h3>
		<div id="iee-addons-list">
			<?php
			if ( ! empty( $plugins ) ) {
				foreach ( $plugins as $plugin ) {				
					$plugin_activation = is_plugin_active( $plugin->slug.'/'. $plugin->slug.'.php' );
					$plugin_not_active = ABSPATH . 'wp-content/plugins/'.$plugin->slug.'/';
					$buy_now = "<a class='iee-status-download button-primary' target='_blank' href='".$plugin->homepage."'>Buy Now</a>";
					?>
					<div class="iee-addon-container">
						<div class="iee-addon-item">
							<div class="iee-details iee-clear" style="height: 165px;">
								<img src="<?php echo $plugin->icons['2x']; ?>">
								<h5 class="iee-addon-name"><?php echo $plugin->name; ?></h5>
								<p class="iee-addon-desc"><?php echo $plugin->short_description; ?></p>
							</div>
							<div class="actions iee-clear">
								<div class="iee-status">
									<strong>
									<?php _e( 'Active Installs: ', 'import-eventbrite-events' ); ?><span class="iee-status-label iee-status-download"><?php echo $plugin->active_installs; ?>+</span></strong>
								</div>
								<div class="iee-action-button">
									
									<?php add_thickbox(); ?>
									<?php if( $plugin_activation == true ){ ?>
										<a class="iee-status-download button-secondary" disabled ><?php _e( 'Actived', 'import-eventbrite-events' ); ?> </a>
										<?php echo $buy_now; ?>
									<?php }elseif( is_dir( $plugin_not_active ) && $plugin_activation == false ){ ?>
										<a class="iee-status-download button-secondary" href="<?php echo admin_url( 'plugins.php' ); ?>" ><?php _e( 'Activate', 'import-eventbrite-events' ); ?></a>
										<?php echo $buy_now; ?>
									<?php }else{ ?>
										<a class="iee-status-download button button-secondary thickbox" href="<?php echo admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin->slug . '&TB_iframe=true&width=772&height=600' ); ?>" >
									<?php _e( 'Install Plugin', 'import-eventbrite-events' ); ?></a>
									<?php echo $buy_now; } ?>
								</div>
							</div>
						</div>
					</div>
				<?php
				}
			}
			?>
			</div>
		<div style="clear: both;">
	</div>
</div>

