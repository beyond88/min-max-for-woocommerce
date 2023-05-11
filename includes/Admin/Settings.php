<?php

namespace MinMaxWoocommerce\Admin;
use MinMaxWoocommerce\Traits\Singleton;

/**
* Initiate plugin action links
*
* @since    1.0.0
*/
class Settings 
{
    
    use Singleton;
    /* Bootstraps the class and hooks required actions & filters.
     *
	 * @since   2.0.0
	 * @params 	none		
	 * @return 	void
     */
    public function init() {   
		add_action( 'add_meta_boxes', [ $this, 'mmfwc_meta_box_create'] );
		add_action( 'save_post', [ $this, 'mmfwc_save_meta_box'] );
		add_action( 'admin_init', [ $this, 'mmfwc_options_register_settings'] );
		add_action( 'admin_menu', [ $this, 'mmfwc_register_options_page'] );
    }

    /**
	 * Add metabox in the edit product page
	 * 
	 * @since   1.0.0
	 * @params 	array		
	 * @return 	void
	*/
	public function mmfwc_meta_box_create() {
		add_meta_box('mmfwc_enable', __('Min Max Quantity', 'min-max-for-woocommerce'), [ $this, 'mmfwc_meta_box' ], 'product', 'side');
	}
	
	/**
	 * Metabox HTML
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
			echo '<label for="_mmfwc_prd_opt_enable" style="float:left; width:50px;">' . __('Enable ', 'min-max-for-woocommerce') . '</label>';
			echo '<input type="hidden" name="_mmfwc_prd_opt_enable" value="0" />';
			echo '<input type="checkbox" id="_mmfwc_prd_opt_enable" class="checkbox" name="_mmfwc_prd_opt_enable" value="1" ' . checked( get_post_meta( $post->ID, '_mmfwc_prd_opt_enable', true ), 1, false ) . ' />';
		echo '</p>';

		echo '<p>';
			echo '<label for="_mmfwc_min">' . __('Min Quantity ', 'min-max-for-woocommerce') . '</label>';
			echo '<input type="number" id="_mmfwc_min" class="short" name="_mmfwc_min" value="' . $min . '" min="1" step="1"/>';
		echo '</p>';

		echo '<p>';
			echo '<label for="_mmfwc_max">' . __('Max Quantity ', 'min-max-for-woocommerce') . '</label>';
			echo '<input type="number" id="_mmfwc_max" class="short" name="_mmfwc_max" value="' . $max . '" min="1" step="1"/>';
		echo '</p>';
		
	}

	/**
	 * Metabox data save
	 * 
	 * @since   1.0.0
	 * @params 	array		
	 * @return 	void
	*/
	public function mmfwc_save_meta_box( $post_id ) {

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return;
		if ( ! isset( $_POST['_mmfwc_prd_opt_enable'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['mmfwc_cst_prd_nonce'] ), 'mmfwc_cst_prd_nonce' ) )
			return;
		update_post_meta( $post_id, '_mmfwc_prd_opt_enable', (int) sanitize_text_field( $_POST['_mmfwc_prd_opt_enable'] ) );
		update_post_meta( $post_id, '_mmfwc_max',(int) sanitize_text_field( $_POST['_mmfwc_max'] ) );
		update_post_meta( $post_id, '_mmfwc_min', (int) sanitize_text_field( $_POST['_mmfwc_min'] ) );

	}
  
	/**
	 * Register option page in the setting menu
	 * 
	 * @since   1.0.0
	 * @params 	array		
	 * @return 	void
	*/
	public function mmfwc_options_register_settings() {
		add_option( 'mmfwc_options_option_name', 'Settings' );
		register_setting( 'mmfwc_options_group', 'mmfwc_options_option_name', 'mmfwc_options_callback' );
	}
 
	/**
	 * Add option page
	 * 
	 * @since   1.0.0
	 * @params 	array		
	 * @return 	void
	*/	
 	public function mmfwc_register_options_page() {
   		add_options_page( 'Min & Max Quantity Settings', 'Min & Max Quantity', 'manage_options', '_mmfwc_', [ $this, 'mmfwc_options_page' ] );
 	}
 
	/**
	 * Setting page HTML
	 * 
	 * @since   1.0.0
	 * @params 	array		
	 * @return 	void
	*/	 
	public function mmfwc_options_page() {
		?>
		<div>
			<h2>
				<?php esc_html_e( 'Min & Max Quantity Settings', 'min-max-for-woocommerce' ); ?>
			</h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'mmfwc_options_group' ); ?>				
				<table>
					<tr valign="top">
						<th scope="row">
							<label for="mmfwc_options_option_name">
								<?php esc_html_e( 'Message for maximum Quantity limit', 'min-max-for-woocommerce' ); ?>
							</label>
						</th>
					</tr>
					<tr>
						<td>
							<textarea cols="60" rows="5" id="mmfwc_options_option_name" name="mmfwc_options_option_name" >
								<?php echo get_option( 'mmfwc_options_option_name' ); ?>
							</textarea>
						</td>
					</tr>
				</table>
				<?php  submit_button(); ?>
			</form>
		</div>
		<?php
	}	

}