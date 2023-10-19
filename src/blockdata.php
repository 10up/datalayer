<?php
/**
 * BlockData class file.
 * 
 * @since 1.0.0
 * 
 * @package 10up
 */

namespace TenUp\Blockdata;

define( 'THEME_DATALAYER_TEMPLATE_URL', get_template_directory_uri() );
define( 'THEME_DATALAYER_SRC_URL', THEME_DATALAYER_TEMPLATE_URL . '/vendor/10up/datalayer/src' );

/**
 * DataLayer Class
 */
class BlockData {
	/**
	 * Data.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return array
	 */
	public $data = array();

	/**
	 * Initiate Datalayer class.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function __construct() {
		$this->add_navigation_tracking();
		$this->add_block_tracking();
		$this->register_scripts();
	}

	/**
	 * Register navigation tracking filters.
	 *
	 * @return void
	 */
	public function add_navigation_tracking() {
		add_filter( 'nav_menu_link_attributes', [ $this, 'navigation_tracking' ], 10, 3 );
	}

	/**
	 * Add tracking attributes to all menu items.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function navigation_tracking( $atts, $item, $args ) {
		
		$atts['data-event']           = str_contains( $args->theme_location, 'footer' ) ? 'footer' : 'navigation';
		$atts['data-ctaText']         = $item->title ?? '';
		$atts['data-destinationLink'] = $atts['href'] ?? '';

		return $atts;
	}

	/**
	 * Register all block data files.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function add_block_tracking() {

		// Get all files withing the blocks folder
		// $blocks = scandir( __DIR__ . '/blocks' );
		$blocks = $this->list_files_recursive();

		foreach ( $blocks as $block ) {

			if ( ! is_array ( $block ) ) {
				continue;
			}

			$block_type = $block[0];
			$block_name = $block[1];

			$block_data = __DIR__ . '/blocks/' . $block_type . '/' . $block_name . '.php';
			if ( file_exists( $block_data ) ) {
				require_once $block_data;
				call_user_func( __NAMESPACE__ . '\\Blocks\\' . $block_type . '\\' . $block_name . '\\setup' );
			}
		}
	}

	/**
	 * Register all block data files.
	 *
	 * @param boolean $dir Directory to scan.
	 * @return array
	 */
	public function list_files_recursive( $dir = false ) {
		$result = [];

		if ( ! $dir ) {
			$dir = __DIR__ . '/blocks';
		}

		$files = scandir( $dir );
	
		foreach ( $files as $file )  {
			if ( $file != '.' && $file != '..' ) {
				$filePath = $dir . '/' . $file;
				if ( is_dir( $filePath ) ) {
					$result = array_merge( $result, $this->list_files_recursive( $filePath ) );
				} else {
					$result[] = $this->extract_block_name_from_path( $filePath );
				}
			}
		}
		return $result;
	}

	/**
	 * Extract the block's name from file path.
	 *
	 * @param string $filePath
	 * @return array/bool
	 */
	public function extract_block_name_from_path( $filePath ) {
		$pathArray = explode( '/', $filePath );
		$count     = count( $pathArray );

		if ( $count >= 2 ) {
			$result = $pathArray[$count - 2] . '/' . $pathArray[$count - 1];

			$block_type = $pathArray[$count - 2];
			$block_name = str_replace( '.php', '', $pathArray[$count - 1] );

			return [ $block_type, $block_name ];
		}

		return false;
	}

	/**
	 * Register tracking scripts.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function register_scripts() {
		if ( is_admin() ) {
			return;
		}

		wp_enqueue_script( 'tenup-datalayer', THEME_DATALAYER_SRC_URL . '/js/frontend.js', array(), '1.0.0', true );
	}
}
