<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\Databases\Migrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use RexTheme\PluginName\Abstracts\DBMigrator;

/**
 * Jobs migration.
 */
class PluginNameMigration extends DBMigrator {

    /**
     * Migrate the jobs table.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate() {

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $schema_jobs = "CREATE TABLE IF NOT EXISTS `{$wpdb->plugin_name}` (

        ) $charset_collate";

        // Create the tables.
        dbDelta( $schema_jobs );
    }
}
