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
 * Add GTM ID.
 */
function add_gtm_id() {
	return 'GTM-XXXXXXX';
}
add_filter( 'tenup_datalayer_gtm_id', __NAMESPACE__ . 'add_gtm_id' );

/**
 * Enqueue the block data class.
 *
 * @return void
 */
function add_datalayer_classes() {
	new \TenUp\DataLayer\Blockdata();
	new \TenUp\DataLayer\Datalayer();
}
add_action( 'wp_loaded', __NAMESPACE__ . '\wp_loaded' );
