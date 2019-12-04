<?php 
/**
 * Plugin Name: WP Rest API Helper
 * Author: Rabiul Islam
 * Author URI: https://rabiul-islam-robi.me
 * Text-domain: wp-rest-api-helper
 * Version: 1.0.0
 * Description: A plugin to help out WP Rest API
 */

if( !defined('ABSPATH') ) : exit(); endif;

add_action( 'rest_api_init', 'add_thumbnail_to_JSON' );
function add_thumbnail_to_JSON() {

    /**
     * Add Feature Image
     */
    register_rest_field( 
        'post', // Where to add the field (Here, blog posts. Could be an array)
        'featured_image_src', // Name of new field (You can call this anything)
        array(
            'get_callback'    => 'get_image_src',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    /**
     * Add Post Author Details
     */
    register_rest_field( 
        'post', // Where to add the field (Here, blog posts. Could be an array)
        'author_details', // Name of new field (You can call this anything)
        array(
            'get_callback'    => 'get_post_author',
            'update_callback' => null,
            'schema'          => null,
        )
    );
    
    /**
     * Add POst Published Date
     */
    register_rest_field( 
        'post', // Where to add the field (Here, blog posts. Could be an array)
        'published_on', // Name of new field (You can call this anything)
        array(
            'get_callback'    => 'get_post_published_date',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    /**
     * Add Post Terms
     */
    register_rest_field( 
        'post', // Where to add the field (Here, blog posts. Could be an array)
        'post_terms', // Name of new field (You can call this anything)
        array(
            'get_callback'    => 'get_post_term_names',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

/**
 * Get Post Feature Image
 */
function get_image_src( $object, $field_name, $request ) {
    $feat_img_array = wp_get_attachment_image_src(
        $object['featured_media'], // Image attachment ID
        'full',  // Size.  Ex. "thumbnail", "large", "full", etc..
        true // Whether the image should be treated as an icon.
    );
    return $feat_img_array[0];
}

/**
 * Get Post Author Details
 */
function get_post_author($object) {
    $author_id = $object['author'];
    $author_details = array(
        'user_nicename' => get_the_author_meta('user_nicename', $author_id),
        'user_url'      => get_author_posts_url($author_id),
    );

    return $author_details;
}

/**
 * Get Post Published Date
 */
function get_post_published_date($object) {
    return $post_punlishde_on = date('M j, Y', strtotime($object['date']));
}

/**
 * Get Term Name
 */
function get_post_term_names($object) {
    $ids = $object['categories'];
    $term_info = [];
    foreach( $ids as $id ) {
        $term = get_term_by('id', $id, 'category');
        $term_info[] = [
            'id'            => $term->term_id,
            'name'          => $term->name,
            'slug'          => $term->slug,
            'description'   => $term->description,
            'parent'        => $term->parent,
            'count'         => $term->count,
            'url'           => get_term_link($term->term_id, 'category')
        ];
    }

    return $term_info;
} 

/**
 * Custom Endpoints
 */
add_action( 'rest_api_init', function() {
    register_rest_route('wp/v2', 'general', [
        'methods' => 'GET',
        'callback' => 'get_general_info'
    ]);
});

function get_general_info() {
    $general = [
        'site_title' => get_bloginfo('name'),
        'site_tag_line' => get_bloginfo('description'),
        'home_url' => home_url('/'),
        'ajax_url' => admin_url('admin-ajax.php'),
        'admin_url' => get_bloginfo('admin_email'),
        'wp_version' => get_bloginfo('version'),
        'language' => get_bloginfo('language'),
        'posts_per_page' => get_option('posts_per_page'),
    ];

    return $general;
}