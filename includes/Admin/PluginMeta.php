<?php

namespace MinMaxWoocommerce\Admin;

/**
* Initiate plugin action links
*
* @since    1.0.0
*/
class PluginMeta {

    public function __construct() {
        add_filter( 'plugin_action_links_' . MMFWC_BASENAME, [ $this, 'plugin_action_links' ] );
        add_filter( 'plugin_row_meta', [ $this, 'plugin_meta_links' ], 10, 2 );
    }

    /**
    * Create plugin action links
    *
    * @since    1.0.0
    * @param    array
    * @return   array
    */
    public function plugin_action_links( $links ) {

		$links[] = '<a href="https://github.com/beyond88/min-max-for-woocommerce/wiki">' . __( 'Docs', 'min-max-for-woocommerce' ) . '</a>';
        return $links;

    }

    /**
    * Create plugin meta links
    *
    * @since    1.0.0
    * @param    array string
    * @return   array
    */
    public function plugin_meta_links( $links, $file ) {
        
        if ( $file !== plugin_basename( MMFWC_FILE ) ) {
			return $links;
		}

		$support_link = '<a target="_blank" href="https://github.com/beyond88/min-max-for-woocommerce/issues" title="' . __('Get help', 'min-max-for-woocommerce') . '">' . __('Support', 'min-max-for-woocommerce') . '</a>';
		$home_link = '<a target="_blank" href="https://github.com/beyond88/min-max-for-woocommerce" title="' . __('Plugin Homepage', 'min-max-for-woocommerce') . '">' . __('Plugin Homepage', 'min-max-for-woocommerce') . '</a>';
		$rate_link = '<a target="_blank" href="https://wordpress.org/support/plugin/min-and-max-for-woocommerce/reviews/#new-post" title="' . __('Rate the plugin', 'min-max-for-woocommerce') . '">' . __('Rate the plugin ★★★★★', 'min-max-for-woocommerce') . '</a>';

		$links[] = $support_link;
		$links[] = $home_link;
		$links[] = $rate_link;

		return $links;

    }
}