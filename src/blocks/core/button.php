<?php
/**
 * Gutenberg Blocks setup
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

function render( $block_content, $block, $instance ) {
	var_dump($block_content);
	exit;
	$block_content = str_replace( 'wp-block-button', 'wp-block-button button', $block_content );
	return $block_content;
}