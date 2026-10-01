<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $iee_events;
$iee_options        = get_option( IEE_OPTIONS );
$eventbrite_options = isset( $iee_options ) ? $iee_options : array();
$iee_google_maps_api_key = get_option( 'iee_google_maps_api_key', array() );

$iee_ap_options       = get_option( IEE_AP_OPTIONS );
$eventbrite_optionsap = isset( $iee_ap_options ) ? $iee_ap_options : array();
?>

<div class="iee-card" style="margin-top:20px;" >
	<div class="iee-content"  aria-expanded="true" style="padding: 10px 20px;">
		<div id="postbox-container-2" class="postbox-container">
			<div class="">
				<div class="iee-app">
					<div class="iee-tabs">
						<div class="tabs-scroller">
							<div class="var-tabs var-tabs--item-horizontal var-tabs--layout-horizontal-padding iee_navbar nav-tab-wrapper">
								<div class="var-tabs__tab-wrap var-tabs--layout-horizontal iee_nav_tabs">
									<a href="javascript:void(0)" class="var-tab var-tab--active iee_tab_link"  data-tab="settings">
										<span class="tab-label"><?php esc_attr_e( 'General Settings', 'import-eventbrite-events' ); ?></span>
									</a>
									<a href="javascript:void(0)" class="var-tab var-tab--inactive iee_tab_link"  data-tab="appearance">
										<span class="tab-label"><?php esc_attr_e( 'Appearance', 'import-eventbrite-events' ); ?></span>
									</a>
									<a href="javascript:void(0)"  class="var-tab var-tab--inactive iee_tab_link" data-tab="google_maps_key" >
										<span class="tab-label"><?php esc_attr_e( 'Google Maps API', 'import-eventbrite-events' ); ?></span>
									</a>
									<?php if( iee_is_pro() ){ ?>
										<a href="javascript:void(0)"  class="var-tab var-tab--inactive iee_tab_link" data-tab="license">
											<span class="tab-label"><?php esc_attr_e( 'License', 'import-eventbrite-events' ); ?></span>
										</a>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div id="poststuff">
					<div id="settings" class="iee_tab_content var-tab--active" style="margin-top: 15px;">
						<div class="iee_container">
							<div class="iee_row">
								<form method="post" id="iee_setting_form">

									<div class="iee-inner-main-section iee-new-feature" >
                                        <div class="iee-inner-section-1" >
                                            <span class="iee-title-text">
												<?php esc_attr_e( 'Import Event With Standard API', 'import-eventbrite-events' ); ?>
												<br/>
												<?php esc_attr_e( '(No Private Token Required)', 'import-eventbrite-events' ); ?>
											</span>
                                        </div>
                                        <div class="iee-inner-section-2" >
                                            <?php
                                                $using_standard_api = isset( $eventbrite_options['using_standard_api'] ) ? $eventbrite_options['using_standard_api'] : 'no';
                                            ?>
                                            <input type="checkbox" name="eventbrite[using_standard_api]" value="yes" <?php if( $using_standard_api == 'yes' ) { echo 'checked="checked"'; } ?> />
                                            <span class="iee_small">
                                                <strong><?php esc_attr_e( 'Using "Import Event With Standard API" lets you fetch events directly. No Eventbrite private token is required.', 'import-eventbrite-events' ); ?></strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="iee-inner-main-section" >
                                        <div class="eventbrite_or_keyandsecrate">
                                            <span class="iee-title-text" ><?php esc_attr_e( '- OR -', 'import-eventbrite-events' ); ?></span>
                                        </div>
                                    </div> 


									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Eventbrite Private token', 'import-eventbrite-events' ); ?></span> 
										</div>
										<div class="iee-inner-section-2">
											<input class="eventbrite_oauth_token iee_input_w25" name="eventbrite[eventbrite_oauth_token]" type="text" value="<?php if ( isset( $eventbrite_options['eventbrite_oauth_token'] ) ) { echo esc_attr( $eventbrite_options['eventbrite_oauth_token'] ); } ?>" />
											<span >
												<?php echo wp_kses_post( 'Insert your eventbrite.com Private token you can get it from <a href="http://www.eventbrite.com/myaccount/apps/" target="_blank">here</a>.', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Display ticket option after event', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$enable_ticket_sec = isset( $eventbrite_options['enable_ticket_sec'] ) ? $eventbrite_options['enable_ticket_sec'] : 'no';
											$ticket_model = isset( $eventbrite_options['ticket_model'] ) ? $eventbrite_options['ticket_model'] : '0';
											?>
											<input type="checkbox" class="enable_ticket_sec" name="eventbrite[enable_ticket_sec]" value="yes" <?php if ( $enable_ticket_sec == 'yes' ) { echo 'checked="checked"'; } ?> />
											<span>
												<?php esc_attr_e( 'Check to display ticket option after event.', 'import-eventbrite-events' ); ?>
											</span>
											<?php if(is_ssl()){ ?>
											<div class="iee_small checkout_model_option">
												<input type="radio" name="eventbrite[ticket_model]" value="0" <?php checked( $ticket_model, '0'); ?>>
													<?php esc_attr_e( 'Non-Modal Checkout', 'import-eventbrite-events' ); ?><br/>
												<input type="radio" name="eventbrite[ticket_model]" value="1" <?php checked( $ticket_model, '1'); ?>>
													<?php esc_attr_e( 'Popup Checkout Widget (Display your checkout as a modal popup)', 'import-eventbrite-events' ); ?><br/>
											</div>
											<?php } ?>
										</div>
									</div>
									
									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Update existing events', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$update_eventbrite_events = isset( $eventbrite_options['update_events'] ) ? $eventbrite_options['update_events'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[update_events]" value="yes" <?php if ( $update_eventbrite_events == 'yes' ) { echo 'checked="checked"'; } ?> />
											<span class="iee_small">
												<?php esc_attr_e( 'Check to updates existing events.', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Automatically Import and Assign Eventbrite Categories', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$eventbritre_category = isset( $eventbrite_options['eventbritre_category'] ) ? $eventbrite_options['eventbritre_category'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[eventbritre_category]" value="yes" <?php if ( $eventbritre_category == 'yes' ) { echo 'checked="checked"'; } ?> />
											<span class="iee_small">
												<?php esc_html_e( 'Enable this option to automatically import Eventbrite categories and assign them in events.', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Automatically Import and Assign Eventbrite Tags', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$eventbritre_tags = isset( $eventbrite_options['eventbritre_tags'] ) ? $eventbrite_options['eventbritre_tags'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[eventbritre_tags]" value="yes" <?php if ( $eventbritre_tags == 'yes' ) { echo 'checked="checked"'; } ?> />
											<span class="iee_small">
												<?php esc_html_e( 'Enable this option to automatically import Eventbrite tags and assign them in events.', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Move past events in trash', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$move_peit = isset( $eventbrite_options['move_peit'] ) ? $eventbrite_options['move_peit'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[move_peit]" value="yes" <?php if ( $move_peit == 'yes' ) { echo 'checked="checked"'; } ?> />
											<span class="iee_small">
												<?php esc_attr_e( 'Check to move past events in the trash, Automatically move events to the trash 24 hours after their end date using wp-cron. This runs once daily in the background.', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>
									
									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Skip Trashed Events', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$skip_trash = isset( $eventbrite_options['skip_trash'] ) ? $eventbrite_options['skip_trash'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[skip_trash]" value="yes" <?php if ( $skip_trash == 'yes' ) { echo 'checked="checked"'; } if ( ! iee_is_pro() ) { echo 'disabled="disabled"'; } ?> />
											<span class="iee_small">
												<?php esc_attr_e( 'Check to enable skip-the-trash events during importing.', 'import-eventbrite-events' ); ?>
											</span>
											<?php do_action( 'iee_render_pro_notice' ); ?>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Accent Color', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$accent_color = isset( $eventbrite_options['accent_color'] ) ? $eventbrite_options['accent_color'] : '#039ED7';
											?>
											<input class="iee_color_field" type="text" name="eventbrite[accent_color]" value="<?php echo esc_attr( $accent_color ); ?>"/>
											<span class="iee_small">
												<?php esc_attr_e( 'Choose accent color for front-end event grid and event widget.', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Direct link to Eventbrite', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$direct_link = isset( $eventbrite_options['direct_link'] ) ? $eventbrite_options['direct_link'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[direct_link]" value="yes" <?php if ( $direct_link == 'yes' ) { echo 'checked="checked"'; } if ( ! iee_is_pro() ) { echo 'disabled="disabled"'; } ?> />
											<span class="iee_small">
												<?php esc_attr_e( 'Check to enable direct event link to eventbrite instead of event detail page.', 'import-eventbrite-events' ); ?>
											</span>
											<?php do_action( 'iee_render_pro_notice' ); ?>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Advanced Synchronization', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$advanced_sync = isset( $eventbrite_options['advanced_sync'] ) ? $eventbrite_options['advanced_sync'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[advanced_sync]" value="yes" <?php if ( $advanced_sync == 'yes' ) { echo 'checked="checked"'; } if ( ! iee_is_pro() ) { echo 'disabled="disabled"'; } ?> />
											<span class="iee_small">
												<?php esc_attr_e( 'Check to enable advanced synchronization, this will delete events which are removed from Eventbrite. Also, it deletes passed events.', 'import-eventbrite-events' ); ?>
											</span>
											<?php do_action( 'iee_render_pro_notice' ); ?>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Import Small Event Thumbnail', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$small_thumbnail = isset( $eventbrite_options['small_thumbnail'] ) ? $eventbrite_options['small_thumbnail'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[small_thumbnail]" value="yes" <?php if ( $small_thumbnail == 'yes' ) { echo 'checked="checked"'; } ?> />
											<span class="iee_small">
												<?php esc_attr_e( 'You can import small thumbnails of events into an event by enabling this option.', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Do Not Import Event Image', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$skip_image_import = isset( $eventbrite_options['skip_image_import'] ) ? $eventbrite_options['skip_image_import'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[skip_image_import]" value="yes" <?php if ( $skip_image_import == 'yes' ) { echo 'checked="checked"'; } ?> />
											<span class="iee_small">
												<?php esc_attr_e( 'Check to save the Eventbrite source image URL and display it without downloading media as the featured image.', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>

									<?php
									$private_events     = isset( $eventbrite_options['private_events'] ) ? $eventbrite_options['private_events'] : 'no';
									$using_standard_api = isset( $eventbrite_options['using_standard_api'] ) ? $eventbrite_options['using_standard_api'] : 'no';
									$disable_section    = ! iee_is_pro() || $using_standard_api == 'yes';
									?>

									<div class="iee-inner-main-section" <?php echo $disable_section ? 'style="opacity:0.5; pointer-events:none;"' : ''; ?>>
										<div class="iee-inner-section-1">
											<span class="iee-title-text"><?php esc_attr_e( 'Import Private Events', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<input type="checkbox" name="eventbrite[private_events]" value="yes"
												<?php 
												if ( $private_events == 'yes' ) { echo 'checked="checked"'; } 
												if ( $disable_section ) { echo 'disabled="disabled"'; } 
												?> 
											/>
											<span class="iee_small">
												<?php esc_attr_e( 'Tick to import Private events, Untick to not import private event.', 'import-eventbrite-events' ); ?>
											</span>
											<?php if ( $disable_section ): ?>
												<div class="iee_notice" style="margin-top:5px; color:#d63638; font-size:13px;">
													<?php esc_html_e( 'This option only works with a eventbrite private token.', 'import-eventbrite-events' ); ?>
												</div>
											<?php endif; ?>
											<?php do_action( 'iee_render_pro_notice' ); ?>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( "Don't Update these data", "import-eventbrite-events" ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$dont_update_sc = isset($eventbrite_options['dont_update'])? $eventbrite_options['dont_update'] : array();
											$sdontupdate = isset( $dont_update_sc['status'] ) ? $dont_update_sc['status'] : 'no';
											$cdontupdate = isset( $dont_update_sc['category'] ) ? $dont_update_sc['category'] : 'no';
											$tdontupdate = isset( $dont_update_sc['tag'] ) ? $dont_update_sc['tag'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[dont_update][status]" value="yes" <?php checked( $sdontupdate, 'yes' ); disabled( iee_is_pro(), false );?> />
											<span>
												<?php esc_attr_e( 'Status ( Publish, Pending, Draft etc.. )', 'import-eventbrite-events' ); ?>
											</span><br/>
											<input type="checkbox" name="eventbrite[dont_update][category]" value="yes" <?php checked( $cdontupdate, 'yes' ); disabled( iee_is_pro(), false );?> />
											<span>
												<?php esc_attr_e( 'Event category', 'import-eventbrite-events' ); ?>
											</span><br/>
											<input type="checkbox" name="eventbrite[dont_update][tag]" value="yes" <?php checked( $tdontupdate, 'yes' ); disabled( iee_is_pro(), false );?> />
											<span>
												<?php esc_attr_e( 'Event tag', 'import-eventbrite-events' ); ?>
											</span><br/>
											<span class="iee_small">
												<?php esc_attr_e( "Select data which you don't want to update during existing events update. (This is applicable only if you have checked 'update existing events')", 'import-eventbrite-events' ); ?>
											</span>
											<?php do_action('iee_render_pro_notice'); ?>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e('Event Slug', 'import-eventbrite-events'); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$event_slug = isset($eventbrite_options['event_slug']) ? $eventbrite_options['event_slug'] : 'eventbrite-event';
											?>
											<input type="text" class="iee_input_w25" name="eventbrite[event_slug]" value="<?php if ( $event_slug ) { echo esc_attr( $event_slug ); } ?>" <?php if (!iee_is_pro()) { echo 'disabled="disabled"'; } ?> />
											<span class="iee_small">
												<?php esc_attr_e('Slug for the event.', 'import-eventbrite-events'); ?>
											</span>
											<?php do_action('iee_render_pro_notice'); ?>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Event Display Time Format', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$time_format = isset( $eventbrite_options['time_format'] ) ? $eventbrite_options['time_format'] : '12hours';
											?>
											<select name="eventbrite[time_format]" class="iee_input_w25">
												<option value="12hours" <?php selected('12hours', $time_format); ?>><?php esc_attr_e( '12 Hours', 'import-eventbrite-events' );  ?></option>
												<option value="24hours" <?php selected('24hours', $time_format); ?>><?php esc_attr_e( '24 Hours', 'import-eventbrite-events' ); ?></option>						
												<option value="wordpress_default" <?php selected('wordpress_default', $time_format); ?>><?php esc_attr_e( 'WordPress Default', 'import-eventbrite-events' ); ?></option>
											</select>
											<span class="iee_small">
												<?php esc_attr_e( 'Choose event display time format for front-end.', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>

									<?php do_action( 'iee_after_eventbrite_settings_section' ); ?> 
									
									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Disable Eventbrite Events', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$deactive_ieevents = isset( $eventbrite_options['deactive_ieevents'] ) ? $eventbrite_options['deactive_ieevents'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[deactive_ieevents]" value="yes" <?php if ( $deactive_ieevents == 'yes' ) { echo 'checked="checked"'; } ?> />
											<span class="iee_small">
												<?php esc_attr_e( 'Check to disable inbuilt event management system.', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Delete Import Eventbrite Events data on Uninstall', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$delete_ieedata = isset( $eventbrite_options['delete_ieedata'] ) ? $eventbrite_options['delete_ieedata'] : 'no';
											?>
											<input type="checkbox" name="eventbrite[delete_ieedata]" value="yes" <?php if ( $delete_ieedata == 'yes' ) { echo 'checked="checked"'; } ?> />
											<span class="iee_small">
												<?php esc_attr_e( 'Delete Import Eventbrite Events data like settings, scheduled imports, import history on Uninstall', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>
									<?php do_action( 'iee_after_settings_section' ); ?>

									<div class="" style="margin-bottom: 5px;">
										<input type="hidden" name="iee_action" value="iee_save_settings" />
										<?php wp_nonce_field( 'iee_setting_form_nonce_action', 'iee_setting_form_nonce' ); ?>
										<input type="submit" class="iee_button" style=""  value="<?php esc_attr_e( 'Save Settings', 'import-eventbrite-events' ); ?>" />
									</div>
								</form>
							</div>
						</div>
					</div>

					<div id="appearance" class="iee_tab_content" style="margin-top: 15px;">
						<div class="iee_container">
							<div class="iee_row">
								<form method="post" id="iee_setting_form">
									<div class="iee-inner-main-section">
										<div class="iee-inner-section-1">
											<span class="iee-title-text"><?php esc_attr_e( 'Single Event Details Layout', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$details_layout = isset( $eventbrite_optionsap['details_layout'] ) ? $eventbrite_optionsap['details_layout'] : 'default';
											$is_pro = iee_is_pro();
											?>
											<select name="eventbrite_ap[details_layout]" id="iee_details_layout" class="iee_input_w25">
												<option value="default" <?php selected( $details_layout, 'default' ); ?>><?php esc_html_e( 'Default Template', 'import-eventbrite-events' ); ?></option>
												<option value="template2" <?php selected( $details_layout, 'template2' ); ?> <?php echo ! $is_pro ? 'disabled' : ''; ?>><?php esc_html_e( 'Template 2 (Sidebar Layout)', 'import-eventbrite-events' ); ?><?php echo ! $is_pro ? ' — Pro' : ''; ?></option>
												<option value="template3" <?php selected( $details_layout, 'template3' ); ?> <?php echo ! $is_pro ? 'disabled' : ''; ?>><?php esc_html_e( 'Template 3 (Modern Grid Layout)', 'import-eventbrite-events' ); ?><?php echo ! $is_pro ? ' — Pro' : ''; ?></option>
												<option value="template4" <?php selected( $details_layout, 'template4' ); ?> <?php echo ! $is_pro ? 'disabled' : ''; ?>><?php esc_html_e( 'Template 4 (Split Screen Layout)', 'import-eventbrite-events' ); ?><?php echo ! $is_pro ? ' — Pro' : ''; ?></option>
												<option value="gutenberg" <?php selected( $details_layout, 'gutenberg' ); ?> <?php echo ! $is_pro ? 'disabled' : ''; ?>><?php esc_html_e( 'Custom Gutenberg Builder', 'import-eventbrite-events' ); ?><?php echo ! $is_pro ? ' — Pro' : ''; ?></option>
												<option value="elementor" <?php selected( $details_layout, 'elementor' ); ?> <?php echo ! $is_pro ? 'disabled' : ''; ?>><?php esc_html_e( 'Custom Elementor Builder', 'import-eventbrite-events' ); ?><?php echo ! $is_pro ? ' — Pro' : ''; ?></option>
											</select>
											<br/>
											<span class="iee_small">
												<?php esc_attr_e( 'Choose the layout template for the single event details page.', 'import-eventbrite-events' ); ?>
											</span>
											<?php if ( ! $is_pro ) : ?>
											<br/>
											<span class="iee_small" style="color: #e67e22; font-weight: 600;">
												🔒 <?php printf( esc_html__( 'Premium templates are available in the Pro version. %s', 'import-eventbrite-events' ), '<a href="https://xylusthemes.com/plugins/import-eventbrite-events/" target="_blank" style="color: #039ED7; font-weight: 700;">' . esc_html__( 'Upgrade to Pro', 'import-eventbrite-events' ) . '</a>' ); ?>
											</span>
											<?php endif; ?>
										</div>
									</div>

									<?php if ( iee_is_pro() ) : ?>
									<div class="iee-inner-main-section" id="iee_sidebar_position_select" style="display: <?php echo $details_layout === 'template2' ? 'flex' : 'none'; ?>;">
										<div class="iee-inner-section-1">
											<span class="iee-title-text"><?php esc_attr_e( 'Sidebar Position', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$sidebar_position = isset( $eventbrite_optionsap['sidebar_position'] ) ? $eventbrite_optionsap['sidebar_position'] : 'right';
											?>
											<select name="eventbrite_ap[sidebar_position]" id="iee_sidebar_position" class="iee_input_w25">
												<option value="right" <?php selected( $sidebar_position, 'right' ); ?>><?php esc_html_e( 'Right Sidebar', 'import-eventbrite-events' ); ?></option>
												<option value="left" <?php selected( $sidebar_position, 'left' ); ?>><?php esc_html_e( 'Left Sidebar', 'import-eventbrite-events' ); ?></option>
											</select>
											<br/>
											<span class="iee_small">
												<?php esc_attr_e( 'Choose whether the sidebar should be on the left or the right side.', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>

									<div class="iee-inner-main-section" id="iee_gutenberg_page_select" style="display: <?php echo $details_layout === 'gutenberg' ? 'flex' : 'none'; ?>;">
										<div class="iee-inner-section-1">
											<span class="iee-title-text"><?php esc_attr_e( 'Select Custom Template Page', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$gutenberg_page_id = isset( $eventbrite_optionsap['gutenberg_page_id'] ) ? $eventbrite_optionsap['gutenberg_page_id'] : '';
											$gutenberg_mode = empty( $gutenberg_page_id ) ? 'auto' : 'manual';
											?>
											<div style="margin-bottom: 15px;">
												<label>
													<input type="radio" name="iee_gutenberg_mode" value="auto" <?php checked($gutenberg_mode, 'auto'); ?>> <?php esc_html_e('Auto-Generate Template', 'import-eventbrite-events'); ?>
												</label>
												&nbsp;&nbsp;
												<label>
													<input type="radio" name="iee_gutenberg_mode" value="manual" <?php checked($gutenberg_mode, 'manual'); ?>> <?php esc_html_e('Choose Existing Page', 'import-eventbrite-events'); ?>
												</label>
											</div>

											<div id="iee_gutenberg_manual_wrap" style="display: <?php echo $gutenberg_mode === 'manual' ? 'block' : 'none'; ?>;">
												<?php
												wp_dropdown_pages( array(
													'name'              => 'eventbrite_ap[gutenberg_page_id]',
													'echo'              => 1,
													'show_option_none'  => __( '&mdash; Select Page &mdash;', 'import-eventbrite-events' ),
													'option_none_value' => '0',
													'selected'          => $gutenberg_page_id,
													'class'             => 'iee_input_w25',
													'id'                => 'iee_gutenberg_page_dropdown'
												) );
												?>
												<br/>
												<span class="iee_small">
													<?php esc_attr_e( 'Select the page you built with IEE Gutenberg blocks to use as the template for single events.', 'import-eventbrite-events' ); ?>
												</span>
											</div>

											<div id="iee_gutenberg_auto_wrap" style="display: <?php echo $gutenberg_mode === 'auto' ? 'block' : 'none'; ?>; padding: 15px; background: #f9f9f9; border-left: 4px solid #039ED7;">
												<strong><?php esc_attr_e( 'Select a pre-built layout to generate:', 'import-eventbrite-events' ); ?></strong>
												<br/>
												<select id="iee_auto_template_select" style="margin-top: 10px;">
													<option value="layout1"><?php esc_html_e( 'Premium Template (Recommended)', 'import-eventbrite-events' ); ?></option>
													<option value="layout3"><?php esc_html_e( 'Exclusive Experience Template', 'import-eventbrite-events' ); ?></option>
													<option value="layout4"><?php esc_html_e( 'Featured Gathering Template', 'import-eventbrite-events' ); ?></option>
												</select>
												<button type="button" class="button" id="iee_generate_template_btn" style="vertical-align: top; margin-top: 10px; margin-left: 5px;"><?php esc_html_e( 'Create & Select', 'import-eventbrite-events' ); ?></button>
												<span class="spinner" id="iee_generate_spinner" style="float: none; margin-top: 10px;"></span>
												<br/>
												<span class="iee_small" style="color: #555;"><?php esc_html_e( 'This will create a new WordPress Page with pre-configured Gutenberg blocks and automatically select it.', 'import-eventbrite-events' ); ?></span>
											</div>
										</div>
									</div>

									<div class="iee-inner-main-section" id="iee_elementor_page_select" style="display: <?php echo $details_layout === 'elementor' ? 'flex' : 'none'; ?>;">
										<div class="iee-inner-section-1">
											<span class="iee-title-text"><?php esc_attr_e( 'Select Elementor Template Page', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$elementor_page_id = isset( $eventbrite_optionsap['elementor_page_id'] ) ? $eventbrite_optionsap['elementor_page_id'] : '';
											
											// Get regular pages
											$pages = get_posts( array(
												'post_type'      => 'page',
												'post_status'    => 'publish',
												'posts_per_page' => -1,
												'orderby'        => 'title',
												'order'          => 'ASC',
											) );

											// Get Elementor saved templates
											$elementor_templates = get_posts( array(
												'post_type'      => 'elementor_library',
												'post_status'    => 'publish',
												'posts_per_page' => -1,
												'orderby'        => 'title',
												'order'          => 'ASC',
											) );
											?>
											<?php $elementor_mode = empty( $elementor_page_id ) ? 'auto' : 'manual'; ?>
											<div style="margin-bottom: 15px;">
												<label>
													<input type="radio" name="iee_elementor_mode" value="auto" <?php checked($elementor_mode, 'auto'); ?>> <?php esc_html_e('Auto-Generate Template', 'import-eventbrite-events'); ?>
												</label>
												&nbsp;&nbsp;
												<label>
													<input type="radio" name="iee_elementor_mode" value="manual" <?php checked($elementor_mode, 'manual'); ?>> <?php esc_html_e('Choose Existing Page', 'import-eventbrite-events'); ?>
												</label>
											</div>

											<div id="iee_elementor_manual_wrap" style="display: <?php echo $elementor_mode === 'manual' ? 'block' : 'none'; ?>; padding-bottom: 10px;">
												<select name="eventbrite_ap[elementor_page_id]" id="iee_elementor_page_dropdown" class="iee_input_w25">
													<option value="0"><?php esc_html_e( '&mdash; Select Page &mdash;', 'import-eventbrite-events' ); ?></option>
													<?php if ( ! empty( $pages ) ) : ?>
														<optgroup label="<?php esc_attr_e( 'Pages', 'import-eventbrite-events' ); ?>">
															<?php foreach ( $pages as $page_item ) : ?>
																<option value="<?php echo esc_attr( $page_item->ID ); ?>" <?php selected( $elementor_page_id, $page_item->ID ); ?>><?php echo esc_html( $page_item->post_title ); ?></option>
															<?php endforeach; ?>
														</optgroup>
													<?php endif; ?>
													<?php if ( ! empty( $elementor_templates ) ) : ?>
														<optgroup label="<?php esc_attr_e( 'Elementor Saved Templates', 'import-eventbrite-events' ); ?>">
															<?php foreach ( $elementor_templates as $template_item ) : ?>
																<option value="<?php echo esc_attr( $template_item->ID ); ?>" <?php selected( $elementor_page_id, $template_item->ID ); ?>><?php echo esc_html( $template_item->post_title ); ?></option>
															<?php endforeach; ?>
														</optgroup>
													<?php endif; ?>
												</select>
												<br/>
												<span class="iee_small">
													<?php esc_attr_e( 'Select the page or template you built with IEE Elementor widgets to use as the template for single events.', 'import-eventbrite-events' ); ?>
												</span>
											</div>
											<div id="iee_elementor_auto_wrap" style="display: <?php echo $elementor_mode === 'auto' ? 'block' : 'none'; ?>; padding: 15px; background: #f9f9f9; border-left: 4px solid #039ED7;">
												<strong><?php esc_attr_e( 'Select a pre-built Elementor layout to generate:', 'import-eventbrite-events' ); ?></strong>
												<br/>
												<select id="iee_auto_elementor_template_select" style="margin-top: 10px;">
													<option value="layout2"><?php esc_html_e( 'Advanced Elementor Template', 'import-eventbrite-events' ); ?></option>
												</select>
												<button type="button" class="button" id="iee_generate_elementor_template_btn" style="vertical-align: top; margin-top: 10px; margin-left: 5px;"><?php esc_html_e( 'Create & Select', 'import-eventbrite-events' ); ?></button>
												<span class="spinner" id="iee_generate_elementor_spinner" style="float: none; margin-top: 10px;"></span>
												<br/>
												<span class="iee_small" style="color: #555;"><?php esc_html_e( 'This will create a new Elementor Saved Template with pre-configured widgets and automatically select it.', 'import-eventbrite-events' ); ?></span>
											</div>
										</div>
									</div>

									<script>
										jQuery(document).ready(function($) {
											$('input[name="iee_gutenberg_mode"]').on('change', function() {
												if ($(this).val() === 'manual') {
													$('#iee_gutenberg_manual_wrap').show();
													$('#iee_gutenberg_auto_wrap').hide();
												} else {
													$('#iee_gutenberg_manual_wrap').hide();
													$('#iee_gutenberg_auto_wrap').show();
												}
											});

											$('input[name="iee_elementor_mode"]').on('change', function() {
												if ($(this).val() === 'manual') {
													$('#iee_elementor_manual_wrap').show();
													$('#iee_elementor_auto_wrap').hide();
												} else {
													$('#iee_elementor_manual_wrap').hide();
													$('#iee_elementor_auto_wrap').show();
												}
											});

											$('#iee_details_layout').on('change', function() {
												if ($(this).val() === 'gutenberg') {
													$('#iee_gutenberg_page_select').css('display', 'flex');
												} else {
													$('#iee_gutenberg_page_select').hide();
												}
												
												if ($(this).val() === 'elementor') {
													$('#iee_elementor_page_select').css('display', 'flex');
												} else {
													$('#iee_elementor_page_select').hide();
												}
												
												if ($(this).val() === 'template2') {
													$('#iee_sidebar_position_select').css('display', 'flex');
												} else {
													$('#iee_sidebar_position_select').hide();
												}
											});

											$('#iee_generate_template_btn').on('click', function(e) {
												e.preventDefault();
												var templateId = $('#iee_auto_template_select').val();
												var $btn = $(this);
												var $spinner = $('#iee_generate_spinner');
												
												if ( confirm('<?php echo esc_js( __( "This will create a new Page with the selected Gutenberg layout. Proceed?", "import-eventbrite-events" ) ); ?>') ) {
													$btn.prop('disabled', true);
													$spinner.addClass('is-active');
													
													$.ajax({
														url: ajaxurl,
														type: 'POST',
														data: {
															action: 'iee_create_gutenberg_template',
															template_id: templateId,
															nonce: '<?php echo wp_create_nonce( "iee_create_template_nonce" ); ?>'
														},
														success: function(response) {
															$btn.prop('disabled', false);
															$spinner.removeClass('is-active');
															
															if ( response.success ) {
																alert(response.data.message);
																
																// Add the new option to the dropdown and select it
																var newOption = new Option(response.data.page_title, response.data.page_id, true, true);
																$('#iee_gutenberg_page_dropdown').append(newOption).trigger('change');
																
																// Auto submit the form to save the settings
																$btn.closest('form').submit();
															} else {
																alert('Error: ' + (response.data.message || response.data));
															}
														},
														error: function() {
															$btn.prop('disabled', false);
															$spinner.removeClass('is-active');
															alert('<?php echo esc_js( __( "An error occurred. Please try again.", "import-eventbrite-events" ) ); ?>');
														}
													});
												}
											});

											$('#iee_generate_elementor_template_btn').on('click', function(e) {
												e.preventDefault();
												var templateId = $('#iee_auto_elementor_template_select').val();
												var $btn = $(this);
												var $spinner = $('#iee_generate_elementor_spinner');
												
												if ( confirm('<?php echo esc_js( __( "This will create a new Elementor Saved Template. Proceed?", "import-eventbrite-events" ) ); ?>') ) {
													$btn.prop('disabled', true);
													$spinner.addClass('is-active');
													
													$.ajax({
														url: ajaxurl,
														type: 'POST',
														data: {
															action: 'iee_create_elementor_template',
															template_id: templateId,
															nonce: '<?php echo wp_create_nonce( "iee_create_template_nonce" ); ?>'
														},
														success: function(response) {
															$btn.prop('disabled', false);
															$spinner.removeClass('is-active');
															
															if ( response.success ) {
																alert(response.data.message);
																
																// Find the Elementor optgroup or append it if it doesn't exist
																var $optgroup = $('#iee_elementor_page_dropdown optgroup[label="Elementor Saved Templates"]');
																if ( $optgroup.length === 0 ) {
																	$('#iee_elementor_page_dropdown').append('<optgroup label="Elementor Saved Templates"></optgroup>');
																	$optgroup = $('#iee_elementor_page_dropdown optgroup[label="Elementor Saved Templates"]');
																}
																
																// Add the new option to the dropdown and select it
																var newOption = new Option(response.data.page_title, response.data.page_id, true, true);
																$optgroup.append(newOption);
																$('#iee_elementor_page_dropdown').val(response.data.page_id).trigger('change');
																
																// Auto submit the form to save the settings
																$btn.closest('form').submit();
															} else {
																alert('Error: ' + (response.data.message || response.data));
															}
														},
														error: function() {
															$btn.prop('disabled', false);
															$spinner.removeClass('is-active');
															alert('<?php echo esc_js( __( "An error occurred. Please try again.", "import-eventbrite-events" ) ); ?>');
														}
													});
												}
											});
										});
									</script>
									<?php endif; // iee_is_pro() ?>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Ticket Button Text', 'import-eventbrite-events' ); ?></span> 
										</div>
										<div class="iee-inner-section-2">
											<input class="eventbrite_oauth_token iee_input_w25" name="eventbrite_ap[ticket_button_text]" type="text" value="<?php echo esc_html( ! empty( $eventbrite_optionsap['ticket_button_text'] ) ? $eventbrite_optionsap['ticket_button_text'] : __( 'Buy Tickets', 'import-eventbrite-events' ) ); ?>" />
										</div>
									</div>
									
									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Next Events Text', 'import-eventbrite-events' ); ?></span> 
										</div>
										<div class="iee-inner-section-2">
											<input class="eventbrite_oauth_token iee_input_w25" name="eventbrite_ap[next_event_text]" type="text" value="<?php echo esc_html( ! empty( $eventbrite_optionsap['next_event_text'] ) ? $eventbrite_optionsap['next_event_text'] : __( 'Next Events', 'import-eventbrite-events' ) ); ?>" />
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Previous Events Text', 'import-eventbrite-events' ); ?></span> 
										</div>
										<div class="iee-inner-section-2">
											<input class="eventbrite_oauth_token iee_input_w25" name="eventbrite_ap[previous_event_text]" type="text" value="<?php echo esc_html( ! empty( $eventbrite_optionsap['previous_event_text'] ) ? $eventbrite_optionsap['previous_event_text'] : __( 'Previous Events', 'import-eventbrite-events' ) ); ?>" />
										</div>
									</div>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Display Buy Ticket section in Past Event', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<?php
											$sbntb = isset( $eventbrite_optionsap['sbntb'] ) ? $eventbrite_optionsap['sbntb'] : 'no';
											?>
											<input type="checkbox" name="eventbrite_ap[sbntb]" value="yes" <?php if ( $sbntb == 'yes' ) { echo 'checked="checked"'; } ?> />
											<span class="iee_small">
												<?php esc_attr_e( 'Check to enable show to buy ticket section in past event', 'import-eventbrite-events' ); ?>
											</span>
										</div>
									</div>

									<div class="" style="margin-bottom: 5px;">
										<input type="hidden" name="iee_ap_action" value="iee_ap_save_settings" />
										<?php wp_nonce_field( 'iee_ap_setting_form_nonce_action', 'iee_ap_setting_form_nonce' ); ?>
										<input type="submit" class="iee_button" style=""  value="<?php esc_attr_e( 'Save Settings', 'import-eventbrite-events' ); ?>" />
									</div>

								</form>
							</div>
						</div>
					</div>
					
					<div id="google_maps_key" class="iee_tab_content" style="margin-top: 15px;">
						<div class="iee_container">
							<div class="iee_row">
								<form method="post" id="iee_gma_setting_form">
									<?php do_action( 'iee_before_settings_section' ); ?>

									<div class="iee-inner-main-section"  >
										<div class="iee-inner-section-1" >
											<span class="iee-title-text" ><?php esc_attr_e( 'Google Maps API', 'import-eventbrite-events' ); ?></span>
										</div>
										<div class="iee-inner-section-2">
											<input class="iee_google_maps_api_key" name="iee_google_maps_api_key" Placeholder="Enter Google Maps API Key Here..." type="text" value="<?php echo( ! empty( $iee_google_maps_api_key ) ? esc_attr( $iee_google_maps_api_key ) : '' ); ?>" />
											<span class="iee_check_key"><a href="javascript:void(0)" ><?php esc_attr_e( 'Check Google Maps Key', 'import-eventbrite-events' ); ?></a><span class="iee_loader" id="iee_loader"></span></span>
											<span id="iee_gmap_error_message"></span>
											<span id="iee_gmap_success_message"></span>
											<span class="iee_small">
												<?php
													printf(
														'%s <a href="https://developers.google.com/maps/documentation/embed/get-api-key#create-api-keys" target="_blank">%s</a> / %s',
														esc_attr__( 'Google maps API Key (Required)', 'import-eventbrite-events' ),
														esc_attr__( 'How to get an API Key', 'import-eventbrite-events' ),
														'<a href="https://developers.google.com/maps/documentation/embed/get-api-key#restrict_key" target="_blank">' . esc_attr__( 'Find out more about API Key restrictions', 'import-eventbrite-events' ) . '</a>'
													);
												?>
											</span>
										</div>
									</div>

									<div style="margin-bottom: 5px;">
										<input type="hidden" name="iee_gma_action" value="iee_save_gma_settings" />
										<?php wp_nonce_field( 'iee_gma_setting_form_nonce_action', 'iee_gma_setting_form_nonce' ); ?>
										<input type="submit" class="iee_button xtei_gma_submit_button" style=""  value="<?php esc_attr_e( 'Save Settings', 'import-eventbrite-events' ); ?>" />
									</div>
								</form>
							</div>
						</div>
					</div>

					<?php if( iee_is_pro() ){ ?>
						<div id="license" class="iee_tab_content">
							<?php
								if( class_exists( 'Import_Eventbrite_Events_Pro_Common' ) && method_exists( $iee_events->common_pro, 'iee_licence_page_in_setting' ) ){
									$iee_events->common_pro->iee_licence_page_in_setting(); 
								}else{
									$license_section = sprintf(
										'<h3 class="setting_bar" >Once you have updated the plugin Pro version <a href="%s">%s</a>, you will be able to access this section.</h3>',
										esc_url( admin_url( 'plugins.php?s=import+eventbrite+events+pro' ) ),
										esc_html__( 'Here', 'import-eventbrite-events' )
									);
									echo wp_kses_post( $license_section );
								}
							?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
