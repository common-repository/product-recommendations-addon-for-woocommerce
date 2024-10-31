<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\Common;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Manage all key strings.
 *
 * @since 1.0.0
 */
class Keys {

    /**
     * Plugin installed option key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_INSTALLED = 'rexprr_for_woocommerce_installed';

    /**
     * Plugin version key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_VERSION = 'rexprr_for_woocommerce_version';

    /**
     * Plugin seeder ran key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_SEEDER_RAN = 'rexprr_for_woocommerce_type_seeder_ran';

    /**
     * Plugin ran key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_RAN = 'rexprr_for_woocommerce_job_seeder_ran';
}
