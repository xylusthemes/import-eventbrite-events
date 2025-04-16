<?php
/**
 * The template for displaying all single Event meta
 */
global $iee_events;

if ( ! isset( $event_id ) || empty( $event_id ) ) {
	$event_id = get_the_ID();
}

$get_gmap_key        = get_option( 'iee_google_maps_api_key', false );
$start_date_str      = get_post_meta( $event_id, 'start_ts', true );
$end_date_str        = get_post_meta( $event_id, 'end_ts', true );
$start_date_formated = date_i18n( 'F j', $start_date_str );
$end_date_formated   = date_i18n( 'F j', $end_date_str );
$website             = get_post_meta( $event_id, 'iee_event_link', true );
$series_id      	 = get_post_meta( $event_id, 'series_id', true );
$real_time  		 = current_time( 'timestamp' );

$iee_options = get_option( IEE_OPTIONS );
$accent_color = isset( $iee_options['accent_color'] ) ? $iee_options['accent_color'] : '#039ED7';
$time_format = isset( $iee_options['time_format'] ) ? $iee_options['time_format'] : '12hours';
if($time_format == '12hours' ){
    $start_time          = date_i18n( 'h:i a', $start_date_str );
    $end_time            = date_i18n( 'h:i a', $end_date_str );
}elseif($time_format == '24hours' ){
    $start_time          = date_i18n( 'G:i', $start_date_str );
    $end_time            = date_i18n( 'G:i', $end_date_str );
}else{
    $start_time          = date_i18n( get_option( 'time_format' ), $start_date_str );
    $end_time            = date_i18n( get_option( 'time_format' ), $end_date_str );
}

?>
<div class="iee_event_meta">
<div class="iee_organizermain">
  <div class="details">
	<div class="titlemain" > <?php esc_html_e( 'Details', 'import-eventbrite-events' ); ?> </div>

	<?php
	if ( date( 'Y-m-d', strtotime( $start_date_str ) ) == date( 'Y-m-d', strtotime( $end_date_str ) ) ) {
		?>
		<strong><?php esc_html_e( 'Date', 'import-eventbrite-events' ); ?>:</strong>
		<p><?php echo esc_attr( $start_date_formated ); ?></p>

		<strong><?php esc_html_e( 'Time', 'import-eventbrite-events' ); ?>:</strong>
		<p><?php echo esc_attr( $start_time ) . ' - ' . esc_attr( $end_time ); ?></p>
		<?php
	} else {
		?>
		<strong><?php esc_html_e( 'Start', 'import-eventbrite-events' ); ?>:</strong>
		<p><?php echo esc_attr( $start_date_formated ). ' - ' . esc_attr( $start_time ); ?></p>

		<strong><?php esc_html_e( 'End', 'import-eventbrite-events' ); ?>:</strong>
		<p><?php echo esc_attr( $end_date_formated ) . ' - ' . esc_attr( $end_time ); ?></p>
		<?php
	}

	$eve_tags         = $eve_cats = array();
	$event_categories = wp_get_post_terms( $event_id, $iee_events->cpt->get_event_categroy_taxonomy() );
	if ( ! empty( $event_categories ) ) {
		foreach ( $event_categories as $event_category ) {
			$eve_cats[] = '<a href="' . esc_url( get_term_link( $event_category->term_id ) ) . '">' . $event_category->name . '</a>';
		}
	}

	$event_tags = wp_get_post_terms( $event_id, $iee_events->cpt->get_event_tag_taxonomy() );
	if ( ! empty( $event_tags ) ) {
		foreach ( $event_tags as $event_tag ) {
			$eve_tags[] = '<a href="' . esc_url( get_term_link( $event_tag->term_id ) ) . '">' . $event_tag->name . '</a>';
		}
	}

	if ( ! empty( $eve_cats ) ) {
		?>
		<strong><?php esc_html_e( 'Event Category', 'import-eventbrite-events' ); ?>:</strong>
		<p><?php echo implode( ', ', $eve_cats ); ?></p>
		<?php
	}

	if ( ! empty( $eve_tags ) ) {
		?>
		<strong><?php esc_html_e( 'Event Tags', 'import-eventbrite-events' ); ?>:</strong>
		<p><?php echo implode( ', ', $eve_tags ); ?></p>
		<?php
	}
	?>

	<?php if ( $website != '' ) { ?>
		<strong><?php esc_html_e( 'Click to Register', 'import-eventbrite-events' ); ?>:</strong>
		<a href="<?php echo esc_url( $website ); ?>"><?php echo esc_url( $website ); ?></a>
	<?php } ?>

  </div>

	<?php
		  // Organizer
		$org_name  = get_post_meta( $event_id, 'organizer_name', true );
		$org_email = get_post_meta( $event_id, 'organizer_email', true );
		$org_phone = get_post_meta( $event_id, 'organizer_phone', true );
		$org_url   = get_post_meta( $event_id, 'organizer_url', true );

	if ( $org_name != '' ) {
		?>
		<div class="organizer">
			<div class="titlemain"><?php esc_html_e( 'Organizer', 'import-eventbrite-events' ); ?></div>
			<p><?php echo esc_attr( $org_name ); ?></p>
			</div>
			<?php if ( $org_email != '' ) { ?>
				<strong><?php esc_html_e( 'Email', 'import-eventbrite-events' ); ?>:</strong>
				<a href="<?php echo 'mailto:' . esc_attr( $org_email ); ?>"><?php echo esc_attr( $org_email ); ?></a>
			<?php } ?>
			<?php if ( $org_phone != '' ) { ?>
				<strong><?php esc_html_e( 'Phone', 'import-eventbrite-events' ); ?>:</strong>
				<a href="<?php echo 'tel:' . esc_attr( $org_phone ); ?>"><?php echo esc_attr( $org_phone ); ?></a>
			<?php } ?>
			<?php if ( $website != '' ) { ?>
				<strong style="display: block;">
					<?php esc_html_e( 'Website', 'import-eventbrite-events' ); ?>:
				</strong>
				<a href="<?php echo esc_url( $org_url ); ?>"><?php echo esc_url( $org_url ); ?></a>
			<?php
}
	}
	?>
	<div style="clear: both"></div>
