<?php
namespace MinMaxWoocommerce;

/**
 * Support language
 * 
 * @since    1.0.0
 */ 
class MinMaxWoocommercei18n 
{

	/**
	* Call language method 
	*
	* @since    1.0.0
	*/
    public function __construct() {
        add_action( 'plugins_loaded', [ $this, 'load_plugin_textdomain' ] );
    }

	/**
	* Load language file from directory
	*
	* @since    1.0.0
	*/
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'min-max-for-woocommerce',
			false,
			dirname( dirname( MMFWC_BASENAME ) ) . '/languages/'
		);

	}

}