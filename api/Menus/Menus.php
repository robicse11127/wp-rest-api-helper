<?php
namespace WPRAH\API\Menus;

class Menus {

    /**
    * Construct Function
    * @since 2.0.0
    */
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'add_endpoint' ] );
    }

    /**
    * Add Custom Endpoint ( Menus )
    * @since 2.0.0
    */
    public function add_endpoint() {
        register_rest_route( 'wp/v2', 'menus', [
            'methods' => 'GET',
            'callback' => [ $this, 'get_registered_menus' ]
        ]);
    }

    /**
    * add_endpoint Callback Function
    * @since 2.0.0
    */
    public function get_registered_menus() {
        $nav_menus  = get_registered_nav_menus();
        $locations  = get_nav_menu_locations();
        $menu_list  = [];
        foreach( $nav_menus as $key=>$nav_menu ) {
            $menu               = wp_get_nav_menu_object( $locations[ $key ] );
            $menu_items         = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );
    
            $parent_menu = [];
            $child_menu  = [];
            $main_menu   = [];
            foreach( $menu_items as $menu_item ) {
                if( $menu_item->menu_item_parent == 0 ) {
                    $parent_menu[] = [
                        'ID'            => $menu_item->ID,
                        'title'         => $menu_item->title,
                        'slug'          => strtolower( trim( preg_replace( '/[^A-Za-z0-9-]+/', '-', $menu_item->title ) ) ),
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
    
            $parent_menu_count = count( $parent_menu );
            $child_menu_count  = count( $child_menu );
            
            for( $i = 0; $i < $parent_menu_count; $i++ ) {
                $child = [];
                for( $j = 0; $j < $child_menu_count; $j++ ) {
                    if( $parent_menu[$i]['ID'] == $child_menu[ $j ][ 'parent_id' ] ) {
                        $child[] = $child_menu[ $j ];
                    }
                }
                
                $main_menu[] = [
                    'parent' => $parent_menu[ $i ],
                    'child'  => $child
                ];
            }
    
            $menu_list[ $key ] = $main_menu;
        }
    
        return $menu_list;
    }

}