</div>

<?php
$venue_name       = get_post_meta( $event_id, 'venue_name', true );
$venue_address    = get_post_meta( $event_id, 'venue_address', true );
$venue['city']    = get_post_meta( $event_id, 'venue_city', true );
$venue['state']   = get_post_meta( $event_id, 'venue_state', true );
$venue['country'] = get_post_meta( $event_id, 'venue_country', true );
$venue['zipcode'] = get_post_meta( $event_id, 'venue_zipcode', true );
$venue['lat']     = get_post_meta( $event_id, 'venue_lat', true );
$venue['lon']     = get_post_meta( $event_id, 'venue_lon', true );
$venue_url        = esc_url( get_post_meta( $event_id, 'venue_url', true ) );
if ( iee_is_pro() && empty( $get_gmap_key ) ) {
	$map_api_key  = IEEPRO_GM_APIKEY;
}elseif( !empty( $get_gmap_key ) ){
	$map_api_key  = $get_gmap_key;
}else{
	$map_api_key  = '';
}

if ( ! empty( $venue_address ) || ( ! empty( $venue['lat'] ) && ! empty( $venue['lon'] ) ) ) {
	?>
	<div class="iee_organizermain library">
		<div class="venue">
			<div class="titlemain"> <?php esc_html_e( 'Venue', 'import-eventbrite-events' ); ?> </div>
			<p><?php echo esc_attr( $venue_name ); ?></p>
			<?php
			if ( $venue_address != '' ) {
				echo '<p><i>' . esc_attr( $venue_address ). '</i></p>';
			}
			$venue_array = array();
			foreach ( $venue as $key => $value ) {
				if ( in_array( $key, array( 'city', 'state', 'country', 'zipcode' ) ) ) {
					if ( $value != '' ) {
						$venue_array[] = $value;
					}
				}
			}
			echo '<p><i>' . implode( ', ', $venue_array ) . '</i></p>';
			?>
		</div>
		<?php
		$q = '';
		$lat_lng = '';
		if ( ! empty( $venue['lat'] ) && ! empty( $venue['lon'] ) ) {
			$lat_lng = esc_attr( $venue['lat'] ) . ',' . esc_attr( $venue['lon'] );
		}
		if ( ! empty( $venue_address ) ) {
			$q = esc_attr( $venue_address );
		}
		if ( ! empty( $venue_name ) && ! empty( $venue_address ) ) {
			$q = esc_attr( $venue_name ) . ',' . esc_attr( $venue_address );
		}
		if(empty($q)){
			$q = $lat_lng;
		}
		if ( ! empty( $q ) ) {
			$params = array(
				'q' => $q
			);
			if ( ! empty( $lat_lng ) ) {
				$params['center'] = $lat_lng;
			}
			$query = http_build_query($params);
			if( empty( $map_api_key ) ){
				$full_address = str_replace( ' ', '%20', $venue_address ) .','. $venue['city'] .','. $venue['state'] .','. $venue['country'].'+(' . str_replace( ' ', '%20', $venue_name ) . ')';	
				?>
				<div class="map">
					<iframe src="https://maps.google.com/maps?q=<?php echo $full_address; ?>&hl=es;z=14&output=embed" width="100%" height="350" frameborder="0" style="border:0; margin:0;" allowfullscreen></iframe>
				</div>
				<?php
			}else{ 
				?>
				<div class="map">
					<iframe src="https://www.google.com/maps/embed/v1/place?key=<?php echo esc_attr( $map_api_key ); ?>&<?php echo esc_attr( $query ); ?>" width="100%" height="350" frameborder="0" style="border:0; margin:0;" allowfullscreen></iframe>
				</div>
				<?php
			}
		}
		?>
		<div style="clear: both;"></div>
	</div>
	<?php
}
		
