<?php
namespace WPRAH\LIBS;

class PluginUpdater {
    /**
    * Update Plugin Version
    * @since 1.0.0
    */
    public function update_plugin_version( $version ) {
        update_option( 'wprah_plugin_version', $version );
    }

    /**
    * Check Plugin Version
    * @since 1.0.0
    */
    public function check_plugin_version( $new_version, $old_version ) {
        if( $new_version !== $old_version ) {
            $this->update_plugin_version( $new_version );
        }
    }

}