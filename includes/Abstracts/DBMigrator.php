<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\Abstracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Database migration class.
 *
 * Abstract class to handle database migration classes.
 */
abstract class DBMigrator {

	/**
	 * Migrate the database table.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * */
	abstract public static function migrate();
}
