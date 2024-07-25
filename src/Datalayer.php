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
		$this->register_scripts();
		$this->header_scripts();
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
						'data-brand',
						'data-prodId',
						'data-prodBrnd',
						'data-prodName',
						'data-prodPrice',
						'data-cmpAction',
						'data-superlative',
						'data-utm_source',
						'data-utm_medium',
						'data-utm_campaign',
						'data-gclid',
						'data-fbclid',
						'data-msclkid',
					]
				),
		];

		$this->get_parameters();
	}

	/**
	 * Get UTM Parameters.
	 *
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function get_parameters() {
		$parameters  = ['utm_source', 'utm_medium', 'utm_campaign', 'gclid', 'fbclid', 'msclkid'];

		foreach ( $parameters as $parameter ) {
			if ( isset( $_GET[ $parameter ] ) ) {
				$this->data[ $parameter ] = sanitize_text_field( wp_unslash( $_GET[ $parameter ] ) );
			}
		}

		$hashed_parameters = ['gclid', 'fbclid', 'msclkid'];

		foreach ( $hashed_parameters as $h_parameter ) {
			if ( isset( $_GET[ $h_parameter ] ) ) {
				$this->data[ $h_parameter ] = sanitize_text_field( md5( wp_unslash( $_GET[ $h_parameter ] ) ) );
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
		global $wp;

		$this->data += [
			'title'    => 'Homepage',
			'url'      => home_url( $wp->request ),
			'template' => 'homepage',
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

				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$this->data[ $type ][] = apply_filters( 'tenup_datalayer_taxonomy_' . $type . '_name', htmlspecialchars_decode( $term->name ), $term );
					}
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
		return apply_filters( 'tenup_datalayer_publish_date', get_post_time( $this->get_date_format(), false, $object_id ) );
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
		return apply_filters( 'tenup_datalayer_date_format', 'Y-m-d H:i' );
	}

	/**
	 * Register tracking scripts.
	 * 
	 * @since  1.0.0
	 * @access public
	 * 
	 * @return void
	 */
	public function header_scripts() {
		if ( is_admin() ) {
			return;
		}

		add_action( 'wp_head', [ $this, 'gtm_head_values' ], 1, 3 );
		add_action( 'wp_head', [ $this, 'gtm_head_script' ], 2, 3 );
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

	/**
	 * Send the abstracted datalayer to GTM.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function gtm_head_values() {
		$account_id = apply_filters( 'tenup_datalayer_gtm_id', false );

		if ( empty( $account_id ) ) {
			return;
		}
		
		?>
		<script>
		// Map the datalayer values before initiation.
		window.dataLayer      = window.dataLayer || [];
		window.tenupDataLayer = window.tenupDataLayer || [];
		window.tenupDataLayer = <?php echo wp_json_encode( $this->get_data() ); ?>;
		// Set the session storage for the URL parameters.
		const params = ['utm_source', 'utm_medium', 'utm_campaign', 'gclid', 'fbclid', 'msclkid'];
		params.forEach((param) => {
			const value = window.tenupDataLayer[param];
			const sessionValue = JSON.parse(sessionStorage.getItem(param));

			if (value) {
				sessionStorage.setItem(param, JSON.stringify({ [param]: value }));
			}

			if (sessionValue && !window.tenupDataLayer[param]) {
				window.tenupDataLayer[param] = sessionValue[param];
			}
		});
		
		// Push the abstracted datalayer to the GTM datalayer.
		dataLayer.push(window.tenupDataLayer);

		</script>
		<?php
	}

	/**
	 * Output Google Tag Manager script in head
	 */
	public function gtm_head_script(): void {
		$account_id = apply_filters( 'tenup_datalayer_gtm_id', false );

		if ( empty( $account_id ) ) {
			return;
		}

		$params_string = '';
		// TODO: re-add these to the plugin.
		// Create a settings page with this?
		// https://gitlab.10up.com/10up-snippets/10up-snippets/-/blob/feature/analytics/analytics/gtm/gtm-mu-plugin.php

		// $gtm_auth      = get_option( GTM_AUTH_PARAM_STRING, false );
		// $gtm_preview   = get_option( GTM_PREVIEW_PARAM_STRING, false );

		// if ( ! empty( $gtm_auth ) && ! empty( $gtm_preview ) ) {
		// 	$params_string = sprintf(
		// 		"+'&gtm_auth=%s&gtm_preview=%s&gtm_cookies_win=x'",
		// 		esc_js( $gtm_auth ),
		// 		esc_js( $gtm_preview )
		// 	);
		// }
		
		?>
		<script>
		// Map the datalayer values before initiation.
		window.dataLayer      = window.dataLayer || [];

		<?php do_action( 'tenup_datalayer_before_gtm_head_script' ); ?>

		(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl<?php echo $params_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need the & in URL. ?>;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','<?php echo esc_js( $account_id ); ?>');

		<?php do_action( 'tenup_datalayer_after_gtm_head_script' ); ?>
		</script>
		<!-- End Google Tag Manager -->

		<?php
	}
}
