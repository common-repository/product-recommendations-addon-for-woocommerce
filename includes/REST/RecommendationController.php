<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\REST;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use RexTheme\RexProductRecommendationsForWoocommerce\Abstracts\RESTController;
use RexTheme\RexProductRecommendationsForWoocommerce\REST\Actions\Admin\RecommendationActions;
use WP_REST_Response;
use WP_REST_Server;

/**
 * API RecommendationController class.
 *
 * @since 1.0.0
 */
class RecommendationController extends RESTController {

    /**
     * Route base.
     *
     * @var string
     * @since 1.0.0
     */
    protected $base = 'engine';

    /**
     * Register all routes related with Recommendation Engine.
     *
     * @return void
     * @since 1.0.0
     */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
            '/' . $this->base,
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'handle_engine_data' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						'id'          => array(
							'description'       => __( 'Engine id to update', 'product-recommendations-addon-for-woocommerce' ),
							'type'              => 'integer',
							'default'           => null,
							'sanitize_callback' => 'absint',
							'validate_callback' => 'rest_validate_request_arg',
						),
						'engine_data' => array(
							'description'       => __( 'Engine engine data to create/update with', 'product-recommendations-addon-for-woocommerce' ),
							'type'              => 'JSON',
							'validate_callback' => 'rest_validate_request_arg',
							'required'          => true,
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_engine_data' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						'id' => array(
							'description'       => __( 'Engine id to fetch', 'product-recommendations-addon-for-woocommerce' ),
							'type'              => 'integer',
							'default'           => null,
							'sanitize_callback' => 'absint',
							'validate_callback' => 'rest_validate_request_arg',
							'required'          => true,
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_engine' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						'id' => array(
							'description'       => __( 'Engine id to delete', 'product-recommendations-addon-for-woocommerce' ),
							'type'              => 'integer',
							'default'           => null,
							'sanitize_callback' => 'absint',
							'validate_callback' => 'rest_validate_request_arg',
							'required'          => true,
						),
					),
				),
			)
		);
		register_rest_route(
			$this->namespace,
            '/engines',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_all_engine_data' ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						'per_page' => array(
							'description'       => __( 'Item number to fetch per page', 'product-recommendations-addon-for-woocommerce' ),
							'type'              => 'integer',
							'default'           => 5,
							'sanitize_callback' => 'absint',
							'validate_callback' => 'rest_validate_request_arg',
							'required'          => false,
						),
						'offset'   => array(
							'description'       => __( 'Item number to fetch per page', 'product-recommendations-addon-for-woocommerce' ),
							'type'              => 'integer',
							'default'           => 0,
							'sanitize_callback' => 'absint',
							'validate_callback' => 'rest_validate_request_arg',
							'required'          => false,
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/engines-data/',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_engines_data' ),
					'permission_callback' => array( $this, 'check_permission' ),
				),
			)
		);
	}

	/**
	 * Handles incoming requests to create or update engine data via the REST API.
	 *
	 * This method processes the engine data received via the REST API request and either creates
	 * a new engine entry or updates an existing one based on the provided parameters.
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 *
	 * @return WP_REST_Response|null Returns a REST API response containing the engine ID if successful,
	 *                              otherwise returns null.
	 * @since 1.0.0
	 */
	public function handle_engine_data( $request ): ?WP_REST_Response {
		$engine_id   = $request->get_param( 'id' );
		$engine_data = $request->get_param( 'engine_data' );

		$notification = array(
			'type'    => __( 'Alert', 'product-recommendations-addon-for-woocommerce' ),
			'message' => __( 'Please fill in all the required field(s).', 'product-recommendations-addon-for-woocommerce' ),
		);

		if ( ! empty( $engine_data ) && is_array( $engine_data ) ) {
			if ( ! empty( $engine_id ) ) {
				$engine_id = RecommendationActions::update_engine( $engine_id, $engine_data );
				if ( ! empty( $engine_id ) ) {
					$notification = array(
						'type'    => __( 'Success', 'product-recommendations-addon-for-woocommerce' ),
						'message' => __( 'Your changes have been saved.', 'product-recommendations-addon-for-woocommerce' ),
					);
				}
			} else {
				$engine_id = RecommendationActions::create_engine( $engine_data );
				if ( ! empty( $engine_id ) ) {
					$notification = array(
						'type'    => __( 'Success', 'product-recommendations-addon-for-woocommerce' ),
						'message' => __( 'Your new recommendation engine has been registered.', 'product-recommendations-addon-for-woocommerce' ),
					);
				}
			}
		}

		return rest_ensure_response(
            array(
				'engine_id'    => $engine_id,
				'notification' => $notification,
            )
        );
	}

	/**
	 * Retrieves data for a specific engine.
	 *
	 * Retrieves data for a specific engine based on the given ID from the request parameter.
	 *
	 * @param \WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|null Returns a response containing engine data if found; otherwise, returns null.
	 * @since 1.0.0
	 */
	public function get_engine_data( $request ): ?WP_REST_Response {
		$engine_id   = $request->get_param( 'id' );
		$engine_data = ! empty( $engine_id ) ? RecommendationActions::get_engine_data( $engine_id ) : array();
		$engine_data = RecommendationActions::get_unserialized_data( $engine_data );
		return rest_ensure_response( $engine_data );
	}

	/**
	 * Retrieves data for all engines with pagination.
	 *
	 * Retrieves data for all engines based on provided pagination parameters from the request.
	 *
	 * @param \WP_REST_Request $request The request object containing per_page and offset parameters.
	 *
	 * @return WP_REST_Response|null Returns the response containing engine data with pagination if available; otherwise, returns null.
	 * @since 1.0.0
	 */
	public function get_all_engine_data( $request ): ?WP_REST_Response {
		$per_page    = $request->get_param( 'per_page' );
		$offset      = $request->get_param( 'offset' );
		$engine_data = RecommendationActions::get_all_engine_data( $per_page, $offset );
		$engine_data = RecommendationActions::get_unserialized_data( $engine_data );
		return rest_ensure_response( $engine_data );
	}

	/**
	 * Deletes a specific engine.
	 *
	 * Deletes an engine based on the given ID from the request parameter.
	 *
	 * @param \WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|null Returns the response indicating the success or failure of the deletion; otherwise, returns null.
	 * @since 1.0.0
	 */
	public function delete_engine( $request ): ?WP_REST_Response {
		$engine_id = $request->get_param( 'id' );
		$response  = ! empty( $engine_id ) ? RecommendationActions::delete_engine( $engine_id ) : false;
		return rest_ensure_response( $response );
	}

	/**
	 * Retrieve data of all recommendation engines.
	 *
	 * @return WP_REST_Response Returns a REST response containing all engine data.
	 *
	 * @since 1.0.3
	 */
	public function get_engines_data() {
		$all_engine_data = RecommendationActions::get_all_engines();
		return rest_ensure_response( $all_engine_data );
	}
}
