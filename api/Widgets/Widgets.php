<?php
namespace WPRAH\API\Widgets;

class Widgets {

    /**
    * Construct Fucntion
    * @since 2.0.0
    */
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'add_endpoint' ] );
    }

    /**
    * Add Custom Endpoint ( Widgets )
    * @since 2.0.0
    */
    public function add_endpoint() {
        register_rest_route( 'wp/v2', 'widgets', [
            'methods' => 'GET',
            'callback' => [ $this, 'get_active_sidebars' ]
        ]);
    }

    /**
    * add_endpoint Callback Function
    * @since 1.0.0
    */
    public function get_active_sidebars() {
        $sidebar_widget_ids = [];
        $sidebar_widgets = $GLOBALS[ 'wp_registered_sidebars' ];
        foreach( $sidebar_widgets as $key => $sidebar_widget ) {
            $sidebar_widget_ids[] = $key;
        }

        $widget_data = [];
    
        foreach( $sidebar_widget_ids as $sidebar_widget_id ) {
            $sidebar_id       = $sidebar_widget_id;
            $sidebars_widgets = wp_get_sidebars_widgets();
            $widget_ids       = $sidebars_widgets[$sidebar_id];
            $widgets          = [];
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
    
                    $query = new \WP_Query($args); wp_reset_postdata();
    
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
                    $categories    = get_categories(array( 'orderby' => 'name', 'order' => 'ASC' ));
                    foreach($categories as $category) {
                        $category_list[] = [
                            'url'  => get_category_link( $category->term_id ),
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
                    $query = new \WP_Query($args); wp_reset_postdata();
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