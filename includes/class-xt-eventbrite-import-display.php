<?php
/**
 * Class for Display Ticket section of Eventbrite on Event page.
 *
 * @link       http://xylusthemes.com/
 * @since      1.0.0
 *
 * @package    XT_Eventbrite_Import
 * @subpackage XT_Eventbrite_Import/includes
 */
class XT_Eventbrite_Import_Display {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Error generated during form submit
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $errors    Error generated during form submit
	 */
	protected $errors;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_filter( 'the_content', array( $this, 'xtei_em_add_ticket_section') );
		add_action( 'tribe_events_single_event_after_the_meta', array( $this, 'xtei_tec_add_ticket_section') );
	}

	public function xtei_tec_add_ticket_section() {
		$xt_post_type =  get_post_type();
		$event_id = get_the_ID();
		if ( $event_id > 0 ) {
			if( XTEI_TEC_POSTTYPE == $xt_post_type ){
				$eventbrite_id = get_post_meta( $event_id, 'xtei_eventbrite_event_id', true );
				if ( $eventbrite_id && $eventbrite_id > 0 && is_numeric( $eventbrite_id ) ) {
					$ticket_section = $this->xtei_get_ticket_section( $eventbrite_id );
					echo $ticket_section;
				}
			}
		}
	}

	/**
	 * Add ticket section to Eventbrite event.
	 *
	 * @since    1.0.0
	 */
	public function xtei_em_add_ticket_section( $content = '' ) {
		$xt_post_type =  get_post_type();
		$event_id = get_the_ID();
		if ( $event_id > 0 ) {
			if( XTEI_EM_POSTTYPE == $xt_post_type ){
				$eventbrite_id = get_post_meta( $event_id, '_xt_eventbrite_event_id', true );
				if ( $eventbrite_id && $eventbrite_id > 0 && is_numeric( $eventbrite_id ) ) {
					$ticket_section = $this->xtei_get_ticket_section( $eventbrite_id );
					return $content.$ticket_section;
				}
			}
		}
		return $content;
	}

	public function xtei_get_ticket_section( $eventbrite_id = 0 ) {
		$xtei_options = get_option( XTEI_OPTIONS, array() );
		$enable_ticket_sec = isset( $xtei_options['enable_ticket_sec'] ) ? $xtei_options['enable_ticket_sec'] : 'yes';
		if ( 'yes' != $enable_ticket_sec ) {
			return '';
		}

		if( $eventbrite_id > 0 ){
			ob_start();
			?>
			<div class="eventbrite-ticket-section" style="width:100%; text-align:left;">
				<iframe id="eventbrite-tickets-<?php echo $eventbrite_id; ?>" src="http://www.eventbrite.com/tickets-external?eid=<?php echo $eventbrite_id; ?>" style="width:100%;height:300px; border: 0px;"></iframe>
			</div>
			<?php
			$ticket = ob_get_clean();
			return $ticket;
		}else{
			return '';
		}
	}

}
