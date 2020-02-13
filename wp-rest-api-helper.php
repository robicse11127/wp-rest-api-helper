<?php 
/**
 * Plugin Name: WP Rest API Helper
 * Plugin URI: https://wpoutline.com/plugins/wp-rest-api-helper
 * Author: MD. Rabiul Islam
 * Author URI: https://robizstory.wordpress.com/
 * Text-domain: wp-rest-api-helper
 * Version: 1.0.0
 * Description: A plugin to help out WP Rest API
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
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
        return $post_published_on = date('M j, Y', strtotime($object['date']));
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
        return $meta = get_post_meta($object['id']);
    }

    /**
     * Update Post meta Values
     */
    function wprah_update_post_meta_values( $field_value, $object ) {
        if( is_array($field_value) ) {
            foreach( $field_value as $key => $value ) {
                update_post_meta( $object->ID, $key, $value );
            }
            return true;
        }
    }
    
}

/**
 * Add Post Slug In Search
 * 
 * @author Rabiul
 * 
 * @since 1.0.0
 */
if( !function_exists( 'wprah_add_post_slug' ) ) {
    add_action( 'rest_api_init', 'wprah_add_post_slug' );
    function wprah_add_post_slug() {
        register_rest_field( 
            'post', 
            'search_slug',
            array(
                'get_callback'    => 'wprah_get_post_slug',
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }
    
    /**
     * Get Post Author Details
     */
    function wprah_get_post_slug($object) {
        $post_id    = $object['id'];
        $slug       = get_post($post_id)->post_name;

        return $slug;
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
* Custom Search
* @author Rabiul
* @since 1.0.0
*/
// Title Filter
function wprah_search_title($where, $wp_query) {
    global $wpdb;
    if ( $search_term = $wp_query->get( 'wprah_post_search_title' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . $wpdb->esc_like( $search_term ) . '%\'';
    }
    return $where;
}
add_action('rest_api_init', function() {
    register_rest_route(
        'wp/v2',
        '/s(?:/(?P<slug>[\A-Za-z0-9\-\_]+))?(?:/(?P<per_page>[\0-9]+))?(?:/(?P<page>[\0-9]+))?',
        [
            'methods' => 'GET',
            'callback' => function(\WP_REST_REQUEST $req) {
                $paged = (get_query_var('paged')) ? absint( get_query_var('paged') ) : 1;
                $args = array(
                    'wprah_post_search_title' => $req->get_param('slug') ? $req->get_param('slug') : '', // search post title only
                    'post_status'       => 'publish',
                    'posts_per_page'    => $req->get_param('per_page') ? $req->get_param('per_page') : -1,
                    'paged'             => $req->get_param('page') ? $req->get_param('page') : 1
                );
                add_filter( 'posts_where', 'wprah_search_title', 10, 2 );
                $query = new WP_Query($args);
                remove_filter( 'posts_where', 'wprah_search_title', 10 );
                $output = [];

                foreach( $query->posts as $post ) {
                    // Featured Image
                    $full_image         = get_the_post_thumbnail_url( $post->ID, 'full' );
                    $large_image        = get_the_post_thumbnail_url( $post->ID, 'large' );
                    $medium_image       = get_the_post_thumbnail_url( $post->ID, 'medium' );
                    $thumbnail_image    = get_the_post_thumbnail_url( $post->ID, 'thumbnail' );

                    $feature_image = [
                        'full'      => $full_image ? $full_image : plugins_url( '/', __FILE__ ) . 'img/placeholder.png',
                        'large'     => $large_image ? $large_image : plugins_url( '/', __FILE__ ) . 'img/placeholder.png',
                        'medium'    => $medium_image ? $medium_image : plugins_url( '/', __FILE__ ) . 'img/placeholder.png',
                        'thumbnail' => $thumbnail_image ? $thumbnail_image : plugins_url( '/', __FILE__ ) . 'img/placeholder.png',
                    ];

                    // Post Terms
                    $terms = wp_get_post_terms( $post->ID, 'category', array('fields' => 'ids') );
                    $ids = $terms;
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
                    $output[] = [
                        'id' => $post->ID,
                        'title' => [
                            'rendered' => $post->post_title
                        ],
                        'slug' => $post->post_name,
                        'excerpt' => [
                            'rendered' => $post->post_excerpt
                        ],
                        'content' => [
                            'rendered' => $post->content
                        ],
                        'author' => $post->post_author,
                        'author_detials' => [
                            'user_nicename' => get_the_author_meta('user_nicename', $post->post_author),
                            'user_url' => get_author_posts_url($post->post_author)
                        ],
                        'featured_image_src' => $feature_image,
                        'published_on' => date('M j, Y', strtotime($post->post_date)),
                        'modified' => date('M j, Y', strtotime($post->post_modified)),
                        'post_terms' => $term_info,
                        'tags' => get_the_tags($post->ID),
                        'link' => get_the_permalink( $post->ID )

                    ];
                }
                return $output;
            }
        ]
    );
});

// Contact Form Endpoints
function contact_form_endpoints() {
    register_rest_route('wp/v2', '/contact-forms', [
        'methods' => 'post',
        'callback' => 'sent_contact_from'
    ]);
}
add_action('rest_api_init', 'contact_form_endpoints');

function sent_contact_from( WP_REST_Request $request ) {
    $name = sanitize_text_field(trim($request['name']));
    $subject = sanitize_text_field(trim($request['subject']));
    $email = sanitize_email(trim($request['email']));
    $message = wp_kses_post(trim($request['message']));
    $errors = [];
    if( empty($name) ) {
        $errors[] = 'Name is required';
    }
    if( empty($subject) ) {
        $errors[] = 'Subject is required';
    }
    if( empty($email) || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
        $errors[] = 'Valid email is required';
    }
    if( empty($message) ) {
        $errors[] = 'Message is required';
    }

    if( count($errors) ) {
        return new WP_Error( 'contact_form_errors', $errors, ['status' => 422] );
    }

    $to         = 'rabiul@bitmascot.com';
    $body    = "Name: {$name}. <br> From: {$email}. <br> Message: {$message}";
    $headers    = "From : <{$email}>";
    $headers 	.= "MIME-Version: 1.0\r\n";
    $headers 	.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $sent_email = mail($to, $subject, $body, $headers);
    if($sent_email) {
        return 'success';
    }else {
        $errors[] = 'Email not sent!';
        return new WP_Error( 'email_not_sent', $errors, ['status' => 422] );
    }
}