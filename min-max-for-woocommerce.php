<?php

/**
 *
 * @link              https://github.com/beyond88
 * @since             1.0.0
 * @package           Min_Max_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Min and Max for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/min-max-for-woocommerce
 * Description:       Effortlessly handle minimum and maximum quantity limits with ease using the powerful features of the WooCommerce Default Quantity plugin.
 * Version:           2.0.0
 * Author:            Mohiuddin Abdul Kader
 * Author URI:        https://github.com/beyond88/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       min-max-for-woocommerce
 * Domain Path:       /languages
 * Requires PHP:      5.6
 * Requires at least: 4.4
 * Tested up to:      6.2
 *
 * WC requires at least: 3.1
 * WC tested up to:   6.7.0 
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class MinMaxWoocommerce {

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '2.0.0';

    /**
     * Class constructor
     */
    private function __construct() 
    {
        $this->define_constants();

        register_activation_hook( __FILE__, [ $this, 'activate' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );

    }

    /**
     * Initializes a singleton instance
     *
     * @return \MinMaxWoocommerce
     */
    public static function init() 
    {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() 
    {
        define( 'MMFWC_VERSION', self::version );
        define( 'MMFWC_FILE', __FILE__ );
        define( 'MMFWC_PATH', __DIR__ );
        define( 'MMFWC_URL', plugins_url( '', MMFWC_FILE ) );
        define( 'MMFWC_ASSETS', MMFWC_URL . '/assets' );
        define( 'MMFWC_BASENAME', plugin_basename( __FILE__ ) );
        define( 'MMFWC_PLUGIN_NAME', 'Min and Max for WooCommerce' );
        define( 'MMFWC_MIN_WC_VERSION', '3.1' );
        define( 'MMFWC_MINIMUM_PHP_VERSION', '5.6.0' );
        define( 'MMFWC_MINIMUM_WP_VERSION', '4.4' );
        define( 'MMFWC_MINIMUM_WC_VERSION', '3.1' );
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() 
    {

        new MinMaxWoocommerce\MinMaxWoocommercei18n();

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            new MinMaxWoocommerce\Ajax();
        }

        if ( is_admin() ) {
            new MinMaxWoocommerce\Admin();
        } else {
            new MinMaxWoocommerce\Frontend();
        }

    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() 
    {
        $installer = new MinMaxWoocommerce\Installer();
        $installer->run();
    }
}

/**
 * Initializes the main plugin
 */
function min_max_for_woocommerce() {
    return MinMaxWoocommerce::init();
}

// kick-off the plugin
min_max_for_woocommerce();