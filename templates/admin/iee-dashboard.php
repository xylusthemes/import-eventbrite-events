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

                        <div class="iee-card" style="margin-top:20px; overflow: hidden; position: relative; border: 1px solid #e0e0e0; border-radius: 8px;">
    
                            <!-- Gradient Background Strip -->
                            <div style="background: linear-gradient(135deg, #f06342 0%, #f6682f 40%, #3d64f4 100%); padding: 28px 30px; position: relative; overflow: hidden;">
                                
                                <!-- Decorative circles -->
                                <div style="position:absolute; top:-30px; right:-30px; width:150px; height:150px; background:rgba(255,255,255,0.08); border-radius:50%;"></div>
                                <div style="position:absolute; bottom:-40px; right:80px; width:100px; height:100px; background:rgba(255,255,255,0.06); border-radius:50%;"></div>

                                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; position:relative; z-index:1;">
                                    
                                    <!-- Left: Icon + Text -->
                                    <div style="display:flex; align-items:center; gap:16px;">
                                        <div style="background:rgba(255,255,255,0.2); border-radius:12px; width:52px; height:52px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            <span style="font-size:26px;">📅</span>
                                        </div>
                                        <div>
                                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                                                <h2 style="margin:0; color:#fff; font-size:20px; font-weight:700; line-height:1.2;">
                                                    <?php esc_attr_e( 'Eventbrite Widget', 'import-eventbrite-events' ); ?>
                                                </h2>
                                                <span style="background:#4CAF50; color:#fff; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; letter-spacing:0.5px; text-transform:uppercase;">NEW</span>
                                            </div>
                                            <p style="margin:0; color:rgba(255,255,255,0.88); font-size:13px; line-height:1.5;">
                                                <?php esc_attr_e( 'Display Eventbrite events directly on your website — no import, no authorization, no API token needed!', 'import-eventbrite-events' ); ?>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Right: CTA Button -->
                                    <div style="flex-shrink:0;">
                                    <?php if ( post_type_exists( 'ieepro_live_feed' ) || defined( 'IEEPRO_VERSION' ) ) : ?>
                                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=ieepro_live_feed' ) ); ?>" 
                                        style="display:inline-flex; align-items:center; gap:6px; background:#fff; color:#f06342; font-size:13px; font-weight:700; padding:10px 20px; border-radius:6px; text-decoration:none; box-shadow:0 2px 8px rgba(0,0,0,0.15);"
                                        onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.2)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.15)';">
                                            ✦ <?php esc_attr_e( 'Try Eventbrite Widget', 'import-eventbrite-events' ); ?>
                                        </a>
                                    <?php else : ?>
                                        <a href="https://xylusthemes.com/plugins/import-eventbrite-events" target="_blank"
                                        style="display:inline-flex; align-items:center; gap:6px; background:#fff; color:#f06342; font-size:13px; font-weight:700; padding:10px 20px; border-radius:6px; text-decoration:none; box-shadow:0 2px 8px rgba(0,0,0,0.15);"
                                        onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.2)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.15)';">
                                            🔒 <?php esc_attr_e( 'Upgrade to PRO', 'import-eventbrite-events' ); ?>
                                        </a>
                                    <?php endif; ?>
</div>
                                </div>
                            </div>

                            <!-- Feature Pills Row -->
                            <div style="background:#fafafa; border-top:1px solid #eee; padding:14px 30px; display:flex; gap:20px; flex-wrap:wrap; align-items:center;">
                                <span style="font-size:12px; color:#555; display:flex; align-items:center; gap:5px;">
                                    <span style="color:#4CAF50; font-size:14px;">✔</span>
                                    <?php esc_attr_e( 'No Import Needed', 'import-eventbrite-events' ); ?>
                                </span>
                                <span style="font-size:12px; color:#555; display:flex; align-items:center; gap:5px;">
                                    <span style="color:#4CAF50; font-size:14px;">✔</span>
                                    <?php esc_attr_e( 'No Auth & Token Required', 'import-eventbrite-events' ); ?>
                                </span>
                                <span style="font-size:12px; color:#555; display:flex; align-items:center; gap:5px;">
                                    <span style="color:#4CAF50; font-size:14px;">✔</span>
                                    <?php esc_attr_e( 'Shortcode Builder', 'import-eventbrite-events' ); ?>
                                </span>
                                <span style="font-size:12px; color:#555; display:flex; align-items:center; gap:5px;">
                                    <span style="color:#4CAF50; font-size:14px;">✔</span>
                                    <?php esc_attr_e( 'Live & Auto-Updated', 'import-eventbrite-events' ); ?>
                                </span>
                            </div>

                        </div>

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
                                                <a class="iee-intro-section-link-tag button iee-button-primary button-hero" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=eventbrite_events' ) ); ?>" target="_blank"><?php esc_attr_e( 'Add New Event', 'import-eventbrite-events' ); ?></a>
                                                <a class="iee-intro-section-link-tag button iee-button-secondary button-hero" href="<?php echo esc_url( admin_url( 'admin.php?page=eventbrite_event&tab=settings' ) ); ?>"target="_blank"><?php esc_attr_e( 'Settings', 'import-eventbrite-events' ); ?></a>
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