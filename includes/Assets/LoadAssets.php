<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\Assets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Load assets class
 *
 * Responsible for managing all the assets (CSS, JS, Images, Locales).
 */
class LoadAssets {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_all_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
	}

	/**
	 * Register all scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_all_scripts() {
		$this->register_styles( $this->get_styles() );
		$this->register_scripts( $this->get_scripts() );
	}

	/**
	 * Get all styles.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_styles(): array {
		return [
			'rexprr-for-woocommerce-css' => [
				'src'     => REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_BUILD . '/index.css',
				'version' => REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_VERSION,
				'deps'    => [],
			],
			'rexprr-for-woocommerce-frontend' => [
				'src'     => REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_ASSETS . '/css/style.css',
				'version' => REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_VERSION,
				'deps'    => [],
			],
		];
	}

	/**
	 * Get all scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_scripts(): array {
		$dependency = file_exists( REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_DIR . '/build/index.asset.php' ) ? require_once REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_DIR . '/build/index.asset.php' : [];

		return [
			'rexprr-for-woocommerce-app' => [
				'src'       => REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_BUILD . '/index.js',
				'version'   => $dependency['version'] ?? null,
				'deps'      => $dependency['dependencies'] ?? [],
				'in_footer' => true,
			],
		];
	}

	/**
	 * Register styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_styles( array $styles ) {
		foreach ( $styles as $handle => $style ) {
			wp_register_style( $handle, $style['src'], $style['deps'], $style['version'] );
		}
	}

	/**
	 * Register scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_scripts( array $scripts ) {
		foreach ( $scripts as $handle =>$script ) {
			wp_register_script( $handle, $script['src'], $script['deps'], $script['version'], $script['in_footer'] );
		}
	}

	/**
	 * Enqueue admin styles and scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_admin_assets() {
		if ( ! is_admin() || REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_SLUG !== wp_unslash( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		wp_enqueue_style( 'rexprr-for-woocommerce-css' );
		wp_enqueue_script( 'rexprr-for-woocommerce-app' );

		wp_localize_script(
			'rexprr-for-woocommerce-app',
			'rexPrRecommendationAdmin',
			[
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'adminUrl'   => admin_url( 'admin.php' ),
				'pluginsUrl' => admin_url( 'plugins.php' ),
				'ajaxNonce'  => wp_create_nonce( 'rexPrRecommendationAdminSecurity' ),
				'isWcActive' => defined( 'WC_VERSION' ),
				'assetsPath' => REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_ASSETS,
				'user_information' => rexprr_logged_in_user_information(),
				'createNewEngineURL' => admin_url( 'admin.php?page=rexprr-product-recommendations#/create-new'),
			]
		);
	}

	/**
	 * Enqueue front-end styles and scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_frontend_assets() {
		if ( is_admin() || isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if( !is_shop() && !is_category() && !is_account_page() && !is_home()){
			wp_enqueue_script( 'rexprr-for-woocommerce-public', REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_ASSETS . '/public/Js/public.js', array( 'jquery' ), REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_VERSION, false  );
			wp_localize_script(
				'rexprr-for-woocommerce-public',
				'rexPrRecommendationFrontend',
				[
					'ajaxAdminUrl'    => admin_url( 'admin-ajax.php' ),
					'ajaxPublicNonce'  => wp_create_nonce( 'rexPrRecommendationPublicSecurity' ),
				]
			);
		}

		wp_enqueue_style( 'rexprr-for-woocommerce-frontend' );
	}
}
