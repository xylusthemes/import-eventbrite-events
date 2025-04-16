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

$iee_options    = get_option( IEE_OPTIONS );
$accent_color   = isset( $iee_options['accent_color'] ) ? $iee_options['accent_color'] : '#039ED7';
$time_format    = isset( $iee_options['time_format'] ) ? $iee_options['time_format'] : '12hours';
$start_date_str = get_post_meta( get_the_ID(), 'start_ts', true);
$end_date_str   = get_post_meta( get_the_ID(), 'end_ts', true);
$event_address  = get_post_meta( get_the_ID(), 'venue_name', true );
$venue_address  = get_post_meta( get_the_ID(), 'venue_address', true );

$image_url = array();
if ( '' !== get_the_post_thumbnail() ) {
    $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
} else {
    $image_date  = date_i18n( 'F+d', $start_date_str );
    $image_url[] = 'https://dummyimage.com/420x210/ccc/969696.png&text=' . $image_date;
}
$img_src = $image_url[0];

$event_url = get_permalink();
$eb_event_url = get_post_meta( get_the_ID(), 'iee_event_link', true );
$target = '';
if ( 'yes' === $direct_link ){
	$event_url = get_post_meta( get_the_ID(), 'iee_event_link', true );
	$target = 'target="_blank"';
}
?>
<div class="iee5_event">
    <div class="iee5_event-details">
        <div class="iee5_event-date" style="color:<?php echo esc_attr( $accent_color ); ?>">
            <?php 
                if ( $start_date_str && $end_date_str ) {
                    $start_date = date_i18n( 'l, F j, Y @ g:i A', $start_date_str );
                    $end_date   = date_i18n( 'l, F j, Y @ g:i A', $end_date_str );
                    echo esc_html( $start_date . ' - ' . $end_date );
                } else {
                    echo esc_html__('Event date not available', 'text-domain');
                }
            ?>
        </div>
        <h3 class="iee5_event-title" >
            <?php the_title(); ?>
        </h3>
        <div class="iee5_event-location">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 48 64" xml:space="preserve" class="iee5_eventbrite-item-location-icon" style="fill:<?php echo esc_attr( $accent_color ); ?>">
                <g>
                    <path d="M24,0C10.7,0,0,11.2,0,25.3c0,12,16.5,31.7,21.6,37.6c0.5,0.8,1.6,1.1,2.4,1.1c1.1,0,1.9-0.5,2.4-1.1 C31.5,57.1,48,37.1,48,25.3C48,11.2,37.3,0,24,0z M24,57.6C14.9,46.9,5.3,32.8,5.3,25.3c0-11.2,8.3-20,18.7-20s18.7,9.1,18.7,20 C42.7,32.8,33.1,46.9,24,57.6z"></path>
                    <path d="M24,13.3c-5.9,0-10.7,4.8-10.7,10.7S18.1,34.7,24,34.7S34.7,29.9,34.7,24S29.9,13.3,24,13.3z M24,29.3 c-2.9,0-5.3-2.4-5.3-5.3s2.4-5.3,5.3-5.3s5.3,2.4,5.3,5.3S26.9,29.3,24,29.3z"></path>
                </g>
            </svg>
            <?php 
                if ($event_address && $venue_address) {
                    echo esc_html( $event_address . ' - ' . $venue_address );
                } elseif ($venue_address) {
                    echo esc_html($venue_address);
                }
            ?>
        </div>
        <a class="iee5_buy-tickets" style="color: #fff;text-decoration: none;background-color:<?php echo esc_attr( $accent_color ); ?>" href="<?php echo esc_url( $eb_event_url ); ?>" <?php echo $target; ?> >
            <div >
                <?php esc_html_e('Buy tickets', 'text-domain'); ?>
            </div>
        </a>
    </div>
    <a href="<?php echo esc_url( $event_url ); ?>" <?php echo $target; ?> class="iee5_event-image">
        <img src="<?php echo esc_url( $img_src ); ?>" alt="<?php the_title_attribute(); ?>">
    </a>
</div>

