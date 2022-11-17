<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $iee_events;
// Add Thickbox support.
add_thickbox();
$listtable = new Import_Eventbrite_Events_History_List_Table();
$listtable->prepare_items();
?>
<div class="iee_container">
	<div class="iee_row">
		<div class="">
			<form id="import-history" method="get">
				<input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) ) : 'eventbrite_event'; ?>" />
				<input type="hidden" name="tab" value="<?php echo isset( $_REQUEST['tab'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) ) ) : 'history'; ?>" />
				<?php
				$listtable->display();
				?>
			</form>
		</div>
	</div>
</div>
