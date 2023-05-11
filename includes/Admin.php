<?php

namespace MinMaxWoocommerce;
use MinMaxWoocommerce\Admin\Settings;

/**
 * The admin class
 */
class Admin {

    /**
     * Initialize the class
     */
    function __construct() {
        Settings::instance()->init();
        new Admin\PluginMeta();
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions( $main, $licence ) {

    }
}