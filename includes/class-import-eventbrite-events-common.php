<?php
/**
 * Common functions class for WP Event aggregator.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    Import_Eventbrite_Events
 * @subpackage Import_Eventbrite_Events/includes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Import_Eventbrite_Events_Common {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'setup_success_messages' ) );
		add_action( 'admin_init', array( $this, 'handle_listtable_oprations' ), 99 );
		add_action( 'admin_init', array( $this, 'handle_import_settings_submit' ), 99 );
		add_action( 'wp_ajax_iee_render_terms_by_plugin', array( $this, 'iee_render_terms_by_plugin' ) );
		add_action( 'tribe_events_single_event_after_the_meta', array( $this, 'iee_add_tec_ticket_section' ) );
		add_filter( 'the_content', array( $this, 'iee_add_em_add_ticket_section' ), 20 );
		add_action( 'ep_after_single_event_contant', array( $this, 'iee_add_eventprime_add_ticket_section' ) );
		add_filter( 'mc_event_content', array( $this, 'iee_add_my_calendar_ticket_section' ), 10, 4 );
		add_action( 'iee_render_pro_notice', array( $this, 'render_pro_notice' ) );
		add_action( 'admin_init', array( $this, 'iee_check_for_minimum_pro_version' ) );
	}

	/**
	 * Render Import into plugin section at import screen.
	 *
	 * @since    1.0.0
	 * @param string $selected Selected suported plugin.
	 * @param array  $taxonomy_terms Terms of perticular taxonomy.
	 * @return void
	 */
	public function render_import_into_and_taxonomy( $selected = '', $taxonomy_terms = array() ) {

		$active_plugins = $this->get_active_supported_event_plugins();
		?>	
		<tr class="event_plugis_wrapper">
			<th scope="row">
				<?php esc_attr_e( 'Import into', 'import-eventbrite-events' ); ?> :
			</th>
			<td>
				<select name="event_plugin" class="eventbrite_event_plugin">
					<?php
					if ( ! empty( $active_plugins ) ) {
						foreach ( $active_plugins as $slug => $name ) {
							?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $selected, $slug ); ?>><?php echo esc_attr( $name ); ?></option>
							<?php
						}
					}
					?>
				</select>
			</td>
		</tr>

		<tr class="event_cats_wrapper">
			<th scope="row">
				<?php esc_attr_e( 'Event Categories for Event Import', 'import-eventbrite-events' ); ?> : 
			</th>
			<td>
				<?php
				$taxo_cats = $taxo_tags = '';
				if ( ! empty( $taxonomy_terms ) && isset( $taxonomy_terms['cats'] ) ) {
					$taxo_cats = implode( ',', $taxonomy_terms['cats'] );
				}
				if ( ! empty( $taxonomy_terms ) && isset( $taxonomy_terms['tags'] ) ) {
					$taxo_tags = implode( ',', $taxonomy_terms['tags'] );
				}
				?>
				<input type="hidden" id="iee_taxo_cats" value="<?php echo esc_attr( $taxo_cats ); ?>" />
				<input type="hidden" id="iee_taxo_tags" value="<?php echo esc_attr( $taxo_tags ); ?>" />
				<div class="event_taxo_terms_wraper">

				</div>
				<span class="iee_small">
					<?php esc_attr_e( 'These categories are assign to imported event.', 'import-eventbrite-events' ); ?>
				</span>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render Taxonomy Terms based on event import into Selection.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function iee_render_terms_by_plugin() {
		global $iee_events;
		$event_plugin   = !empty( esc_attr( wp_unslash( $_REQUEST['event_plugin'] ) ) ) ? esc_attr( wp_unslash( $_REQUEST['event_plugin'] ) ) : '';
		$event_taxonomy = '';
		$taxo_cats      = $taxo_tags = array();
		if ( isset( $_REQUEST['taxo_cats'] ) ) {
			$taxo_cats = explode( ',', wp_unslash ( $_REQUEST['taxo_cats'] ) );
		}
		if ( isset( $_REQUEST['taxo_tags'] ) ) {
			$taxo_tags = explode( ',', wp_unslash ( $_REQUEST['taxo_tags'] ) );
		}

		if ( ! empty( $event_plugin ) ) {
			$event_taxonomy = $iee_events->$event_plugin->get_taxonomy();
		}

		$terms = array();
		if ( $event_taxonomy != '' ) {
			if ( taxonomy_exists( $event_taxonomy ) ) {
				$terms = get_terms( $event_taxonomy, array( 'hide_empty' => false ) );
			}
		}
		if ( ! empty( $terms ) ) {
		?>
			<select name="event_cats[]" multiple="multiple">
				<?php foreach ( $terms as $term ) { ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php if ( in_array( $term->term_id, $taxo_cats ) ) { echo 'selected="selected"'; } ?> >
						<?php echo esc_attr( $term->name ); ?>                                	
					</option>
				<?php } ?> 
			</select>
			<?php
		}
		wp_die();
	}

	/**
	 * Get Active supported active plugins.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_active_supported_event_plugins() {

		$supported_plugins = array();
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		// check The Events Calendar active or not if active add it into array.
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			$supported_plugins['tec'] = __( 'The Events Calendar', 'import-eventbrite-events' );
		}

		// check Events Manager.
		if ( defined( 'EM_VERSION' ) ) {
			$supported_plugins['em'] = __( 'Events Manager', 'import-eventbrite-events' );
		}

		// Check event_organizer.
		if ( defined( 'EVENT_ORGANISER_VER' ) && defined( 'EVENT_ORGANISER_DIR' ) ) {
			$supported_plugins['event_organizer'] = __( 'Event Organiser', 'import-eventbrite-events' );
		}

		// check EventON.
		if ( class_exists( 'EventON' ) ) {
			$supported_plugins['eventon'] = __( 'EventON', 'import-eventbrite-events' );
		}

		// check EventPrime.
		if ( class_exists( 'Eventprime_Event_Calendar_Management_Admin' ) ) {
			$supported_plugins['eventprime'] = __( 'EventPrime', 'import-eventbrite-events' );
		}

		// check All in one Event Calendar
		if ( class_exists( 'Ai1ec_Event' ) ) {
			$supported_plugins['aioec'] = __( 'All in one Event Calendar', 'import-eventbrite-events' );
		}

		// check My Calendar
		if ( is_plugin_active( 'my-calendar/my-calendar.php' ) ) {
			$supported_plugins['my_calendar'] = __( 'My Calendar', 'import-eventbrite-events' );
		}

		// Check EE4
		if ( defined( 'EVENT_ESPRESSO_VERSION' ) && defined( 'EVENT_ESPRESSO_MAIN_FILE' ) ) {
			$supported_plugins['ee4'] = __( 'Event Espresso (EE4)', 'import-eventbrite-events' );
		}

		$iee_options       = get_option( IEE_OPTIONS );
		$deactive_ieevents = isset( $iee_options['deactive_ieevents'] ) ? $iee_options['deactive_ieevents'] : 'no';
		if ( $deactive_ieevents != 'yes' ) {
			$supported_plugins['iee'] = __( 'Eventbrite Events', 'import-eventbrite-events' );
		}
		$supported_plugins = apply_filters( 'iee_supported_plugins', $supported_plugins );
		return $supported_plugins;
	}

	/**
	 * Setup Featured image to events
	 *
	 * @since    1.0.0
	 * @param int    $event_id event id.
	 * @param string $image_url Image URL.
	 * @return int $attachment_id Attachment ID
	 */
	public function setup_featured_image_to_event( $event_id, $image_url = '' ) {
		if ( $image_url == '' ) {
			return;
		}
		$event = get_post( $event_id );
		if ( empty( $event ) ) {
			return;
		}

		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$event_title = $event->post_title;

		if ( ! empty( $image_url ) ) {
			$without_ext = false;
			// Set variables for storage, fix file filename for query strings.
			preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png|webp)\b/i', $image_url, $matches );
			if ( ! $matches ) {
				if(strpos($image_url, "https://cdn.evbuc.com") === 0 || strpos($image_url, "https://img.evbuc.com") === 0){
					$without_ext = true;
				}else{
					return new WP_Error( 'image_sideload_failed', __( 'Invalid image URL', 'import-eventbrite-events' ) );
				}
			}
			$iee_options         = get_option( IEE_OPTIONS );
			$small_thumbnail     = isset( $iee_options['small_thumbnail'] ) ? $iee_options['small_thumbnail'] : 'no';
			if( $small_thumbnail == 'yes'){
				$image_url       = str_replace( 'original.', 'logo.', $image_url );
			}

			$args = array(
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'fields'      => 'ids',
				'meta_query'  => array( // @codingStandardsIgnoreLine.
					array(
						'value' => $image_url,
						'key'   => '_iee_attachment_source',
					),
				),
			);

			$id  = 0;
			$ids = get_posts( $args ); // @codingStandardsIgnoreLine.
			if ( $ids ) {
				$id = current( $ids );
			}

			if ( $id && $id > 0 ) {
				set_post_thumbnail( $event_id, $id );
				return $id;
			}

			$file_array         = array();
			$file_array['name'] = $event->ID . '_image';
			if($without_ext === true){
				$file_array['name'] .= '.jpg';
			}else{
				$file_array['name'] .=  '_'.basename( $matches[0] );
			}

			// Download file to temp location.
			$file_array['tmp_name'] = download_url( $image_url );

			// If error storing temporarily, return the error.
			if ( is_wp_error( $file_array['tmp_name'] ) ) {
				return $file_array['tmp_name'];
			}

			// Do the validation and storage stuff.
			$att_id = media_handle_sideload( $file_array, $event_id, $event_title );

			// If error storing permanently, unlink.
			if ( is_wp_error( $att_id ) ) {
				@unlink( $file_array['tmp_name'] );
				return $att_id;
			}

			if ( $att_id ) {
				set_post_thumbnail( $event_id, $att_id );
			}

			// Save attachment source for future reference.
			update_post_meta( $att_id, '_iee_attachment_source', $image_url );

			return $att_id;
		}
	}

	/**
	 * Display Ticket Section after eventbrite events.
	 *
	 * @since 1.0.0
	 */
	public function iee_add_tec_ticket_section() {
		global $iee_events;
		$event_id            = get_the_ID();
		$xt_post_type        = get_post_type();
		$event_origin        = get_post_meta( $event_id, 'iee_event_origin', true );
		$eventbrite_event_id = get_post_meta( $event_id, 'iee_eventbrite_event_id', true );
		if ( $event_id > 0 ) {
			if ( $event_origin == 'eventbrite' ) {
				if ( $iee_events->tec->get_event_posttype() == $xt_post_type ) {
					$eventbrite_id = get_post_meta( $event_id, 'iee_event_id', true );
					$series_id  = get_post_meta( $event_id, 'series_id', true );
					if( !empty( $series_id ) ){
						$eventbrite_id = $series_id;
					}
					if ( $eventbrite_id && $eventbrite_id > 0 && is_numeric( $eventbrite_id ) ) {
						$ticket_section = $this->iee_get_ticket_section( $eventbrite_id );
						echo $ticket_section;
					}
				}
			} elseif ( $eventbrite_event_id && $eventbrite_event_id > 0 && is_numeric( $eventbrite_event_id ) ) {
				$ticket_section = $this->iee_get_ticket_section( $eventbrite_event_id );
				echo $ticket_section;
			}
		}
	}

	/**
	 * Display Ticket Section after eventbrite events.
	 *
	 * @since 1.0.0
	 */
	public function iee_add_my_calendar_ticket_section( $details = '', $event = array(), $type = '', $time = '' ) {
		global $iee_events;
		$ticket_section = '';
		if ( $type == 'single' ) {
			$event_id     = $event->event_post;
			$xt_post_type = get_post_type( $event_id );
			$event_origin = get_post_meta( $event_id, 'iee_event_origin', true );
			if ( $event_id > 0 && $event_origin == 'eventbrite' ) {
				if ( $iee_events->my_calendar->get_event_posttype() == $xt_post_type ) {
					$eventbrite_id = get_post_meta( $event_id, 'iee_event_id', true );
					$series_id  = get_post_meta( $event_id, 'series_id', true );
					if( !empty( $series_id ) ){
						$eventbrite_id = $series_id;
					}
					if ( $eventbrite_id && $eventbrite_id > 0 && is_numeric( $eventbrite_id ) ) {
						$ticket_section = $this->iee_get_ticket_section( $eventbrite_id );
					}
				}
			}
		}
		return $details . $ticket_section;
	}

	/**
	 * Get do not update data fields
	 *
	 * @since  1.5.3
	 * @return array
	 */
	public function iee_is_updatable( $field = '' ) {
		if ( empty( $field ) ){ return true; }
		if ( !iee_is_pro() ){ return true; }
		$iee_options = get_option( IEE_OPTIONS, array() );
		$eventbrite_options = isset( $iee_options['dont_update'] ) ? $iee_options['dont_update'] : array();
		if ( isset( $eventbrite_options[$field] ) &&  'yes' == $eventbrite_options[$field] ){
			return false;
		}
		return true;
	}


	/**
	 * Add ticket section to Eventbrite event.
	 *
	 * @since    1.0.0
	 */
	public function iee_add_em_add_ticket_section( $content = '' ) {
		global $iee_events;
		$xt_post_type = get_post_type();
		$event_id     = get_the_ID();
		$event_origin = get_post_meta( $event_id, 'iee_event_origin', true );
		if ( $event_id > 0 && $event_origin == 'eventbrite' ) {
			if ( ( $iee_events->em->get_event_posttype() == $xt_post_type ) || ( $iee_events->eventprime->get_event_posttype() == $xt_post_type ) || ( $iee_events->aioec->get_event_posttype() == $xt_post_type ) || ( $iee_events->iee->get_event_posttype() == $xt_post_type ) || ( $iee_events->eventon->get_event_posttype() == $xt_post_type ) ) {
				$eventbrite_id = get_post_meta( $event_id, 'iee_event_id', true );
				$series_id  = get_post_meta( $event_id, 'series_id', true );
				if( !empty( $series_id ) ){
					$eventbrite_id = $series_id;
				}
				if ( $eventbrite_id && $eventbrite_id > 0 && is_numeric( $eventbrite_id ) ) {
					$ticket_section = $this->iee_get_ticket_section( $eventbrite_id );
					return $content . $ticket_section;
				}
			}
		}
		return $content;
	}

	/**
	 * Add ticket section to Eventbrite event.
	 *
	 * @since    1.0.0
	 */
	public function iee_add_eventprime_add_ticket_section() {
		global $iee_events;
		
		$xt_post_type = $iee_events->eventprime->get_event_posttype();
		$event_id     = isset( $_GET['event'] ) ?  $_GET['event'] : 0;
		$event_origin = get_post_meta( $event_id, 'iee_event_origin', true );
		if ( $event_id > 0 && $event_origin == 'eventbrite' ) {
			if ( ( $iee_events->eventprime->get_event_posttype() == $xt_post_type ) ) {
				$eventbrite_id = get_post_meta( $event_id, 'iee_event_id', true );
				$series_id     = get_post_meta( $event_id, 'series_id', true );
				if( !empty( $series_id ) ){
					$eventbrite_id = $series_id;
				}
				if ( $eventbrite_id && $eventbrite_id > 0 && is_numeric( $eventbrite_id ) ) {
					$ticket_section = $this->iee_get_ticket_section( $eventbrite_id );
					echo $ticket_section;
				}
			}
		}
	}

	/**
	 * Get Ticket Sectoin for eventbrite events.
	 *
	 * @since  1.1.0
	 * @return html
	 */
	public function iee_get_ticket_section( $eventbrite_id = 0 ) {
		$options = iee_get_import_options( 'eventbrite' );

		$enable_ticket_sec = isset( $options['enable_ticket_sec'] ) ? $options['enable_ticket_sec'] : 'no';
		$ticket_model = isset( $options['ticket_model'] ) ? $options['ticket_model'] : '0';
		if ( 'yes' != $enable_ticket_sec ) {
			return '';
		}

		if ( $eventbrite_id > 0 ) {
			ob_start();
			if( is_ssl() ){
				if('1'=== $ticket_model ){
					echo iee_model_checkout_markup($eventbrite_id);
				}else{
					echo iee_nonmodel_checkout_markup($eventbrite_id);
				}
			} else {
				?>
				<div class="eventbrite-ticket-section" style="width:100%; text-align:left;">
					<iframe id="eventbrite-tickets-<?php echo esc_attr( $eventbrite_id ); ?>" src="//www.eventbrite.com/tickets-external?eid=<?php echo esc_attr( $eventbrite_id ); ?>" style="width:100%;height:300px; border: 0px;"></iframe>
				</div>
				<?php
			}
			$ticket = ob_get_clean();
			return $ticket;
		} else {
			return '';
		}

	}

	/**
	 * Check if user has minimum pro version.
	 *
	 * @since    1.6
	 * @return void
	 */
	public function iee_check_for_minimum_pro_version() {
		if ( defined( 'IEEPRO_VERSION' ) ) {
			if ( version_compare( IEEPRO_VERSION, IEE_MIN_PRO_VERSION, '<' ) ) {
				global $iee_warnings;
				$iee_warnings[] = __( 'Your current "Import Eventbrite Pro" add-on is not compatible with the Free plugin. Please Upgrade Pro latest to work event importing Flawlessly.', 'import-eventbrite-events' );
			}
		}
	}

	/**
	 * Format events arguments as per TEC
	 *
	 * @since    1.0.0
	 * @param array $eventbrite_event Eventbrite event.
	 * @return array
	 */
	public function display_import_success_message( $import_data = array(), $import_args = array(), $schedule_post = '', $error_reason = '' ) {
		global $iee_success_msg, $iee_errors;
		if ( ! empty( $iee_errors ) ) {
			return;
		}

		$import_status = $import_ids = array();
		if( !empty( $import_data ) ){
			foreach ( $import_data as $key => $value ) {
				if ( $value['status'] == 'created' ) {
					$import_status['created'][] = $value;
				} elseif ( $value['status'] == 'updated' ) {
					$import_status['updated'][] = $value;
				} elseif ( $value['status'] == 'skipped' ) {
					$import_status['skipped'][] = $value;
				} elseif ( $value['status'] == 'skip_trash' ) {
					$import_status['skip_trash'][] = $value;
				} else {

				}
				if ( isset( $value['id'] ) ) {
					$import_ids[] = $value['id'];
				}
			}	
		}
		
		$created = $updated = $skipped = $skip_trash = 0;
		$created = isset( $import_status['created'] ) ? count( $import_status['created'] ) : 0;
		$updated = isset( $import_status['updated'] ) ? count( $import_status['updated'] ) : 0;
		$skipped = isset( $import_status['skipped'] ) ? count( $import_status['skipped'] ) : 0;
		$skip_trash = isset( $import_status['skip_trash'] ) ? count( $import_status['skip_trash'] ) : 0;

		$success_message = esc_html__( 'Event(s) are imported successfully.', 'import-eventbrite-events' ) . "<br>";
		if ( $created > 0 ) {
			$success_message .= "<strong>" . sprintf( __( '%d Created', 'import-eventbrite-events' ), $created ) . "</strong><br>";
		}
		if ( $updated > 0 ) {
			$success_message .= "<strong>" . sprintf( __( '%d Updated', 'import-eventbrite-events' ), $updated ) . "</strong><br>";
		}
		if ( $skipped > 0 ) {
			$success_message .= "<strong>" . sprintf( __( '%d Skipped (Already exists)', 'import-eventbrite-events' ), $skipped ) . "</strong><br>";
		}
		if ( $skip_trash > 0 ) {
			$success_message .= "<strong>" . sprintf( __( '%d Skipped (Already exists in Trash )', 'import-eventbrite-events' ), $skip_trash ) . "</strong><br>";
		}
		if ( !empty( $error_reason ) ) {
			$success_message .= "<strong>" . sprintf( __( '%d ', 'import-eventbrite-events' ), $error_reason ) . "</strong><br>";
		}
		$iee_success_msg[]    = $success_message;

		if ( $schedule_post != '' && $schedule_post > 0 ) {
			$temp_title = get_the_title( $schedule_post );
		} else {
			$temp_title = 'Manual Import';
		}
		$nothing_to_import = false;
		if($created == 0 && $updated == 0 && $skipped == 0 && $skip_trash == 0 ){
			$nothing_to_import = true;
		}

		if ( $created > 0 || $updated > 0 || $skipped > 0 || $nothing_to_import) {
			$insert_args = array(
				'post_type'   => 'iee_import_history',
				'post_status' => 'publish',
				'post_title'  => $temp_title . ' - ' . ucfirst( $import_args['import_origin'] ),
			);

			$insert = wp_insert_post( $insert_args, true );
			if ( ! is_wp_error( $insert ) ) {
				update_post_meta( $insert, 'import_origin', $import_args['import_origin'] );
				update_post_meta( $insert, 'created', $created );
				update_post_meta( $insert, 'updated', $updated );
				update_post_meta( $insert, 'skipped', $skipped );
				update_post_meta( $insert, 'skip_trash', $skip_trash );
				update_post_meta( $insert, 'nothing_to_import', $nothing_to_import );
				update_post_meta( $insert, 'error_reason', $error_reason );
				update_post_meta( $insert, 'imported_data', $import_data );
				update_post_meta( $insert, 'import_data', $import_args );
				if ( $schedule_post != '' && $schedule_post > 0 ) {
					update_post_meta( $insert, 'schedule_import_id', $schedule_post );
				}
			}
		}
	}

	/**
	 * Get Import events into selected destination.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function import_events_into( $centralize_array, $event_args ) {
		global $iee_events;
		$import_result     = array();
		$event_import_into = isset( $event_args['import_into'] ) ? $event_args['import_into'] : 'tec';
		if ( ! empty( $event_import_into ) ) {
			$import_result = $iee_events->$event_import_into->import_event( $centralize_array, $event_args );
		}
		return $import_result;
	}

	/**
	 * Render import Frequency
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function render_import_frequency( $selected = 'daily' ) {
		?>
		<select name="import_frequency" class="import_frequency" <?php if ( ! iee_is_pro() ) { echo 'disabled="disabled"'; } ?> >
			<option value='hourly' <?php selected( $selected, 'hourly' ); ?>>
				<?php esc_html_e( 'Once Hourly', 'import-eventbrite-events' ); ?>
			</option>
			<option value='twicedaily' <?php selected( $selected, 'twicedaily' ); ?>>
				<?php esc_html_e( 'Twice Daily', 'import-eventbrite-events' ); ?>
			</option>
			<option value="daily" <?php selected( $selected, 'daily' ); ?>>
				<?php esc_html_e( 'Once Daily', 'import-eventbrite-events' ); ?>
			</option>
			<option value="weekly" <?php selected( $selected, 'weekly' ); ?>>
				<?php esc_html_e( 'Once Weekly', 'import-eventbrite-events' ); ?>
			</option>
			<option value="monthly" <?php selected( $selected, 'monthly' ); ?>>
				<?php esc_html_e( 'Once a Month', 'import-eventbrite-events' ); ?>
			</option>
		</select>
		<?php
	}

	/**
	 * Render import type, one time or scheduled
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function render_import_type() {
		?>
		<select name="import_type" id="import_type" <?php if ( ! iee_is_pro() ) { echo 'disabled="disabled"'; } ?> >
			<option value="onetime" ><?php esc_attr_e( 'One-time Import', 'import-eventbrite-events' ); ?></option>
			<option value="scheduled" <?php if ( ! iee_is_pro() ) { echo 'disabled="disabled"  selected="selected"'; } ?> >
				<?php esc_attr_e( 'Scheduled Import', 'import-eventbrite-events' ); ?>
			</option>
		</select>
		<span class="hide_frequency">
			<?php $this->render_import_frequency(); ?>
		</span>
		<?php do_action( 'iee_render_pro_notice' ); ?>
		<?php
	}

	/**
	 * Clean URL.
	 *
	 * @since 1.0.0
	 */
	function clean_url( $url ) {

		$url = str_replace( '&amp;#038;', '&', $url );
		$url = str_replace( '&#038;', '&', $url );
		return $url;

	}

	/**
	 * Get UTC offset
	 *
	 * @since    1.0.0
	 */
	function get_utc_offset( $datetime ) {
		try {
			$datetime = new DateTime( $datetime );
		} catch ( Exception $e ) {
			return '';
		}

		$timezone = $datetime->getTimezone();
		$offset   = $timezone->getOffset( $datetime ) / 60 / 60;

		if ( $offset >= 0 ) {
			$offset = '+' . $offset;
		}

		return 'UTC' . $offset;
	}

	/**
	 * Render dropdown for Imported event status.
	 *
	 * @since 1.0
	 * @return void
	 */
	function render_eventstatus_input( $selected = 'publish' ) {
		?>
		<tr class="event_status_wrapper">
			<th scope="row">
				<?php esc_attr_e( 'Status', 'import-eventbrite-events' ); ?> :
			</th>
			<td>
				<select name="event_status" >
					<option value="publish" <?php selected( $selected, 'publish' ); ?> >
						<?php esc_html_e( 'Published', 'import-eventbrite-events' ); ?>
					</option>
					<option value="pending" <?php selected( $selected, 'pending' ); ?>>
						<?php esc_html_e( 'Pending', 'import-eventbrite-events' ); ?>
					</option>
					<option value="draft" <?php selected( $selected, 'draft' ); ?>>
						<?php esc_html_e( 'Draft', 'import-eventbrite-events' ); ?>
					</option>
				</select>
			</td>
		</tr>
		<?php
	}

	/**
	 * remove query string from URL.
	 *
	 * @since 1.0.0
	 */
	function convert_datetime_to_db_datetime( $datetime ) {
		try {
			$datetime = new DateTime( $datetime );
			return $datetime->format( 'Y-m-d H:i:s' );
		} catch ( Exception $e ) {
			return $datetime;
		}
	}

	/**
	 * Check for Existing Event
	 *
	 * @since    1.0.0
	 * @param int $event_id event id.
	 * @return /boolean
	 */
	public function get_event_by_event_id( $post_type, $event_id ) {
		global $wpdb;
		$iee_options       = get_option( IEE_OPTIONS );
		$skip_trash        = isset( $iee_options['skip_trash'] ) ? $iee_options['skip_trash'] : 'no';
		if( $skip_trash === 'yes' ){
			$get_post_id = $wpdb->get_col(
				$wpdb->prepare(
					'SELECT ' . $wpdb->prefix . 'posts.ID FROM ' . $wpdb->prefix . 'posts, ' . $wpdb->prefix . 'postmeta WHERE ' . $wpdb->prefix . 'posts.post_type = %s AND ' . $wpdb->prefix . 'postmeta.post_id = ' . $wpdb->prefix . 'posts.ID AND (' . $wpdb->prefix . 'postmeta.meta_key = %s AND ' . $wpdb->prefix . 'postmeta.meta_value = %s ) LIMIT 1',
					$post_type,
					'iee_event_id',
					$event_id
				)
			);
		}else{
			$get_post_id = $wpdb->get_col(
				$wpdb->prepare(
					'SELECT ' . $wpdb->prefix . 'posts.ID FROM ' . $wpdb->prefix . 'posts, ' . $wpdb->prefix . 'postmeta WHERE ' . $wpdb->prefix . 'posts.post_type = %s AND ' . $wpdb->prefix . 'postmeta.post_id = ' . $wpdb->prefix . 'posts.ID AND ' . $wpdb->prefix . 'posts.post_status != %s AND (' . $wpdb->prefix . 'postmeta.meta_key = %s AND ' . $wpdb->prefix . 'postmeta.meta_value = %s ) LIMIT 1',
					$post_type,
					'trash',
					'iee_event_id',
					$event_id
				)
			);
		}

		if ( !empty( $get_post_id[0] ) ) {
			return $get_post_id[0];
		}
		return false;
	}

	/**
	 * Display upgrade to pro notice in form.
	 *
	 * @since 1.0.0
	 */
	public function render_pro_notice() {
		if ( ! iee_is_pro() ) {
		?>
		<span class="iee_small">
			<?php printf( '<span style="color: red">%s</span> <a href="' . esc_url( IEE_PLUGIN_BUY_NOW_URL ) . '" target="_blank" >%s</a>', esc_html__( 'Available in Pro version.', 'import-eventbrite-events' ), esc_html__( 'Upgrade to PRO', 'import-eventbrite-events' ) ); ?>
		</span>
		<?php
		}
	}

	/**
	 * Get Active supported active plugins.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function iee_get_country_code( $country ) {
		if ( $country == '' ) {
			return '';
		}

		$countries = array(
			'AF' => 'AFGHANISTAN',
			'AL' => 'ALBANIA',
			'DZ' => 'ALGERIA',
			'AS' => 'AMERICAN SAMOA',
			'AD' => 'ANDORRA',
			'AO' => 'ANGOLA',
			'AI' => 'ANGUILLA',
			'AQ' => 'ANTARCTICA',
			'AG' => 'ANTIGUA AND BARBUDA',
			'AR' => 'ARGENTINA',
			'AM' => 'ARMENIA',
			'AW' => 'ARUBA',
			'AU' => 'AUSTRALIA',
			'AT' => 'AUSTRIA',
			'AZ' => 'AZERBAIJAN',
			'BS' => 'BAHAMAS',
			'BH' => 'BAHRAIN',
			'BD' => 'BANGLADESH',
			'BB' => 'BARBADOS',
			'BY' => 'BELARUS',
			'BE' => 'BELGIUM',
			'BZ' => 'BELIZE',
			'BJ' => 'BENIN',
			'BM' => 'BERMUDA',
			'BT' => 'BHUTAN',
			'BO' => 'BOLIVIA',
			'BA' => 'BOSNIA AND HERZEGOVINA',
			'BW' => 'BOTSWANA',
			'BV' => 'BOUVET ISLAND',
			'BR' => 'BRAZIL',
			'IO' => 'BRITISH INDIAN OCEAN TERRITORY',
			'BN' => 'BRUNEI DARUSSALAM',
			'BG' => 'BULGARIA',
			'BF' => 'BURKINA FASO',
			'BI' => 'BURUNDI',
			'KH' => 'CAMBODIA',
			'CM' => 'CAMEROON',
			'CA' => 'CANADA',
			'CV' => 'CAPE VERDE',
			'KY' => 'CAYMAN ISLANDS',
			'CF' => 'CENTRAL AFRICAN REPUBLIC',
			'TD' => 'CHAD',
			'CL' => 'CHILE',
			'CN' => 'CHINA',
			'CX' => 'CHRISTMAS ISLAND',
			'CC' => 'COCOS (KEELING) ISLANDS',
			'CO' => 'COLOMBIA',
			'KM' => 'COMOROS',
			'CG' => 'CONGO',
			'CD' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
			'CK' => 'COOK ISLANDS',
			'CR' => 'COSTA RICA',
			'CI' => 'COTE D IVOIRE',
			'HR' => 'CROATIA',
			'CU' => 'CUBA',
			'CY' => 'CYPRUS',
			'CZ' => 'CZECH REPUBLIC',
			'DK' => 'DENMARK',
			'DJ' => 'DJIBOUTI',
			'DM' => 'DOMINICA',
			'DO' => 'DOMINICAN REPUBLIC',
			'TP' => 'EAST TIMOR',
			'EC' => 'ECUADOR',
			'EG' => 'EGYPT',
			'SV' => 'EL SALVADOR',
			'GQ' => 'EQUATORIAL GUINEA',
			'ER' => 'ERITREA',
			'EE' => 'ESTONIA',
			'ET' => 'ETHIOPIA',
			'FK' => 'FALKLAND ISLANDS (MALVINAS)',
			'FO' => 'FAROE ISLANDS',
			'FJ' => 'FIJI',
			'FI' => 'FINLAND',
			'FR' => 'FRANCE',
			'GF' => 'FRENCH GUIANA',
			'PF' => 'FRENCH POLYNESIA',
			'TF' => 'FRENCH SOUTHERN TERRITORIES',
			'GA' => 'GABON',
			'GM' => 'GAMBIA',
			'GE' => 'GEORGIA',
			'DE' => 'GERMANY',
			'GH' => 'GHANA',
			'GI' => 'GIBRALTAR',
			'GR' => 'GREECE',
			'GL' => 'GREENLAND',
			'GD' => 'GRENADA',
			'GP' => 'GUADELOUPE',
			'GU' => 'GUAM',
			'GT' => 'GUATEMALA',
			'GN' => 'GUINEA',
			'GW' => 'GUINEA-BISSAU',
			'GY' => 'GUYANA',
			'HT' => 'HAITI',
			'HM' => 'HEARD ISLAND AND MCDONALD ISLANDS',
			'VA' => 'HOLY SEE (VATICAN CITY STATE)',
			'HN' => 'HONDURAS',
			'HK' => 'HONG KONG',
			'HU' => 'HUNGARY',
			'IS' => 'ICELAND',
			'IN' => 'INDIA',
			'ID' => 'INDONESIA',
			'IR' => 'IRAN, ISLAMIC REPUBLIC OF',
			'IQ' => 'IRAQ',
			'IE' => 'IRELAND',
			'IL' => 'ISRAEL',
			'IT' => 'ITALY',
			'JM' => 'JAMAICA',
			'JP' => 'JAPAN',
			'JO' => 'JORDAN',
			'KZ' => 'KAZAKSTAN',
			'KE' => 'KENYA',
			'KI' => 'KIRIBATI',
			'KP' => 'KOREA DEMOCRATIC PEOPLES REPUBLIC OF',
			'KR' => 'KOREA REPUBLIC OF',
			'KW' => 'KUWAIT',
			'KG' => 'KYRGYZSTAN',
			'LA' => 'LAO PEOPLES DEMOCRATIC REPUBLIC',
			'LV' => 'LATVIA',
			'LB' => 'LEBANON',
			'LS' => 'LESOTHO',
			'LR' => 'LIBERIA',
			'LY' => 'LIBYAN ARAB JAMAHIRIYA',
			'LI' => 'LIECHTENSTEIN',
			'LT' => 'LITHUANIA',
			'LU' => 'LUXEMBOURG',
			'MO' => 'MACAU',
			'MK' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
			'MG' => 'MADAGASCAR',
			'MW' => 'MALAWI',
			'MY' => 'MALAYSIA',
			'MV' => 'MALDIVES',
			'ML' => 'MALI',
			'MT' => 'MALTA',
			'MH' => 'MARSHALL ISLANDS',
			'MQ' => 'MARTINIQUE',
			'MR' => 'MAURITANIA',
			'MU' => 'MAURITIUS',
			'YT' => 'MAYOTTE',
			'MX' => 'MEXICO',
			'FM' => 'MICRONESIA, FEDERATED STATES OF',
			'MD' => 'MOLDOVA, REPUBLIC OF',
			'MC' => 'MONACO',
			'MN' => 'MONGOLIA',
			'MS' => 'MONTSERRAT',
			'MA' => 'MOROCCO',
			'MZ' => 'MOZAMBIQUE',
			'MM' => 'MYANMAR',
			'NA' => 'NAMIBIA',
			'NR' => 'NAURU',
			'NP' => 'NEPAL',
			'NL' => 'NETHERLANDS',
			'AN' => 'NETHERLANDS ANTILLES',
			'NC' => 'NEW CALEDONIA',
			'NZ' => 'NEW ZEALAND',
			'NI' => 'NICARAGUA',
			'NE' => 'NIGER',
			'NG' => 'NIGERIA',
			'NU' => 'NIUE',
			'NF' => 'NORFOLK ISLAND',
			'MP' => 'NORTHERN MARIANA ISLANDS',
			'NO' => 'NORWAY',
			'OM' => 'OMAN',
			'PK' => 'PAKISTAN',
			'PW' => 'PALAU',
			'PS' => 'PALESTINIAN TERRITORY, OCCUPIED',
			'PA' => 'PANAMA',
			'PG' => 'PAPUA NEW GUINEA',
			'PY' => 'PARAGUAY',
			'PE' => 'PERU',
			'PH' => 'PHILIPPINES',
			'PN' => 'PITCAIRN',
			'PL' => 'POLAND',
			'PT' => 'PORTUGAL',
			'PR' => 'PUERTO RICO',
			'QA' => 'QATAR',
			'RE' => 'REUNION',
			'RO' => 'ROMANIA',
			'RU' => 'RUSSIAN FEDERATION',
			'RW' => 'RWANDA',
			'SH' => 'SAINT HELENA',
			'KN' => 'SAINT KITTS AND NEVIS',
			'LC' => 'SAINT LUCIA',
			'PM' => 'SAINT PIERRE AND MIQUELON',
			'VC' => 'SAINT VINCENT AND THE GRENADINES',
			'WS' => 'SAMOA',
			'SM' => 'SAN MARINO',
			'ST' => 'SAO TOME AND PRINCIPE',
			'SA' => 'SAUDI ARABIA',
			'SN' => 'SENEGAL',
			'SC' => 'SEYCHELLES',
			'SL' => 'SIERRA LEONE',
			'SG' => 'SINGAPORE',
			'SK' => 'SLOVAKIA',
			'SI' => 'SLOVENIA',
			'SB' => 'SOLOMON ISLANDS',
			'SO' => 'SOMALIA',
			'ZA' => 'SOUTH AFRICA',
			'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
			'ES' => 'SPAIN',
			'LK' => 'SRI LANKA',
			'SD' => 'SUDAN',
			'SR' => 'SURINAME',
			'SJ' => 'SVALBARD AND JAN MAYEN',
			'SZ' => 'SWAZILAND',
			'SE' => 'SWEDEN',
			'CH' => 'SWITZERLAND',
			'SY' => 'SYRIAN ARAB REPUBLIC',
			'TW' => 'TAIWAN, PROVINCE OF CHINA',
			'TJ' => 'TAJIKISTAN',
			'TZ' => 'TANZANIA, UNITED REPUBLIC OF',
			'TH' => 'THAILAND',
			'TG' => 'TOGO',
			'TK' => 'TOKELAU',
			'TO' => 'TONGA',
			'TT' => 'TRINIDAD AND TOBAGO',
			'TN' => 'TUNISIA',
			'TR' => 'TURKEY',
			'TM' => 'TURKMENISTAN',
			'TC' => 'TURKS AND CAICOS ISLANDS',
			'TV' => 'TUVALU',
			'UG' => 'UGANDA',
			'UA' => 'UKRAINE',
			'AE' => 'UNITED ARAB EMIRATES',
			'GB' => 'UNITED KINGDOM',
			'US' => 'UNITED STATES',
			'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS',
			'UY' => 'URUGUAY',
			'UZ' => 'UZBEKISTAN',
			'VU' => 'VANUATU',
			'VE' => 'VENEZUELA',
			'VN' => 'VIET NAM',
			'VG' => 'VIRGIN ISLANDS, BRITISH',
			'VI' => 'VIRGIN ISLANDS, U.S.',
			'WF' => 'WALLIS AND FUTUNA',
			'EH' => 'WESTERN SAHARA',
			'YE' => 'YEMEN',
			'YU' => 'YUGOSLAVIA',
			'ZM' => 'ZAMBIA',
			'ZW' => 'ZIMBABWE',
		);

		foreach ( $countries as $code => $name ) {
			if ( strtoupper( $country ) == $name ) {
				return $code;
			}
		}
		return $country;
	}

	/**
	 * Setup Success messages.
	 *
	 * @since    1.0.0
	 */
	public function setup_success_messages() {
		global $iee_success_msg;
		if ( isset( $_GET['iee_msg'] ) && $_GET['iee_msg'] != '' ) {
			switch ( $_GET['iee_msg'] ) {
				case 'import_del':
					$iee_success_msg[] = esc_html__( 'Scheduled import deleted successfully.', 'import-eventbrite-events' );
					break;

				case 'import_dels':
					$iee_success_msg[] = esc_html__( 'Scheduled imports are deleted successfully.', 'import-eventbrite-events' );
					break;

				case 'import_success':
					$iee_success_msg[] = esc_html__( 'Scheduled import has been run successfully.', 'import-eventbrite-events' );
					break;

				case 'history_del':
					$iee_success_msg[] = esc_html__( 'Import history deleted successfully.', 'import-eventbrite-events' );
					break;

				case 'history_dels':
					$iee_success_msg[] = esc_html__( 'Import histories are deleted successfully.', 'import-eventbrite-events' );
					break;

				case 'upgrade_finish':
					$iee_success_msg[] = esc_html__( 'Update has been finish successfully.', 'import-eventbrite-events' );
					break;

				case 'ieesiu_success':
					$iee_success_msg[] = esc_html__( 'Scheduled import has been updated successfully.', 'import-eventbrite-events' );
					break;

				default:
					$iee_success_msg[] = esc_html__( 'Scheduled imports are deleted successfully.', 'import-eventbrite-events' );
					break;
			}
		}
	}

	/**
	 * Delete scheduled import from list table.
	 *
	 * @since    1.0.0
	 */
	public function handle_listtable_oprations() {

		global $iee_success_msg;
		if ( isset( $_GET['iee_action'] ) && $_GET['iee_action'] == 'iee_simport_delete' && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'iee_delete_import_nonce' ) ) {
			$import_id   = isset( $_GET['import_id'] ) ? wp_unslash( $_GET['import_id'] ) : '0';
			$page        = isset( $_GET['page'] ) ? $_GET['page'] : 'eventbrite_event';
			$tab         = isset( $_GET['tab'] ) ? $_GET['tab'] : 'scheduled';
			$wp_redirect = admin_url( 'admin.php?page=' . $page );		
			if ( $import_id > 0 ) {
				$post_type = get_post_type( $import_id );
				if ( $post_type == 'iee_scheduled_import' ) {
					$timestamp = wp_next_scheduled( 'iee_run_scheduled_import', array( 'post_id' => (int)$import_id ) );
					if ( $timestamp ) {
						wp_unschedule_event( $timestamp, 'iee_run_scheduled_import', array( 'post_id' => (int)$import_id ) );
					}
					wp_delete_post( $import_id, true );
					$query_args = array(
						'iee_msg' => 'import_del',
						'tab'     => $tab,
					);
					wp_redirect( add_query_arg( $query_args, $wp_redirect ) );
					exit;
				}
			}
		}

		if ( isset( $_GET['iee_action'] ) && $_GET['iee_action'] == 'iee_history_delete' && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'iee_delete_history_nonce' ) ) {
			$history_id  = isset( $_GET['history_id'] ) ? wp_unslash( $_GET['history_id'] ) : '0';
			$page        = isset( $_GET['page'] ) ? $_GET['page'] : 'eventbrite_event';
			$tab         = isset( $_GET['tab'] ) ? $_GET['tab'] : 'history';
			$wp_redirect = admin_url( 'admin.php?page=' . $page );
			if ( $history_id > 0 ) {
				wp_delete_post( $history_id, true );
				$query_args = array(
					'iee_msg' => 'history_del',
					'tab'     => $tab,
				);
				wp_redirect( add_query_arg( $query_args, $wp_redirect ) );
				exit;
			}
		}

		if ( isset( $_GET['iee_action'] ) && $_GET['iee_action'] == 'iee_run_import' && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'iee_run_import_nonce' ) ) {
			$import_id   = isset( $_GET['import_id'] ) ? wp_unslash( $_GET['import_id'] ) : '0';
			$page        = isset( $_GET['page'] ) ? $_GET['page'] : 'eventbrite_event';
			$tab         = isset( $_GET['tab'] ) ? $_GET['tab'] : 'scheduled';
			$wp_redirect = admin_url( 'admin.php?page=' . $page );
			if ( $import_id > 0 ) {
				do_action( 'iee_run_scheduled_import', $import_id );
				$query_args = array(
					'iee_msg' => 'import_success',
					'tab'     => $tab,
				);
				wp_redirect( add_query_arg( $query_args, $wp_redirect ) );
				exit;
			}
		}

		$is_bulk_delete = ( ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) || ( isset( $_GET['action2'] ) && $_GET['action2'] == 'delete' ) );

		if ( $is_bulk_delete && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'bulk-iee_scheduled_import' ) ) {
			$page        = isset( $_GET['page'] ) ? $_GET['page'] : 'eventbrite_event';
			$tab         = isset( $_GET['tab'] ) ? $_GET['tab'] : 'scheduled';
			$wp_redirect = admin_url( 'admin.php?page=' . $page );
			$delete_ids  = isset( $_REQUEST['iee_scheduled_import'] ) ? wp_unslash( $_REQUEST['iee_scheduled_import'] ) : '0';
			if ( ! empty( $delete_ids ) ) {
				foreach ( $delete_ids as $delete_id ) {
					$timestamp = wp_next_scheduled( 'iee_run_scheduled_import', array( 'post_id' => (int)$delete_id ) );
					if ( $timestamp ) {
						wp_unschedule_event( $timestamp, 'iee_run_scheduled_import', array( 'post_id' => (int)$delete_id ) );
					}
					wp_delete_post( $delete_id, true );
				}
			}
			$query_args = array(
				'iee_msg' => 'import_dels',
				'tab'     => $tab,
			);
			wp_redirect( add_query_arg( $query_args, $wp_redirect ) );
			exit;
		}
		
		// Delete All History Data 
		if ( isset( $_GET['iee_action'] ) && $_GET['iee_action'] == 'iee_all_history_delete' && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'iee_delete_all_history_nonce' ) ) {
			global $wpdb;
			$page        = isset( $_GET['page'] ) ? $_GET['page'] : 'eventbrite_event';
			$tab         = isset( $_GET['tab'] ) ? $_GET['tab'] : 'history';
			$wp_redirect = admin_url( 'admin.php?page=' . $page );

			$delete_ids  = get_posts( array( 'numberposts' => -1,'fields' => 'ids', 'post_type' => 'iee_import_history' ) );
			if ( !empty( $delete_ids ) ) {
				foreach ( $delete_ids as $delete_id ) {
					wp_delete_post( $delete_id, true );
				}
			}		
			$query_args = array(
				'iee_msg' => 'history_dels',
				'tab'     => $tab,
			);			
			wp_redirect( add_query_arg( $query_args, $wp_redirect ) );
			exit;
		}

		if ( $is_bulk_delete && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'bulk-iee_import_histories' ) ) {
			$page        = isset( $_GET['page'] ) ? $_GET['page'] : 'eventbrite_event';
			$tab         = isset( $_GET['tab'] ) ? $_GET['tab'] : 'history';
			$wp_redirect = admin_url( 'admin.php?page=' . $page );
			$delete_ids  = isset( $_REQUEST['import_history'] ) ? wp_unslash( $_REQUEST['import_history'] ) : '0';
			if ( ! empty( $delete_ids ) ) {
				foreach ( $delete_ids as $delete_id ) {
					wp_delete_post( $delete_id, true );
				}
			}
			$query_args = array(
				'iee_msg' => 'history_dels',
				'tab'     => $tab,
			);
			wp_redirect( add_query_arg( $query_args, $wp_redirect ) );
			exit;
		}
	}

	/**
	 * Process insert group form for TEC.
	 *
	 * @since    1.0.0
	 */
	public function handle_import_settings_submit() {
		global $iee_errors, $iee_success_msg, $iee_events;
		if ( isset( $_POST['iee_action'] ) && $_POST['iee_action'] == 'iee_save_settings' && check_admin_referer( 'iee_setting_form_nonce_action', 'iee_setting_form_nonce' ) ) {

			$iee_options = array();
			$iee_options = isset( $_POST['eventbrite'] ) ? $_POST['eventbrite'] : array();
			if( iee_is_pro() ){
				$eventbrite_event_slug 	 = isset( $iee_options['event_slug'] ) ? $iee_options['event_slug']  : 'eventbrite-event'; 
				$iee_events->cpt->event_slug = $eventbrite_event_slug;
				$iee_events->cpt->register_event_post_type();
				flush_rewrite_rules();
			}

			$is_update = update_option( IEE_OPTIONS, $iee_options );
			if ( $is_update ) {
				$iee_success_msg[] = __( 'Import settings has been saved successfully.', 'import-eventbrite-events' );
			}
		}
	}

	/**
     * Create missing Scheduled Import
     *
     * @param int $post_id Post id.
     */
    public function iee_recreate_missing_schedule_import( $post_id ){
		        
        $si_data           = get_post_meta( $post_id, 'import_eventdata', true );
        $import_frequency  = ( $si_data['import_frequency'] ) ? $si_data['import_frequency'] : 'not_repeat';
        $cron_time         = time() - (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
        
        if( $import_frequency !== 'not_repeat' ) {
            $scheduled = wp_schedule_event( $cron_time, $import_frequency, 'iee_run_scheduled_import', array( 'post_id' => $post_id ) );
        }
    }

}


