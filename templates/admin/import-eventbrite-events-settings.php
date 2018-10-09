<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$iee_options        = get_option( IEE_OPTIONS );
$eventbrite_options = isset( $iee_options ) ? $iee_options : array();
?>
<div class="iee_container">
	<div class="iee_row">
	
			<form method="post" id="iee_setting_form">                

			<h3 class="setting_bar"><?php esc_attr_e( 'Eventbrite Settings', 'import-eventbrite-events' ); ?></h3>
			<p><?php _e( 'You need a Eventbrite Personal OAuth token to import your events from Eventbrite.', 'import-eventbrite-events' ); ?> </p>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php _e( 'Eventbrite Personal OAuth token', 'import-eventbrite-events' ); ?> : 
						</th>
						<td>
							<input class="eventbrite_oauth_token" name="eventbrite[eventbrite_oauth_token]" type="text" value="<?php if ( isset( $eventbrite_options['eventbrite_oauth_token'] ) ) { echo $eventbrite_options['eventbrite_oauth_token']; } ?>" />
							<span class="xtei_small">
								<?php _e( 'Insert your eventbrite.com Personal OAuth token you can get it from <a href="http://www.eventbrite.com/myaccount/apps/" target="_blank">here</a>.', 'xt-eventbrite-import-pro' ); ?>
							</span>
						</td>
					</tr>		
					<tr>
						<th scope="row">
							<?php _e( 'Display ticket option after event', 'import-eventbrite-events' ); ?> : 
						</th>
						<td>
							<?php
							$enable_ticket_sec = isset( $eventbrite_options['enable_ticket_sec'] ) ? $eventbrite_options['enable_ticket_sec'] : 'no';
							?>
							<input type="checkbox" name="eventbrite[enable_ticket_sec]" value="yes" <?php if ( $enable_ticket_sec == 'yes' ) { echo 'checked="checked"'; } ?> />
							<span class="xtei_small">
								<?php _e( 'Check to display ticket option after event.', 'import-eventbrite-events' ); ?>
							</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e( 'Update existing events', 'import-eventbrite-events' ); ?> : 
						</th>
						<td>
							<?php
							$update_eventbrite_events = isset( $eventbrite_options['update_events'] ) ? $eventbrite_options['update_events'] : 'no';
							?>
							<input type="checkbox" name="eventbrite[update_events]" value="yes" <?php if ( $update_eventbrite_events == 'yes' ) { echo 'checked="checked"'; } ?> />
							<span class="xtei_small">
								<?php _e( 'Check to updates existing events.', 'import-eventbrite-events' ); ?>
							</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e( 'Advanced Synchronization', 'import-eventbrite-events' ); ?> : 
						</th>
						<td>
							<?php
							$advanced_sync = isset( $eventbrite_options['advanced_sync'] ) ? $eventbrite_options['advanced_sync'] : 'no';
							?>
							<input type="checkbox" name="eventbrite[advanced_sync]" value="yes" <?php if ( $advanced_sync == 'yes' ) { echo 'checked="checked"'; } if ( ! iee_is_pro() ) { echo 'disabled="disabled"'; } ?> />
							<span>
								<?php _e( 'Check to enable advanced synchronization, this will delete events which are removed from Eventbrite. Also, it deletes passed events.', 'import-eventbrite-events' ); ?>
							</span>
							<?php do_action( 'iee_render_pro_notice' ); ?>
						</td>
					</tr>
					<?php do_action( 'iee_after_eventbrite_settings_section' ); ?> 
				</tbody>
			</table>
			<br/>

			<h3 class="setting_bar"><?php esc_attr_e( 'Import Eventbrite Events Settings', 'import-eventbrite-events' ); ?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<?php _e( 'Disable Eventbrite Events', 'import-eventbrite-events' ); ?> : 
						</th>
						<td>
							<?php
							$deactive_ieevents = isset( $eventbrite_options['deactive_ieevents'] ) ? $eventbrite_options['deactive_ieevents'] : 'no';
							?>
							<input type="checkbox" name="eventbrite[deactive_ieevents]" value="yes" <?php if ( $deactive_ieevents == 'yes' ) {
								echo 'checked="checked"'; } ?> />
							<span>
								<?php _e( 'Check to disable inbuilt event management system.', 'import-eventbrite-events' ); ?>
							</span>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php _e( 'Delete Import Eventbrite Events data on Uninstall', 'import-eventbrite-events' ); ?> : 
						</th>
						<td>
							<?php
							$delete_ieedata = isset( $eventbrite_options['delete_ieedata'] ) ? $eventbrite_options['delete_ieedata'] : 'no';
							?>
							<input type="checkbox" name="eventbrite[delete_ieedata]" value="yes" <?php if ( $delete_ieedata == 'yes' ) { echo 'checked="checked"'; } ?> />
							<span>
								<?php _e( 'Delete Import Eventbrite Events data like settings, scheduled imports, import history on Uninstall', 'import-eventbrite-events' ); ?>
							</span>
						</td>
					</tr>
					<?php do_action( 'iee_after_settings_section' ); ?>
				</tbody>
			</table>
			<br/>

			<div class="iee_element">
				<input type="hidden" name="iee_action" value="iee_save_settings" />
				<?php wp_nonce_field( 'iee_setting_form_nonce_action', 'iee_setting_form_nonce' ); ?>
				<input type="submit" class="button-primary xtei_submit_button" style=""  value="<?php esc_attr_e( 'Save Settings', 'import-eventbrite-events' ); ?>" />
			</div>
			</form>
	</div>
</div>
