<?php
/**
 * Plugin Name: Datalayer
 * Version: 1.0.0
 */

//  namespace Tenup\DataLayer;

/**
 * Note: We have not decided yet whether to use it as standalone WordPress plugin OR a class.
 */

class DataLayer {
    public $data = array();

    public function __construct() {
        add_action( 'wp_head', [ $this, 'setup' ] );
        // add_action( 'wp_head', __NAMESPACE__ . '\\setup' );
    }

    public function setup() {
        echo "<pre>"; print_r($this->get_data()); echo "</pre>";
    }

    public function get_data() {
        $id = get_queried_object_id();
        
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
                $this->data['title'] = $post->post_title;
                $this->data['url'] = get_the_permalink( $id );
                $this->data['categories'] = $taxonomy_data['category'];
                $this->data['tags'] = $taxonomy_data['post_tag'];
                $this->data['post_type'] = get_post_type( $id );
                $this->data['template'] = 'single';
                $this->data['author'] = $this->get_author_name( $post->post_author );
            } else {

            }
        }
    
        $this->data['publish_date'] = $this->get_publish_date( $id );

        /**
         * Data values of datalayer.
         * 
         * @since 1.0.0
         */
        $this->data = apply_filters( 'tenup_datalayer_data_values', $this->data );

        return wp_json_encode( $this->data );
    }

    /**
     * Get Author Name by ID.
     * 
     * @param int $author_id Author ID.
     * 
     * @since 1.0.0
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
     * @since 1.0.0
     * 
     * @return string
     */
    public function get_publish_date( $id ) {
        return apply_filters( 'tenup_datalayer_publish_date', get_the_date( $this->get_date_format(), $id ) );
    }

    /**
     * Get Date Format.
     * 
     * @since 1.0.0
     * 
     * @return string
     */
    public function get_date_format() {
        return apply_filters( 'tenup_datalayer_date_format', get_option('date_format') );
    }

    public function get_taxonomy_data( $id ) {
        // Setup Taxonomy Data.
        $taxonomies = get_taxonomies();
        
        foreach( $taxonomies as $type => $taxonomy ) {
            $object_terms[ $type ] = wp_get_object_terms( $id, $type );
        }

        return $object_terms;
    }
}

// add_action( 'init', [])
$datalayer = new DataLayer();
// echo "<pre>";print_r($datalayer->setup()); echo "</pre>";