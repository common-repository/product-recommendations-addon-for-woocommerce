<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use RexTheme\RexProductRecommendationsForWoocommerce\Abstracts\ProductsEngine;

class Products extends ProductsEngine {

	/**
	 * Constructor to initiate rendering of product recommendations.
	 * Invokes the method to render product recommendations when the class instance is created.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter(
            'woocommerce_shortcode_products_query_results',
            function ( $results ) {
				$this->product_ids = ! empty( $results->ids ) ? $results->ids : array();
				return array();
			}
        );

		add_action( 'wp_loaded', array( $this, 'add_multiple_products_to_cart' ) );

		$this->render_product_recommendations();
	}

	/**
	 * Renders different types of product recommendations based on configured engines.
	 * Retrieves configured product engines and attaches rendering actions based on engine type and visibility location.
	 *
	 * @since 1.0.0
	 */
	public function render_product_recommendations() {
		$engines_location = $this->get_engines_with_visibility_location();

		foreach ( $engines_location as $engine ) {
			if ( ! empty( $engine['location'] ) && ! empty( $engine['engine_id'] ) ) {
				$engine_id = $engine['engine_id'];
				$location  = @unserialize( $engine['location'] ); // phpcs:ignore
				if ( ! empty( $location['value'] ) ) {
					$primary_hook  = $location['value'];
					$hook_name     = $primary_hook;
					$template_name = rexprr_get_wc_template_name_by_hook_name( $hook_name );

					if ( ! empty( $template_name ) && rexprr_is_wc_using_block_template( $template_name ) ) {
						$hook_name = rexprr_get_wc_block_template_filter_hook( $hook_name );
						add_filter(
							$hook_name,
							function ( $html ) use ( $engine_id, $primary_hook ) {
								$engine         = $this->get_engine_data( $engine_id );
								$engine_type    = @unserialize( $engine['engine_type'] ); // phpcs:ignore
								$engine_type    = $engine_type['value'];
								$engine_title   = $engine['engine_title'] ?? '';
								$engine_filters = ! empty( $engine['filters'] ) && is_string( $engine['filters'] ) ? @unserialize( $engine['filters'] ) : array(); // phpcs:ignore
								$this->generate_where_query( $engine_filters );
								ob_start();
								$this->modify_queries();
								$this->$engine_type( $engine_title, 4, 4, 'alignwide', $engine_id, $primary_hook );
								$this->remove_query_modifications();
								$content = ob_get_contents();
								ob_end_clean();
								return "{$content} {$html}";
							}
						);
					} else {
						add_action(
							$hook_name,
							function () use ( $engine_id, $primary_hook ) {
								$engine         = $this->get_engine_data( $engine_id );
								$engine_type    = @unserialize( $engine['engine_type'] ); // phpcs:ignore
								$engine_type    = $engine_type['value'];
								$engine_title   = $engine['engine_title'] ?? '';
								$engine_filters = ! empty( $engine['filters'] ) && is_string( $engine['filters'] ) ? @unserialize( $engine['filters'] ) : array(); // phpcs:ignore
								$this->generate_where_query( $engine_filters );
								$this->modify_queries();
								$this->$engine_type( $engine_title, 4, 4, '', $engine_id, $primary_hook );
								$this->remove_query_modifications();
							},
                            2
						);
					}
				}
			}
		}
	}

	/**
	 * Renders frequently bought together products based on engine filters for a given product.
	 *
	 * @param string $engine_title   The title of the recommendation engine.
	 *
	 * @since 1.0.0
	 */
	public function frequently_bought_together( $engine_title, $limit = 4, $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		global $product;
		if ( ! empty( $product ) && method_exists( $product, 'get_id' ) && is_single( $product->get_id() ) ) {
			$this->render_frequently_bought_together_products( $engine_title, $product->get_id(), $limit, $columns, $blockified_class, $engine_id, $primary_location );
		}
	}

	/**
	 * Renders alternative out-of-stock products based on engine filters for a given product.
	 *
	 * @param string $engine_title   The title of the recommendation engine.
	 *
	 * @since 1.0.0
	 */
	public function out_of_stock_product_alternatives( $engine_title, $limit = 4, $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		global $product;
		if ( ! empty( $product ) && method_exists( $product, 'get_id' ) && is_single( $product->get_id() ) && 'instock' !== $product->get_stock_status() ) {
			$this->render_out_of_stock_alternative_products( $engine_title, $product->get_id(), $limit, $columns, $blockified_class, $engine_id, $primary_location );
		}
	}

	/**
	 * Retrieves and renders top-rated products based on specified engine filters.
	 *
	 * This function fetches the top-rated product IDs using provided filters and optionally considering the current product. It then renders these products based on the given recommendation engine title.
	 *
	 * @param string $engine_title   The title of the recommendation engine.
	 *
	 * @since 1.0.0
	 */
	public function top_rated_products( $engine_title, $limit = 4, $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		global $product;
		$product_id = ! empty( $product ) && method_exists( $product, 'get_id' ) ? $product->get_id() : null;
		$product_id = ! empty( $product_id ) && is_single( $product_id ) ? $product_id : null;
		$this->render_top_rated_products( $engine_title, $product_id, "limit='{$limit}'", $columns, $blockified_class, $engine_id, $primary_location );
	}

