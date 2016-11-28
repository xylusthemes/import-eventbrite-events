<?php
/**
 * File for render eventbrite auto import tab content.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.1
 *
 * @package    XT_Eventbrite_Import
 * @subpackage XT_Eventbrite_Import/admin/partials
 */
?>
<div class="xtei_container">
    <div class="xtei_row">
        <div class="xtei-column xtei_well">
            <h3><?php esc_attr_e( 'Eventbrite Automatic Import', 'xt-eventbrite-import' ); ?></h3>
                                
            <form method="post" enctype="multipart/form-data" id="xtei_setup_auto_import_form">

                <?php if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) { 
                        $is_tec_enable =  false;
                        if( isset( $xtei_auto_options['enable_auto_import'] ) && in_array( 'tec', $xtei_auto_options['enable_auto_import'] ) ){
                            $is_tec_enable =  true;
                        }
                        $tec_cats = isset( $xtei_auto_options['xtei_event_cats'] ) ? $xtei_auto_options['xtei_event_cats'] : array();
                    ?>
                    <div class="xtei_element">
                        <input type="checkbox" name="enable_auto_import[]" value="tec" id="tecauto_import" <?php checked( $is_tec_enable ); ?> >
                        <label for="tecauto_import">
                        <?php 
                            esc_attr_e( 'Enable automatic import your eventbrite events for','xt-eventbrite-import' );
                            printf('<b> "%s"</b>', esc_html__('The Event Calendar.', 'xt-eventbrite-import') ); 
                        ?>
                        </label>
                        <span class="xtei_desciption">
                            <?php esc_attr_e( 'It will import events from your account( which is used in personal OAuth token ).' ); ?>
                        </span>
                    </div>
                    <div class="xtei_element tecauto_cat">
                        <label class="xtei_label"> <?php esc_attr_e( 'Event Categories for Event Import','xt-eventbrite-import' ); ?> : </label>
                        <select name="xtei_event_cats[]" multiple="multiple">
                            <?php if( ! empty( $xtei_event_cats ) ): ?>
                                <?php foreach ($xtei_event_cats as $xtei_cat ): ?>
                                    <option value="<?php echo $xtei_cat->term_id; ?>" <?php if( in_array( $xtei_cat->term_id, $tec_cats)){ echo 'selected="selected"'; } ?>>
                                        <?php echo $xtei_cat->name; ?>
                                    </option>
                                    <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <span class="xtei_small">
                            <?php esc_attr_e( 'These categories are assign to imported event.', 'xt-eventbrite-import' ); ?>
                        </span>
                    </div>
                <?php } ?>

                <?php if ( is_plugin_active( 'events-manager/events-manager.php' ) ) { 
                        $is_em_enable =  false;
                        if( isset( $xtei_auto_options['enable_auto_import'] ) && in_array( 'em', $xtei_auto_options['enable_auto_import'] ) ){
                            $is_em_enable =  true;
                        }
                        $em_cats = isset( $xtei_auto_options['xtei_event_em_cats'] ) ? $xtei_auto_options['xtei_event_em_cats'] : array();
                    ?>
                    <div class="xtei_element">
                        <input type="checkbox" name="enable_auto_import[]" value="em" id="emauto_import" <?php checked( $is_em_enable ); ?> >
                        <label for="emauto_import">
                        <?php 
                            esc_attr_e( 'Enable automatic import your eventbrite events for','xt-eventbrite-import' );  
                            printf('<b> "%s"</b>', esc_html__('Events Manager.', 'xt-eventbrite-import') ); ?>
                        </label>
                        <span class="xtei_desciption">
                            <?php esc_attr_e( 'It will import events from your account( which is used in personal OAuth token ).' ); ?>
                    </div>
                    <div class="xtei_element emauto_cat">
                        <label class="xtei_label"> <?php esc_attr_e( 'Event Categories for Event Import','xt-eventbrite-import' ); ?> : </label>
                        <select name="xtei_event_em_cats[]" multiple="multiple">
                            <?php if( ! empty( $xtei_event_em_cats ) ): ?>
                                <?php foreach ($xtei_event_em_cats as $event_em_cat ): ?>
                                    <option value="<?php echo $event_em_cat->term_id; ?>" <?php if( in_array( $event_em_cat->term_id, $em_cats)){ echo 'selected="selected"'; } ?> >
                                        <?php echo $event_em_cat->name; ?>
                                    </option>
                                    <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <span class="xtei_small">
                            <?php esc_attr_e( 'These categories are assign to imported event.', 'xt-eventbrite-import' ); ?>
                        </span>
                    </div>
                <?php } ?>

                <div class="xtei_element">
                    <label class="xtei_label"> <?php esc_attr_e( 'Import Frequency', 'xt-meetup-import' ); ?>: </label>
                    <?php
                    $cron_interval = isset( $xtei_auto_options['cron_interval'] ) ? $xtei_auto_options['cron_interval'] : 'twicedaily';
                    ?>
                    <select name="cron_interval" >
                        <option value='hourly' <?php selected( $cron_interval, 'hourly' ); ?> >
                            <?php esc_html_e( 'Once Hourly','xt-meetup-import' ); ?>
                        </option>
                        <option value='twicedaily' <?php selected( $cron_interval, 'twicedaily' ); ?> >
                            <?php esc_html_e( 'Twice Daily','xt-meetup-import' ); ?>
                        </option>
                        <option value="daily" <?php selected( $cron_interval, 'daily' ); ?> >
                            <?php esc_html_e( 'Once Daily','xt-meetup-import' ); ?>
                        </option>
                        <option value="weekly" <?php selected( $cron_interval, 'weekly' ); ?> >
                            <?php esc_html_e( 'Once Weekly','xt-meetup-import' ); ?>
                        </option>
                        <option value="monthly" <?php selected( $cron_interval, 'monthly' ); ?> >
                            <?php esc_html_e( 'Once a Month','xt-meetup-import' ); ?>
                        </option>
                    </select>
                    <span class="xtei_small">
                        <?php esc_html_e( 'Applicable only if you had select "Automatic" import type.', 'xt-meetup-import' ); ?><br />
                    </span>
                </div>

                <div class="xtei_element">
                    <input type="hidden" name="xtei_action" value="xtei_auto_import_submit" />
                    <?php wp_nonce_field( 'xtei_auto_import_form_nonce_action', 'xtei_auto_import_form_nonce' ); ?>
                    <input type="submit" class="button-primary xtei_submit_button" style=""  value="<?php esc_attr_e( 'Save Automatic Import', 'xt-eventbrite-import' ); ?>" disabled="disabled" />
                    <br />
                    <span style="color: red">
                        <?php esc_attr_e( 'Available in Pro version.', 'xt-eventbrite-import' ); ?>
                    </span>
                    <a href="<?php echo esc_url(XTEI_PLUGIN_BUY_NOW_URL); ?>">
                        <?php esc_attr_e( 'Buy Now', 'xt-eventbrite-import' ); ?>
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>