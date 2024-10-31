<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\Abstracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WP_REST_Controller;

/**
 * Rest Controller base class.
 *
 * @since 0.3.0
 */
abstract class RESTController extends WP_REST_Controller {

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'rex-pr-recommendation/v1';

    /**
     * Check default permission for rest routes.
     *
     * @since 0.3.0
     *
     * @TODO: manage permissions from capabilities.
     *
     * @return bool
     */
    public function check_permission(): bool {
		$capability = is_multisite() ? 'delete_sites' : 'manage_options';
		return current_user_can( $capability );
    }
}
