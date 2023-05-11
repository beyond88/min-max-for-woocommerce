<?php

namespace MinMaxWoocommerce;

/**
 * The admin class
 */
class Admin 
{

    /**
     * Initialize the class
     */
    function __construct() 
    {

        new Admin\Settings();
        new Admin\PluginMeta();
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions( $main, $licence ) 
    {

    }
}