<?php
/**
 * Plugin Name:     Product Recommendations Addon for WooCommerce
 * Plugin URI:      https://rextheme.com/product-recommendations-for-woocommerce/
 * Description:     An WooCommerce add-on that allows store owners to generate dynamic product recommendations based different criteria and shows it to the customers.
 * Version:         1.0.0
 * Author:          RexTheme
 * Author URI:      https://rextheme.com
 * Text Domain:     product-recommendations-addon-for-woocommerce
 * Domain Path:     /languages
 * Requires PHP:    7.4
 * Tested up to:    6.6
 * Requires WP:     6.0
 * Requires Plugins: woocommerce
 *
 * Namespace:       RexProductRecommendationsForWoocommerce
 * License:         GPL-2.0+
 */

defined( 'ABSPATH' ) || exit;
const REXPRR_PRODUCT_RECOMMENDATIONS_VERSION = '1.0.0';

define( 'REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_VERSION', REXPRR_PRODUCT_RECOMMENDATIONS_VERSION );
final class RexProductRecommendationsForWoocommerce {
    /**
     * Plugin version.
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * Plugin slug.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const SLUG = 'rexprr-product-recommendations';

    /**
     * Holds various class instances.
     *
     * @var array
     *
     * @since 1.0.0
     */
    private $container = array();