/**
 * Check is pro active or not.
 *
 * @since  1.5.0
 * @return boolean
 */
function iee_is_pro() {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if ( is_plugin_active( 'import-eventbrite-events-pro/import-eventbrite-events-pro.php' ) ) {
		return true;
	}
	return false;
}


/**
 * Template Functions
 *
 * Template functions specifically created for Event Listings
 *
 * @author      Dharmesh Patel
 * @version     1.5.0
 */

/**
 * Gets and includes template files.
 *
 * @since 1.5.0
 * @param mixed  $template_name
 * @param array  $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function get_iee_template( $template_name, $args = array(), $template_path = 'import-eventbrite-events', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}
	include locate_iee_template( $template_name, $template_path, $default_path );
}

/**
 * Locates a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *      yourtheme       /   $template_path  /   $template_name
 *      yourtheme       /   $template_name
 *      $default_path   /   $template_name
 *
 * @since 1.5.0
 * @param string      $template_name
 * @param string      $template_path (default: 'import-eventbrite-events')
 * @param string|bool $default_path (default: '') False to not load a default
 * @return string
 */
function locate_iee_template( $template_name, $template_path = 'import-eventbrite-events', $default_path = '' ) {
	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);
	// Get default template
	if ( ! $template && $default_path !== false ) {
		$default_path = $default_path ? $default_path : IEE_PLUGIN_DIR . '/templates/';
		if ( file_exists( trailingslashit( $default_path ) . $template_name ) ) {
			$template = trailingslashit( $default_path ) . $template_name;
		}
	}
	// Return what we found
	return apply_filters( 'iee_locate_template', $template, $template_name, $template_path );
}

