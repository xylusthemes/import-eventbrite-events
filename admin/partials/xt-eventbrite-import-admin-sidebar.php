<?php
/**
 * Sidebar for Admin Page
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    XT_Eventbrite_Import
 * @subpackage XT_Eventbrite_Import/admin/partials
 * @copyright   Copyright (c) 2016, Dharmesh Patel
 * @since       1.0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="upgrade_to_pro">
	<h2><?php esc_html_e( 'Upgrade to Pro','xt-eventbrite-import'); ?></h2>
	<p><?php esc_html_e( 'Import events automatically from your eventbrite account, Upgrade today!!','xt-eventbrite-import'); ?></p>
	<a class="button button-primary upgrade_button" href="<?php echo esc_url(XTEI_PLUGIN_BUY_NOW_URL); ?>" target="_blank">
		<?php esc_html_e( 'Upgrade to Pro','xt-eventbrite-import'); ?>
	</a>
</div>

<div class="upgrade_to_pro">
	<h2><?php esc_html_e( 'Custom WordPress Development Services','xt-eventbrite-import'); ?></h2>
	<p><?php esc_html_e( "From small blog to complex web apps, we push the limits of what's possible with WordPress.","xt-eventbrite-import" ); ?></p>
	<a class="button button-primary upgrade_button" href="<?php echo esc_url('https://xylusthemes.com/contact/?utm_source=insideplugin&utm_medium=web&utm_content=sidebar&utm_campaign=freeplugin'); ?>" target="_blank">
		<?php esc_html_e( 'Hire Us','xt-eventbrite-import'); ?>
	</a>
</div>

<div>
	<p style="text-align:center">
		<strong><?php esc_html_e( 'Would you like to remove these ads?','wp-bulk-delete'); ?></strong><br>
		<a href="<?php echo esc_url(XTEI_PLUGIN_BUY_NOW_URL); ?>" target="_blank">
			<?php esc_html_e( 'Get Premium','wp-bulk-delete'); ?>
		</a>
	</p>
</div>
<?php
