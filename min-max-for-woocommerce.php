<?php

/**
 *
 * @link              https://www.thenextwp.co
 * @since             1.0.0
 * @package           Min_Max_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Min and Max for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/min-max-for-woocommerce
 * Description:       Handle minimum and maximum quantity with the easiest way.
 * Version:           1.0.1
 * Author:            Mohiuddin Abdul Kader
 * Author URI:        https://profiles.wordpress.org/hossain88/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       min-max-for-woocommerce
 * Domain Path:       /languages
 * Requires PHP:      5.6
 * Requires at least: 4.4
 * Tested up to:      5.6
 *
 * WC requires at least: 3.1
 * WC tested up to:   4.9.1 
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'MMFWC_VERSION', '1.0.1' );
define( 'MMFWC_MINIMUM_PHP_VERSION', '5.6.0' );
define( 'MMFWC_MINIMUM_WP_VERSION', '4.4' );
define( 'MMFWC_MINIMUM_WC_VERSION', '3.0.9' );
define( 'MMFWC_URL', plugins_url( '/', __FILE__ ) );
define( 'MMFWC_ADMIN_URL', MMFWC_URL . 'admin/' );
define( 'MMFWC_PUBLIC_URL', MMFWC_URL . 'public/' );
define( 'MMFWC_FILE', __FILE__ );
define( 'MMFWC_ROOT_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'MMFWC_ADMIN_DIR_PATH', MMFWC_ROOT_DIR_PATH . 'admin/' );
define( 'MMFWC_PUBLIC_PATH', MMFWC_ROOT_DIR_PATH . 'public/' );
define( 'MMFWC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'MMFWC_PLUGIN_NAME', 'Min and Max for WooCommerce' );

/**
 * Min and Max for WooCommerce Start.
 *
 * @since 1.0.0
 */
class Min_Max_For_Woocommerce_Launch {	

	/** @var \Min_Max_For_Woocommerce_Launch single instance of this class */
	private static $instance;

	/** @var array the admin notices to add */
	private $notices = array();

