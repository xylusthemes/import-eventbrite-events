<?php
/**
 * Eventbrite Events Block Initializer
 *
 * @since   1.6
 * @package    Import_Eventbrite_Events
 * @subpackage Import_Eventbrite_Events/includes
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Gutenberg Block
 *
 * @return void
 */
function iee_register_gutenberg_block() {
	global $iee_events;
	if ( function_exists( 'register_block_type' ) ) {
		// Register block editor script.
		$js_dir = IEE_PLUGIN_URL . 'assets/js/blocks/';
		wp_register_script(
			'iee-eventbrite-events-block',
			$js_dir . 'gutenberg.blocks.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ),
			IEE_VERSION,
			true
		);

		// Register block editor style.
		$css_dir = IEE_PLUGIN_URL . 'assets/css/';
		wp_register_style(
			'iee-eventbrite-events-block-style',
			$css_dir . 'import-eventbrite-events.css',
			array(),
			IEE_VERSION
		);
		wp_register_style(
			'iee-eventbrite-events-block-style2',
			$css_dir . 'grid-style2.css',
			array(),
			IEE_VERSION
		);

		// Register our block.
		register_block_type( 'iee-block/eventbrite-events', array(
			'attributes'      => array(
				'col'            => array(
					'type'    => 'number',
					'default' => 2,
				),
				'posts_per_page' => array(
					'type'    => 'number',
					'default' => 12,
				),
				'past_events'    => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'start_date'     => array(
					'type'    => 'string',
					'default' => '',
				),
				'end_date'       => array(
					'type'    => 'string',
					'default' => '',
				),
				'order'          => array(
					'type'    => 'string',
					'default' => 'ASC',
				),
				'orderby'        => array(
					'type'    => 'string',
					'default' => 'event_start_date',
				),
				'layout'         => array(
					'type'    => 'string',
					'default' => '',
				),

			),
			'editor_script'   => 'iee-eventbrite-events-block', // The script name we gave in the wp_register_script() call.
			'editor_style'    => 'iee-eventbrite-events-block-style', // The script name we gave in the wp_register_style() call.
			'style'           => 'iee-eventbrite-events-block-style2', 
			'render_callback' => array( $iee_events->cpt, 'eventbrite_events_archive' ),
		) );
	}
}

add_action( 'init', 'iee_register_gutenberg_block' );
