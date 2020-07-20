<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.thewpnext.com
 * @since      1.0.0
 *
 * @package    Min_Max_For_Woocommerce
 * @subpackage Min_Max_For_Woocommerce/includes
 * @author     Mohiuddin Abdul Kader <muhin.cse.diu@gmail.com>
 */
class Min_Max_For_Woocommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Min_Max_For_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MMFWC_VERSION' ) ) {
			$this->version = MMFWC_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'min-max-for-woocommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Min_Max_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Min_Max_For_Woocommerce_i18n. Defines internationalization functionality.
	 * - Min_Max_For_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Min_Max_For_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-min-max-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-min-max-for-woocommerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-min-max-for-woocommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-min-max-for-woocommerce-public.php';

		$this->loader = new Min_Max_For_Woocommerce_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Min_Max_For_Woocommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Min_Max_For_Woocommerce_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Min_Max_For_Woocommerce_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'mmfwc_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'mmfwc_enqueue_scripts' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'mmfwc_meta_box_create' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'mmfwc_save_meta_box' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'mmfwc_options_register_settings' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'mmfwc_register_options_page' );
		$this->loader->add_filter( 'woocommerce_inventory_settings', $plugin_admin, 'mmfwc_default_quantity_settings' );
		$this->loader->add_action( 'product_cat_add_form_fields',  $plugin_admin, 'mmfwc_taxonomy_add_new_meta_field', 10, 2 );
		$this->loader->add_action( 'product_cat_edit_form_fields', $plugin_admin, 'mmfwc_taxonomy_edit_meta_field', 10, 2 );
		$this->loader->add_action( 'edited_product_cat', $plugin_admin, 'mmfwc_save_taxonomy_custom_meta', 10, 2 );
		$this->loader->add_action( 'create_product_cat', $plugin_admin, 'mmfwc_save_taxonomy_custom_meta', 10, 2 );			

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Min_Max_For_Woocommerce_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'mmfwc_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'mmfwc_enqueue_scripts' );
		$this->loader->add_filter( 'woocommerce_quantity_input_args',  $plugin_public, 'mmfwc_quantity_input_args', 10, 2 );
		$this->loader->add_action( 'woocommerce_add_to_cart',  $plugin_public, 'mmfwc_custom_add_to_cart', 10, 2 );
		$this->loader->add_filter( 'woocommerce_loop_add_to_cart_link',  $plugin_public, 'mmfwc_add_to_cart' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Min_Max_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
