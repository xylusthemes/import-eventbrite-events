<?php
/**
 * iCal export admin page.
 *
 * @package Import_Eventbrite_Events
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $iee_events;

$export_post_type = $iee_events->ical_export->get_export_post_type();
$export_taxonomy  = $iee_events->ical_export->get_export_taxonomy();

?>

<div class="iee-card iee-ical-export-card" style="margin-top: 20px;">
	<form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="iee-ical-export-form">
		<input type="hidden" name="action" value="iee_export_ical" />
		<?php wp_nonce_field( 'iee_export_ical_nonce_action', 'iee_export_ical_nonce' ); ?>

		<?php
		if ( empty( $export_post_type ) || ! post_type_exists( $export_post_type ) ) : ?>
			<h3><?php esc_html_e( 'The Eventbrite Events post type is not available for export.', 'import-eventbrite-events' ); ?></h3>
		<?php else : ?>
			<div class="iee-inner-main-section">
				<div class="iee-inner-section-1">
					<label for="iee_ical_date_filter"><?php esc_html_e( 'Date filter', 'import-eventbrite-events' ); ?></label>
				</div>
				<div class="iee-inner-section-2">
					<select id="iee_ical_date_filter" name="date_filter" class="iee-ical-control">
						<option value="upcoming"><?php esc_html_e( 'Upcoming only', 'import-eventbrite-events' ); ?></option>
						<option value="past"><?php esc_html_e( 'Past only', 'import-eventbrite-events' ); ?></option>
						<option value="all"><?php esc_html_e( 'All events', 'import-eventbrite-events' ); ?></option>
						<option value="range"><?php esc_html_e( 'Custom date range', 'import-eventbrite-events' ); ?></option>
					</select>
				</div>
			</div>

			<div class="iee-inner-main-section date-range-section" style="display: none;">
				<div class="iee-inner-section-1">
					<span><?php esc_html_e( 'Date range', 'import-eventbrite-events' ); ?></span>
				</div>
				<div class="iee-inner-section-2">
					<div class="iee-ical-inline-fields">
						<label for="iee_ical_start_date"><?php esc_html_e( 'Start', 'import-eventbrite-events' ); ?></label>
						<input type="date" id="iee_ical_start_date" name="start_date" value="" />

						<label for="iee_ical_end_date"><?php esc_html_e( 'End', 'import-eventbrite-events' ); ?></label>
						<input type="date" id="iee_ical_end_date" name="end_date" value="" />
					</div>
					<p class="description"><?php esc_html_e( 'Used only when Custom date range is selected.', 'import-eventbrite-events' ); ?></p>
				</div>
			</div>

			<div class="iee-inner-main-section">
				<div class="iee-inner-section-1">
					<label for="iee_ical_post_status"><?php esc_html_e( 'Post status', 'import-eventbrite-events' ); ?></label>
				</div>
				<div class="iee-inner-section-2">
					<select id="iee_ical_post_status" name="post_status" class="iee-ical-control">
						<option value="publish"><?php esc_html_e( 'Published', 'import-eventbrite-events' ); ?></option>
						<option value="future"><?php esc_html_e( 'Scheduled', 'import-eventbrite-events' ); ?></option>
						<option value="draft"><?php esc_html_e( 'Draft', 'import-eventbrite-events' ); ?></option>
						<option value="pending"><?php esc_html_e( 'Pending', 'import-eventbrite-events' ); ?></option>
						<option value="private"><?php esc_html_e( 'Private', 'import-eventbrite-events' ); ?></option>
						<option value="any"><?php esc_html_e( 'Any status', 'import-eventbrite-events' ); ?></option>
					</select>
				</div>
			</div>

			<div class="iee-inner-main-section">
				<div class="iee-inner-section-1">
					<label for="iee_ical_event_cat"><?php esc_html_e( 'Category', 'import-eventbrite-events' ); ?></label>
				</div>
				<div class="iee-inner-section-2">
					<select id="iee_ical_event_cat" name="event_cat" class="iee-ical-control">
						<option value="0"><?php esc_html_e( 'Any category', 'import-eventbrite-events' ); ?></option>
						<?php
						$terms = array();
						if ( ! empty( $export_taxonomy ) && taxonomy_exists( $export_taxonomy ) ) {
							$terms = get_terms(
								array(
									'taxonomy'   => $export_taxonomy,
									'hide_empty' => false,
								)
							);
						}

						if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) :
							foreach ( $terms as $term ) :
								?>
								<option value="<?php echo esc_attr( $term->term_id ); ?>">
									<?php echo esc_html( $term->name ); ?>
								</option>
								<?php
							endforeach;
						endif;
						?>
					</select>
				</div>
			</div>

			<div class="iee-inner-main-section">
				<div class="iee-inner-section-1">
					<label for="iee_ical_search"><?php esc_html_e( 'Keyword', 'import-eventbrite-events' ); ?></label>
				</div>
				<div class="iee-inner-section-2">
					<input type="search" id="iee_ical_search" name="s" placeholder="<?php esc_attr_e( 'Search Keywords...', 'import-eventbrite-events' ); ?>" class="regular-text iee-ical-control" value="" />
				</div>
			</div>

			<div class="iee-inner-main-section">
				<div class="iee-inner-section-1">
					<label for="iee_ical_limit"><?php esc_html_e( 'Maximum events', 'import-eventbrite-events' ); ?></label>
				</div>
				<div class="iee-inner-section-2">
					<input type="number" id="iee_ical_limit" name="limit" min="1" max="5000" value="500" class="iee-ical-number" />
				</div>
			</div>

			<div class="iee-inner-main-section">
				<div class="iee-inner-section-1">
					<span><?php esc_html_e( 'iCal fields', 'import-eventbrite-events' ); ?></span>
				</div>
				<div class="iee-inner-section-2">
					<div class="iee-ical-checkboxes">
						<label>
							<input type="checkbox" name="include_description" value="1" checked="checked" />
							<?php esc_html_e( 'Include description', 'import-eventbrite-events' ); ?>
						</label>
						<label>
							<input type="checkbox" name="include_location" value="1" checked="checked" />
							<?php esc_html_e( 'Include location', 'import-eventbrite-events' ); ?>
						</label>
					</div>
				</div>
			</div>

			<div class="iee-ical-actions">
				<?php submit_button( __( 'Export iCal File', 'import-eventbrite-events' ), 'primary', 'submit', false ); ?>
			</div>
		<?php endif; ?>
	</form>
</div>
