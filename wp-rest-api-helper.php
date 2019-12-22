<?php 
/**
 * Plugin Name: WP Rest API Helper
 * Author: MD. Rabiul Islam
 * Author URI: https://rabiul-islam-robi.me
 * Text-domain: wp-rest-api-helper
 * Version: 1.0.0
 * Description: A plugin to help out WP Rest API
 */

if( !defined('ABSPATH') ) : exit(); endif;


/**
 * Add Featured Image Source to Posts & Pages
 * 
 * @author Rabiul
 * 
 * @since 1.0.0
 */
if( !function_exists( 'wprah_add_feature_image_src)' ) ) {
    add_action( 'rest_api_init', 'wprah_add_feature_image_src' );
    function wprah_add_feature_image_src() {
        register_rest_field( 
            ['post', 'page'],
            'featured_image_src',
            array(
                'get_callback'    => 'wprah_get_image_src',
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    /**
     * Get Post Feature Image
     */
    function wprah_get_image_src( $object, $field_name, $request ) {
        $full_image         = get_the_post_thumbnail_url( $object['id'], 'full' );
        $large_image        = get_the_post_thumbnail_url( $object['id'], 'large' );
        $medium_image       = get_the_post_thumbnail_url( $object['id'], 'medium' );
        $thumbnail_image    = get_the_post_thumbnail_url( $object['id'], 'thumbnail' );

        $feature_image = [
            'full'      => $full_image ? $full_image : plugins_url( '/', __FILE__ ) . 'img/placeholder.png',
            'large'     => $large_image ? $large_image : plugins_url( '/', __FILE__ ) . 'img/placeholder.png',
            'medium'    => $medium_image ? $medium_image : plugins_url( '/', __FILE__ ) . 'img/placeholder.png',
            'thumbnail' => $thumbnail_image ? $thumbnail_image : plugins_url( '/', __FILE__ ) . 'img/placeholder.png',
        ];

        return $feature_image;
    }
}

/**
 * Add Author Details to Posts & Pages
 * 
 * @author Rabiul
 * 
 * @since 1.0.0
 */
if( !function_exists( 'wprah_add_author_details' ) ) {
    add_action( 'rest_api_init', 'wprah_add_author_details' );
    function wprah_add_author_details() {
        register_rest_field( 
            'post', 
            'author_details',
            array(
                'get_callback'    => 'wprah_get_post_author',
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    
    /**
     * Get Post Author Details
     */
    function wprah_get_post_author($object) {
        $author_id = $object['author'];
        $author_details = array(
            'user_nicename' => get_the_author_meta('user_nicename', $author_id),
            'user_url'      => get_author_posts_url($author_id),
        );

        return $author_details;
    }

}

/**
 * Add Published Date to Posts & Pages
 * 
 * @author Rabiul
 * 
 * @since 1.0.0
 */
if( !function_exists( 'wprah_add_published_date' ) ) {
    add_action( 'rest_api_init', 'wprah_add_published_date' );
    function wprah_add_published_date() {
        register_rest_field( 
            'post', 
            'published_on',
            array(
                'get_callback'    => 'wprah_get_post_published_date',
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }
    
    /**
     * Get Post Published Date
     */
    function wprah_get_post_published_date($object) {
        return $post_punlishde_on = date('M j, Y', strtotime($object['date']));
    }

}

/**
 * Add Posts Terms to Posts
 * 
 * @author Rabiul
 * 
 * @since 1.0.0
 */
if( !function_exists( 'wprah_add_post_terms' ) ) {
    add_action( 'rest_api_init', 'wprah_add_post_terms' );
    function wprah_add_post_terms() {
        register_rest_field( 
            'post',
            'post_terms',
            array(
                'get_callback'    => 'wprah_get_post_term_names',
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }
    
    /**
     * Get Term Name
     */
    function wprah_get_post_term_names($object) {
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

}

/**
 * Add Post Meta Box Values
 * 
 * @author Rabiul
 * 
 * @since 1.0.0
 */
if( !function_exists( 'wprah_add_meta_box_values' ) ) {
    add_action( 'rest_api_init', 'wprah_add_meta_box_values' );
    function wprah_add_meta_box_values() {
        register_rest_field( 
            'post',
            'metaboxes',
            array(
                'get_callback'    => 'wprah_get_post_meta_values',
                'update_callback' => 'wprah_update_post_meta_values',
                'schema'          => null,
            )
        );
    }

    /**
     * Get post meta boxes
     */
    function wprah_get_post_meta_values($object) {
        $loved = get_post_meta(15, '_post_loved', true);
        return $meta = get_post_meta($object['id']);
    }

    /**
     * Update Post meta Values
     */
    function wprah_update_post_meta_values( $value, $object, $field_name ) {
        return update_post_meta( $object['id'], $field_name, $value );
    }
}


/**
 * Custom Endpoint ( General Info )
 * 
 * @author Rabiul
 * 
 * @since 1.0.0
 */
if( !function_exists( 'wprah_get_general_info' ) ) {

    add_action( 'rest_api_init', function() {
        register_rest_route('wp/v2', 'general', [
            'methods' => 'GET',
            'callback' => 'wprah_get_general_info'
        ]);
    });
    
    function wprah_get_general_info() {
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

}

/**
 * Custom Endpoints ( Registered Menus )
 * 
 * @author Rabiul
 * 
 * @since 1.0.0
 */
if( !function_exists( 'wprah_get_registered_menus' ) ) {

    add_action( 'rest_api_init', function() {
        register_rest_route( 'wp/v2', 'menus', [
            'methods' => 'GET',
            'callback' => 'wprah_get_registered_menus'
        ]);
    });
    
    function wprah_get_registered_menus() {
        $nav_menus  = get_registered_nav_menus();
        $locations  = get_nav_menu_locations();
        $menu_list  = [];
        foreach( $nav_menus as $key=>$nav_menu ) {
            $menu               = wp_get_nav_menu_object( $locations[ $key ] );
            $menu_items         = wp_get_nav_menu_items($menu->term_id, array( 'order' => 'DESC' ));
    
            $parent_menu = [];
            $child_menu = [];
            $main_menu = [];
            foreach( $menu_items as $menu_item ) {
                if( $menu_item->menu_item_parent == 0 ) {
                    $parent_menu[] = [
                        'ID'            => $menu_item->ID,
                        'title'         => $menu_item->title,
                        'slug'          => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $menu_item->title))),
                        'menu_order'    => $menu_item->menu_order,
                        'parent_id'     => $menu_item->menu_item_parent,
                        'post_type'     => $menu_item->post_type,
                        'url'           => $menu_item->url,
                        'type'          => $menu_item->type
                    ];
                }else {
                    $child_menu[] = [
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
            }
    
            $parent_menu_count = count($parent_menu);
            $child_menu_count = count($child_menu);
            
            for( $i = 0; $i < $parent_menu_count; $i++ ) {
                $child = [];
                for( $j = 0; $j < $child_menu_count; $j++ ) {
                    if( $parent_menu[$i]['ID'] == $child_menu[$j]['parent_id'] ) {
                        $child[] = $child_menu[$j];
                    }
                }
                
                $main_menu[] = [
                    'parent' => $parent_menu[$i],
                    'child' => $child
                ];
            }
    
            $menu_list[$key] = $main_menu;
        }
    
        return $menu_list;
    }
}

/**
 * Custom Endpoints ( Active Widget Locations )
 */
if( !function_exists( 'wprah_get_active_sidebars' ) ) {

    add_action( 'rest_api_init', function() {
        register_rest_route( 'wp/v2', 'widgets', [
            'methods' => 'GET',
            'callback' => 'wprah_get_active_sidebars'
        ]);
    });
    
    function wprah_get_active_sidebars() {
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
                            'id'        => $post->ID,
                            'title'     => get_the_title($post->ID),
                            'url'       => get_the_permalink($post->ID),
                            'slug'      => $post->post_name,
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
    
                    $menu_items = wp_get_nav_menu_items($instance[$key]['nav_menu'], array( 'order' => 'DESC' ));
                    foreach( $menu_items as $menu_item ) {
                        $value[] = [
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
                    $value = $category_list;
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
                            'slug'      => $post->post_name,
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
                    $value = get_comments([
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
}

/**
 * Custom Endpoints ( Get Wp Options )
 */
add_action( 'rest_api_init', function() {
    register_rest_route( 'wp/v2', 'options', [
        'methods' => 'GET',
        'callback' => 'get_wp_options'
    ]);
});

function get_wp_options() {
    $all_options = wp_load_alloptions();
    $my_options  = array();
    
    foreach ( $all_options as $name => $value ) {
        if ( stristr( $name, '_transient' ) ) {
            $my_options[ $name ] = $value;
        }
    }

    return $my_options;
}