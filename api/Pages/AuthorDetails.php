<?php
namespace WPRAH\API\Pages;

class AuthorDetails {

    /**
    * Construct Function
    * @since 2.0.0
    */
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'add_field' ] );
    }

    /**
    * Add author_details field in posts json object
    * @since 2.0.0
    */
    public function add_field() {
        register_rest_field(
            [ 'page' ],
            'author_details',
            array(
                'get_callback'    => [ $this,  'get_post_author' ],
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    /**
    * add_field Callback Function
    * @since 2.0.0
    */
    public function get_post_author( $object ) {
        $author_id = $object['author'];
        $author_details = array(
            'user_nicename' => get_the_author_meta( 'user_nicename', $author_id ),
            'user_url'      => get_author_posts_url( $author_id ),
        );

        return $author_details;
    }

}