if( !empty( $series_id ) ){

	$args = array(
		'post_type'    => 'eventbrite_events',
		'numberposts'  => 5,
		'order'        => 'ASC',
		'meta_query'   => array(
			'relation' => 'AND',
			array(
				'key'     => 'series_id',
				'value'   => $series_id,
				'compare' => '=',
			),
			array(
				'key'     => 'start_ts',
				'value'   => $real_time,
				'compare' => '>=',
			),
			array(
				'key'     => 'start_ts',
				'value'   => $start_date_str,
				'compare' => '!=',
			),
		),
	);
	$multiple_events = get_posts( $args );

	if ( !empty( $multiple_events ) && $multiple_events > 0 ) { ?>
		<div class="iee_recurring_list_container">
			<div class="recurring_title" style="text-align:center;" > <?php esc_html_e( 'Multiple Dates', 'import-eventbrite-events' ); ?> </div>
			<ul class="iee_recurring_list_main" >
			<?php 
				foreach ( $multiple_events as $multiple_event ) {
					$start_date 	= get_post_meta( $multiple_event->ID, 'start_ts', true );
					$end_date   	= get_post_meta( $multiple_event->ID, 'end_ts', true );
					?>
						<li class="iee_recurring_list" >
							<div class="iee-multiple-date-container" >
								<div class="iee-multiple-date-container-min" >
									<p class="iee-multiple-date" ><?php echo esc_attr( date_i18n( 'M', $start_date ) );?></p>
									<p class="iee-multiple-date1" ><?php echo esc_attr( date_i18n( 'd', $start_date ) );?></p>
								</div>
							</div>
							<div class="iee-multiple-date-container">
								<div class="iee-multiple-date-container-min">
									<div class="iee-date-title" ><?php echo esc_attr( date_i18n( 'D ', $start_date ) ).','. esc_attr( date_i18n( ' g:i A ', $start_date ) ) ." - ". esc_attr( date_i18n( ' g:i A', $end_date ) ); ?></div>
									<div class="iee_multidate-title"><a href="<?php echo esc_attr( $multiple_event->guid ); ?>" ><?php echo esc_attr( $multiple_event->post_title ); ?></a></div>
								</div>
							</div>
							<div class="iee-multiple-date-container">
								<a href="javascript:void(0)" class="iee-multidate-button" id="iee-eventbrite-recurring-checkout-<?php echo esc_attr( $multiple_event->ID );?>" data-series-id="<?php echo esc_attr( $series_id );  ?>" ><?php esc_html_e( 'Tickets', 'import-eventbrite-events' ); ?></a>
							</div>
						</li>
					<?php 
				} ?>
			</ul>
		</div><?php
	} 
}
?>
</div>
<div style="clear: both;"></div>
<style>
.iee-multidate-button{
	background-color: <?php echo esc_attr( $accent_color ); ?>
}
</style>
<script src="https://www.eventbrite.com/static/widgets/eb_widgets.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('.iee-multidate-button').on("click", function(){
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
