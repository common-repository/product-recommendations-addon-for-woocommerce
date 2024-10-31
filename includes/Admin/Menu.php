<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Menu {

	/**
	 * Admin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'init_menu' ), 40 );
	}


	/**
	 * Init menu
	 *
	 * @since 1.0.0
	 */
	public function init_menu() {
		$capability = 'manage_options';

		if ( defined( 'WC_VERSION' ) && current_user_can( $capability ) ) {
			add_submenu_page(
				'woocommerce',
				esc_attr__( 'Product Recommendations', 'product-recommendations-addon-for-woocommerce' ),
				esc_attr__( 'Product Recommendations', 'product-recommendations-addon-for-woocommerce' ),
				$capability,
				REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_SLUG,
				array( $this, 'plugin_page' )
			);
		}
	}


	/**
	 * Render the plugin page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function plugin_page() {
		require_once REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_TEMPLATE_PATH . '/app.php';
	}
}
