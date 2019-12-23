<?php
// If this file is called directly, abort.
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
	<div class="wrap"style="min-width: 100%;">
            <h3 class="setting_bar"><?php esc_attr_e( 'Getting Support', 'import-eventbrite-events' ); ?></h3>
            <div class="xylus-support-page">
                <div class="support-block">
                    <img src="<?php echo plugin_dir_url( dirname( dirname( __FILE__ ) ) ).'assets/images/target.png'; ?>" alt="Looking for Something?">
                    <h3>Looking for Something?</h3>
                    <p>We have documentation of how to import eventbrite events.</p>
                    <a target="_blank" class="button button-primary" href="https://docs.xylusthemes.com/docs/import-eventbrite-events-plugin/">Visit the Plugin Documentation</a>
                </div>

                <div class="support-block">
                    <img src="<?php echo plugin_dir_url( dirname( dirname( __FILE__ ) ) ).'assets/images/assistance.png'; ?>" alt="Need Any Assistance?">
                    <h3>Need Any Assistance?</h3>
                    <p>Our EXPERT Support Team is always ready to Help you out.</p>
                    <a target="_blank" class="button button-primary" href="https://xylusthemes.com/support/">Contact Support</a>
                </div>

                <div class="support-block">
                    <img src="<?php echo plugin_dir_url( dirname( dirname( __FILE__ ) ) ).'assets/images/bug.png'; ?>" alt="Found Any Bugs?">
                    <h3>Found Any Bugs?</h3>
                    <p>Report any Bug that you Discovered, Get Instant Solutions.</p>
                    <a target="_blank" class="button button-primary" href="https://github.com/xylusthemes/import-eventbrite-events">Report to GitHub</a>
                </div>

                <div class="support-block">
                    <img src="<?php echo plugin_dir_url( dirname( dirname( __FILE__ ) ) ).'assets/images/tools.png'; ?>" alt="Require Customization?">
                    <h3>Require Customization?</h3>
                    <p>We would Love to hear your Integration and Customization Ideas.</p>
                    <a target="_blank" class="button button-primary" href="https://xylusthemes.com/what-we-do/">Connect Our Service</a>
                </div>

                <div class="support-block">
                    <img src="<?php echo plugin_dir_url( dirname( dirname( __FILE__ ) ) ).'assets/images/like.png'; ?>" alt="Like The Plugin?">
                    <h3>Like The Plugin?</h3>
                    <p>Your Review is very important to us as it helps us to grow more.</p>
                    <a target="_blank" class="button button-primary" href="https://wordpress.org/support/plugin/import-eventbrite-events/reviews/?rate=5#new-post">Review US on WP.org</a>
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
		<div class="" style="margin-top: 20px;">
			<h3 class="setting_bar"><?php _e( 'Plugins you should try', 'import-eventbrite-events' ); ?></h3>
			<?php
			if ( ! empty( $plugins ) ) {
				foreach ( $plugins as $plugin ) {
					?>
					<div class="plugin_box">
						<?php if ( $plugin->banners['low'] != '' ) { ?>
							<img src="<?php echo $plugin->banners['low']; ?>" class="plugin_img" title="<?php echo $plugin->name; ?>">
						<?php } ?>                    
						<div class="plugin_content">
							<h3><?php echo $plugin->name; ?></h3>

							<?php
							wp_star_rating(
								array(
									'rating' => $plugin->rating,
									'type'   => 'percent',
									'number' => $plugin->num_ratings,
								)
							);
							?>

							<?php if ( $plugin->version != '' ) { ?>
								<p><strong><?php _e( 'Version:', 'import-eventbrite-events' ); ?> </strong><?php echo $plugin->version; ?></p>
							<?php } ?>

							<?php if ( $plugin->requires != '' ) { ?>
								<p>
									<strong><?php _e( 'Requires:', 'import-eventbrite-events' ); ?> </strong> <?php _e( 'WordPress ', 'import-eventbrite-events' ); echo $plugin->requires; ?>+</p>
							<?php } ?>

							<?php if ( $plugin->active_installs != '' ) { ?>
								<p><strong><?php _e( 'Active Installs:', 'import-eventbrite-events' ); ?> </strong><?php echo $plugin->active_installs; ?>+</p>
							<?php } ?>

							<?php // print_r( $plugin ); ?>
							<a class="button button-secondary" href="<?php echo admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin->slug . '&TB_iframe=1&width=772&height=600' ); ?>" target="_blank">
								<?php _e( 'Install Now', 'import-eventbrite-events' ); ?>
							</a>
							<a class="button button-primary" href="<?php echo $plugin->homepage . '?utm_source=crosssell&utm_medium=web&utm_content=supportpage&utm_campaign=freeplugin'; ?>" target="_blank">
								<?php _e( 'Buy Now', 'import-eventbrite-events' ); ?>
							</a>
						</div>
					</div>
					<?php
				}
			}
			?>
			<div style="clear: both;">
		</div>
	</div>

</div>
