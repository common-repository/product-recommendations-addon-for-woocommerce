<?php
namespace RexTheme\RexProductRecommendationsForWoocommerce\Setup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use RexTheme\RexProductRecommendationsForWoocommerce\Common\Keys;

class Installer {

    /**
     * Run the installer.
     *
     * @since 1.0.0
     */
    public function run() {
        // Update the installed version.
        $this->add_version();
		$this->set_product_recommendation_activation_transients();
    }


    /**
     * Add time and version on DB.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_version(): void {
        $installed = get_option( Keys::REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_INSTALLED );
        if ( ! $installed ) {
			update_site_option( Keys::REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_INSTALLED, time() );
        }
		update_site_option( Keys::REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_VERSION, REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_VERSION );
    }


    /**
     * Register table names.
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function register_table_names(): void {
        global $wpdb;

        // Register the tables to wpdb global.
        $wpdb->plugin_name = $wpdb->prefix . 'plugin_name';
    }


    /**
     * Create necessary database tables.
     *
     * @since JOB_PLACE_
     *
     * @return void
     */
    public function create_tables() {
        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        // Run the database table migrations.
        \RexTheme\PluginName\Databases\Migrations\PluginNameMigration::migrate();
    }


	/**
	 * See if we need to redirect the admin to setup wizard or not.
	 *
	 * @since 1.0.0
	 */
	private function set_product_recommendation_activation_transients() {
		if ( $this->is_new_install() ) {
			set_transient( '_rexprr_product_rec_activation_redirect', 1, 30 );
		}
	}

	/**
	 * Brand new install of wpfunnels
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	public function is_new_install() {
		return is_null( get_site_option( 'rexprr_for_woocommerce_version', null ) );
	}
}
