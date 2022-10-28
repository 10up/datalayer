<?php
/**
 * DataLayer class file.
 * 
 * @since 1.0.0
 * 
 * @package 10up
 */

namespace TenUp\DataLayer;

/**
 * DataLayer Class
 */
class DataLayer {
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
     * Initiate Datalayer.
     * 
     * @since  1.0.0
     * @access public
     * 
     * @return void
     */
    public function __construct() {
        add_action( 'wp_head', [ $this, 'setup' ] );
    }

    /**
     * Setup Datalayer.
     * 
     * @since  1.0.0
     * @access public
     * 
     * @return void
     */
    public function setup() {
        /**
         * List of script handles to have support for datalayer parameters.
         * 
         * @since 1.0.0
         * 
         * @return array
         */
        $handles = apply_filters( 'tenup_datalayer_script_handles', [] );

        // Loop through the list of handles to localize the datalayer parameters to the script handles.
        if ( ! empty( $handles ) && is_array( $handles ) ) {
            foreach( $handles as $handle ) {
                wp_localize_script(
                    $handle,
                    'datalayer_args',
                    $this->prepare_data()
                );        
            }
        }
    }

    /**
     * Prepare Data.
     * 
     * @since  1.0.0
     * @access public
     * 
     * @return array
     */
    public function prepare_data() {
        // Get queried object ID.
        $id = get_queried_object_id();
        
        // Assign the object ID to the datalayer data.
        $this->data['id'] = $id;
        
        if ( is_archive() ) {
            $term = get_term( $id );
            
            $this->data['title'] = $term->name;
            $this->data['url'] = get_term_link( $id );
            $this->data['author'] = 0;
            
            if ( is_category() ) {
                $this->data['template'] = 'category';
            } elseif ( is_tag() ) {
                $this->data['template'] = 'tag';
            } elseif ( is_author() ) {
                $this->data['template'] = 'author';
            } elseif ( is_post_type_archive() ) {
                $this->data['template'] = 'post_type';
            }
        } elseif ( is_404() ) {
            global $wp;

            $this->data = [
                'title'    => '404',
                'url'      => home_url( $wp->request ),
                'author'   => 0,
                'template' => '404',
            ];
        } else {
            $post = get_post( $id );
            $taxonomy_data = $this->get_taxonomy_data( $id );
            
            if ( is_front_page() || is_home() ) {
                $this->data['template'] = 'home';
            } elseif ( is_singular() ) {
                $this->data['title']      = $post->post_title;
                $this->data['url']        = get_the_permalink( $id );
                $this->data['categories'] = ! empty( $taxonomy_data['category'] ) ? $taxonomy_data['category'] : '';
                $this->data['tags']       = ! empty( $taxonomy_data['post_tag'] ) ? $taxonomy_data['post_tag'] : '';
                $this->data['post_type']  = get_post_type( $id );
                $this->data['template']   = 'single';
                $this->data['author']     = $this->get_author_name( $post->post_author );
            }
        }
    
        // Assign publish date to the datalayer data.
        $this->data['publish_date'] = $this->get_publish_date( $id );

        /**
         * Data values of datalayer.
         * 
         * @since 1.0.0
         */
        $this->data = apply_filters( 'tenup_datalayer_data_values', $this->data );

        // Return the prepared data.
        return $this->data;
    }

    /**
     * Get JSON formatted data.
     * 
     * @since  1.0.0
     * @access public
     * 
     * @return string
     */
    public function get_json_data() {
        return wp_json_encode( $this->prepare_data() );
    }

    /**
     * Get Author Name by ID.
     * 
     * @param int $author_id Author ID.
     * 
     * @since  1.0.0
     * @access public
     * 
     * @return string
     */
    public function get_author_name( $author_id ) {
        return $author_id;
    }

    /**
     * Get Publish Date.
     * 
     * @param int $id ID.
     * 
     * @since  1.0.0
     * @access public
     * 
     * @return string
     */
    public function get_publish_date( $id ) {
        return apply_filters( 'tenup_datalayer_publish_date', get_the_date( $this->get_date_format(), $id ) );
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
     * Get Taxonomy data.
     * 
     * @since  1.0.0
     * @access public
     * 
     * @return array
     */
    public function get_taxonomy_data( $id ) {
        // Setup Taxonomy Data.
        $taxonomies = get_taxonomies();
        
        foreach( $taxonomies as $type => $taxonomy ) {
            $object_terms[ $type ] = wp_get_object_terms( $id, $type );
        }

        return $object_terms;
    }
}
