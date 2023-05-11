<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/beyond88
 * @since      1.0.0
 *
 * @package    Min_Max_For_Woocommerce
 * @subpackage Min_Max_For_Woocommerce/includes
 * @author     Mohiuddin Abdul Kader <muhin.cse.diu@gmail.com>
 */
class Min_Max_For_Woocommerce_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'min-max-for-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
	
}
