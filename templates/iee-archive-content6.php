<?php
/**
 * Template part for displaying dynamic events
 */
global $iee_events;
$start_date_str      = get_post_meta( get_the_ID(), 'start_ts', true );
$start_date_formated = date_i18n( 'F j, Y ', $start_date_str );
$event_address       = get_post_meta( get_the_ID(), 'venue_name', true );
$venue_address       = get_post_meta( get_the_ID(), 'venue_address', true );
$iee_event_id        = get_post_meta( get_the_ID(), 'iee_event_id', true );

if ( $event_address != '' && $venue_address != '' ) {
    $event_address .= ' - ' . $venue_address;
} elseif ( $venue_address != '' ) {
    $event_address = $venue_address;
}

$iee_options = get_option( IEE_OPTIONS );
$accent_color = isset( $iee_options['accent_color'] ) ? $iee_options['accent_color'] : '#039ED7';
$time_format  = isset( $iee_options['time_format'] ) ? $iee_options['time_format'] : '12hours';

if( $time_format === '12hours' ){
    $start_time = date_i18n( 'h:i a', $start_date_str );
}elseif($time_format === '24hours' ){
    $start_time = date_i18n( 'G:i', $start_date_str );
}else{
    $start_time = date_i18n( get_option( 'time_format' ), $start_date_str );
}

$image_url = array();
if ( has_post_thumbnail() ) {
    $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
    $event_image = $image_url[0];
} else {
    $image_date  = date_i18n( 'F+d', $start_date_str );
    $event_image = 'https://dummyimage.com/420x210/ccc/969696.png&text=' . $image_date;
}

$event_url = get_permalink();
$eb_event_url = get_post_meta( get_the_ID(), 'iee_event_link', true );
$target = '';
if ( 'yes' === $direct_link ){
	$event_url = $eb_event_url;
	$target = 'target="_blank"';
}


$event_title       = get_the_title();
$event_description = wp_trim_words( get_the_excerpt(), 15, '...' );
?>
<div <?php post_class( array( $css_class, 'archive-event' ) ); ?> >
    <div class="iee6_event-card">
        <div class="iee6_event-image">
            <a href="<?php echo esc_url( $event_url ); ?>" <?php echo esc_attr( $target ); ?> >
                <?php // phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>
                <img src="<?php echo esc_url( $event_image ); ?>" alt="<?php echo esc_attr( $event_title ); ?>">
            </a>
        </div>
        <div class="iee6_event-info">
            <span class="iee6_event-date" style="color:<?php echo esc_attr( $accent_color ); ?>" >
                <?php echo esc_attr( date_i18n( 'D j', $start_date_str ) ); ?>
            </span>
            <a class="iee6_event-title"  href="<?php echo esc_url( $event_url ); ?>" <?php echo esc_attr( $target ); ?> >
                <div style="color:<?php echo esc_attr( $accent_color ); ?>"  ><?php echo esc_html( $event_title ); ?></div>
            </a>
            <div class="iee6_event-time"  >
                <svg class="iee6_eventbrite-item-datetime-icon"  viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 4a8 8 0 1 0 0 16 8 8 0 0 0 0-16zM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12zm10-6a1 1 0 0 1 1 1v4.586l2.707 2.707a1 1 0 0 1-1.414 1.414l-3-3A1 1 0 0 1 11 12V7a1 1 0 0 1 1-1z" />
                </svg>
                <?php echo esc_html( $start_date_formated ) . ' @ ' . esc_html( $start_time ); ?>
            </div>
            <div class="iee6_event-location">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 48 64" xml:space="preserve" class="iee6_eventbrite-item-location-icon" >
                    <g>
                        <path d="M24,0C10.7,0,0,11.2,0,25.3c0,12,16.5,31.7,21.6,37.6c0.5,0.8,1.6,1.1,2.4,1.1c1.1,0,1.9-0.5,2.4-1.1 C31.5,57.1,48,37.1,48,25.3C48,11.2,37.3,0,24,0z M24,57.6C14.9,46.9,5.3,32.8,5.3,25.3c0-11.2,8.3-20,18.7-20s18.7,9.1,18.7,20 C42.7,32.8,33.1,46.9,24,57.6z"></path>
                        <path d="M24,13.3c-5.9,0-10.7,4.8-10.7,10.7S18.1,34.7,24,34.7S34.7,29.9,34.7,24S29.9,13.3,24,13.3z M24,29.3 c-2.9,0-5.3-2.4-5.3-5.3s2.4-5.3,5.3-5.3s5.3,2.4,5.3,5.3S26.9,29.3,24,29.3z"></path>
                    </g>
                </svg>
                <?php echo esc_html( $event_address ); ?>
            </div>
            <div class="iee6_event-description"><?php echo esc_html( $event_description ); ?></div>
            <div class="iee6_event-buttons">
                <a class="iee6_buy-btn" href="<?php echo esc_url( $eb_event_url ); ?>" style="background-color:<?php echo esc_attr( $accent_color ); ?>"  >
                    <?php esc_html_e( 'Buy tickets', 'import-eventbrite-events' ); ?>
                </a>
                <a class="iee6_details-btn" href="<?php echo esc_url( $event_url ); ?>" <?php echo esc_attr( $target ); ?> ><?php esc_html_e( 'View details', 'import-eventbrite-events' ); ?></a>
            </div>
        </div>
    </div>
</div>
