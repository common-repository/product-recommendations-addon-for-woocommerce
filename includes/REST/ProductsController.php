<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\REST;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use RexTheme\RexProductRecommendationsForWoocommerce\Abstracts\RESTController;
use RexTheme\RexProductRecommendationsForWoocommerce\REST\Actions\Admin\ProductActions;
use WP_REST_Response;
use WP_REST_Server;

/**
 * API ProductsController class.
 *
 * @since 1.0.0
 */
class ProductsController extends RESTController {

	/**
	 * Route base.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $base = '/products';

	/**
	 * Register all routes related with WC Products.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
            $this->base . '/categories',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_categories' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						's' => array(
							'description'       => __( 'Keywords to search product categories.', 'product-recommendations-addon-for-woocommerce' ),
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => 'rest_validate_request_arg',
							'required'          => false,
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
            $this->base . '/tags',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_tags' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						's' => array(
							'description'       => __( 'Keywords to search product tags.', 'product-recommendations-addon-for-woocommerce' ),
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => 'rest_validate_request_arg',
							'required'          => false,
						),
					),
				),
			)
		);
	}

	/**
	 * Retrieves product categories based on search keywords from a REST API request.
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response|null Returns a REST API response with product categories or null if no keywords are provided.
	 * @since 1.0.0
	 */
	public function get_categories( $request ): ?WP_REST_Response {
		$keywords = $request->get_param( 's' );
		return rest_ensure_response( ProductActions::get_categories( $keywords ) );
	}

	/**
	 * Retrieves product tags based on search keywords from a REST API request.
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response|null Returns a REST API response with product categories or null if no keywords are provided.
	 * @since 1.0.0
	 */
	public function get_tags( $request ): ?WP_REST_Response {
		$keywords = $request->get_param( 's' );
		return rest_ensure_response( ProductActions::get_tags( $keywords ) );
	}
}
