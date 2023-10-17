<?php
/**
 * Bind the data to the block
 *
 * @package TenUp\DataLayer
 */

namespace TenUp\DataLayer\Blocks\Core\Button;

/**
 * Set up blocks
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_filter( 'render_block_core/button', $n( 'render' ), 10, 3 );
}

/**
 * Update the block content to include tracking attributes
 *
 * @param string $block_content The block content about to be rendered.
 * @param array $block The block data being rendered.
 * @param WP_Block $instance The block instance being rendered.
 * @return void
 */
function render( $block_content, $block, $instance ) {

	$block_content = new \WP_HTML_Tag_Processor( $block_content );

	if ( $block_content->next_tag( array( 'class_name' => 'wp-element-button' ) ) ) {

		$cta_text    = trim( strip_tags( $block['innerContent'][0] ) ) ?? '';
		$destination = $block_content->get_attribute( 'href' ) ?? '';

		$block_content->set_attribute( 'data-event', 'button' );
		$block_content->set_attribute( 'data-ctaText', $cta_text );
		$block_content->set_attribute( 'data-destinationLink', $destination );
		$block_content->get_updated_html();
	}

	return $block_content;
}