    /**
     * Constructor for the PluginName class.
     *
     * Sets up all the appropriate hooks and actions within our plugin.
     *
     * @since 1.0.0
     */
    private function __construct() {
        require_once __DIR__ . '/vendor/autoload.php';
        require_once __DIR__ . '/includes/core-functions.php';

        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'wp_loaded', array( $this, 'flush_rewrite_rules' ) );
        $this->init_plugin();
    }

    /**
     * Initializes the PluginBoilerplate() class.
     *
     * Checks for an existing PluginBoilerplate() instance
     * and if it doesn't find one, creates it.
     *
     * @since 1.0.0
     *
     * @return RexProductRecommendationsForWoocommerce|bool
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new RexProductRecommendationsForWoocommerce();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @since 1.0.0
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @since 1.0.0
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset( $prop ) {
        return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
    }

    /**
     * Define the constants.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function define_constants() {
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_SLUG', self::SLUG );
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_FILE', __FILE__ );
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_DIR', __DIR__ );
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_PATH', dirname( REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_FILE ) );
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_INCLUDES', REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_PATH . '/includes' );
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_TEMPLATE_PATH', REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_PATH . '/views' );
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_URL', plugins_url( '', REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_FILE ) );
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_BUILD', REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_URL . '/build' );
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_ASSETS', REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_URL . '/includes/Assets' );
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_PRODUCTION', 'yes' );
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_SL_STORE_URL', sanitize_url( 'https://rextheme.com/' ) );
        define( 'REXPRR_PRODUCT_RECOMMENDATIONS_ITEM_ID', '371619' );
		define( 'REXPRR_PRODUCT_RECOMMENDATIONS_WEBHOOK_URL', sanitize_url( 'https://rextheme.com/?mailmint=1&route=webhook&topic=contact&hash=d3458455-4a34-4158-a007-a9f5d20bfb3b' ) );
	}

    /**
     * Load the plugin after all plugins are loaded.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        $this->init_hooks();

        /**
         * Fires after the plugin is loaded.
         *
         * @since 1.0.0
         */
        do_action( 'rexprr_product_recommendations_for_woocommerce_loaded' );
    }

    /**
     * Activating the plugin.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function activate() {
    }

    /**
     * Placeholder for deactivation function.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function deactivate() {
    }

    /**
     * Flush rewrite rules after plugin is activated.
     *
     * Nothing being added here yet.
     *
     * @since 1.0.0
     */
    public function flush_rewrite_rules() {
        // fix rewrite rules
    }

    /**
     * Run the installer to create necessary migrations and seeders.
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function install() {
        $installer = new RexTheme\RexProductRecommendationsForWoocommerce\Setup\Installer();
        $installer->run();
    }

    /**
     * Include the required files.
     *
     * @since 0.2.0
     *
     * @return void
     */
	public function includes() {
		if ( $this->is_request( 'admin' ) ) {
			$this->container['admin_menu'] = new RexTheme\RexProductRecommendationsForWoocommerce\Admin\Menu();
		}
		$this->container['assets']            = new RexTheme\RexProductRecommendationsForWoocommerce\Assets\LoadAssets();
		$this->container['rest_api']          = new RexTheme\RexProductRecommendationsForWoocommerce\REST\API();
		$this->container['post_type']         = new RexTheme\RexProductRecommendationsForWoocommerce\PostTypes\RecommendationEnginePostType();
		$this->container['frontend_products'] = new RexTheme\RexProductRecommendationsForWoocommerce\Frontend\Products();
	}

    /**
     * Initialize the hooks.
     *
     * @since 0.2.0
     *
     * @return void
     */
    public function init_hooks() {
        // Init classes
        add_action( 'init', array( $this, 'init_classes' ) );

        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );

        // Add the plugin page links
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
    }

    /**
     * Instantiate the required classes.
     *
     * @since 0.2.0
     *
     * @return void
     */
    public function init_classes() {
        // Init necessary hooks
        new RexTheme\RexProductRecommendationsForWoocommerce\Hooks\Common();
    }

    /**
     * Initialize plugin for localization.
     *
     * @uses load_plugin_textdomain()
     *
     * @since 0.2.0
     *
     * @return void
     */
    public function localization_setup() {
        load_plugin_textdomain( 'product-recommendations-addon-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        // Load the React-pages translations.
        if ( is_admin() ) {
            // Load wp-script translation for plugin-name-app
            wp_set_script_translations( 'product-recommendations-addon-for-woocommerce-app', 'product-recommendations-addon-for-woocommerce', plugin_dir_path( __FILE__ ) . 'languages/' );
        }
    }

    /**
     * What type of request is this.
     *
     * @since 0.2.0
     *
     * @param string $type admin, ajax, cron or frontend
     *
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin':
                return is_admin();

            case 'ajax':
                return defined( 'DOING_AJAX' );

            case 'rest':
                return defined( 'REST_REQUEST' );

            case 'cron':
                return defined( 'DOING_CRON' );

            case 'frontend':
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

    /**
     * Plugin action links
     *
     * @param array $links
     *
     * @since 0.2.0
     *
     * @return array
     */
    public function plugin_action_links( $links ) {
        $links[] = '<a href="' . admin_url( 'admin.php?page=rexprr-product-recommendations#/' ) . '">' . __( 'Engines', 'product-recommendations-addon-for-woocommerce' ) . '</a>';
		$links[] = '<a href="' . admin_url( 'admin.php?page=rexprr-product-recommendations#/analytics' ) . '">' . __( 'Analytics', 'product-recommendations-addon-for-woocommerce' ) . '</a>';
		$links[] = '<a href="' . admin_url( 'admin.php?page=rexprr-product-recommendations#/setup-wizard' ) . '">' . __( 'Setup Wizard', 'product-recommendations-addon-for-woocommerce' ) . '</a>';
		return $links;
    }
}

/**
 * Initialize the main plugin.
 *
 * @since 1.0.0
 *
 * @return \RexProductRecommendationsForWoocommerce|bool
 */
function rexprr_product_recommendations_for_woocommerce_main_function() { // phpcs:ignore
	return RexProductRecommendationsForWoocommerce::init();
}

/**
 * Displays an error notice if WooCommerce is not installed or activated.
 *
 * This function outputs an error notice within the WordPress admin area if the plugin requires WooCommerce
 * but it's not installed or activated. It prompts the user to install and activate WooCommerce to use the plugin.
 *
 * @since 1.0.0
 */
