<?php
/**
 * Template file for admin import events form.
 *
 * @package Import_Eventbrite_Events
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $iee_events;
$counts = $iee_events->common->iee_get_eventbrite_events_counts();

?>
<div class="iee-container" style="margin-top: 60px;">
    <div class="iee-wrap" >
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <?php 
                    do_action( 'iee_display_all_notice' );
                ?>
                <div class="delete_notice"></div>
                <div id="postbox-container-2" class="postbox-container">
                    <div class="iee-app">
                        <div class="iee-card" style="margin-top:20px;" >			
                            <div class="iee-content"  aria-expanded="true"  >
                                <div id="iee-dashboard" class="wrap about-wrap" >
                                    <div class="iee-w-row" >
                                        <div class="iee-intro-section" >
                                            <div class="iee-w-box-content iee-intro-section-welcome" >
                                                <h3><?php esc_attr_e( 'Getting started with Import Eventbrite Events', 'import-eventbrite-events' ); ?></h3>
                                                <p style="margin-bottom: 25px;"><?php esc_attr_e( 'In this video, you can learn how to Import Eventbrite event into your website. Please watch this 3 minutes video to the end.', 'import-eventbrite-events' ); ?></p>
                                            </div>
                                            <div class="iee-w-box-content iee-intro-section-ifarme" >
                                                <iframe width="850" height="450" src="https://www.youtube.com/embed/pbtcEcy4J4o?si=iHSv-EtnECWKLL36" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen=""></iframe>
                                            </div>
                                            <div class="iee-intro-section-links wp-core-ui" >
                                                <a class="iee-intro-section-link-tag button iee-button-primary button-hero" href="https://plugindevelopment.in/wp-admin/post-new.php?post_type=eventbrite_events" target="_blank"><?php esc_attr_e( 'Add New Event', 'import-eventbrite-events' ); ?></a>
                                                <a class="iee-intro-section-link-tag button iee-button-secondary button-hero" href="https://plugindevelopment.in/wp-admin/admin.php?page=eventbrite_event&tab=settings"target="_blank"><?php esc_attr_e( 'Settings', 'import-eventbrite-events' ); ?></a>
                                                <a class="iee-intro-section-link-tag button iee-button-secondary button-hero" href="https://docs.xylusthemes.com/docs/import-eventbrite-events/" target="_blank"><?php esc_attr_e( 'Documentation', 'import-eventbrite-events' ); ?></a>
                                            </div>
                                        </div>

                                        <div class="iee-counter-main-container" >
                                            <div class="iee-col-sm-3" >
                                                <div class="iee-w-box " >
                                                    <p class="iee_dash_count"><?php echo esc_attr( $counts['all'] ); ?></p>
                                                    <span><strong><?php esc_attr_e( 'Total Events', 'import-eventbrite-events' ); ?></strong></span>
                                                </div>
                                            </div>
                                            <div class="iee-col-sm-3" >
                                                <div class="iee-w-box " >
                                                    <p class="iee_dash_count"><?php echo esc_attr( $counts['upcoming'] ); ?></p>
                                                    <span><strong><?php esc_attr_e( 'Upcoming Events', 'import-eventbrite-events' ); ?></strong></span>
                                                </div>
                                            </div>
                                            <div class="iee-col-sm-3" >
                                                <div class="iee-w-box " >
                                                    <p class="iee_dash_count"><?php echo esc_attr( $counts['past'] ); ?></p>
                                                    <span><strong><?php esc_attr_e( 'Past Events', 'import-eventbrite-events' ); ?></strong></span>
                                                </div>
                                            </div>
                                            <div class="iee-col-sm-3" >
                                                <div class="iee-w-box " >
                                                    <p class="iee_dash_count"><?php echo esc_attr( IEE_VERSION ); ?></p>
                                                    <span><strong><?php esc_attr_e( 'Version', 'import-eventbrite-events' ); ?></strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>
</div>