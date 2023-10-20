<?php
/**
 * Datalayer class file.
 * 
 * @since 1.0.0
 * 
 * @package 10up
 */

namespace TenUp\DataLayer;

/**
 * DataLayer Class
 */
class Datalayer {
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
		$this->add_gtm_tag();
	}

	/**
	 * Setup Datalayer.
	 * 
	 * @param string $return_type Return Type. Default: array | Supported values: `array` and `json`.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function setup( $return_type = 'array' ) {
		return $return_type === 'json' ? wp_json_encode( $this->get_data() ) : $this->get_data();
	}

	/**
	 * Get Data.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return array
	 */
	public function get_data() {

		$object_id = get_queried_object_id() ?? 0;

		$this->get_global_data();
		
		if ( is_archive() ) {
			$this->get_archive_data( $object_id );
		} elseif ( is_404() ) {
			$this->get_404_data();
		} elseif ( is_search() ) {
			$this->get_search_data();
		} elseif ( is_front_page() || is_home() ) {
			$this->get_homepage_data();
		} elseif ( is_singular() ) {
			$this->get_singular_data( $object_id );
		}

		/**
		 * Data values of datalayer.
		 * 
		 * @since  1.0.0
		 * @access public
		 */
		$this->data = apply_filters( 'tenup_datalayer_data_values', $this->data );

		// Return the prepared data.
		return $this->data;
	}

	/**
	 * Get globalized data.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_global_data() {
		$this->data += [
			'environment' => wp_get_environment_type(),
			'trackingAttrs' => apply_filters( 
				'tenup_datalayer_tracking_attrs',
					[
						'data-event',
						'data-ctaText',
						'data-destinationLink',
						'data-module',
						'data-prodBrnd',
						'data-prodName',
						'data-prodPrice',
						'data-cmpAction',
						'data-superlative'
					]
				),
		];

		$this->get_utm_parameters();
	}

	/**
	 * Get UTM Parameters.
	 *
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function get_utm_parameters() {
		$utm_parameters = [
			'utm_source',
			'utm_medium',
		];

		foreach ( $utm_parameters as $parameter ) {
			if ( isset( $_GET[ $parameter ] ) ) {
				$this->data[ $parameter ] = sanitize_text_field( wp_unslash( $_GET[ $parameter ] ) );
			}
		}
	}

	/**
	 * Get WordPress Archive Data.
	 * 
	 * @since  1.0.0
	 * @access public
	 *
	 * @param int $object_id Object ID.
	 * @return void
	 */
	public function get_archive_data( $object_id ) {
		$term = get_term( $object_id );

		$this->data += [
			'id'            => $object_id,
			'title'         => $term->name,
			'page'          => $term->slug,
			'url'           => get_term_link( $id ),
			'template'      => 'archive',
			$term->taxonomy => $term->name,
		];
	}

	/**
	 * Get WordPress 404 Data.
	 * 
	 * @since  1.0.0
	 * @access public
	 *
	 * @param int $object_id Object ID.
	 * @return void
	 */
	public function get_404_data() {
		global $wp;

		$this->data += [
			'title'    => '404',
			'url'      => home_url( $wp->request ),
			'template' => '404',
		];
	}

	/**
	 * Get WordPress Search Results Data.
	 * 
	 * @since  1.0.0
	 * @access public
	 *
	 * @param int $object_id Object ID.
	 * @return void
	 */
	public function get_search_data() {
		global $wp;

		$this->data += [
			'title'    => 'Search',
			'url'      => home_url( $wp->request ),
			'template' => 'search',
		];
	}

	/**
	 * Get WordPress Homepage Data.
	 * 
	 * @since  1.0.0
	 * @access public
	 *
	 * @param int $object_id Object ID.
	 * @return void
	 */
	public function get_homepage_data() {
		$this->data += [
			'title'    => 'Homepage',
			'url'      => home_url( $wp->request ),
			'template' => 'home',
		];
	}

	/**
	 * Get WordPress Singluarl Post Data.
	 * 
	 * @since  1.0.0
	 * @access public
	 *
	 * @param int $object_id Object ID.
	 * @return void
	 */
	public function get_singular_data( $object_id ) {
		$post       = get_post( $object_id );
		$this->data += [
			'id'            => $object_id,
			'title'         => $post->post_title,
			'page'          => $post->post_name,
			'url'           => get_the_permalink( $object_id ),
			'post_type'     => get_post_type( $object_id ),
			'template'      => 'single',
			'author'        => $this->get_author_name( $post->post_author ),
			'publish_date'  => $this->get_publish_date( $object_id ),
			'post_modified' => $this->get_post_modified_date( $object_id ),
		];

		$this->add_post_taxonomy_data( $object_id );
	}

	/**
	 * Get the taxonomy data for a post.
	 * 
	 * @since  1.0.0
	 * @access public
	 *
	 * @param int $object_id Object ID.
	 * @return void
	 */
	public function add_post_taxonomy_data( $object_id ) {

		// Setup Taxonomy Data.
		$taxonomies = get_taxonomies();

		$excluded_taxonomies = apply_filters( 'tenup_datalayer_exclude_taxonomies', 
			['author','nav_menu', 'link_category', 'post_format', 'wp_theme', 'wp_template_part_area']
		);
		
		foreach( $taxonomies as $type => $taxonomy ) {

			if ( ! in_array( $type, $excluded_taxonomies ) ) {
				$terms = get_the_terms( $object_id, $type );

				foreach ( $terms as $term ) {
					$this->data[ $type ][] = apply_filters( 'tenup_datalayer_taxonomy_' . $type . '_name', $term->name, $term );
				}
			}
		}
	}

	/**
	 * Get Author Name by ID.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @param int $author_id Author ID.
	 * 
	 * @return string
	 */
	public function get_author_name( $author_id ) {
		return apply_filters( 'tenup_datalayer_author_name', get_the_author_meta( 'display_name', $author_id ), $author_id );
	}

	/**
	 * Get Publish Date.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @param int $object_id ID.
	 * 
	 * @return string
	 */
	public function get_publish_date( $object_id ) {
		return apply_filters( 'tenup_datalayer_publish_date', get_the_date( $this->get_date_format(), $object_id ) );
	}

	/**
	 * Get the Last Modified Date.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @param int $object_id ID.
	 * 
	 * @return string
	 */
	public function get_post_modified_date( $object_id ) {
		return apply_filters( 'tenup_datalayer_updated_date', get_the_modified_date( $this->get_date_format(), $object_id ) );
	}

	/**
	 * Get Date Format.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return string
	 */
	public function get_date_format() {
		return apply_filters( 'tenup_datalayer_date_format', get_option('date_format') );
	}

	/**
	 * Add the GTM tag.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function add_gtm_tag() {

		if ( is_admin() ) {
			return;
		}

		$gtm_id = apply_filters( 'tenup_datalayer_gtm_id', false );

		if ( empty( $gtm_id ) ) {
			return;
		}

		wp_enqueue_script(
			'tenup-datalayer-gtm',
			'https://www.googletagmanager.com/gtm.js?id=' . esc_attr( $gtm_id ),
			array(),
			'1.0.0',
			[
				'strategy' => 'async',
			]
		);
	}
}
