<?php

namespace MinMaxWoocommerce\Frontend;
use MinMaxWoocommerce\Traits\Singleton;

class Storefront 
{

    use Singleton;
    /* Bootstraps the class and hooks required actions & filters.
    *
    * @since   2.0.0
    * @params 	none		
    * @return 	void
    */
    public function init() {
		
		add_filter( 'woocommerce_quantity_input_args',  [ $this, 'mmfwc_quantity_input_args' ], 10, 2 );
		add_action( 'woocommerce_add_to_cart',  [ $this, 'mmfwc_custom_add_to_cart' ], 10, 2 );
		add_filter( 'woocommerce_loop_add_to_cart_link',  [ $this, 'mmfwc_add_to_cart' ] );

    }

    /**
	 * 
	 * 
	 * @since   1.0.0
	 * @params 	array, object		
	 * @return 	void
	*/		
	public function mmfwc_quantity_input_args( $args, $product ) {
		
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

	/**
	 * 
	 * 
	 * @since   1.0.0
	 * @params 	array, object		
	 * @return 	void
	*/		
	public function mmfwc_custom_add_to_cart( $args, $product ) {

		$orderQTY   = sanitize_text_field( $_POST['quantity'] );
		$mmaxEnable = get_post_meta( $product, '_mmfwc_prd_opt_enable', true );
		$minQty     = get_post_meta( $product, '_mmfwc_min', true );
		$maxQty     = get_post_meta( $product, '_mmfwc_max', true );
		$cartQty 	= $this->mmfwc_woo_in_cart( $product ); 

		if( get_option('mmfwc_options_option_name') != NULL && get_option('mmfwc_options_option_name') !='' ) {
			$maxQTYMsg = get_option('mmfwc_options_option_name');
		} else {
			$maxQTYMsg = esc_html__( 'You have already added the maximum Quantity for the product for the current purchase', 'min-max-for-woocommerce' );
		}

		if( $maxQty < $cartQty && $mmaxEnable == 1 ) {
			wc_add_notice( $maxQTYMsg, 'error' );
			exit( wp_redirect( get_permalink($product) ) );
		}

		if( ($orderQTY + $cartQty)  < $minQty && $mmaxEnable == 1 ) {
			wc_add_notice( __( 'You have ordered '.$orderQTY.' which is less than the allowed Minimum Quantity '.$minQty.'', 'min-max-for-woocommerce' ), 'error' );
			exit( wp_redirect( get_permalink($product) ) );
		}

	}

	/**
	 * 
	 * 
	 * @since   1.0.0
	 * @params 	int		
	 * @return 	void
	*/	
	public function mmfwc_woo_in_cart( $product_id ) {

		global $woocommerce;
		foreach($woocommerce->cart->get_cart() as $key => $val ) {
		
			$_product = $val['data'];
			if( $product_id == $_product->get_id() ) {
				return  $val['quantity'];
			}
		}
	
		return 0;

	}

	/**
	 * 
	 * 
	 * @since   1.0.0
	 * @params 	string		
	 * @return 	void
	*/	
	public function mmfwc_add_to_cart( $link ) {
		
		global $product;
		$product_id 	= $product->id;
		$product_sku 	= $product->get_sku();
		$product_type 	= $product->get_type();
		$qtylink 		= ''; 
		$mmaxEnable 	= get_post_meta( $product_id, '_mmfwc_prd_opt_enable', true );
		$minQty     	= get_post_meta( $product_id, '_mmfwc_min', true );
		
		if( $product_type !='simple' && $mmaxEnable == 1 ) {
			$qtylink = '&quantity='.$minQty;
		} 

		$ajax_cart_en = 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' );
		
		$ajax_class = '';
		if ($ajax_cart_en &&  $mmaxEnable == 0) { 
			$ajax_class = 'ajax_add_to_cart'; 
		}
		
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