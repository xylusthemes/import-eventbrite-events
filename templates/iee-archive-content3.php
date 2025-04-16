<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */
global $iee_events;
$start_date_str      = get_post_meta( get_the_ID(), 'start_ts', true );
$end_date_str        = get_post_meta( get_the_ID(), 'end_ts', true );
$iee_event_id        = get_post_meta( get_the_ID(), 'iee_event_id', true );
$start_date_formated = date_i18n( 'F j, Y ', $start_date_str );
$event_address       = get_post_meta( get_the_ID(), 'venue_name', true );
$venue_address       = get_post_meta( get_the_ID(), 'venue_address', true );
$day                 = date('d', $start_date_str);
$month               = date('M', $start_date_str);

if ( $event_address != '' && $venue_address != '' ) {
	$event_address  .= ' - ' . $venue_address;
} elseif ( $venue_address != '' ) {
	$event_address   = $venue_address;
}

$iee_options  = get_option( IEE_OPTIONS );
$accent_color = isset( $iee_options['accent_color'] ) ? $iee_options['accent_color'] : '#039ED7';
$time_format  = isset( $iee_options['time_format'] ) ? $iee_options['time_format'] : '12hours';

if( $time_format === '12hours' ){
	$start_time = date_i18n( 'h:i A', $start_date_str );
	$end_time   = date_i18n( 'h:i A', $end_date_str );
}elseif($time_format === '24hours' ){
	$start_time = date_i18n( 'G:i', $start_date_str );
	$end_time   = date_i18n( 'G:i', $end_date_str );
}else{
    $start_time = date_i18n( get_option( 'time_format' ), $start_date_str );
    $end_time   = date_i18n( get_option( 'time_format' ), $end_date_str );
}



$image_url = array();
if ( '' !== get_the_post_thumbnail() ) {
	$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
} else {
	$image_date  = date_i18n( 'F+d', $start_date_str );
	$image_url[] = 'https://dummyimage.com/420x210/ccc/969696.png&text=' . $image_date;
}

$event_url = get_permalink();
$target    = '';
if ( 'yes' === $direct_link ){
	$event_url = get_post_meta( get_the_ID(), 'iee_event_link', true );
	$target    = 'target="_blank"';
}

$post_description    = get_the_content();
$limited_description = wp_html_excerpt( $post_description, 100, '...' );
$event_address       = wp_html_excerpt( $event_address, 80, '...' );

?>
<div class="iee-event-item">
    <span class="iee-event-count"><?php echo esc_attr( $day ); ?><span><?php echo esc_attr( $month ); ?></span></span>
    <div class="iee-event-content-wrap">
        <div class="iee-event-img">
            <img src="<?php echo esc_url( $image_url[0] ); ?>" alt="">
        </div>
        <div class="iee-event-content">
            <div class="iee-event-info">
                <div class="iee-event-meta">
                    <div class="iee-event-dtl-meta">
                        <div class="iee-event-time">
							<?php
								if( wp_is_mobile() ) {
									echo '<div ' . esc_attr( 'data-day="' . $day . '" data-month="' . $month . '"' ) . '>
											<i class="fa fa-calendar"></i>
											' . esc_html( $day . ' - ' . $month ) . '
										</div>';
								}
							?>
							<i class="fa fa-clock-o"></i><?php echo esc_attr( ' '. $start_time .' - ' . $end_time ) ; ?>
						 </div>
                        <div class="iee-event-location" ><i class="fa fa-map-marker"></i> <?php echo esc_attr( ucfirst( $event_address ) ); ?></div>
                    </div>
                </div>
                <h4><a <?php echo esc_attr( $target ); ?> href="<?php echo esc_url( $event_url ); ?>"><?php the_title(); ?></a></h4>
                <p><?php echo esc_attr( $limited_description );  ?></p>
            </div>
        </div>
        <div class="iee-event-bottom" >
            <a href="javascript:void(0)" class="iee-theme-btn" data-series-id="<?php echo esc_attr( $iee_event_id );  ?>" id="iee-eventbrite-recurring-checkout-<?php echo esc_attr( $iee_event_id );?>" ><?php esc_html_e( 'Buy Tickets', 'import-eventbrite-events' ); ?></a>
        </div>
    </div>
</div>
<script src="https://www.eventbrite.com/static/widgets/eb_widgets.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('.iee-theme-btn').on("click", function(){
			const id        = jQuery(this).attr('id');
			const series_id = jQuery(this).data('series-id');
			var orderCompleteCallback = function() {
				console.log("Order complete!");
			};
			window.EBWidgets.createWidget({
				widgetType: "checkout",
				eventId: series_id,
				modal: true,
				modalTriggerElementId: id,
				onOrderComplete: orderCompleteCallback
			});
		});
	});
</script>