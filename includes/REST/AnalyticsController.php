<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\REST;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use RexTheme\RexProductRecommendationsForWoocommerce\Abstracts\RESTController;
use RexTheme\RexProductRecommendationsForWoocommerce\REST\Actions\Admin\AnalyticsActions;
use WP_REST_Response;
use WP_REST_Server;

/**
 * API AnalyticsController class.
 *
 * @since 1.0.0
 */
class AnalyticsController extends RESTController {

    /**
     * Route base.
     *
     * @var string
     * @since 1.0.0
     */
    protected $base = '/analytics';

    /**
     * Register route related to analytics.
     *
     * @return void
     * @since 1.0.0
     */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
            $this->base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'handle_analytics_data' ),
					'permission_callback' => array( $this, 'check_permission' ),
				),
			)
		);
	}

	/**
	 * Handle analytics data request.
	 *
	 * @param \WP_REST_Request $request The REST request object.
	 * @return \WP_REST_Response The REST response object containing analytics data.
	 *
	 * @since 1.0.0
	 */
	public function handle_analytics_data( $request ): WP_REST_Response {

		$engine_type = ! empty( $request->get_param( 'engine_id' ) ) ? sanitize_text_field( $request->get_param( 'engine_id' ) ) : '0';
		$filter      = ! empty( $request->get_param( 'filter' ) ) ? sanitize_text_field( $request->get_param( 'filter' ) ) : 'all';

		$analytics_actions = new AnalyticsActions();
		$analytics_data    = $analytics_actions->get_analytics_data( $engine_type, $filter );
		$response          = new WP_REST_Response( $analytics_data );
		$response->set_status( 200 );
		return $response;
	}
}