function rexprr_product_recommendations_wc_missing_notice() {
	?>
	<div class="error">
		<p>
			<?php
			sprintf(
			    // translators: %s: Name of the product.
				esc_html__(
                    '<strong>%s</strong> requires WooCommerce to be installed and activate first.',
					'product-recommendations-addon-for-woocommerce'
                ),
                'Product Recommendations for WooCommerce'
            )
			?>
		</p>
	</div>
	<?php
}

/**
 * Initializes the plugin if WooCommerce is available.
 *
 * This function checks if WooCommerce is defined and available. If not, it hooks into the admin_notices action
 * to display an error notice using 'rexprr_product_recommendations_wc_missing_notice'. If WooCommerce is available,
 * it triggers the main function 'rexprr_product_recommendations_for_woocommerce_main_function'.
 *
 * @since 1.0.0
 */
function rexprr_product_recommendations_init() {
	if ( ! defined( 'WC_VERSION' ) ) {
		add_action( 'admin_notices', 'rexprr_product_recommendations_wc_missing_notice' );
		add_action( 'admin_init', 'rexprr_product_recommendations_self_deactivation' );
		return;
	}

	/**
	 * Kick-off the plugin.
	 *
	 * @since 1.0.0
	 */
	rexprr_product_recommendations_for_woocommerce_main_function();
}
add_action( 'plugins_loaded', 'rexprr_product_recommendations_init', 99 );

/**
 * Deactivates the Product Recommendations for WooCommerce plugin.
 *
 * @since 1.0.0
 */
function rexprr_product_recommendations_self_deactivation() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Redirects to the license management page after activating the plugin.
 *
 * @param string $plugin The basename of the activated plugin file.
 *
 * @since 1.0.0
 */
function rexprr_redirect_after_activation( $plugin ) {
	if ( defined( 'WC_VERSION' ) && plugin_basename( __FILE__ ) === $plugin ) {
		if ( get_transient( '_rex_product_rec_activation_redirect' ) ) {
			$do_redirect = true;
			if ( wp_doing_ajax() || is_network_admin() || ! current_user_can( 'manage_options' ) ) {
				$do_redirect = false;
			}
			if ( $do_redirect ) {
				delete_transient( '_rex_product_rec_activation_redirect' );
				$url = admin_url( 'admin.php?page=rexprr-product-recommendations#/setup-wizard' );
				wp_safe_redirect( wp_sanitize_redirect( esc_url_raw( $url ) ) );
				exit;
			}
		}
	}
}
add_action( 'activated_plugin', 'rexprr_redirect_after_activation' );
/**
 * See if we need to redirect the admin to set up wizard or not.
 *
 * @since 1.0.0
 */
function rexprr_set_product_recommendation_activation_transients() {
	if ( rexprr_is_product_recommendation_new_install() ) {
		set_transient( '_rex_product_rec_activation_redirect', 1, 30 );
	}
}

/**
 * Brand new install of wpfunnels
 *
 * @return bool
 * @since  1.0.0
 */
function rexprr_is_product_recommendation_new_install() {
	return is_null( get_site_option( 'rexprr_product_recommendations_for_woocommerce_version', null ) );
}
function rexprr_update_product_recommendation_version() {
	update_site_option( 'rexprr_product_recommendations_for_woocommerce_version', REXPRR_PRODUCT_RECOMMENDATIONS_VERSION );
}


register_activation_hook( __FILE__, 'rexprr_activate_product_recommendations' );

function rexprr_activate_product_recommendations() {
	rexprr_set_product_recommendation_activation_transients();
	rexprr_update_product_recommendation_version();
}

/**
 * Declare plugin's compatibility with WooCommerce HPOS
 *
 * @return void
 * @since 7.2.31
 */
function rexprr_wc_hpos_compatibility() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__ );
	}
}
add_action( 'before_woocommerce_init', 'rexprr_wc_hpos_compatibility' );
