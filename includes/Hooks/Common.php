<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use RexTheme\RexProductRecommendationsForWoocommerce\Models\Contact;

/**
 * Class Common
 *
 * @package RexTheme\PluginName\Hooks
 *
 * @since 1.0.0
 */
class Common {

	/**
	 * Initializes the Common class.
	 * Sets up actions for license validation checks on WordPress initialization.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'remove_all_admin_notices' ), 99 );

		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_custom_metadata_to_cart_item' ), 10, 4 );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'save_custom_metadata_to_order_itemmeta' ), 10, 4 );

		add_action( 'template_redirect', array( $this, 'rexprr_track_recently_viewed_product' ) );
		add_action( 'woocommerce_cart_updated', array( $this, 'get_the_updated_cart_data' ) );

		add_action( 'wp_ajax_add_custom_metadata_to_cart_item', array( $this, 'ajax_add_custom_metadata_to_cart_item' ) );
		add_action( 'wp_ajax_nopriv_add_custom_metadata_to_cart_item', array( $this, 'ajax_add_custom_metadata_to_cart_item' ) );

		add_action( 'wp_ajax_rexprr_create_contact', array( $this, 'create_contact' ) );
		add_action( 'wp_ajax_nopriv_rexprr_create_contact', array( $this, 'create_contact' ) );
	}


	/**
	 * Ajax callback function to add custom metadata to cart item.
	 *
	 * @since 1.0.3
	 */
	public function ajax_add_custom_metadata_to_cart_item() {
		$engine_id       = filter_input( INPUT_POST, 'engine_id', FILTER_SANITIZE_NUMBER_INT );
		$engine_position = filter_input( INPUT_POST, 'engine_position', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$product_id      = filter_input( INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT );
		$security        = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( ! wp_verify_nonce( $security, 'rexPrRecommendationPublicSecurity' ) ) {
			return;
		}

		$cart = WC()->cart->get_cart();
		if ( is_array( $cart ) && ! empty( $cart ) ) {
			foreach ( $cart as $cart_item_key => $cart_item ) {
				if ( isset( $cart_item['product_id'] ) && $cart_item['product_id'] === $product_id && ! isset( $cart_item['rexprr_item_data'] ) ) {
					WC()->cart->cart_contents[ $cart_item_key ]['rexprr_item_data'] = array(
						'quantity'               => $cart_item['quantity'],
						'price_total'            => wc_get_product( $cart_item['product_id'] )->get_price() * $cart_item['quantity'],
						'product_id'             => $cart_item['product_id'],
						'variation_id'           => $cart_item['variation_id'],
						'rexprr_engine_id'       => $engine_id,
						'rexprr_engine_position' => htmlspecialchars( $engine_position ),
					);
				}
			}
		}

		WC()->cart->set_session();
		WC()->cart->calculate_totals();
		wp_send_json_success( 'Metadata added successfully' );
	}

	/**
	 * Retrieves and updates the cart data.
	 *
	 * This function updates the quantity and total price for items in the cart.
	 * It checks if there are any changes in quantity and updates the cart item data accordingly.
	 *
	 * @since 1.0.3
	 */
	public function get_the_updated_cart_data() {
		$cart = WC()->cart->get_cart();
		if ( is_array( $cart ) && ! empty( $cart ) ) {
			foreach ( $cart as $cart_item_key => $cart_item ) {
				if ( isset( $cart_item['rexprr_item_data']['quantity'] ) && $cart_item['quantity'] !== $cart_item['rexprr_item_data']['quantity'] ) {
					WC()->cart->cart_contents[ $cart_item_key ]['rexprr_item_data']['quantity']    = $cart_item['quantity'];
					WC()->cart->cart_contents[ $cart_item_key ]['rexprr_item_data']['price_total'] = wc_get_product( $cart_item['product_id'] )->get_price() * $cart_item['quantity'];
				}
			}
		}
	}

	/**
	 * Track the recently viewed product.
	 *
	 * This function sets a cookie to track the products recently viewed by the user.
	 * The cookie stores the IDs of the viewed products.
	 *
	 * @since 1.0.2
	 */
	public function rexprr_track_recently_viewed_product() {
		if ( ! is_singular( 'product' ) ) {
			return;
        }
		global $post;
		if ( empty( $_COOKIE['rexprr_track_recently_viewed_product'] ) ) {
			$viewed_products = array();
		} else {
			$viewed_products = wp_parse_id_list( (array) explode( '|', sanitize_text_field( wp_unslash( $_COOKIE['rexprr_track_recently_viewed_product'] ) ) ) );
		}
		$keys = array_flip( $viewed_products );
		if ( isset( $keys[ $post->ID ] ) ) {
			unset( $viewed_products[ $keys[ $post->ID ] ] );
		}
		$viewed_products[] = $post->ID;

		// Set expiration time for the cookie (6 hours).
		$expiration_time = time() + ( 6 * 60 * 60 );

		wc_setcookie( 'rexprr_track_recently_viewed_product', implode( '|', $viewed_products ), $expiration_time );
	}

	/**
	 * Saves custom metadata to the order item meta when processing the order.
	 *
	 * @param \WC_Order_Item $item          The order item object.
	 * @param string         $cart_item_key The key used to identify the cart item.
	 * @param array          $values        An array of cart item values.
	 *
	 * @since 1.0.0
	 */
	public function save_custom_metadata_to_order_itemmeta( $item, $cart_item_key, $values, $order ) { // phpcs:ignore
		if ( ! empty( $values['rexprr_item_data'] ) ) {
			$item->add_meta_data( 'rexprr_item_data', $values['rexprr_item_data'] );
		}
	}

	/**
	 * Adds custom metadata to the cart item when a product is added to the cart.
	 *
	 * @param array $cart_item_data An array of cart item data.
	 * @param int   $product_id     The ID of the product.
	 * @param int   $variation_id   The ID of the product variation.
	 * @param int   $quantity       The quantity of the product.
	 *
	 * @return array The modified cart item data.
	 *
	 * @since 1.0.0
	 */
	public function add_custom_metadata_to_cart_item( $cart_item_data, $product_id, $variation_id, $quantity ) {
		$engine_id       = filter_input( INPUT_GET, 'rexprr_engine_id', FILTER_SANITIZE_NUMBER_INT );
		$engine_position = filter_input( INPUT_GET, 'rexprr_engine_position', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! empty( $engine_id ) && ! empty( $engine_position ) ) {
			// Check if the product already exists in the cart
			$cart_item_data['rexprr_item_data'] = array(
				'product_id'             => $product_id,
				'variation_id'           => $variation_id,
				'quantity'               => $quantity,
				'price_total'            => wc_get_product( $product_id )->get_price() * $quantity,
				'rexprr_engine_id'       => $engine_id,
				'rexprr_engine_position' => htmlspecialchars( $engine_position ),
			);
		}
		return $cart_item_data;
	}


	/**
	 * Removes all admin notices if the current page is one of the Rex Product Recommendations pages.
	 *
	 * @since 1.0.0
	 */
	public function remove_all_admin_notices() {
		if ( REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_SLUG === wp_unslash( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'network_admin_notices' );
			remove_all_actions( 'all_admin_notices' ); // For older versions of WordPress
		}
	}

	/**
	 * Retrieve general settings related to the product recommendation plugin.
	 *
	 * @return array An associative array containing general settings.
	 *               - 'log_enabled' (string): The status of log saving (default: 'no').
	 *
	 * @since 1.0.0
	 */
	public function get_general_settings() {
		return array(
			'log_enabled' => get_option( 'rexprr_save_log', 'no' ),
		);
	}

	/**
	 * Handles the creation of a contact via an AJAX request.
	 *
	 * This function verifies the nonce for security, sanitizes and validates the email and name fields,
	 * and creates a new contact using the provided information. If the nonce is invalid or the email is missing,
	 * it returns an error response.
	 *
	 * @since 1.0.4
	 */
	public function create_contact() {
		$nonce = filter_input( INPUT_POST, 'security', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! wp_verify_nonce( $nonce, 'rexPrRecommendationAdminSecurity' ) ) {
			wp_send_json_error( [ 'message' => 'Invalid nonce' ], 400 );
		}

		$email = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
		$name  = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! $email ) {
			return new \WP_Error( 'no_email', 'Email is required', [ 'status' => 400 ] );
		}

		$contact = new Contact( $email, $name );

		$data = $contact->create_contact_via_webhook();

		return rest_ensure_response( $data );
	}
}
