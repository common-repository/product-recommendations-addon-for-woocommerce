<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'rexprr_is_wc_using_block_template' ) ) {
	/**
	 * Is using the block template in a specific template page?
	 * Requires WooCommerce 7.9 or greater and WordPress 5.9 or greater.
	 *
	 * @param string $template_name The template to check.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	function rexprr_is_wc_using_block_template( $template_name ): bool {
		static $use_blocks = array();
		if ( ! isset( $use_blocks[ $template_name ] ) ) {
			// The blockified templates are available by default since WooCommerce 7.9.
			$use_blocks[ $template_name ] = function_exists( 'WC' ) && version_compare( WC()->version, '7.9.0', '>=' );

			/**
			 * WooCommerce 7.9 includes blockified templates for the following templates,
			 * so, if the template retrieved by the query is not found, and it's in this list,
			 * we can assume it's blockified.
			 */
			$blokified_templates = array( 'archive-product', 'product-search-results', 'single-product', 'taxonomy-product_attribute', 'taxonomy-product_cat', 'taxonomy-product_tag' );

			$use_blocks[ $template_name ] = $use_blocks[ $template_name ] && function_exists( 'wp_is_block_theme' ) && wp_is_block_theme();

			if ( $use_blocks[ $template_name ] ) {
				$templates = get_block_templates( array( 'slug__in' => array( $template_name ) ) );

				$is_block_template = function ( $content ) use ( $template_name ) {
					switch ( $template_name ) {
						case 'cart':
							return has_block( 'woocommerce/cart', $content );
						case 'checkout':
							return has_block( 'woocommerce/checkout', $content );
						default:
							return ! has_block( 'woocommerce/legacy-template', $content );
					}
				};

				if ( ! empty( $templates[0]->content ) ) {
					$content = $templates[0]->content;
					if ( ! $is_block_template( $content ) ) {
						$use_blocks[ $template_name ] = false;
					} elseif ( has_block( 'core/pattern', $content ) ) {
						// Search also in patterns (only one depth).
						$blocks = parse_blocks( $content );
						foreach ( $blocks as $block ) {
							$name = $block['blockName'];
							if ( 'core/pattern' === $name ) {
								$registry = WP_Block_Patterns_Registry::get_instance();
								$slug     = $block['attrs']['slug'] ?? '';

								if ( $registry->is_registered( $slug ) ) {
									$pattern = $registry->get_registered( $slug );
									if ( ! $is_block_template( $pattern['content'] ) ) {
										$use_blocks[ $template_name ] = false;
										break;
									}
								}
							}
						}
					}
				} elseif ( ! in_array( $template_name, $blokified_templates, true ) ) {
					$use_blocks[ $template_name ] = false;
				}
			}
		}

		return $use_blocks[ $template_name ];
	}
}

if ( ! function_exists( 'rexprr_get_wc_template_name_by_hook_name' ) ) {
	/**
	 * Retrieves WooCommerce template name based on the provided hook name.
	 *
	 * This function maps specific WooCommerce template hooks to corresponding template names.
	 *
	 * @param string $hook_name The name of the WooCommerce template hook.
	 *
	 * @return string The corresponding template name or the original hook name if no match is found.
	 *
	 * @since 1.0.0
	 */
	function rexprr_get_wc_template_name_by_hook_name( $hook_name ) {
		$template_hooks = array(
			'single-product'  => array(
				'woocommerce_before_single_product_summary',
				'woocommerce_after_single_product_summary',
			),
			'archive-product' => array(),
			'cart-product'    => array(),
			'checkout'        => array(),
		);

		foreach ( $template_hooks as $template_name => $hooks ) {
			if ( in_array( $hook_name, $hooks, true ) ) {
				return $template_name;
			}
		}
		return $hook_name;
	}
}

if ( ! function_exists( 'rexprr_get_wc_block_template_filter_hook' ) ) {
	/**
	 * Retrieves WooCommerce block template filter hook based on the provided hook name.
	 *
	 * This function maps specific WooCommerce template hooks to corresponding block template names.
	 *
	 * @param string $hook_name The name of the WooCommerce template hook.
	 *
	 * @return string The corresponding block template name or the original hook name if no match is found.
	 *
	 * @since 1.0.0
	 */
	function rexprr_get_wc_block_template_filter_hook( $hook_name ) {
		$template_hooks = array(
			'woocommerce_before_single_product_summary' => 'render_block_woocommerce/product-details',
			'woocommerce_after_single_product_summary'  => 'render_block_woocommerce/product-details',
		);

		return ! empty( $template_hooks[ $hook_name ] ) ? $template_hooks[ $hook_name ] : $hook_name;
	}
}

if ( ! function_exists( 'rexprr_encode_data' ) ) {
	/**
	 * Encodes data by URL encoding, base64 encoding, and building a query string.
	 *
	 * This function encodes provided data by first building a query string from the array,
	 * then base64 encoding it, and finally URL encoding the result. It returns the encoded string.
	 *
	 * @param array $data (Optional) The data to be encoded. Default is an empty array.
	 *
	 * @return string The encoded data string or an empty string if the input data is not a non-empty array.
	 *
	 * @since 1.0.0
	 */
	function rexprr_encode_data( $data = array() ) {
		if ( is_array( $data ) && ! empty( $data ) ) {
			return urlencode( base64_encode( http_build_query( $data ) ) ); // phpcs:ignore
		}
		return '';
	}
}

if ( ! function_exists( 'rexprr_decode_data' ) ) {
	/**
	 * Decodes encoded data by reversing URL decoding, base64 decoding, and parsing the query string.
	 *
	 * This function decodes the provided encoded data by first reversing the URL encoding,
	 * then base64 decoding it, and finally parsing it into an array using WordPress' wp_parse_str function.
	 * It returns the decoded array or an empty array if the input data is not a non-empty string.
	 *
	 * @param string $data (Optional) The encoded data string to be decoded. Default is an empty string.
	 *
	 * @return array The decoded data array or an empty array if the input data is not a non-empty string.
	 *
	 * @since 1.0.0
	 */
	function rexprr_decode_data( $data = '' ) {
		if ( is_string( $data ) && ! empty( $data ) ) {
			$decoded_data = array();
			wp_parse_str( base64_decode( urldecode( $data ) ), $decoded_data ); // phpcs:ignore
			return $decoded_data;
		}
		return array();
	}
}

if ( ! function_exists( 'rexprr_logged_in_user_information' ) ) {
	/**
	 * Retrieve information about the currently logged-in user.
	 *
	 * This method fetches the current WordPress user and returns an array containing
	 * the user's email and display name.
	 *
	 * @since 1.0.4
	 * @return array An array containing the user's email and display name.
	 */
	function rexprr_logged_in_user_information() {
		$admin_user = wp_get_current_user();
		return array(
			'email' => ! empty( $admin_user->user_email ) ? $admin_user->user_email : '',
			'name'  => ! empty( $admin_user->display_name ) ? $admin_user->display_name : '',
		);
	}
}
