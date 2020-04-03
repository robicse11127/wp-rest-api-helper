<?php
namespace WPRAH\API\Posts;

class PostTerms {

    /**
    * Construct Function
    * @since 2.0.0
    */
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'add_field' ] );
    }

    /**
    * Add post_terms field in posts json object
    * @since 2.0.0
    */
    public function add_field() {
        register_rest_field(
            [ 'post' ],
            'post_terms',
            array(
                'get_callback'    => [ $this, 'get_post_terms' ],
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    /**
    * add_field Callback Function
    * @since 2.0.0
    */
    public function get_post_terms( $object ) {
        $ids = $object[ 'categories' ];
        $term_info = [];
        foreach( $ids as $id ) {
            $term = get_term_by( 'id', $id, 'category' );
            $term_info[] = [
                'id'            => $term->term_id,
                'name'          => $term->name,
                'slug'          => $term->slug,
                'description'   => $term->description,
                'parent'        => $term->parent,
                'count'         => $term->count,
                'url'           => get_term_link( $term->term_id, 'category' )
            ];
        }

        return $term_info;
    }

}