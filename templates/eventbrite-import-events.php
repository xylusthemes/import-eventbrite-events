<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
global $iee_events;
?>
<div class="iee_container">
    <div class="iee_row">
        <div class="iee-column iee_well">
            <h3><?php esc_attr_e( 'Eventbrite Import', 'import-eventbrite-events' ); ?></h3>
            <form method="post" enctype="multipart/form-data" id="iee_eventbrite_form">
           	
               	<table class="form-table">
		            <tbody>
		                <tr>
					        <th scope="row">
					        	<?php esc_attr_e( 'Import by','import-eventbrite-events' ); ?> :
					        </th>
					        <td>
					            <select name="eventbrite_import_by" id="eventbrite_import_by">
			                    	<option value="event_id"><?php esc_attr_e( 'Event ID','import-eventbrite-events' ); ?></option>
			                    	<option value="your_events"><?php esc_attr_e( 'Your Events','import-eventbrite-events' ); ?></option>
			                    	<option value="organizer_id"><?php esc_attr_e( 'Organazer ID','import-eventbrite-events' ); ?></option>
			                    </select>
			                    <span class="iee_small">
			                        <?php _e( 'Select Event source. 1. by Event ID, 2. Your Events ( Events associted with your Eventbrite account ), 3. by Oraganizer ID.', 'import-eventbrite-events' ); ?>
			                    </span>
					        </td>
					    </tr>
					    
					    <tr class="eventbrite_event_id">
					    	<th scope="row">
					    		<?php esc_attr_e( 'Eventbrite Event ID','import-eventbrite-events' ); ?> : 
					    	</th>
					    	<td>
					    		<input class="iee_text" name="iee_eventbrite_id" type="text" />
			                    <span class="iee_small">
			                        <?php _e( 'Insert eventbrite event ID ( Eg. https://www.eventbrite.com/e/event-import-with-wordpress-<span class="borderall">12265498440</span>  ).', 'import-eventbrite-events' ); ?>
			                    </span>
					    	</td>
					    </tr>

					    <tr class="eventbrite_organizer_id">
					    	<th scope="row">
					    		<?php esc_attr_e( 'Eventbrite Organizer ID','import-eventbrite-events' ); ?> : 
					    	</th>
					    	<td>
					    		<input class="iee_text" name="iee_organizer_id" type="text" disabled="disabled" />
			                    <span class="iee_small">
			                        <?php _e( 'Insert eventbrite organizer ID ( Eg. https://www.eventbrite.com/o/cept-university-<span class="borderall">9151813372</span>  ).', 'import-eventbrite-events' ); ?>
			                    </span>
			                    <?php do_action( 'iee_render_pro_notice' ); ?>
					    	</td>
					    </tr>

					    <tr class="import_type_wrapper">
					    	<th scope="row">
					    		<?php esc_attr_e( 'Import type','import-eventbrite-events' ); ?> : 
					    	</th>
					    	<td>
						    	<?php $iee_events->common->render_import_type(); ?>
					    	</td>
					    </tr>

					    <?php 
						// import into.
					    $iee_events->common->render_import_into_and_taxonomy();
					    $iee_events->common->render_eventstatus_input();
					    ?>
					</tbody>
		        </table>
                
                <div class="iee_element">
                	<input type="hidden" name="import_origin" value="eventbrite" />
                    <input type="hidden" name="iee_action" value="iee_import_submit" />
                    <?php wp_nonce_field( 'iee_import_form_nonce_action', 'iee_import_form_nonce' ); ?>
                    <input type="submit" class="button-primary iee_submit_button" style=""  value="<?php esc_attr_e( 'Import Event', 'import-eventbrite-events' ); ?>" />
                </div>
            </form>
        </div>
    </div>
</div>
