<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.thewpnext.com
 * @since      1.0.0
 * @package    Min_Max_For_Woocommerce
 * @subpackage Min_Max_For_Woocommerce/admin
 * @author     Mohiuddin Abdul Kader <muhin.cse.diu@gmail.com>
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
	 * Register the min max quantity settings for the products.
	 *
	 * @since   1.0.0
	 * @params 	array		
	 * @return 	void 
	*/	
	public function mmfwc_default_quantity_settings( $settings ) {

		$new_settings = array();
		foreach ( $settings as &$setting ) {

			$new_settings[] = $setting;
			if ( 'woocommerce_manage_stock' === $setting['id'] ) {
				$new_settings[] = array(
					'title'             => __( 'Global minimum quantity', 'min-max-for-woocommerce' ),
					'desc'              => __( 'Choose a minimum quantity for all your products. You can override this for individual categories/products', 'min-max-for-woocommerce' ),
					'id'                => 'woocommerce_min_quantity',
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => 0,
						'step' => 1,
					),
					'css'               => 'width: 80px;',
					'default'           => '1',
					'autoload'          => false,
					'class'             => 'manage_stock_field',
				); 
				$new_settings[] = array(
					'title'             => __( 'Global maximum quantity', 'min-max-for-woocommerce' ),
					'desc'              => __( 'Choose a maximum quantity for all your products. You can override this for individual categories/products', 'min-max-for-woocommerce' ),
					'id'                => 'woocommerce_max_quantity',
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => 0,
						'step' => 1,
					),
					'css'               => 'width: 80px;',
					'default'           => '1',
					'autoload'          => false,
					'class'             => 'manage_stock_field',
				); 				
			}

		}

		return $new_settings; 	
	}

	/**
	 * Add product category meta.
	 *
	 * @since   1.0.0
	 * @params 	none		
	 * @return 	void 
	*/	
	public function mmfwc_taxonomy_add_new_meta_field() {

		?>

		<div class="form-field">
			<label for="term_meta[mmfwc_min_quantity]">
				<?php _e('Minimum quantity', 'min-max-for-woocommerce'); ?>
			</label>
			<input type="number" name="term_meta[mmfwc_min_quantity]" id="term_meta[mmfwc_min_quantity]" min="0" step="1">
			<p class="description">
				<?php _e('Enter minimum quantity', 'min-max-for-woocommerce'); ?>
			</p>
		</div>

		<div class="form-field">
			<label for="term_meta[mmfwc_max_quantity]">
				<?php _e('Minimum quantity', 'min-max-for-woocommerce'); ?>
			</label>
			<input type="number" name="term_meta[mmfwc_max_quantity]" id="term_meta[mmfwc_max_quantity]" min="0" step="1">
			<p class="description">
				<?php _e('Enter maximum quantity', 'min-max-for-woocommerce'); ?>
			</p>
		</div>		

		<?php

	}

	/**
	 * Edit product category meta.
	 *
	 * @since   1.0.0
	 * @params 	object		
	 * @return 	void 
	*/
	public function mmfwc_taxonomy_edit_meta_field( $term ) {

		$term_id 	= $term->term_id;
		$term_meta  = get_option( "taxonomy_" . $term_id );
		?>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="term_meta[mmfwc_min_quantity]">
					<?php _e('Minimum quantity', 'min-max-for-woocommerce'); ?>
				</label>
			</th>
			<td>
				<input type="number" name="term_meta[mmfwc_min_quantity]" id="term_meta[mmfwc_min_quantity]" min="0" step="1" value="<?php echo esc_attr($term_meta['mmfwc_min_quantity']) ? esc_attr($term_meta['mmfwc_min_quantity']) : ''; ?>">
				<p class="description">
					<?php _e('Enter default quantity', 'min-max-for-woocommerce'); ?>
				</p>
			</td>			
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="term_meta[mmfwc_max_quantity]">
					<?php _e('Maximum quantity', 'min-max-for-woocommerce'); ?>
				</label>
			</th>
			<td>
				<input type="number" name="term_meta[mmfwc_max_quantity]" id="term_meta[mmfwc_max_quantity]" min="0" step="1" value="<?php echo esc_attr($term_meta['mmfwc_max_quantity']) ? esc_attr($term_meta['mmfwc_max_quantity']) : ''; ?>">
				<p class="description">
					<?php _e('Enter maximum quantity', 'min-max-for-woocommerce'); ?>
				</p>
			</td>			
		</tr>		

		<?php
	}

	/**
	 * Save product category meta value.
	 *
	 * @since   1.0.0
	 * @params 	int, POST		
	 * @return 	void 
	*/
	public function mmfwc_save_taxonomy_custom_meta( $term_id ) {
		
		if ( isset($_POST['term_meta']) ) {
			
			$term_meta = get_option("taxonomy_" . $term_id);
			$cat_keys = array_keys($_POST['term_meta']);
			
			foreach ($cat_keys as $key) {
				if ( isset( $_POST['term_meta'][$key] ) ) {
					$term_meta[$key] = sanitize_text_field( $_POST['term_meta'][$key] );
				}
			}
			
			update_option("taxonomy_" . $term_id, $term_meta);
		}
		
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
			echo '<label for="_mmfwc_prd_opt_enable" style="float:left; width:50px;">' . __('Enable', 'min-max-for-woocommerce') . '</label>';
			echo '<input type="hidden" name="_mmfwc_prd_opt_enable" value="0" />';
			echo '<input type="checkbox" id="_mmfwc_prd_opt_enable" class="checkbox" name="_mmfwc_prd_opt_enable" value="1" ' . checked( get_post_meta( $post->ID, '_mmfwc_prd_opt_enable', true ), 1, false ) . ' />';
		echo '</p>';

		echo '<p>';
			echo '<label for="_mmfwc_min">' . __('Min Quantity', 'min-max-for-woocommerce') . '</label>';
			echo '<input type="number" id="_mmfwc_min" class="short" min="0" step="1" name="_mmfwc_min" value="' . $min . '" />';
		echo '</p>';

		echo '<p>';
			echo '<label for="_mmfwc_max">' . __('Max Quantity', 'min-max-for-woocommerce') . '</label>';
			echo '<input type="number" id="_mmfwc_max" class="short" step="1" name="_mmfwc_max" value="' . $max . '" />';
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
