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
			<h3 class="setting_bar" ><?php esc_attr_e( 'Getting Support', 'import-eventbrite-events' ); ?></h3>

			<div class="iee-support-features">
				<div class="iee-support-features-card">
					<div class="iee-support-features-img">
						<?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
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
						<?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
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
						<?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
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
						<?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
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
						<?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
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
			$plugin_list = array();
			$plugin_list = $iee_events->common->iee_get_xylus_themes_plugins();
		?>
		<div class="" style="margin-top: 20px;">
			<h3 class="setting_bar"><?php esc_html_e( 'Plugins you should try','import-eventbrite-events' ); ?></h3>
			<div class="iee-about-us-plugins">
				<!-- <div class="iee-row"> -->
				<div class="iee-support-features2">
				
					<?php 
						if( !empty( $plugin_list ) ){
							foreach ($plugin_list as $key => $plugin ) {

								$plugin_slug = ucwords( str_replace( '-', ' ', $key ) );
								$plugin_name =  $plugin['plugin_name'];
								$plugin_description =  $plugin['description'];
								if( $key == 'wp-event-aggregator' ){
									$plugin_icon = 'https://ps.w.org/'.$key.'/assets/icon-256x256.jpg';
								} else {
									$plugin_icon = 'https://ps.w.org/'.$key.'/assets/icon-256x256.png';
								}

								// Check if the plugin is installed
								$plugin_installed = false;
								$plugin_active = false;
								include_once(ABSPATH . 'wp-admin/includes/plugin.php');
								$all_plugins = get_plugins();
								$plugin_path = $key . '/' . $key . '.php';

								if (isset($all_plugins[$plugin_path])) {
									$plugin_installed = true;
									$plugin_active = is_plugin_active($plugin_path);
								}

								// Determine the status text
								$status_text = 'Not Installed';
								if ($plugin_installed) {
									$status_text = $plugin_active ? 'Active' : 'Installed (Inactive)';
								}
								
								?>
								<div class="iee-support-features-card2 iee-plugin">
									<div class="iee-plugin-main">
										<div>
											<?php
												// translators: %s: Plugin slug used in image alt text.
												$alt_text = sprintf( esc_attr__( '%s Image', 'import-eventbrite-events' ), $plugin_slug ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											?>
											<?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
											<img alt="<?php echo $alt_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" src="<?php echo esc_url( $plugin_icon ); ?>">
										</div>
										<div>
											<div class="iee-main-name"><?php echo esc_attr( $plugin_slug ); ?></div>
											<div><?php echo esc_attr( $plugin_description ); ?></div>
										</div>
									</div>
									<div class="iee-plugin-footer">
										<div class="iee-footer-status">
											<div class="iee-footer-status-label"><?php esc_attr_e( 'Status : ', 'import-eventbrite-events' ); ?></div>
											<div class="iee-footer-status iee-footer-status-<?php echo esc_attr( strtolower(str_replace(' ', '-', $status_text) ) ); ?>">
												<span <?php echo ( $status_text == 'Active' ) ? 'style="color:green;"' : ''; ?>>
													<?php echo esc_attr( $status_text ); ?>
												</span>
											</div>
										</div>
										<div class="iee-footer-action">
											<?php if (!$plugin_installed): ?>
												<a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=xylus&tab=search&type=term' ) ); ?>" type="button" class="button button-primary">Install Free Plugin</a>
											<?php elseif (!$plugin_active): ?>
												<?php 
													$activate_nonce = wp_create_nonce('activate_plugin_' . $plugin_slug); 
													$activation_url = add_query_arg(array( 'action' => 'activate_plugin', 'plugin_slug' => $plugin_slug, 'nonce' => $activate_nonce, ), admin_url('admin.php?page=eventbrite_event&tab=support'));
												?>
												<a href="<?php echo esc_url( admin_url( 'plugins.php?s='. $plugin_name ) ); ?>" class="button button-primary">Activate Plugin</a>
											<?php endif; ?>
										</div>
									</div>
								</div>
								<?php
							}
						}
					?>
				</div>
			</div>
			<div style="clear: both;">
		</div>
	</div>
</div>

