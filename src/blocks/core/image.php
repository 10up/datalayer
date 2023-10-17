<?php
/**
 * Bind the data to the block
 *
 * @package TenUp\DataLayer
 */

namespace TenUp\DataLayer\Blocks\Core\Image;

/**
 * Set up blocks
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_filter( 'render_block_core/image', $n( 'render' ), 10, 3 );
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

	$destination = false;

	$temp_block_content = new \WP_HTML_Tag_Processor( $block_content );
	if ( $temp_block_content->next_tag( 'a' ) ) {
		$destination = $temp_block_content->get_attribute( 'href' ) ?? '';
	}

	if ( ! empty( $destination ) ) {
		$block_content = new \WP_HTML_Tag_Processor( $block_content );
		if ( $block_content->next_tag( 'img' ) ) {
	
		$block_content->set_attribute( 'data-event', 'clickable_image' );
			$block_content->set_attribute( 'data-destinationLink', $destination );
			$block_content->get_updated_html();
		}
	}

	return $block_content;
}