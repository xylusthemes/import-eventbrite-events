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
            <h3><?php esc_attr_e( 'Eventbrite Import Settings', 'xt-eventbrite-import' ); ?></h3>
            <form method="post" enctype="multipart/form-data" id="xtei_eventbrite_setting_form">
                <div class="xtei_element">
                    <label class="xtei_label"> <?php esc_attr_e( 'Eventbrite Personal OAuth token','xt-eventbrite-import' ); ?> : </label>
                    <input class="xtei_eventbrite_oauth_token" name="xtei_eventbrite_oauth_token" type="text" required="required" value="<?php if ( isset( $xtei_options['eventbrite_oauth_token'] ) ) { echo $xtei_options['eventbrite_oauth_token']; } ?>" />
                    <span class="xtei_small">
                        <?php _e( 'Insert your eventbrite.com Personal OAuth token you can get it from <a href="http://www.eventbrite.com/myaccount/apps/ target="_blank">here</a>.', 'xt-eventbrite-import' ); ?>
                    </span>
                </div>
                <div class="xtei_element">
                    <label class="xtei_label"> <?php esc_attr_e( 'Default status to use for imported events','xt-eventbrite-import' ); ?> : </label>
                    <?php
                    $defualt_status = isset( $xtei_options['default_status'] ) ? $xtei_options['default_status'] : 'pending';
                    ?>
                    <select name="xtei_default_status" >
                        <option value="publish" <?php if ( $defualt_status == 'publish' ) { echo 'selected="selected"'; } ?> >
                            <?php esc_html_e( 'Published','xt-eventbrite-import' ); ?>
                        </option>
                        <option value="pending" <?php if ( $defualt_status == 'pending' ) { echo 'selected="selected"'; } ?>>
                            <?php esc_html_e( 'Pending','xt-eventbrite-import' ); ?>
                        </option>
                        <option value="draft" <?php if ( $defualt_status == 'draft' ) { echo 'selected="selected"'; } ?> >
                            <?php esc_html_e( 'Draft','xt-eventbrite-import' ); ?>
                        </option>
                    </select>
                </div>

                <?php
                $enable_ticket_sec = isset( $xtei_options['enable_ticket_sec'] ) ? $xtei_options['enable_ticket_sec'] : 'yes';
                ?>
                <div class="xtei_element">
                    <label class="xtei_label"> <?php esc_attr_e( 'Display ticket option after event','xt-eventbrite-import' ); ?> : </label>
                    <input type="checkbox" name="enable_ticket_sec" value="yes" <?php if( $enable_ticket_sec == 'yes' ) { echo 'checked="checked"'; } ?> />
                    <?php esc_html_e( 'Check to display ticket option after event.', 'xt-eventbrite-import' ); ?>
                </div>

                <div class="xtei_element">
                    <input type="hidden" name="xtei_action" value="xtei_save_settings" />
                    <?php wp_nonce_field( 'xtei_setting_form_nonce_action', 'xtei_setting_form_nonce' ); ?>
                    <input type="submit" class="button-primary xtei_submit_button" style=""  value="<?php esc_attr_e( 'Save Settings', 'xt-eventbrite-import' ); ?>" />
                </div>

            </form>
        </div>
    </div>
</div>
