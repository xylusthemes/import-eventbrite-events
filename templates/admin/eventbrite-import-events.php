<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $iee_events;
?>
<form method="post" enctype="multipart/form-data" id="iee_eventbrite_form">
	<div class="iee-card" style="margin-top:20px;" >			
		<div class="iee-content"  aria-expanded="true" style=" ">
			<div class="iee-inner-main-section">
				<div class="iee-inner-section-1" >
					<span class="iee-title-text" >
						<?php esc_attr_e( 'Import by', 'import-eventbrite-events' ); ?>
						<span class="iee-tooltip">
							<div>
								<svg viewBox="0 0 20 20" fill="#000" xmlns="http://www.w3.org/2000/svg" class="iee-circle-question-mark">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M1.6665 10.0001C1.6665 5.40008 5.39984 1.66675 9.99984 1.66675C14.5998 1.66675 18.3332 5.40008 18.3332 10.0001C18.3332 14.6001 14.5998 18.3334 9.99984 18.3334C5.39984 18.3334 1.6665 14.6001 1.6665 10.0001ZM10.8332 13.3334V15.0001H9.1665V13.3334H10.8332ZM9.99984 16.6667C6.32484 16.6667 3.33317 13.6751 3.33317 10.0001C3.33317 6.32508 6.32484 3.33341 9.99984 3.33341C13.6748 3.33341 16.6665 6.32508 16.6665 10.0001C16.6665 13.6751 13.6748 16.6667 9.99984 16.6667ZM6.6665 8.33341C6.6665 6.49175 8.15817 5.00008 9.99984 5.00008C11.8415 5.00008 13.3332 6.49175 13.3332 8.33341C13.3332 9.40251 12.6748 9.97785 12.0338 10.538C11.4257 11.0695 10.8332 11.5873 10.8332 12.5001H9.1665C9.1665 10.9824 9.9516 10.3806 10.6419 9.85148C11.1834 9.43642 11.6665 9.06609 11.6665 8.33341C11.6665 7.41675 10.9165 6.66675 9.99984 6.66675C9.08317 6.66675 8.33317 7.41675 8.33317 8.33341H6.6665Z" fill="currentColor"></path>
								</svg>
								<span class="iee-popper">
									<?php 
										$text = sprintf(
											/* translators: 1: First option (by Facebook Event ID), 2: Second option (Facebook Page) */
											esc_html__( 'Select Event source. %1$s, %2$s, %3$s.', 'import-eventbrite-events' ),
											'<br><strong>' . esc_html__( '1. Eventbrite Event ID', 'import-eventbrite-events' ) . '</strong>',
											'<br><strong>' . esc_html__( '2. Eventbrite Organizer ID', 'import-eventbrite-events' ) . '</strong>',
											'<br><strong>' . esc_html__( '3. Eventbrite Collection ID', 'import-eventbrite-events' ) . '</strong>'
										);
										
										echo wp_kses(
											$text,
											array(
												'strong' => array(),
												'br' => array(),
											)
										);
									?>
									<div class="iee-popper__arrow"></div>
								</span>
							</div>
						</span>
					</span>
				</div>
				<div class="iee-inner-section-2 ">
					<select name="eventbrite_import_by" id="eventbrite_import_by">
						<option value="event_id"><?php esc_attr_e( 'Event ID', 'import-eventbrite-events' ); ?></option>
						<option value="organizer_id"><?php esc_attr_e( 'Organizer ID', 'import-eventbrite-events' ); ?></option>
						<option value="collection_id"><?php esc_attr_e( 'Collection ID', 'import-eventbrite-events' ); ?></option>
					</select>
				</div>
			</div>

			<div class="iee-inner-main-section eventbrite_event_id">
				<div class="iee-inner-section-1" >
					<span class="iee-title-text" ><?php esc_attr_e( 'Eventbrite Event ID', 'import-eventbrite-events' ); ?></span>
				</div>
				<div class="iee-inner-section-2">
					<?php if ( iee_is_pro() ) { ?>
					<textarea class="iee_eventbrite_ids" name="iee_eventbrite_id" type="text" rows="5" cols="50"></textarea>
					<span class="iee_small">
						<?php echo wp_kses_post( 'Insert eventbrite event IDs, One event ID per line ( Eg. https://www.eventbrite.com/e/event-import-with-wordpress-<span class="borderall">12265498440</span>  ).', 'import-eventbrite-events' ); ?>
					</span>
					<?php } else { ?>
					<input class="iee_text" name="iee_eventbrite_id" type="text" />
					<span class="iee_small">
						<?php echo wp_kses_post( 'Insert eventbrite event ID ( Eg. https://www.eventbrite.com/e/event-import-with-wordpress-<span class="borderall">12265498440</span>  ).', 'import-eventbrite-events' ); ?>
					</span>
					<?php } ?>
				</div>
			</div>

			<div class="iee-inner-main-section eventbrite_organizer_id">
				<div class="iee-inner-section-1" >
					<span class="iee-title-text" ><?php esc_attr_e( 'Eventbrite Organizer ID', 'import-eventbrite-events' ); ?></span>
				</div>
				<div class="iee-inner-section-2">
					<input class="iee_text" name="iee_organizer_id" type="text" <?php if ( ! iee_is_pro() ) { echo 'disabled="disabled"'; } ?> />
					<span class="iee_small">
						<?php echo wp_kses_post( 'Insert eventbrite organizer ID ( Eg. https://www.eventbrite.com/o/cept-university-<span class="borderall">9151813372</span>  ).', 'import-eventbrite-events' ); ?>
					</span>
					<?php do_action( 'iee_render_pro_notice' ); ?>
				</div>
			</div>

			<div class="iee-inner-main-section eventbrite_collection_id">
				<div class="iee-inner-section-1" >
					<span class="iee-title-text" ><?php esc_attr_e( 'Eventbrite Collection ID', 'import-eventbrite-events' ); ?></span>
				</div>
				<div class="iee-inner-section-2">
					<input class="iee_text" name="iee_collection_id" type="text" <?php if ( ! iee_is_pro() ) { echo 'disabled="disabled"'; } ?> />
					<span class="iee_small">
						<?php echo wp_kses_post( 'Insert Eventbrite Collection ID ( Eg. https://www.eventbrite.com/cc/collection-<span class="borderall">3732699</span>  ).', 'import-eventbrite-events' ); ?>
					</span>
					<?php do_action( 'iee_render_pro_notice' ); ?>
				</div>
			</div>

			<div class="iee-inner-main-section import_type_wrapper">
				<div class="iee-inner-section-1" >
					<span class="iee-title-text" ><?php esc_attr_e( 'Import type', 'import-eventbrite-events' ); ?></span>
				</div>
				<div class="iee-inner-section-2">
					<?php $iee_events->common->render_import_type(); ?>
				</div>
			</div>

			<?php
				// import into.
				$iee_events->common->render_import_into_and_taxonomy();
				$iee_events->common->render_eventstatus_input();
			?>
			<div class="iee-inner-main-section">
				<div class="iee-inner-section-1" >
					<span class="iee-title-text" ><?php esc_attr_e( 'Author', 'import-eventbrite-events' ); ?> 
						<span class="iee-tooltip">
							<div>
								<svg viewBox="0 0 20 20" fill="#000" xmlns="http://www.w3.org/2000/svg" class="iee-circle-question-mark">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M1.6665 10.0001C1.6665 5.40008 5.39984 1.66675 9.99984 1.66675C14.5998 1.66675 18.3332 5.40008 18.3332 10.0001C18.3332 14.6001 14.5998 18.3334 9.99984 18.3334C5.39984 18.3334 1.6665 14.6001 1.6665 10.0001ZM10.8332 13.3334V15.0001H9.1665V13.3334H10.8332ZM9.99984 16.6667C6.32484 16.6667 3.33317 13.6751 3.33317 10.0001C3.33317 6.32508 6.32484 3.33341 9.99984 3.33341C13.6748 3.33341 16.6665 6.32508 16.6665 10.0001C16.6665 13.6751 13.6748 16.6667 9.99984 16.6667ZM6.6665 8.33341C6.6665 6.49175 8.15817 5.00008 9.99984 5.00008C11.8415 5.00008 13.3332 6.49175 13.3332 8.33341C13.3332 9.40251 12.6748 9.97785 12.0338 10.538C11.4257 11.0695 10.8332 11.5873 10.8332 12.5001H9.1665C9.1665 10.9824 9.9516 10.3806 10.6419 9.85148C11.1834 9.43642 11.6665 9.06609 11.6665 8.33341C11.6665 7.41675 10.9165 6.66675 9.99984 6.66675C9.08317 6.66675 8.33317 7.41675 8.33317 8.33341H6.6665Z" fill="currentColor"></path>
								</svg>
								<span class="iee-popper">
									<?php esc_attr_e( 'Select event author for imported events. Default event auther is current loggedin user.', 'import-eventbrite-events' ); ?>
									<div class="iee-popper__arrow"></div>
								</span>
							</div>
						</span>
					</span>
				</div>
				<div class="iee-inner-section-2">
					<?php wp_dropdown_users( array( 'show_option_none' => esc_attr__( 'Select Author','import-eventbrite-events'), 'name' => 'event_author', 'option_none_value' => get_current_user_id() ) ); ?>
				</div>
			</div>

			<div class="">
				<input type="hidden" name="import_origin" value="eventbrite" />
				<input type="hidden" name="iee_action" value="iee_import_submit" />
				<?php wp_nonce_field( 'iee_import_form_nonce_action', 'iee_import_form_nonce' ); ?>
				<input type="submit" class="iee_button iee_submit_button" style=""  value="<?php esc_attr_e( 'Import Event', 'import-eventbrite-events' ); ?>" />
			</div>
		</div>
	</div>
</form>
