<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.thewpnext.com
 * @since      1.0.0
 *
 * @package    Min_Max_For_Woocommerce
 * @subpackage Min_Max_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Min_Max_For_Woocommerce
 * @subpackage Min_Max_For_Woocommerce/public
 * @author     TheWPNext <thewpnext@gmail.com>
 */
class Min_Max_For_Woocommerce_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/min-max-for-woocommerce-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/min-max-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );
	}


	public function wc_mmax_quantity_input_args( $args, $product ) {
		
		if( function_exists('icl_object_id') ) {
			$default_language = wpml_get_default_language();
			$prod_id = icl_object_id( $product->get_id(), 'product', true, $default_language );
		} else {
			$prod_id = $product->get_id();
		}

		$mmaxEnable = get_post_meta( $prod_id, '_mmfwc_prd_opt_enable', true );
		$minQty     = get_post_meta( $prod_id, '_mmfwc_min', true );
		$maxQty     = get_post_meta( $prod_id, '_mmfwc_max', true );
		
		if ( $minQty > 0 && $maxQty > 0 && $mmaxEnable == 1 ) {
			$args['min_value'] = $minQty; // Starting value
			$args['max_value'] = $maxQty; // Ending value
		}

		return $args;
	}

	public function wc_mmax_custom_add_to_cart( $args,$product ) {

		$orderQTY = $_POST['quantity'];
		$mmaxEnable = get_post_meta($product, '_mmfwc_prd_opt_enable', true);
		$minQty     = get_post_meta($product, '_mmfwc_min', true);
		$maxQty     = get_post_meta($product, '_mmfwc_max', true);
		$cartQty =  wc_mmax_woo_in_cart($product);
		if(get_option('_wcmmax_options_option_name') != NULL && get_option('_wcmmax_options_option_name') !=''){
			$maxQTYMsg = get_option('_wcmmax_options_option_name');
		}else {
			$maxQTYMsg = 'You have already added the maximum Quantity for the product for the current purchase';
		}

		if( $maxQty < $cartQty && $mmaxEnable == 1 ) {
			wc_add_notice( $maxQTYMsg, 'error' );
			exit( wp_redirect( get_permalink($product) ) );
		}

		if( ($orderQTY + $cartQty)  < $minQty && $mmaxEnable == 1 ) {
			wc_add_notice(__( 'You have ordered '.$orderQTY.'  which is less than the allowed Minimum Quantity '.$minQty.'','min-max-for-woocommerce'),'error' );
			exit( wp_redirect( get_permalink($product) ) );
		}

	}

	public function wc_mmax_woo_in_cart( $product_id ) {
		global $woocommerce;
		foreach($woocommerce->cart->get_cart() as $key => $val ) {
		
			$_product = $val['data'];
			if($product_id == $_product->get_id()) {

			
			return  $val['quantity'];
		
			}
		}
	
		return 0;
	}

	public function _wcmmax_add2cart( $link ) {
		global $product;
		$product_id = $product->id;
		$product_sku = $product->get_sku();
		$product_type = $product->get_type();
		$qtylink = ''; 
			$mmaxEnable = get_post_meta($product_id, '_wc_mmax_prd_opt_enable', true);
			$minQty     = get_post_meta($product_id, '_wc_mmax_min', true);
		
		if($product_type !='simple' && $mmaxEnable == 1){
			$qtylink = '&quantity='.$minQty;
		} 
		$ajax_cart_en = 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' );
			if ($ajax_cart_en &&  $mmaxEnable == 0) { $ajax_class = 'ajax_add_to_cart'; }
		$link = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s %s">%s</a>',
				esc_url( $product->add_to_cart_url().$qtylink ),
				esc_attr( $product->id ),
				esc_attr( $product->get_sku() ),
				esc_attr( isset( $minQty ) ? $minQty : 1 ),
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				esc_attr( $product->product_type ),
				esc_attr( $ajax_class ),
				esc_html( $product->add_to_cart_text() )
			);
		return $link;
	}	

}
