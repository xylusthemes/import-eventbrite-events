<?php
/**
 * Content for eventbrite import page.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    XT_Eventbrite_Import
 * @subpackage XT_Eventbrite_Import/admin/partials
 */

?>
<div class="wrap">
    <h2><?php esc_html_e( 'Eventbrite import', 'xt-eventbrite-import' ); ?></h2>
    <?php
    // Set Default Tab to S`ettings.
    $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'settings';
    ?>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <div id="postbox-container-1" class="postbox-container">
                <?php require_once 'xt-eventbrite-import-admin-sidebar.php'; ?>
            </div>
            <div id="postbox-container-2" class="postbox-container">

                <h2 class="nav-tab-wrapper">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>" class="nav-tab <?php if ( $active_tab == 'settings' ) { echo 'nav-tab-active'; } ?>">
                        <?php esc_html_e( 'Settings', 'xt-eventbrite-import' ); ?>
                    </a>
                    <?php
                    if ( ! function_exists( 'is_plugin_active' ) ) {
            			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            		}
            		if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) { ?>
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'etec_import' ) ); ?>" class="nav-tab <?php if ( $active_tab == 'etec_import' ) { echo 'nav-tab-active'; } ?>">
                        <?php esc_html_e( 'The Events Calendar import', 'xt-eventbrite-import' ); ?>
                    </a>
                    <?php } ?>

                    <?php if ( is_plugin_active( 'events-manager/events-manager.php' ) ) { ?>
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'eem_import' ) ); ?>" class="nav-tab <?php if ( $active_tab == 'eem_import' ) { echo 'nav-tab-active'; } ?>">
                        <?php esc_html_e( 'Events Manager import', 'xt-eventbrite-import' ); ?>
                    </a>
                    <?php } ?>
                    <a href="<?php echo esc_url( add_query_arg( 'tab', 'auto_import' ) ); ?>" class="nav-tab <?php if ( $active_tab == 'auto_import' ) { echo 'nav-tab-active'; } ?>">
                        <?php esc_html_e( 'Automatic import', 'xt-eventbrite-import' ); ?>
                        <span style="color: red">
                           <?php esc_html_e( '(Pro)', 'xt-eventbrite-import' ); ?>
                        </span>
                    </a>
                </h2>
                <?php
                    if ( $active_tab == 'settings' ) {
                        require_once 'xt-eventbrite-import-tab-settings.php';
                    } elseif( $active_tab == 'etec_import' ) {
                        $xtei_event_cats = get_terms( XTEI_TEC_TAXONOMY, array( 'hide_empty' => 0 ) );
                        require_once 'xt-eventbrite-import-tec-tab-content.php';
                    } elseif( $active_tab == 'eem_import' ) {
                        $xtei_event_em_cats = get_terms( XTEI_EM_TAXONOMY, array( 'hide_empty' => 0 ) );
                        require_once 'xt-eventbrite-import-em-tab-content.php';
                    } elseif( $active_tab == 'auto_import' ) {
                        $xtei_event_cats = get_terms( XTEI_TEC_TAXONOMY, array( 'hide_empty' => 0 ) );
                        $xtei_event_em_cats = get_terms( XTEI_EM_TAXONOMY, array( 'hide_empty' => 0 ) );
                        $user_info = 
                        require_once 'xt-eventbrite-import-auto-import-tab-content.php';
                    }
                    ?>
                </div>
        </div>
        <br class="clear">
    </div>
</div>
