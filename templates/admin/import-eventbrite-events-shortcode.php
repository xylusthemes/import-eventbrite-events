<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$ShortcodeTable = new Shortcode_List_Table();
$ShortcodeTable->prepare_items();

?>

<div class="iee-xylus-promo-wrapper">
    <div class="iee-xylus-promo-header">
        <h2><?php esc_attr_e( '🎉 Try Our New Plugin – Easy Events Calendar', 'import-eventbrite-events' ); ?></h2>
        <p><?php esc_attr_e( 'A modern, clean and powerful way to display events. Includes calendar view, search, filters, pagination, and tons of settings. And it’s 100% FREE!', 'import-eventbrite-events' ); ?></p>
    </div>
    <div class="iee-xylus-main-inner-container">
        <div>
            <ul class="iee-xylus-feature-list">
                <li><?php esc_attr_e( '✅ Full Calendar Monthly View', 'import-eventbrite-events' ); ?></li>
                <li><?php esc_attr_e( '🔍 Event Search & Filter Support', 'import-eventbrite-events' ); ?></li>
                <li><?php esc_attr_e( '📅 Pagination & Multiple Layouts', 'import-eventbrite-events' ); ?></li>
                <li><?php esc_attr_e( '⚙️ Tons of Settings for Customization', 'import-eventbrite-events' ); ?></li>
                <li><?php esc_attr_e( '🎨 Frontend Styling Options', 'import-eventbrite-events' ); ?></li>
                <li><?php esc_attr_e( '💯 100% Free Plugin', 'import-eventbrite-events' ); ?></li>
            </ul>
            <?php
                $plugin_slug = 'xylus-events-calendar';
                $plugin_file = 'xylus-events-calendar/xylus-events-calendar.php';
                $current_page = admin_url( 'admin.php?page=eventbrite_event&tab=shortcodes' );
                if ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
                    $install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
                    echo '<a href="' . esc_url( $install_url ) . '" class="button button-primary">🚀 Install Now – It’s Free!</a>';
                } elseif ( ! is_plugin_active( $plugin_file ) ) {
                    $activate_url = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $plugin_file ), 'activate-plugin_' . $plugin_file );
                    echo '<a href="' . esc_url( $activate_url ) . '" class="button button-secondary">⚡ Activate Plugin</a>';
                } else {
                    echo '<div class="iee-xylus-plugin-box">';
                    echo '<h3>✅ Easy Events Calendar is Active</h3>';
                    echo '<p style="margin: 0;">You can now display events anywhere using this shortcode</p>';
                    echo '<span class="iee_short_code">[easy_events_calendar]</span>';
                    echo '<button class="iee-btn-copy-shortcode iee_button" data-value="[easy_events_calendar]">Copy</button>';
                    echo '</div>';
                }
            ?>
        </div>
        <div class="iee-xylus-screenshot-slider">
            <div class="iee-screenshot-slide active">
                <?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
                <img src="<?php echo esc_url( IEE_PLUGIN_URL.'assets/images/screenshot-1.jpg' ); ?>" alt="Monthly View">
            </div>
            <div class="iee-screenshot-slide">
                <?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
                <img src="<?php echo esc_url( IEE_PLUGIN_URL.'assets/images/screenshot-2.jpg' ); ?>" alt="Event Settings">
            </div>
            <div class="iee-screenshot-slide">
                <?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
                <img src="<?php echo esc_url( IEE_PLUGIN_URL.'assets/images/screenshot-3.jpg' ); ?>" alt="List View">
            </div>
            <div class="iee-screenshot-slide">
                <?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
                <img src="<?php echo esc_url( IEE_PLUGIN_URL.'assets/images/screenshot-4.jpg' ); ?>" alt="Event Details">
            </div>
        </div>
    </div>
</div>

<div class="iee_container">
    <div class="iee_row">
    <h3 class="setting_bar"><?php esc_attr_e( 'Eventbrite Shortcodes', 'import-eventbrite-events' ); ?></h3>
        <?php $ShortcodeTable->display(); ?>
    </div>
</div>