	/**
	 * Displays best-selling products based on the provided engine title.
	 *
	 * This function fetches the current product ID and ensures it's valid for a single product page. It then invokes the method to render best-selling products with a specified engine title and a limit of 4 products.
	 *
	 * @param string $engine_title The title of the recommendation engine.
	 *
	 * @since 1.0.0
	 */
	public function best_selling_products( $engine_title, $limit = 4, $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		global $product;
		$product_id = ! empty( $product ) && method_exists( $product, 'get_id' ) ? $product->get_id() : null;
		$product_id = ! empty( $product_id ) && is_single( $product_id ) ? $product_id : null;
		$this->render_best_selling_products( $engine_title, $product_id, "limit='{$limit}'", $columns, $blockified_class, $engine_id, $primary_location );
	}

	/**
	 * Renders a block displaying popular products on sale based on the provided engine title.
	 *
	 * This function generates a block to display popular products on sale by calling the internal method
	 * 'render_popular_on_sale_products'. It uses the provided engine title and the current product ID
	 * (if available and when viewing a single product) to render the block content limited to a maximum of 4 products.
	 *
	 * @param string $engine_title The title of the recommendation engine.
	 *
	 * @global \WC_Product|object $product Global variable representing the current product.
	 *
	 * @since 1.0.0
	 */
	public function popular_on_sale_products( $engine_title, $limit = 4, $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		global $product;
		$product_id = ! empty( $product ) && method_exists( $product, 'get_id' ) ? $product->get_id() : null;
		$product_id = ! empty( $product_id ) && is_single( $product_id ) ? $product_id : null;
		$this->render_popular_on_sale_products( $engine_title, $product_id, "limit='{$limit}'", $columns, $blockified_class, $engine_id, $primary_location );
	}

	/**
	 * Renders a block displaying new arrival products based on the provided engine title.
	 *
	 * This function generates a block to display new arrival products by calling the internal method
	 * 'render_new_arrival_products'. It uses the provided engine title and the current product ID
	 * (if available and when viewing a single product) to render the block content limited to a maximum of 4 products.
	 *
	 * @param string $engine_title The title of the recommendation engine.
	 *
	 * @global WP_Post|object $product Global variable representing the current product.
	 *
	 * @since 1.0.0
	 */
	public function new_arrival_products( $engine_title, $limit = 4, $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		global $product;
		$product_id = ! empty( $product ) && method_exists( $product, 'get_id' ) ? $product->get_id() : null;
		$product_id = ! empty( $product_id ) && is_single( $product_id ) ? $product_id : null;
		$this->render_new_arrival_products( $engine_title, $product_id, "limit='{$limit}'", $columns, $blockified_class, $engine_id, $primary_location );
	}

	/**
	 * Display recently viewed products.
	 *
	 * Renders the recently viewed products using the specified engine title and optional parameters.
	 *
	 * @param string $engine_title The title of the engine.
	 * @param int    $limit The limit of products to display. Default is 4.
	 * @param int    $columns The number of columns for product display. Default is 4.
	 * @param string $blockified_class Additional class for blockified display. Default is empty.
	 * @param mixed  $engine_id Optional engine ID.
	 * @param string $primary_location Primary location for product display. Default is empty.
	 * @return void
	 *
	 * @since 1.0.2
	 */
	public function recently_viewed_products( $engine_title, $limit = 4, $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		$this->render_recently_viewed_products( $engine_title, "limit='{$limit}'", $columns, $blockified_class, $engine_id, $primary_location );
	}

	/**
	 * Adds multiple products to the cart based on a recommendation token and nonce verification.
	 *
	 * This function processes the addition of multiple products to the WooCommerce cart based on a recommendation token.
	 * It verifies the provided nonce and action, decodes the token to extract product IDs, and adds each product to the cart.
	 * After adding products, it generates a success message and redirects users to the cart or the product page based on settings.
	 *
	 * @since 1.0.0
	 */
	public function add_multiple_products_to_cart() {
		$nonce_action = 'rexprr_product_frequently_bought_together';
		$nonce        = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$token        = filter_input( INPUT_GET, 'recommendation_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$product_ids  = rexprr_decode_data( $token );

		if ( wp_verify_nonce( $nonce, $nonce_action ) && ! empty( $token ) ) {
			foreach ( $product_ids as $product_id ) {
				$product      = wc_get_product( $product_id );
				$attr         = array();
				$variation_id = '';

				if ( $product->is_type( 'variation' ) ) {
					$attr         = $product->get_variation_attributes();
					$variation_id = $product->get_id();
					$product_id   = $product->get_parent_id();
				}

				$cart_item_key = WC()->cart->add_to_cart( $product_id, 1, $variation_id, $attr );
				if ( $cart_item_key ) {
					$products_added[ $cart_item_key ] = $variation_id ? $variation_id : $product_id;
					$message[ $product_id ]           = 1;
				}
			}

			if ( ! empty( $message ) ) {
				wc_add_to_cart_message( $message );
			}

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				$url = wc_get_cart_url();
			} else {
				// redirect to product page.
				$url = remove_query_arg( array( 'action', '_wpnonce', 'recommendation_token' ) );
			}

			wp_safe_redirect( esc_url( $url ) );
			exit;
		}
	}
}
