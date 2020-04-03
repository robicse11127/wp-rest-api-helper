<?php
/**
 * Plugin Name: WP Rest API Helper
 * Author: MD. Rabiul Islam
 * Text-domain: wp-rest-api-helper
 * Version: 2.0.1
 * Description: A plugin to help out WP Rest API
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if( !defined('ABSPATH') ) : exit(); endif;

/**
* Require Autoloader
* @since 2.0.0
*/
require_once 'vendor/autoload.php';

use WPRAH\API\Menus\Menus;
use WPRAH\API\Pages\Pages;
use WPRAH\API\Posts\Posts;
use WPRAH\LIBS\PluginUpdater;
use WPRAH\API\General\General;
use WPRAH\API\Widgets\Widgets;

final class WP_REST_API_Helper {

    /**
     * Define Plugin Version
     */
    const version = '2.0.1';

    /**
    * Construct Function
    * @since 2.0.0
    */
    private function __construct() {
        $this->plugin_constants();
        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
    }

    /**
    * Define Plugin Constants
    * @since 2.0.0
    */
    public function plugin_constants() {
        define( 'WPRAH_VERSION', self::version );
        define( 'WPRAH_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
        define( 'WPRAH_PLUGIN_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
    }

    /**
     * Singletone Instance
     * @since 2.0.0
     */
    public static function init() {
        static $instance = false;

        if( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
    * On Plugin Activation
    * @since 2.0.0
    */
    public function activate() {
        $updater = new PluginUpdater();
        // Update Plugin Installed Version
        update_option( 'wprah_plugin_version', $updater->update_plugin_version( WPRAH_VERSION ) );
    }

    /**
    * Plugin Init
    * @since 2.0.0
    */
    public function init_plugin() {
        new Posts();
        new Pages();
        new Menus();
        new Widgets();
        new General();

        // Check for Version Update
        $updater = new PluginUpdater();
        $updater->check_plugin_version( WPRAH_VERSION, get_option( 'wprah_plugin_version' ) );
        $updater->plugin_update_message();
    }

}

/**
* Init Main Plugin
* @since 2.0.0
*/
function wp_rest_api_helper() {
    return WP_REST_API_Helper::init();
}

// Run the plugin
wp_rest_api_helper();
