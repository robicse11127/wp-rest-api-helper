<?php
namespace WPRAH\API\Posts;

class PostDate {

    /**
    * Construct Function
    * @since 2.0.0
    */
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'add_field' ] );
    }

    /**
    * Add published_on field in posts json object
    * @since 2.0.0
    */
    public function add_field() {
        register_rest_field(
            [ 'post' ],
            'published_on',
            array(
                'get_callback'    => [ $this, 'get_post_published_date' ],
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    /**
    * add_field Callback Function
    * @since 2.0.0
    */
    public function get_post_published_date( $object ) {
        $post_published_on = date( 'M j, Y', strtotime( $object['date'] ) );
        return $post_published_on;
    }

}