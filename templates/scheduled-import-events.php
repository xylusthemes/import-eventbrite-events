<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;
global $iee_events;
?>
<div class="iee_container">
    <div class="iee_row">
        <div class="">
			<form id="scheduled-import" method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<input type="hidden" name="tab" value="<?php echo $tab = isset($_REQUEST['tab'])? $_REQUEST['tab'] : 'scheduled' ?>" />
				<input type="hidden" name="ntab" value="" />
				<?php
				do_action( 'iee_render_pro_notice' );
        		?>
			</form>
        </div>
    </div>
</div>
