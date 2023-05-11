<?php

namespace MinMaxWoocommerce;

/**
 * Installer class
 */
class Installer {

    /**
    * Run the installer
    *
    * @param none
    * @return void
    */
    public function run() {
        $this->add_version();
    }

    /**
     * Add time and version on DB
     * 
     * @param none
     * @return void
     */
    public function add_version() {
        $installed = get_option( 'mmfwc_installed' );

        if ( ! $installed ) {
            update_option( 'mmfwc_installed', time() );
        }

        update_option( 'mmfwc_version', MMFWC_VERSION );
    }

}