/**
 * Gets template part (for templates in loops).
 *
 * @since 1.0.0
 * @param string      $slug
 * @param string      $name (default: '')
 * @param string      $template_path (default: 'import-eventbrite-events')
 * @param string|bool $default_path (default: '') False to not load a default
 */
function get_iee_template_part( $slug, $name = '', $template_path = 'import-eventbrite-events', $default_path = '' ) {
	$template = '';
	if ( $name ) {
		$template = locate_iee_template( "{$slug}-{$name}.php", $template_path, $default_path );
	}
	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/import-eventbrite-events/slug.php
	if ( ! $template ) {
		$template = locate_iee_template( "{$slug}.php", $template_path, $default_path );
	}
	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get Batch of in-progress background imports.
 *
 * @return array $batches
 */
function iee_get_inprogress_import(){
	global $wpdb;
	$batch_query = "SELECT * FROM {$wpdb->options} WHERE option_name LIKE '%iee_import_batch_%' ORDER BY option_id ASC";
	if ( is_multisite() ) {
		$batch_query = "SELECT * FROM {$wpdb->sitemeta} WHERE meta_key LIKE '%iee_import_batch_%' ORDER BY meta_id ASC";
	}
	$batches = $wpdb->get_results( $batch_query );
	return $batches;
}

/**
 * Get Markup for eventbrite non-model checkout.
 *
 * @return string
 */
function iee_nonmodel_checkout_markup( $eventbrite_id ){
	ob_start();
	?>
	<div id="iee-eventbrite-checkout-widget"></div>
	<script src="https://www.eventbrite.com/static/widgets/eb_widgets.js"></script>
	<script type="text/javascript">
		var orderCompleteCallback = function() {
			console.log("Order complete!");
		};
		window.EBWidgets.createWidget({
			widgetType: "checkout",
			eventId: "<?php echo esc_attr( $eventbrite_id ); ?>",
			iframeContainerId: "iee-eventbrite-checkout-widget",
			iframeContainerHeight: <?php echo apply_filters('iee_embeded_checkout_height', 530); ?>,
			onOrderComplete: orderCompleteCallback
		});
	</script>
	<?php
	return ob_get_clean();
}

/**
 * Get Markup for eventbrite model checkout.
 *
 * @return string
 */
function iee_model_checkout_markup( $eventbrite_id ){
	ob_start();
	?>
	<button id="iee-eventbrite-checkout-trigger" type="button">
		<?php esc_html_e( 'Buy Tickets', 'import-eventbrite-events' ); ?>
	</button>
	<script src="https://www.eventbrite.com/static/widgets/eb_widgets.js"></script>
	<script type="text/javascript">
		var orderCompleteCallback = function() {
			console.log("Order complete!");
		};
		window.EBWidgets.createWidget({
			widgetType: "checkout",
			eventId: "<?php echo esc_attr( $eventbrite_id ); ?>",
			modal: true,
			modalTriggerElementId: "iee-eventbrite-checkout-trigger",
			onOrderComplete: orderCompleteCallback
		});
	</script>
	<?php
	return ob_get_clean();
}
