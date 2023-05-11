<?php

namespace MinMaxWoocommerce;
use MinMaxWoocommerce\Frontend\Storefront;

/**
* Frontend handler class
* 
* @since    1.0.0
* @param    none
* @return   object
*/
class Frontend {

    /**
    * Initialize the class
    *
    * @since    1.0.0
    * @param    none
    * @return   object
    */
    function __construct() 
    {
        Storefront::instance()->init();
    }
}