	/**
	 * Loads Min and Max for WooCommerce Start.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {

		register_activation_hook( __FILE__, array( $this, 'mmfwc_activation_check' ) );

		// handle notices and activation errors
		add_action( 'admin_init',    array( $this, 'mmfwc_check_environment' ) );
		add_action( 'admin_init',    array( $this, 'mmfwc_add_plugin_notices' ) );
		add_action( 'admin_notices', array( $this, 'mmfwc_admin_notices' ), 15 );

		// if the environment check fails, initialize the plugin
		if ( $this->mmfwc_is_environment_compatible() ) {
			add_action( 'plugins_loaded', array( $this, 'mmfwc_init_plugin' ) );
		}
	}	

	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot clone instances of %s.', get_class( $this ) ), '1.0.0' );
	}

	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot unserialize instances of %s.', get_class( $this ) ), '1.0.0' );
	}

	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function mmfwc_init_plugin() {

		if ( ! $this->mmfwc_plugins_compatible() ) {
			return;
		}

		// load the main plugin class
		require_once( MMFWC_ROOT_DIR_PATH . 'includes/class-min-max-for-woocommerce.php' );

		$plugin = new Min_Max_For_Woocommerce();
		$plugin->run();
	}

	/**
	 * Checks the server environment and other factors and deactivates plugins as necessary.
	 *
	 * Based on http://wptavern.com/how-to-prevent-wordpress-plugins-from-activating-on-sites-with-incompatible-hosting-environments
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function mmfwc_activation_check() {

		if ( ! $this->mmfwc_is_environment_compatible() ) {

			$this->mmfwc_deactivate_plugin();

			wp_die( MMFWC_PLUGIN_NAME . ' could not be activated. ' . $this->mmfwc_get_environment_message() );
		
		} else {

			/**
			* Reqrite the rules on activation.
			*/
			flush_rewrite_rules();
			
		}
	}

	/**
	 * Checks the environment on loading WordPress, just in case the environment changes after activation.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function mmfwc_check_environment() {

		if ( ! $this->mmfwc_is_environment_compatible() && is_plugin_active( plugin_basename( __FILE__ ) ) ) {

			$this->mmfwc_deactivate_plugin();

			$this->mmfwc_add_admin_notice( 'bad_environment', 'error', MMFWC_PLUGIN_NAME . ' has been deactivated. ' . $this->mmfwc_get_environment_message() );
		}
	}

	/**
	 * Adds notices for out-of-date WordPress and/or WooCommerce versions.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function mmfwc_add_plugin_notices() {

		if ( ! $this->mmfwc_is_wp_compatible() ) {

			$this->mmfwc_add_admin_notice( 'update_wordpress', 'error', sprintf(
				'%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
				'<strong>' . MMFWC_PLUGIN_NAME . '</strong>',
				MMFWC_MINIMUM_WP_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
			) );
		}

		if ( ! $this->mmfwc_is_wc_compatible() ) {

			$this->mmfwc_add_admin_notice( 'update_woocommerce', 'error', sprintf(
				'%1$s requires WooCommerce version %2$s or higher. Please %3$supdate WooCommerce%4$s to the latest version, or %5$sdownload the minimum required version &raquo;%6$s',
				'<strong>' . MMFWC_PLUGIN_NAME . '</strong>',
				MMFWC_MINIMUM_WC_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>',
				'<a href="' . esc_url( 'https://downloads.wordpress.org/plugin/woocommerce.' . MMFWC_MINIMUM_WC_VERSION . '.zip' ) . '">', '</a>'
			) );
		}
	}

	/**
	 * Determines if the required plugins are compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function mmfwc_plugins_compatible() {

		return $this->mmfwc_is_wp_compatible() && $this->mmfwc_is_wc_compatible();
	}

	/**
	 * Determines if the WordPress compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function mmfwc_is_wp_compatible() {

		return version_compare( get_bloginfo( 'version' ), MMFWC_MINIMUM_WP_VERSION, '>=' );
	}

	/**
	 * Determines if the WooCommerce compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function mmfwc_is_wc_compatible() {

		return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, MMFWC_MINIMUM_WC_VERSION, '>=' );
	}

	/**
	 * Deactivates the plugin.
	 *
	 * @since 1.0.0
	 */
	private function mmfwc_deactivate_plugin() {

		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

	/**
	 * Adds an admin notice to be displayed.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug the slug for the notice
	 * @param string $class the css class for the notice
	 * @param string $message the notice message
	 */
	public function mmfwc_add_admin_notice( $slug, $class, $message ) {

		$this->notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message
		);
	}

	/**
	 * Displays admin notices.
	 *
	 * @since 1.0.0
	 */
	public function mmfwc_admin_notices() {

		foreach ( $this->notices as $notice_key => $notice ) :

			?>
			<div class="<?php echo esc_attr( $notice['class'] ); ?>">
				<p><?php echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
			</div>
			<?php

		endforeach;
	}

	/**
	 * Determines if the server environment is compatible with this plugin.
	 *
	 * Override this method to add checks for more than just the PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function mmfwc_is_environment_compatible() {

		return version_compare( PHP_VERSION, MMFWC_MINIMUM_PHP_VERSION, '>=' );
	}

	/**
	 * Gets the message for display when the environment is incompatible with this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function mmfwc_get_environment_message() {

		return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', MMFWC_MINIMUM_PHP_VERSION, PHP_VERSION );
	}

	/**
	 * Gets the main Measurement Price Calculator loader instance.
	 *
	 * Ensures only one instance can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return \Min_Max_For_Woocommerce_Launch
	 */
	public static function instance() {

		if ( null === self::$instance ) {

			self::$instance = new self();
		}

		return self::$instance;
	}


}

// fire it up!
Min_Max_For_Woocommerce_Launch::instance();
