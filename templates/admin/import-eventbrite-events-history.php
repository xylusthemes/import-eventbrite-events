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
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
				<input type="hidden" name="tab" value="<?php echo $tab = isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : 'history'; ?>" />
				<?php
				$listtable->display();
				?>
			</form>
		</div>
	</div>
</div>
