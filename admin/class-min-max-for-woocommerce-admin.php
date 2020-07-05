<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.thewpnext.com
 * @since      1.0.0
 *
 * @package    Min_Max_For_Woocommerce
 * @subpackage Min_Max_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Min_Max_For_Woocommerce
 * @subpackage Min_Max_For_Woocommerce/admin
 * @author     TheWPNext <thewpnext@gmail.com>
 */
class Min_Max_For_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name  The name of this plugin.
	 * @param    string    $version   The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function mmfwc_enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/min-max-for-woocommerce-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since   1.0.0
	 * @params 	array		
	 * @return 	void 
	*/
	public function mmfwc_enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/min-max-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 *
	 * 
	 * @since   1.0.0
	 * @params 	array		
	 * @return 	void
	*/
	public function mmfwc_meta_box_create() {
		add_meta_box('mmfwc_enable', __('Min Max Quantity', 'min-max-for-woocommerce'), [$this, 'mmfwc_meta_box'], 'product', 'side');
	}
	
	/**
	 *
	 * 
	 * @since   1.0.0
	 * @params 	object		
	 * @return 	void
	*/	
	public function mmfwc_meta_box( $post ) {

		$max = get_post_meta( $post->ID, '_mmfwc_max', true );
		$min = get_post_meta( $post->ID, '_mmfwc_min', true );

		wp_nonce_field('mmfwc_cst_prd_nonce', 'mmfwc_cst_prd_nonce');
		
		echo '<p>';
			echo '<label for="_mmfwc_prd_opt_enable" style="float:left; width:50px;">' . __('Enable', 'min-max-for-woocommerce') . '</label>';
			echo '<input type="hidden" name="_mmfwc_prd_opt_enable" value="0" />';
			echo '<input type="checkbox" id="_mmfwc_prd_opt_enable" class="checkbox" name="_mmfwc_prd_opt_enable" value="1" ' . checked(get_post_meta($post->ID, '_mmfwc_prd_opt_enable', true), 1, false) . ' />';
		echo '</p>';

		echo '<p>';
			echo '<label for="_mmfwc_min">' . __('Min Quantity', 'min-max-for-woocommerce') . '</label>';
			echo '<input type="number" id="_mmfwc_min" class="short" name="_mmfwc_min" value="' . $min . '" />';
		echo '</p>';

		echo '<p>';
			echo '<label for="_mmfwc_max">' . __('Max Quantity', 'min-max-for-woocommerce') . '</label>';
			echo '<input type="number" id="_mmfwc_max" class="short" name="_mmfwc_max" value="' . $max . '" />';
		echo '</p>';
		
	}

	/**
	 * 
	 * 
	 * @since   1.0.0
	 * @params 	array		
	 * @return 	void
	*/
	public function mmfwc_save_meta_box( $post_id ) {

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return;
		if ( ! isset( $_POST['_mmfwc_prd_opt_enable'] ) || ! wp_verify_nonce( sanitize_text_field($_POST['mmfwc_cst_prd_nonce']), 'mmfwc_cst_prd_nonce' ) )
			return;
		update_post_meta( $post_id, '_mmfwc_prd_opt_enable', (int) sanitize_text_field($_POST['_mmfwc_prd_opt_enable']) );
		update_post_meta( $post_id, '_mmfwc_max',(int) sanitize_text_field($_POST['_mmfwc_max']) );
		update_post_meta( $post_id, '_mmfwc_min', (int) sanitize_text_field($_POST['_mmfwc_min']) );

	}	

}
