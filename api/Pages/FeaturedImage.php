<?php
namespace WPRAH\API\Pages;

class FeaturedImage {

    /**
    * Construct Function
    * @since 2.0.0
    */
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'add_field' ] );
    }

    /**
    * Add featured_image_src field in posts json object
    * @since 2.0.0
    */
    public function add_field() {
        register_rest_field(
            [ 'page' ],
            'featured_image_src',
            array(
                'get_callback'    => [ $this, 'get_feature_image' ],
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    /**
    * add_field Callback Function
    * @since 2.0.0
    */
    public function get_feature_image( $object ) {
        $full_image         = get_the_post_thumbnail_url( $object['id'], 'full' );
        $large_image        = get_the_post_thumbnail_url( $object['id'], 'large' );
        $medium_image       = get_the_post_thumbnail_url( $object['id'], 'medium' );
        $thumbnail_image    = get_the_post_thumbnail_url( $object['id'], 'thumbnail' );

        $feature_image = [
            'full'      => $full_image ? $full_image : WPRAH_PLUGIN_URL . 'assets/img/placeholder.png',
            'large'     => $large_image ? $large_image : WPRAH_PLUGIN_URL . 'assets/img/placeholder.png',
            'medium'    => $medium_image ? $medium_image : WPRAH_PLUGIN_URL . 'assets/img/placeholder.png',
            'thumbnail' => $thumbnail_image ? $thumbnail_image : WPRAH_PLUGIN_URL . 'assets/img/placeholder.png',
        ];

        return $feature_image;
    }

}