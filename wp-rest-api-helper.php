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
        ['post', 'page'], // Where to add the field (Here, blog posts. Could be an array)
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
    $full_image         = get_the_post_thumbnail_url( $object['id'], 'full' );
    $large_image        = get_the_post_thumbnail_url( $object['id'], 'large' );
    $medium_image       = get_the_post_thumbnail_url( $object['id'], 'medium' );
    $thumbnail_image    = get_the_post_thumbnail_url( $object['id'], 'thumbnail' );

    $feature_image = [
        'full'      => $full_image ? $full_image : '',
        'large'     => $large_image ? $large_image : '',
        'medium'    => $medium_image ? $medium_image : '',
        'thumbnail' => $thumbnail_image ? $thumbnail_image : '',
    ];

    return $feature_image;
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
 * Custom Endpoint ( General Info )
 */
add_action( 'rest_api_init', function() {
    register_rest_route('wp/v2', 'general', [
        'methods' => 'GET',
        'callback' => 'get_general_info'
    ]);
});

function get_general_info() {
    $general = [
        'site_title'    => get_bloginfo('name'),
        'site_tag_line' => get_bloginfo('description'),
        'home_url'      => home_url('/'),
        'ajax_url'      => admin_url('admin-ajax.php'),
        'admin_url'     => get_bloginfo('admin_email'),
        'wp_version'    => get_bloginfo('version'),
        'language'      => get_bloginfo('language'),
        'posts_per_page' => get_option('posts_per_page'),
    ];

    return $general;
}

/**
 * Custom Endpoints ( Registered Menus )
 */
add_action( 'rest_api_init', function() {
    register_rest_route( 'wp/v2', 'menus', [
        'methods' => 'GET',
        'callback' => 'get_registered_menus'
    ]);
});

function get_registered_menus() {
    $nav_menus  = get_registered_nav_menus();
    $locations  = get_nav_menu_locations();
    $menu_list  = [];
    foreach( $nav_menus as $key=>$nav_menu ) {
        $menu               = wp_get_nav_menu_object( $locations[ $key ] );
        $menu_items         = wp_get_nav_menu_items($menu->term_id, array( 'order' => 'DESC' ));

        $menu_output = [];
        foreach( $menu_items as $menu_item ) {
            $menu_output[] = [
                'ID'            => $menu_item->ID,
                'title'         => $menu_item->title,
                'slug'          => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $menu_item->title))),
                'menu_order'    => $menu_item->menu_order,
                'parent_id'     => $menu_item->menu_item_parent,
                'post_type'     => $menu_item->post_type,
                'url'           => $menu_item->url,
                'type'          => $menu_item->type
            ];
        }
        $menu_list[$key]    = $menu_output;
    }

    return $menu_list;
}

/**
 * Custom Endpoints ( Active Widget Locations )
 */
add_action( 'rest_api_init', function() {
    register_rest_route( 'wp/v2', 'widgets', [
        'methods' => 'GET',
        'callback' => 'get_active_sidebars'
    ]);
});

function get_active_sidebars() {
    $sidebar_widget_ids = [];
    $sidebar_widgets = $GLOBALS['wp_registered_sidebars'];
    foreach( $sidebar_widgets as $key => $sidebar_widget ) {
        $sidebar_widget_ids[] = $key;
    }

    // return $sidebar_widget_ids;
    $widget_data = [];

    foreach( $sidebar_widget_ids as $sidebar_widget_id ) {
        $sidebar_id = $sidebar_widget_id;
        $sidebars_widgets = wp_get_sidebars_widgets();
        $widget_ids = $sidebars_widgets[$sidebar_id]; 
        $widgets = [];
        foreach( $widget_ids as $id ) {
            $wdgtvar    = 'widget_'._get_widget_id_base( $id );
            $type       = _get_widget_id_base( $id );
            $instance   = get_option( $wdgtvar );
            $key        = str_replace( $type.'-', '', $id );
            $value      = [];
            /**
             * Recent Post Widget
             */
            if( 'recent-posts' == $type ) {
                $args = [
                    'post_type' => 'post',
                    'posts_per_page' => $instance[$key]['number']
                ];

                $query = new WP_Query($args); wp_reset_postdata();

                foreach( $query->posts as $post ) {
                    $value [] = [
                        'title'     => get_the_title($post->ID),
                        'url'       => get_the_permalink($post->ID),
                        'feature_image' => [
                            'thumbnail' => get_the_post_thumbnail_url($post->ID, 'thumbnail'),
                            'medium'    => get_the_post_thumbnail_url($post->ID, 'medium'),
                            'full'      => get_the_post_thumbnail_url($post->ID, 'full')
                        ],
                        'date'      => get_the_date('F j, Y', $post->ID)
                    ];
                }
            }

            /**
             * Nav Menu Widget
             */
            if( 'nav_menu' == $type ) {
                $value[] = [
                    'menu_item' => wp_get_nav_menu_items($instance[$key]['nav_menu'], array( 'order' => 'DESC' ))
                ];
            }

            /**
             * Category Widgets
             */
            if( 'categories' == $type ) {
                $category_list = [];
                $categories = get_categories(array( 'orderby' => 'name', 'order' => 'ASC' ));
                foreach($categories as $category) {
                    $category_list[] = [
                        'url' => get_category_link( $category->term_id ),
                        'term' => $category,
                    ];
                }
                $value[] = $category_list;
            }
            
            /**
             * Page Widget
             */
            if( 'pages' == $type ) {
                $args = [
                    'post_type'         => 'page',
                    'posts_per_page'    => -1,
                    'post__not_in'      => array( $instance[$key]['exclude'] ),
                    'orderby'           => $instance[$key]['sortby'],
                ];
                $query = new WP_Query($args); wp_reset_postdata();
                foreach( $query->posts as $post ) {
                    $value[] = [
                        'title'     => get_the_title($post->ID),
                        'url'       => get_the_permalink($post->ID),
                        'feature_image' => [
                            'thumbnail' => get_the_post_thumbnail_url($post->ID, 'thumbnail'),
                            'medium'    => get_the_post_thumbnail_url($post->ID, 'medium'),
                            'full'      => get_the_post_thumbnail_url($post->ID, 'full')
                        ],
                        'date'      => get_the_date('F j, Y', $post->ID)
                    ];
                }
            }

            /**
             * Comment Widget
             */
            if( 'recent-comments' == $type ) {
                $value[] = get_comments([
                    'number' => $instance[$key]['number']
                ]);

            }

            $widgets[] = [
                'type'      => $type,
                'instance'  => $instance[$key],
                'value'     => $value
            ];
        }

        $widget_data[$sidebar_widget_id] = $widgets;
    }
        
    return $widget_data;
}
