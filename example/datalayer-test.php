<?php
/**
 * Plugin Name: 10up's Datalayer - Test Plugin
 * Version: 1.0.0
 * Author: 10up
 * Author URI: https://10up.com
 */

namespace DataLayer\Test;

// Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Autoload using vendor directory via composer.
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Enqueue the block data class.
 *
 * @return void
 */
function add_block_data() {
	new \TenUp\DataLayer\BlockData();
}
add_action( 'wp_loaded', __NAMESPACE__ . 'add_block_data' );


/**
 * 
 */
function header_section() {
    // Initiate and setup Datalayer.
    $datalayer = ( new \TenUp\DataLayer\Datalayer() )->setup( 'json' );
    
    // Use `$datalayer` parameters based on your need.
}
add_action( 'wp_head', __NAMESPACE__ . '\header_section' );

/**
 * Enqueue Scripts.
 * 
 * @since 1.0.0
 * 
 * @return void
 */
function enqueue_scripts() {
    // Initiate and setup Datalayer.
    $datalayer = ( new \TenUp\DataLayer\Datalayer() )->setup( 'array' ); // `array` is the default value.

    // Enqueue and Localize script where you would like to use the `$datalayer` parameters.
    wp_enqueue_script( 'datalayer-test', plugins_url( '/ad-datalayer/test.js' ) );
    wp_localize_script( 'datalayer-test', 'tenupDataLayer', $datalayer );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts' ); 
