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
$event_date     = get_post_meta( get_the_ID(), 'event_start_date', true );
$start_hours    = get_post_meta( get_the_ID(), 'event_start_hour', true );
$start_minutes  = get_post_meta( get_the_ID(), 'event_start_minute', true );
$start_meridian = get_post_meta( get_the_ID(), 'event_start_meridian', true );
if ( $event_date != '' ) {
	$event_date = strtotime( $event_date );
}
$event_address = get_post_meta( get_the_ID(), 'venue_name', true );
$venue_address = get_post_meta( get_the_ID(), 'venue_address', true );
if ( $event_address != '' && $venue_address != '' ) {
    $event_address .= ' - ' . $venue_address;
} elseif ( $venue_address != '' ) {
    $event_address = $venue_address;
}

$iee_options = get_option( IEE_OPTIONS );
$accent_color = isset( $iee_options['accent_color'] ) ? $iee_options['accent_color'] : '#039ED7';

$image_url = array();
if ( '' !== get_the_post_thumbnail() ) {
    $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
} else {
    $image_date  = date_i18n( 'F+d', $event_date );
    $image_url[] = 'https://dummyimage.com/420x210/ccc/969696.png&text=' . $image_date;
}

if ( '' !== get_the_post_thumbnail() ) {
    $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
}

$event_url = esc_url( get_permalink() );
$target = '';
if ( 'yes' === $direct_link ){
    $event_url = get_post_meta( get_the_ID(), 'iee_event_link', true );
    $target = 'target="_blank"';
}

$eve_cats = array();
$event_categories = wp_get_post_terms( get_the_ID(), $iee_events->cpt->get_event_categroy_taxonomy() );
if ( ! empty( $event_categories ) ) {
    foreach ( $event_categories as $event_category ) {
        $eve_cats[] = '<a class="iee_event_cat" style="background-color:'. $accent_color .';" href="' . esc_url( get_term_link( $event_category->term_id ) ) . '">' . $event_category->name . '</a>';
    }
}

?>
<div <?php post_class( array( $css_class, 'archive-event' ) ); ?> >
    <div class="iee-style2">
        <div class="iee_event_style2">
            <div class="iee_event_thumbnail">
                <a href="<?php echo $event_url; ?>">
                    <div class="iee_img_placeholder" style=" background: url('<?php echo $image_url[0]; ?>') no-repeat left top;"></div>
                </a>
                <span class="iee_event_meta_cat">
                    <?php
                        foreach( $eve_cats as $eve_cat ){
                            echo $eve_cat;
                        }
                    ?>
                </span>
            </div>
            <div class="iee_event_detail_style2">
                <h2 class="event_title_style2">
                    <a style="color:<?php echo $accent_color; ?>" href="<?php echo $event_url; ?>"><?php the_title(); ?></a>
                </h2>
                <div class="iee_event_location_time">
                    <div class="iee_event_time">
                        <span class="iee_time">
                            <i style="color:<?php echo $accent_color; ?>" class="fa fa-clock-o" aria-hidden="true"></i> <?php echo date_i18n( 'D, F j, Y ', $event_date ) . $start_hours . ':'. $start_minutes . ' ' . $start_meridian ; ?>
                        </span>
                    </div>
                    <div class="iee_location_style2">
                        <div class="iee_event_location">
                            <?php if ( $event_address != '' ) { ?>
                                <i style="color:<?php echo $accent_color; ?>" class="fa fa-map-marker"></i> <?php echo ucfirst( $event_address ); ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>