<?php
/**
 * File for render eventbrite import tab content.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    XT_Eventbrite_Import
 * @subpackage XT_Eventbrite_Import/admin/partials
 */

?>
<div class="xtei_container">
    <div class="xtei_row">
        <div class="xtei-column xtei_well">
            <h3><?php esc_attr_e( 'Eventbrite Import', 'xt-eventbrite-import' ); ?></h3>
            <form method="post" enctype="multipart/form-data" id="xtei_eventbrite_form">
                <div class="xtei_element">
                    <label class="xtei_label"> <?php esc_attr_e( 'Eventbrite Event ID','xt-eventbrite-import' ); ?> : </label>
                    <input class="xtei_text" name="xtei_eventbrite_id" type="text" required="required" />
                    <span class="xtei_small">
                        <?php _e( 'Insert eventbrite event ID ( Eg. https://www.eventbrite.com/e/event-import-with-wordpress-<span class="borderall">12265498440</span>  ).', 'xt-eventbrite-import' ); ?>
                    </span>
                </div>
                <div class="xtei_element">
                    <label class="xtei_label"> <?php esc_attr_e( 'Event Categories for Event Import','xt-eventbrite-import' ); ?> : </label>
                    <select name="xtei_event_cats[]" multiple="multiple">
                        <?php if( ! empty( $xtei_event_em_cats ) ): ?>
                            <!-- print_r( $xtei_event_em_cats); -->
                            <?php foreach ($xtei_event_em_cats as $xtei_cat ): ?>
                                <option value="<?php echo $xtei_cat->term_id; ?>"><?php echo $xtei_cat->name; ?></option>
                                <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <span class="xtei_small">
                        <?php esc_attr_e( 'These categories are assign to imported event.', 'xt-eventbrite-import' ); ?>
                    </span>
                </div>
                <div class="xtei_element">
                    <input type="hidden" name="xtei_action" value="xtei_em_import_submit" />
                    <?php wp_nonce_field( 'xtei_import_form_nonce_action', 'xtei_import_form_nonce' ); ?>
                    <input type="submit" class="button-primary xtei_submit_button" style=""  value="<?php esc_attr_e( 'Import Event', 'xt-eventbrite-import' ); ?>" />
                </div>
            </form>
        </div>
    </div>
</div>
