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
$event_address       = get_post_meta( get_the_ID(), 'venue_name', true );
$venue_address       = get_post_meta( get_the_ID(), 'venue_address', true );

//Date-Time
$start_date_formated = date_i18n( 'F j, Y ', $start_date_str );
$start_date_ymd      = date_i18n( 'd-m-Y ', $start_date_str );
if ( $venue_address != '' ) {
	$event_address  .= ' - ' . $venue_address;
} elseif ( $venue_address != '' ) {
	$event_address   = $venue_address;
}

//Get Options
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

//Image Url
$image_url = array();
if ( '' !== get_the_post_thumbnail() ) {
	$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
} else {
	$image_date  = date_i18n( 'd', $start_date_str );
	$image_url[] = 'https://dummyimage.com/420x210/ccc/969696.png&text=' . $image_date;
}

$event_url = get_permalink();
$target    = '';
if ( 'yes' === $direct_link ){
	$event_url = get_post_meta( get_the_ID(), 'iee_event_link', true );
	$target    = 'target="_blank"';
}

if( !empty( $accent_color ) ){
    $color = "color:".$accent_color.";";
}

?>

<div class="iee-style4-main-div">
	<div class="iee-style4-child-div">
        <div class="iee-style4-event-box">
			<div class="iee-style4-te-title">
                <a style="<?php echo esc_attr( $color ); ?>" <?php echo esc_attr( $target ); ?>href="<?php echo esc_url( $event_url ); ?>" >
                    <?php the_title(); ?>
                </a>
            </div>
			<div class="iee-style4-te-meta"><i class="fa fa-map-marker" style="<?php echo esc_attr( $color ); ?>" ></i><?php echo esc_attr( ' '.$event_address ); ?></div>
		</div>
        <div class="iee-style4-event-dt">
			<div class="iee-style4-te-title" style="<?php echo esc_attr( $color ); ?>" ><?php esc_attr_e( 'Date - Time', 'import-eventbrite-events' ); ?></div>
			<div class="iee-style4-te-meta"><i class="fa fa-calendar" style="<?php echo esc_attr( $color ); ?>"></i> <?php echo esc_attr( $start_date_ymd ); ?></div>
			<div class="iee-style4-te-meta"><i class="fa fa-clock-o" style="<?php echo esc_attr( $color ); ?>"></i> <?php echo esc_attr( $start_time . ' -  ' . $end_time ); ?></div>
		</div>

		<div class="iee-style4-event-image">
			<div class="iee-style4-event-image-img">
				<?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
                <img src="<?php echo esc_url( $image_url[0] ); ?>" alt="">
			</div>
		</div>
	</div>
</div>