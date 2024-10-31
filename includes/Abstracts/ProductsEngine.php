<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\Abstracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class ProductsEngine {

	/**
	 * Holds custom WHERE conditions for SQL queries.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $custom_where = '';

	/**
	 * Represents the count of meta tables used in SQL JOIN operations.
	 *
	 * @var int
	 * @since 1.0.0
	 */
	protected $meta_table_count = 0;

	/**
	 * Represents the count of term tables used in SQL JOIN operations.
	 *
	 * @var int
	 * @since 1.0.0
	 */
	protected $term_table_count = 0;

	/**
	 * Holds the product ids.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $product_ids = array();

	/**
	 * Renders a block of products based on the given product IDs.
	 *
	 * @param string $engine_title  The title of the recommendation engine.
	 * @param array  $product_ids   An array of product IDs to render.
	 * @param int    $columns       The number of columns for product layout.
	 * @param string $custom_class  Additional CSS class for styling purposes.
	 *
	 * @since 1.0.0
	 */
	protected function render_product_block( $engine_title, $product_ids, $columns = 4, $custom_class = '', $engine_id = null, $primary_location = '' ) {
		if ( ! empty( $product_ids ) && is_array( $product_ids ) ) {
			wc_set_loop_prop( 'columns', $columns );

			if ( ! empty( $engine_id ) && ! empty( $primary_location ) ) {
				/**
				 * Modifies the WooCommerce product add to cart URL by appending custom parameters.
				 *
				 * @param string $add_to_cart_url The original add to cart URL.
				 *
				 * @return string The modified add to cart URL with additional parameters.
				 * @since 1.0.0
				 */
				add_filter(
					'woocommerce_product_add_to_cart_url',
					function ( $add_to_cart_url ) use ( $engine_id, $primary_location ) {
						if ( false !== strpos( $add_to_cart_url, '?add-to-cart=' ) ) {
							$add_to_cart_url .= "&rexprr_engine_id={$engine_id}&rexprr_engine_position={$primary_location}";
						}
						return $add_to_cart_url;
					}
				);

				/**
				 * Hook to add an extra class to the add to cart link on the WooCommerce loop.
				 *
				 * This hook calls the add_extra_class_to_add_to_cart_link function defined elsewhere.
				 * It adds an extra class to the add to cart link HTML if the product is purchasable.
				 *
				 * @since 1.0.1
				 */
				add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'add_extra_class_to_add_to_cart_link' ), 10, 2 );
			}
			include REXPRR_PRODUCT_RECOMMENDATIONS_FOR_WOOCOMMERCE_INCLUDES . '/Templates/products-block.php';
		}
	}

	/**
	 * Adds an extra class to the add to cart link HTML.
	 *
	 * @since 1.0.1
	 *
	 * @param string     $html    The HTML for the add to cart link.
	 * @param WC_Product $product The WooCommerce product object.
	 * @return string         The modified HTML.
	 */
	public function add_extra_class_to_add_to_cart_link( $html, $product ) {
		$extra_class = 'product_recommendation_add_to_cart_button';
		if ( $product && $product->is_purchasable() ) {
			$html = str_replace( 'class="', 'class="' . $extra_class . ' ', $html );
		}
		return $html;
	}

	/**
	 * Renders the "Add All to Cart" button for frequently bought together products.
	 *
	 * @param array $product_ids Array of product IDs for frequently bought together products.
	 *
	 * @since 1.0.0
	 */
	public function render_add_all_button( $product_ids ) {
		if ( ! empty( $product_ids ) && is_array( $product_ids ) && 2 <= count( $product_ids ) ) {
			$action      = 'rexprr_product_frequently_bought_together';
			$nonce       = wp_create_nonce( $action );
			$total_price = $this->get_total_price( $product_ids );
			$product_ids = rexprr_encode_data( $product_ids );
			?>
			<div class="rex-product-recommendation-add-all-btn">
				<a href="?action=<?php echo esc_attr( $action ); ?>&recommendation_token=<?php echo esc_attr( $product_ids ); ?>&_wpnonce=<?php echo esc_attr( $nonce ); ?>"
					class="button wp-element-button">
					<?php
					// translators: %s is the total price of the products.
					sprintf( esc_html__( 'Total price: %s - Add all to cart', 'product-recommendations-addon-for-woocommerce' ), wc_price( $total_price ) );
					?>
				</a>
			</div>
			<?php
		}
	}

	/**
	 * Renders the block of frequently bought together products.
	 *
	 * @param string   $engine_title      The title of the recommendation engine.
	 * @param int|null $product_id      The ID of the current product (optional).
	 * @param numeric  $limit            Limit the number of products (optional).
	 * @param int      $columns              Number of columns in the product block (default is 4).
	 * @param string   $blockified_class  Additional CSS class for styling (optional).
	 *
	 * @since 1.0.0
	 */
	protected function render_frequently_bought_together_products( $engine_title, $product_id = null, $limit = 4, $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		$engine_settings = ! empty( $engine_id ) ? $this->get_engine_settings( $engine_id ) : array();
		if ( ! empty( $engine_settings ) && is_array( $engine_settings ) ) {
			$columns = ! empty( $engine_settings['columns'] ) ? (int) $engine_settings['columns'] : 4;
			$rows    = ! empty( $engine_settings['rows'] ) ? (int) $engine_settings['rows'] : 1;
			$limit   = $rows * $columns;

			$blockified_class .= ! empty( $engine_settings['customClass'] ) ? " {$engine_settings[ 'customClass' ]}" : '';
		}

		$product_ids = $this->get_frequently_bought_together_product_ids( $product_id, $limit );

		add_action(
			'rexprr_after_product_loop',
			function () use ( $product_ids ) {
				$this->render_add_all_button( $product_ids );
			}
		);

		$this->render_product_block( $engine_title, $product_ids, $columns, "rex-prr-frequently-bought-together {$blockified_class}", $engine_id, $primary_location );
	}

	/**
	 * Renders alternative products for out-of-stock items based on the same category.
	 *
	 * @param string|null $engine_title      The title of the recommendation engine.
	 * @param int|null    $product_id        The ID of the out-of-stock product.
	 * @param int         $limit             The limit of alternative products to display.
	 * @param int         $columns           The number of columns for product layout.
	 * @param string      $blockified_class  Additional CSS class for styling purposes.
	 *
	 * @since 1.0.0
	 */
	protected function render_out_of_stock_alternative_products( $engine_title, $product_id = null, $limit = 4, $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		if ( ! empty( $product_id ) ) {
			$engine_settings = ! empty( $engine_id ) ? $this->get_engine_settings( $engine_id ) : array();
			if ( ! empty( $engine_settings ) && is_array( $engine_settings ) ) {
				$columns = ! empty( $engine_settings['columns'] ) ? (int) $engine_settings['columns'] : 4;
				$rows    = ! empty( $engine_settings['rows'] ) ? (int) $engine_settings['rows'] : 1;
				$limit   = $rows * $columns;

				$blockified_class .= ! empty( $engine_settings['customClass'] ) ? " {$engine_settings[ 'customClass' ]}" : '';
			}

			$product     = wc_get_product( $product_id );
			$product_ids = $this->get_filtered_product_ids( array( $product_id ), $product->get_category_ids(), $limit );
			$this->render_product_block( $engine_title, $product_ids, $columns, "rex-prr-frequently-bought-together {$blockified_class}", $engine_id, $primary_location );
		}
	}

	/**
	 * Renders the block of top-rated products.
	 *
	 * @param string   $engine_title      The title of the recommendation engine.
	 * @param int|null $product_id      The ID of the current product (optional).
	 * @param string   $limit             Limit the number of products (optional).
	 * @param int      $columns              Number of columns in the product block (default is 4).
	 * @param string   $blockified_class  Additional CSS class for styling (optional).
	 *
	 * @since 1.0.0
	 */
	protected function render_top_rated_products( $engine_title, $product_id = null, $limit = '', $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		$engine_settings = ! empty( $engine_id ) ? $this->get_engine_settings( $engine_id ) : array();
		if ( ! empty( $engine_settings ) && is_array( $engine_settings ) ) {
			$columns = ! empty( $engine_settings['columns'] ) ? (int) $engine_settings['columns'] : 4;
			$rows    = ! empty( $engine_settings['rows'] ) ? (int) $engine_settings['rows'] : 1;
			$limit   = $rows * $columns;
			$limit   = "limit='{$limit}'";

			$blockified_class .= ! empty( $engine_settings['customClass'] ) ? " {$engine_settings[ 'customClass' ]}" : '';
		}

		do_shortcode( "[products cache='false' {$limit} orderby='rating' top_rated='true']" );
		$this->render_product_block( $engine_title, $this->product_ids, $columns, "rex-prr-new-arrival-products {$blockified_class}", $engine_id, $primary_location );
		$this->product_ids = array();
	}

	/**
	 * Renders the block of best-selling products.
	 *
	 * @param string   $engine_title      The title of the recommendation engine.
	 * @param int|null $product_id      The ID of the current product (optional).
	 * @param string   $limit             Limit the number of products (optional).
	 * @param int      $columns              Number of columns in the product block (default is 4).
	 * @param string   $blockified_class  Additional CSS class for styling (optional).
	 *
	 * @since 1.0.0
	 */
	protected function render_best_selling_products( $engine_title, $product_id = null, $limit = '', $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		$engine_settings = ! empty( $engine_id ) ? $this->get_engine_settings( $engine_id ) : array();
		if ( ! empty( $engine_settings ) && is_array( $engine_settings ) ) {
			$columns = ! empty( $engine_settings['columns'] ) ? (int) $engine_settings['columns'] : 4;
			$rows    = ! empty( $engine_settings['rows'] ) ? (int) $engine_settings['rows'] : 1;
			$limit   = $rows * $columns;
			$limit   = "limit='{$limit}'";

			$blockified_class .= ! empty( $engine_settings['customClass'] ) ? " {$engine_settings[ 'customClass' ]}" : '';
		}

		do_shortcode( "[products cache='false' {$limit} orderby='popularity' best_selling='true']" );
		$this->render_product_block( $engine_title, $this->product_ids, $columns, "rex-prr-new-arrival-products {$blockified_class}", $engine_id, $primary_location );
		$this->product_ids = array();
	}

	/**
	 * Renders the block of popular on-sale products.
	 *
	 * @param string   $engine_title      The title of the recommendation engine.
	 * @param int|null $product_id      The ID of the current product (optional).
	 * @param string   $limit             Limit the number of products (optional).
	 * @param int      $columns              Number of columns in the product block (default is 4).
	 * @param string   $blockified_class  Additional CSS class for styling (optional).
	 *
	 * @since 1.0.0
	 */
	protected function render_popular_on_sale_products( $engine_title, $product_id = null, $limit = '', $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		$engine_settings = ! empty( $engine_id ) ? $this->get_engine_settings( $engine_id ) : array();
		if ( ! empty( $engine_settings ) && is_array( $engine_settings ) ) {
			$columns = ! empty( $engine_settings['columns'] ) ? (int) $engine_settings['columns'] : 4;
			$rows    = ! empty( $engine_settings['rows'] ) ? (int) $engine_settings['rows'] : 1;
			$limit   = $rows * $columns;
			$limit   = "limit='{$limit}'";

			$blockified_class .= ! empty( $engine_settings['customClass'] ) ? " {$engine_settings[ 'customClass' ]}" : '';
		}

		do_shortcode( "[products cache='false' {$limit} orderby='popularity' on_sale='true']" );
		$this->render_product_block( $engine_title, $this->product_ids, $columns, "rex-prr-popular-onsale-products {$blockified_class}", $engine_id, $primary_location );
		$this->product_ids = array();
	}

	/**
	 * Renders the block of new arrival products.
	 *
	 * @param string   $engine_title      The title of the recommendation engine.
	 * @param int|null $product_id      The ID of the current product (optional).
	 * @param string   $limit             Limit the number of products (optional).
	 * @param int      $columns              Number of columns in the product block (default is 4).
	 * @param string   $blockified_class  Additional CSS class for styling (optional).
	 *
	 * @since 1.0.0
	 */
	protected function render_new_arrival_products( $engine_title, $product_id = null, $limit = '', $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		$engine_settings = ! empty( $engine_id ) ? $this->get_engine_settings( $engine_id ) : array();
		if ( ! empty( $engine_settings ) && is_array( $engine_settings ) ) {
			$columns = ! empty( $engine_settings['columns'] ) ? (int) $engine_settings['columns'] : 4;
			$rows    = ! empty( $engine_settings['rows'] ) ? (int) $engine_settings['rows'] : 1;
			$limit   = $rows * $columns;
			$limit   = "limit='{$limit}'";

			$blockified_class .= ! empty( $engine_settings['customClass'] ) ? " {$engine_settings[ 'customClass' ]}" : '';
		}

		do_shortcode( "[products cache='false' {$limit} orderby='date' order='DESC']" );
		$this->render_product_block( $engine_title, $this->product_ids, $columns, "rex-prr-new-arrival-products {$blockified_class}", $engine_id, $primary_location );
		$this->product_ids = array();
	}


	/**
	 * Renders the display for recently viewed products.
	 *
	 * This function renders the display for recently viewed products using the specified engine title and optional parameters.
	 * If an engine ID is provided, it retrieves settings specific to that engine and adjusts the display accordingly.
	 *
	 * @param string $engine_title The title of the engine.
	 * @param string $limit The limit of products to display.
	 * @param int    $columns The number of columns for product display. Default is 4.
	 * @param string $blockified_class Additional class for blockified display. Default is empty.
	 * @param mixed  $engine_id Optional engine ID.
	 * @param string $primary_location Primary location for product display. Default is empty.
	 * @return void
	 *
	 * @since 1.0.2
	 */
	protected function render_recently_viewed_products( $engine_title, $limit = '', $columns = 4, $blockified_class = '', $engine_id = null, $primary_location = '' ) {
		$engine_settings = ! empty( $engine_id ) ? $this->get_engine_settings( $engine_id ) : array();
		if ( ! empty( $engine_settings ) && is_array( $engine_settings ) ) {
			$columns           = ! empty( $engine_settings['columns'] ) ? (int) $engine_settings['columns'] : 4;
			$rows              = ! empty( $engine_settings['rows'] ) ? (int) $engine_settings['rows'] : 1;
			$limit             = $rows * $columns;
			$blockified_class .= ! empty( $engine_settings['customClass'] ) ? " {$engine_settings[ 'customClass' ]}" : '';
		}
		$viewed_products = ! empty( $_COOKIE['rexprr_track_recently_viewed_product'] ) ? explode( '|', sanitize_text_field( wp_unslash( $_COOKIE['rexprr_track_recently_viewed_product'] ) ) ) : array();
		$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
		$key             = array_search( get_the_ID(), $viewed_products, true );
		if ( is_int( $key ) ) {
			unset( $viewed_products[ $key ] );
		}
		$sliced_products = array_slice( array_values( $viewed_products ), 0, $limit );
		$this->render_product_block( $engine_title, $sliced_products, $columns, "rex-prr-recently-viewed-products {$blockified_class}", $engine_id, $primary_location );
	}

	/**
	 * Retrieves recommendation engines along with their visibility locations from the database.
	 *
	 * This function fetches recommendation engines and their visibility locations stored in the WordPress database.
	 * It queries the postmeta table to select distinct location values associated with the '_rexprr_recommendation_engine_visibility_location' meta key.
	 * The results include the location and the ID of the recommendation engine.
	 *
	 * @global \wpdb $wpdb WordPress database object.
	 *
	 * @return array An array containing the visibility locations and corresponding engine IDs or an empty array if no results are found.
	 *
	 * @since 1.0.0
	 */
	protected function get_engines_with_visibility_location() {
		global $wpdb;
		//phpcs:disable
		return $wpdb->get_results(
			$wpdb->prepare(
				'SELECT `meta_value` AS `location`, `post_id` AS `engine_id` FROM %1s JOIN %1s ON `post_id`=`ID` WHERE `meta_key`=%s AND `post_status`=%s GROUP BY `location`, `engine_id`', // phpcs:ignore
				array( $wpdb->postmeta, $wpdb->posts, '_rexprr_recommendation_engine_visibility_location', 'publish' )
			),
			ARRAY_A
		);
		//phpcs:enable
	}

	/**
	 * Retrieves data for a specific recommendation engine based on the provided engine ID.
	 *
	 * This function fetches data associated with a recommendation engine from the WordPress database.
	 * It queries the posts table to select the ID, title, type, and filters/content of the engine based on the provided engine ID.
	 *
	 * @param int $engine_id The ID of the recommendation engine.
	 *
	 * @global \wpdb $wpdb WordPress database object.
	 *
	 * @return array|null An associative array containing engine data (ID, title, type, filters) or null if no data is found.
	 *
	 * @since 1.0.0
	 */
	protected function get_engine_data( $engine_id ) {
		global $wpdb;
		//phpcs:disable
		return $wpdb->get_row(
			$wpdb->prepare(
				'SELECT `ID` AS `id`, `post_title` AS `engine_title`, `post_excerpt` AS `engine_type`, `post_content` AS `filters` FROM %i WHERE `ID`=%d',
				array( $wpdb->posts, $engine_id )
			),
			ARRAY_A
		);
		//phpcs:enable
	}

	/**
	 * Retrieves IDs of products frequently bought together based on provided filters and limitations.
	 *
	 * @param int|null $product_id ID of the product.
	 * @param int|null $limit Limit for the number of product IDs returned.
	 * @return array IDs of products frequently bought together.
	 * @since 1.0.0
	 */
	protected function get_frequently_bought_together_product_ids( $product_id = null, $limit = null ) {
		$filtered_product_ids = $this->get_filtered_product_ids( ! empty( $product_id ) ? array( $product_id ) : array() );
		$order_ids            = $this->get_wc_order_ids( $product_id );
		$item_ids             = $this->get_order_item_ids( $order_ids, $product_id );
		if ( ! empty( $item_ids ) && is_array( $item_ids ) ) {
			$item_ids = ! empty( $limit ) ? array_slice( $item_ids, 0, $limit, true ) : $item_ids;
		}
		return ! empty( $item_ids ) ? array_intersect( $item_ids, $filtered_product_ids ) : array();
	}

	/**
	 * Retrieves filtered product IDs based on various conditions.
	 *
	 * @param array    $exclude_product_ids IDs to exclude from the result.
	 * @param array    $include_category_ids Category IDs to include in the query.
	 * @param int|null $limit Limit for the number of product IDs returned.
	 * @return array Filtered product IDs.
	 * @since 1.0.0
	 */
	protected function get_filtered_product_ids( $exclude_product_ids = array(), $include_category_ids = array(), $limit = null ) {
		global $product;
		if ( $product && $product->get_id() ) {
			$args = array(
				'post_type'              => array( 'product' ),
				'fields'                 => 'ids',
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'offset'                 => 0,
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'post__not_in'           => $exclude_product_ids, // phpcs:ignore
				'update_post_term_cache' => true,
				'update_post_meta_cache' => true,
				'cache_results'          => false,
				'suppress_filters'       => false,
			);

			if ( ! empty( $include_category_ids ) ) {
				$args['tax_query'] = array( // phpcs:ignore
											array(
												'taxonomy' => 'product_cat',
												'terms'    => $include_category_ids,
												'field'    => 'term_id',
												'operator' => 'IN',
												'include_children' => true,
											),
				);
			}

			$result      = new \WP_Query( $args );
			$product_ids = $result->posts;
			$product_ids = ! empty( $limit ) ? array_slice( $product_ids, 0, $limit, true ) : $product_ids;
		}
		return $product_ids ?? array();
	}

	/**
	 * Modifies WordPress queries by adding filters to alter the posts join and where clauses.
	 *
	 * This function adds filters to modify the posts join and where clauses for WordPress queries.
	 *
	 * @since 1.0.0
	 */
	protected function modify_queries() {
		add_filter( 'posts_join', array( $this, 'modify_join_query' ) );
		add_filter( 'posts_where', array( $this, 'modify_where_query' ) );
	}

	/**
	 * Removes modifications made to WordPress queries.
	 *
	 * This function removes the filters added to modify the posts join and where clauses for WordPress queries.
	 *
	 * @since 1.0.0
	 */
	protected function remove_query_modifications() {
		remove_filter( 'posts_join', array( $this, 'modify_join_query' ) );
		remove_filter( 'posts_where', array( $this, 'modify_where_query' ) );
	}

	/**
	 * Modifies the WHERE clause of a SQL query based on custom conditions.
	 *
	 * @param string $where The original WHERE clause of the SQL query.
	 * @return string The modified WHERE clause with additional custom conditions.
	 * @since 1.0.0
	 */
	public function modify_where_query( $where ) {
		$table_column     = 'RexRecomTerm0.term_taxonomy_id';
		$simple_type_id   = $this->get_term_id( 'simple', 'product_type' );
		$variable_type_id = $this->get_term_id( 'variable', 'product_type' );

		if ( ! empty( $simple_type_id ) && ! empty( $variable_type_id ) ) {
			$where .= " AND {$table_column} IN ({$simple_type_id}, {$variable_type_id})";
		} elseif ( ! empty( $simple_type_id ) ) {
			$where .= " AND {$table_column} IN ({$simple_type_id}";
		} elseif ( ! empty( $variable_type_id ) ) {
			$where .= " AND {$table_column} IN ({$variable_type_id})";
		}

		if ( ! empty( $this->custom_where ) ) {
			$where .= " AND {$this->custom_where} ";
		}

		global $product;
		if ( ! empty( $product ) && method_exists( $product, 'get_id' ) ) {
			global $wpdb;
			$where .= " AND {$wpdb->posts}.ID <> {$product->get_id()}";
		}
		return $where;
	}

	/**
	 * Modifies the JOIN clause of a SQL query for postmeta and term relationships.
	 *
	 * @param string $join The original JOIN clause of the SQL query.
	 * @return string The modified JOIN clause to include postmeta and term relationships.
	 * @since 1.0.0
	 */
	public function modify_join_query( $join ) {
		global $wpdb;
		$join .= " LEFT JOIN {$wpdb->term_relationships} AS RexRecomTerm0";
		$join .= " ON ({$wpdb->posts}.ID = RexRecomTerm0.object_id) ";

		if ( ! empty( $this->custom_where ) ) {
			if ( ! empty( $this->term_table_count ) ) {
				for ( $i = 1; $i <= $this->term_table_count; $i++ ) {
					$join .= " LEFT JOIN {$wpdb->term_relationships} AS RexRecomTerm{$i}";
					$join .= " ON ({$wpdb->posts}.ID = RexRecomTerm{$i}.object_id) ";
				}
			}

			if ( ! empty( $this->meta_table_count ) ) {
				for ( $i = 1; $i <= $this->meta_table_count; $i++ ) {
					$join .= " INNER JOIN {$wpdb->postmeta} AS RexRecomMeta{$i}";
					$join .= " ON ({$wpdb->posts}.ID = RexRecomMeta{$i}.post_id) ";
				}
			}
		}

		return $join;
	}

	/**
	 * Create custom where query with custom filters
	 *
	 * @param $filters
	 * @since 1.0.0
	 */
	protected function generate_where_query( $filters ) {
		$this->custom_where     = '';
		$this->meta_table_count = 0;
		$this->term_table_count = 0;

		foreach ( $filters as $key => $filter ) {
			if ( ! empty( $filter['if'] ) && ! empty( $filter['condition'] ) && isset( $filter['value'] ) ) {
				$if        = $this->get_column_name( $filter['if'] );
				$condition = $filter['condition'];
				$value     = $filter['value'];

				if ( $this->is_date_column( $if ) ) {
					if ( '_sale_price_dates_to' === $if ) {
						$value = strtotime( $filter['value'] . ' 23:59:59' );
					} else {
						$value = strtotime( $filter['value'] );
					}
				}

				$prefix = $this->get_method_prefix( $filter['if'] );

				if ( 'postterm_' === $prefix ) {
					++$this->term_table_count;
				} elseif ( 'postmeta_' === $prefix ) {
					++$this->meta_table_count;
				}

				$function = "{$prefix}{$condition}";

				if ( method_exists( $this, $function ) ) {
					$temp_where = $this->$function( $if, $value, 'inc' );
					if ( $temp_where ) {
						$this->custom_where .= $key > 0 && $this->custom_where ? " AND ({$temp_where})" : "({$temp_where})";
					}
				}
			}
		}
	}

	/**
	 * Checks if a given column is a date-related column.
	 *
	 * @param string $column The column name to check.
	 *
	 * @return bool True if the column is a date-related column, false otherwise.
	 * @since 1.0.0
	 */
	private function is_date_column( $column ) {
		return in_array(
			$column,
			array(
				'_sale_price_dates_from',
				'_sale_price_dates_to',
			),
			true
		);
	}

	/**
	 * Get method prefix for custom filter helper methods
	 *
	 * @param $column
	 * @return string
	 * @since 1.0.0
	 */
	private function get_method_prefix( $column ) {
		$meta_table_attr     = array(
			'manufacturer',
			'featured_image',
			'sku',
			'quantity',
			'_price',
			'_regular_price',
			'_sale_price',
			'weight',
			'width',
			'height',
			'length',
			'_sale_price_dates_from',
			'_sale_price_dates_to',
			'total_sales',
			'_stock_status',
			'_wc_average_rating',
			'_wc_review_count',
		);
		$term_rel_table_attr = array(
			'product_cats',
			'product_tags',
			'product_brands',
		);

		if ( in_array( $column, $meta_table_attr, true ) ) {
			return 'postmeta_';
		} elseif ( in_array( $column, $term_rel_table_attr, true ) || preg_match( '/^pa_/i', $column ) ) {
			return 'postterm_';
		}
		return 'post_';
	}

	/**
	 * Get database column name
	 *
	 * @param $column
	 * @return mixed|string
	 * @since 1.0.0
	 */
	private function get_column_name( $column ) {
		if ( preg_match( '/^pa_/i', $column ) ) {
			return 'term_taxonomy_id';
		}

		switch ( $column ) {
			case 'id':
				return 'ID';
			case 'title':
				return 'post_title';
			case 'description':
				return 'post_content';
			case 'short_description':
				return 'post_excerpt';
			case 'manufacturer':
				return '_wpfm_product_brand';
			case 'featured_image':
				return '_thumbnail_id';
			case 'availability':
				return '_stock_status';
			case 'sku':
				return '_sku';
			case 'quantity':
				return '_stock';
			case 'price':
				return '_regular_price';
			case 'sale_price':
				return '_sale_price';
			case 'weight':
				return '_weight';
			case 'width':
				return '_width';
			case 'height':
				return '_height';
			case 'length':
				return '_length';
			case 'rating_total':
				return '_wc_review_count';
			case 'rating_average':
				return '_wc_average_rating';
			case 'sale_price_dates_from':
				return '_sale_price_dates_from';
			case 'sale_price_dates_to':
				return '_sale_price_dates_to';
			case 'product_cats':
			case 'product_tags':
			case 'product_brands':
				return 'term_taxonomy_id';
			default:
				return $column;
		}
	}

	/**
	 * Get term id by slug or name
	 *
	 * @param $slug
	 * @param $taxonomy
	 * @return int|null
	 */
	private function get_term_id( $slug, $taxonomy ) {
		$term = get_term_by( 'slug', $slug, $taxonomy );
		return ! empty( $term->term_id ) ? $term->term_id : null;
	}

	/**
	 * Helper method to create custom where query for value `Contains` in `wp_post` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function post_contain( $column, $value, $operator ) {
		global $wpdb;
		$op = 'exc' === $operator ? 'NOT LIKE' : 'LIKE';
		return "{$wpdb->posts}.{$column} {$op} '%{$wpdb->esc_like( $value )}%'";
	}

	/**
	 * Helper method to create custom where query for value `Does not contain` in `wp_post` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function post_dn_contain( $column, $value, $operator ) {
		global $wpdb;
		$op = 'exc' === $operator ? 'LIKE' : 'NOT LIKE';
		return "{$wpdb->posts}.{$column} {$op} '%{$wpdb->esc_like( $value )}%'";
	}

	/**
	 * Helper method to create custom where query for value `Is equal to` in `wp_post` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function post_equal_to( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '<>' : '=';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return "{$wpdb->posts}.{$column} {$op} {$value}";
	}

	/**
	 * Helper method to create custom where query for value `Is not equal to` in `wp_post` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function post_nequal_to( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '=' : '<>';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return "{$wpdb->posts}.{$column} {$op} {$value}";
	}

	/**
	 * Helper method to create custom where query for value `Greater than` in `wp_post` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function post_greater_than( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '<' : '>';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return "{$wpdb->posts}.{$column} {$op} {$value}";
	}

	/**
	 * Helper method to create custom where query for value `Greater than or equal to` in `wp_post` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function post_greater_than_equal( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '<=' : '>=';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return "{$wpdb->posts}.{$column} {$op} {$value}";
	}

	/**
	 * Helper method to create custom where query for value `Less than` in `wp_post` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function post_less_than( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '>' : '<';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return "{$wpdb->posts}.{$column} {$op} {$value}";
	}

	/**
	 * Helper method to create custom where query for value `Less than or equal to` in `wp_post` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function post_less_than_equal( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '<=' : '>=';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return "{$wpdb->posts}.{$column} {$op} {$value}";
	}

	/**
	 * Helper method to create custom where query for value `Contains` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postmeta_contain( $column, $value, $operator ) {
		global $wpdb;
		$op = 'exc' === $operator ? 'NOT LIKE' : 'LIKE';
		return '(RexRecomMeta' . $this->meta_table_count . ".meta_key = '{$column}' AND RexRecomMeta" . $this->meta_table_count . ".meta_value {$op} '%{$wpdb->esc_like( $value )}%')";
	}

	/**
	 * Helper method to create custom where query for value `Does not contain` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postmeta_dn_contain( $column, $value, $operator ) {
		global $wpdb;
		$op = 'exc' === $operator ? 'LIKE' : 'NOT LIKE';
		return '(RexRecomMeta' . $this->meta_table_count . ".meta_key = '{$column}' AND RexRecomMeta" . $this->meta_table_count . ".meta_value {$op} '%{$wpdb->esc_like( $value )}%')";
	}

	/**
	 * Helper method to create custom where query for value `Is equal to` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postmeta_equal_to( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '<>' : '=';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return '(RexRecomMeta' . $this->meta_table_count . ".meta_key = '{$column}' AND RexRecomMeta" . $this->meta_table_count . ".meta_value {$op} {$value})";
	}

	/**
	 * Helper method to create custom where query for value `Is not equal to` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postmeta_nequal_to( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '=' : '<>';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return '(RexRecomMeta' . $this->meta_table_count . ".meta_key = '{$column}' AND RexRecomMeta" . $this->meta_table_count . ".meta_value {$op} {$value})";
	}

	/**
	 * Helper method to create custom where query for value `Greater than` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postmeta_greater_than( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '<' : '>';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return '(RexRecomMeta' . $this->meta_table_count . ".meta_key = '{$column}' AND RexRecomMeta" . $this->meta_table_count . ".meta_value {$op} {$value})";
	}

	/**
	 * Helper method to create custom where query for value `Greater than or equal to` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postmeta_greater_than_equal( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '<' : '>=';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return '(RexRecomMeta' . $this->meta_table_count . ".meta_key = '{$column}' AND RexRecomMeta" . $this->meta_table_count . ".meta_value {$op} {$value})";
	}

	/**
	 * Helper method to create custom where query for value `Less than` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postmeta_less_than( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '>=' : '<';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return '(RexRecomMeta' . $this->meta_table_count . ".meta_key = '{$column}' AND RexRecomMeta" . $this->meta_table_count . ".meta_value {$op} {$value})";
	}

	/**
	 * Helper method to create custom where query for value `Less than or equal to` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postmeta_less_than_equal( $column, $value, $operator ) {
		global $wpdb;
		$op    = 'exc' === $operator ? '>' : '<=';
		$value = is_numeric( $value ) ? $wpdb->esc_like( $value ) : "'{$wpdb->esc_like( $value )}'";
		return '(RexRecomMeta' . $this->meta_table_count . ".meta_key = '{$column}' AND RexRecomMeta" . $this->meta_table_count . ".meta_value {$op} {$value})";
	}

	/**
	 * Helper method to create custom where query for value `Contains` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postterm_contain( $column, $value, $operator ) {
		global $wpdb;
		$table_column = 'RexRecomTerm' . $this->term_table_count . ".{$column}";
		$op           = 'IN';
		if ( 'exc' === $operator ) {
			$op           = 'NOT IN';
			$value        = $this->get_term_product_ids( $value ); // Comma separated
			$table_column = "$wpdb->posts.ID";
		}
		$value = self::process_term_ids( $value );
		$value = ! empty( $value ) && is_array( $value ) ? implode( ', ', $value ) : $value;
		return $value ? "({$table_column} {$op} ({$value}))" : '';
	}

	/**
	 * Helper method to create custom where query for value `Does not contain` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postterm_dn_contain( $column, $value, $operator ) {
		global $wpdb;
		$table_column = 'RexRecomTerm' . $this->term_table_count . ".{$column}";
		$op           = 'IN';
		if ( 'inc' === $operator ) {
			$op           = 'NOT IN';
			$value        = $this->get_term_product_ids( $value ); // Comma separated
			$table_column = "$wpdb->posts.ID";
		}
		$value = self::process_term_ids( $value );
		$value = ! empty( $value ) && is_array( $value ) ? implode( ', ', $value ) : $value;
		return $value ? "({$table_column} {$op} ({$value}))" : '';
	}

	/**
	 * Helper method to create custom where query for value `Is equal to` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postterm_equal_to( $column, $value, $operator ) {
		global $wpdb;
		$table_column = 'RexRecomTerm' . $this->term_table_count . ".{$column}";
		$op           = 'IN';
		if ( 'exc' === $operator ) {
			$op           = 'NOT IN';
			$value        = $this->get_term_product_ids( $value ); // Comma separated
			$table_column = "$wpdb->posts.ID";
		}
		$value = self::process_term_ids( $value );
		$value = ! empty( $value ) && is_array( $value ) ? implode( ', ', $value ) : $value;
		return $value ? "({$table_column} {$op} ({$value}))" : '';
	}

	/**
	 * Helper method to create custom where query for value `Is not equal to` in `wp_postmeta` table
	 *
	 * @param string     $column Table column name.
	 * @param string|int $value Attribute value.
	 * @param string     $operator MySQL operator.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function postterm_nequal_to( $column, $value, $operator ) {
		global $wpdb;
		$table_column = 'RexRecomTerm' . $this->term_table_count . ".{$column}";
		$op           = 'IN';
		if ( 'inc' === $operator ) {
			$op           = 'NOT IN';
			$value        = $this->get_term_product_ids( $value ); // Comma separated
			$table_column = "$wpdb->posts.ID";
		}
		$value = self::process_term_ids( $value );
		$value = ! empty( $value ) && is_array( $value ) ? implode( ', ', $value ) : $value;
		return $value ? "({$table_column} {$op} ({$value}))" : '';
	}

	/**
	 * Get product ids [comma separated] by term id
	 *
	 * @param int|string $term_ids Taxonomy ID.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function get_term_product_ids( $term_ids ) {
		global $wpdb;
		if ( empty( $term_ids ) ) {
			return '';
		}
		$term_ids    = self::process_term_ids( $term_ids );
		$term_ids    = ! empty( $term_ids ) && is_array( $term_ids ) ? implode( ', ', $term_ids ) : $term_ids; // phpcs:ignore
		$product_ids = $wpdb->get_col( $wpdb->prepare( 'SELECT `object_id` FROM %i WHERE `term_taxonomy_id` IN (%1s)', array( $wpdb->term_relationships, $term_ids ) ) );  // phpcs:ignore
		return is_array( $product_ids ) && ! empty( $product_ids ) ? implode( ', ', $product_ids ) : '';
	}

	/**
	 * Processes an array of term IDs, ensuring they are integers and non-negative.
	 *
	 * @param array|null $term_ids An array of term IDs to process.
	 * @return array Returns the processed array of term IDs, ensuring they are integers and non-negative.
	 * @since 1.0.0
	 */
	private function process_term_ids( $term_ids ) {
		if ( ! empty( $term_ids ) && is_array( $term_ids ) ) {
			$term_ids = array_column( $term_ids, 'value' );
			$term_ids = array_map( 'absint', $term_ids );
		}
		return $term_ids ?? array();
	}

	/**
	 * Retrieves WooCommerce order IDs based on specified parameters.
	 *
	 * This function fetches distinct order IDs from WooCommerce order items and their metadata, considering optional item ID, from date, and to date criteria. It queries the database to retrieve completed or processing orders within a specified date range and optionally for a particular product ID.
	 *
	 * @param int|null    $item_id   (Optional) The product ID to filter orders. Default is null.
	 * @param string|null $from_date (Optional) Start date for filtering orders. Default is 31 days ago.
	 * @param string|null $to_date   (Optional) End date for filtering orders. Default is tomorrow.
	 *
	 * @return array An array containing distinct order IDs that meet the specified criteria.
	 *
	 * @since 1.0.0
	 */
	protected function get_wc_order_ids( $item_id = null, $from_date = null, $to_date = null ) {
		global $wpdb;

		$order_table   = $wpdb->posts;
		$id_col        = 'ID';
		$status_col    = 'post_status';
		$post_type_col = 'post_type';
		$date_col      = 'post_date';
		if ( 'yes' === get_option( 'woocommerce_custom_orders_table_enabled', 'no' ) ) {
			$order_table   = "{$wpdb->prefix}wc_orders";
			$status_col    = 'status';
			$post_type_col = 'type';
			$date_col      = 'date_created_gmt';
			$id_col        = 'id';
		}

		$from_date = ! empty( $from_date ) ? $from_date : date_i18n( 'Y-m-d', strtotime( '-31 days' ) );
		$to_date   = ! empty( $to_date ) ? $to_date : date_i18n( 'Y-m-d', strtotime( '+1 days' ) );

		$item_where = ! empty( $item_id ) ? $wpdb->prepare( 'AND order_item_meta.meta_key = %s AND order_item_meta.meta_value = %d', array( '_product_id', $item_id ) ) : '';

		// phpcs:disable
		return $wpdb->get_col( $wpdb->prepare( "
			    SELECT DISTINCT order_items.order_id
			    FROM %i as order_items
			    LEFT JOIN %i as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
			    LEFT JOIN %i AS orders ON order_items.order_id = orders.%1s
			    WHERE orders.%1s = %s
			    AND orders.%1s IN ('wc-completed', 'wc-processing')
			    AND order_items.order_item_type = %s
			    AND (orders.%1s > %s
			    AND orders.%1s < %s)
			    {$item_where}
			", [
			"{$wpdb->prefix}woocommerce_order_items",
			"{$wpdb->prefix}woocommerce_order_itemmeta",
			$order_table, $id_col, $post_type_col,
			'shop_order', $status_col, 'line_item',
			$date_col, $from_date, $date_col, $to_date
		] ) );
		// phpcs:enable
	}

	/**
	 * Retrieves an array of order item IDs excluding a specific product ID.
	 *
	 * This function takes an array of order IDs and a product ID as input and iterates through each order to extract order items. It excludes items with a matching product ID and counts the occurrences of other product IDs, creating an array of unique item IDs sorted by the number of occurrences in descending order.
	 *
	 * @param array    $order_ids  Array of order IDs.
	 * @param int|null $product_id The product ID to exclude from the item IDs list.
	 *
	 * @return array An array containing unique order item IDs sorted by occurrence, excluding the provided product ID.
	 *
	 * @since 1.0.0
	 */
	protected function get_order_item_ids( $order_ids, $product_id ) {
		$item_ids = array();
		if ( ! empty( $order_ids ) && is_array( $order_ids ) ) {
			foreach ( $order_ids as $order_id ) {
				$order = wc_get_order( $order_id );
				if ( $order instanceof \Automattic\WooCommerce\Admin\Overrides\Order ) {
					$order_items = $order->get_items( 'line_item' );
					if ( ! empty( $order_items ) && is_array( $order_items ) ) {
						foreach ( $order_items as $order_item ) {
							if ( $order_item instanceof \WC_Order_Item_Product ) {
								$item_data = $order_item->get_data();
								if ( ! empty( $item_data['product_id'] ) ) {
									$item_id = $item_data['product_id'];
									if ( (int) $product_id !== (int) $item_id ) {
										$item_ids[ $item_id ] = ! empty( $item_ids[ $item_id ] ) ? (int) $item_ids[ $item_id ] + 1 : 1;
									}
								}
							}
						}
					}
				}
			}
		}
		if ( ! empty( $item_ids ) && is_array( $item_ids ) ) {
			arsort( $item_ids, SORT_NUMERIC );
			$item_ids = array_keys( $item_ids );
		}
		return $item_ids ?? array();
	}

	/**
	 * Retrieves the total price of products based on provided product IDs.
	 *
	 * This function calculates the total price of products by querying the WordPress database postmeta table.
	 * It fetches and sums the '_price' meta values for the provided product IDs.
	 *
	 * @param array $product_ids An array of product IDs.
	 *
	 * @global object $wpdb WordPress database object.
	 *
	 * @return mixed|null The total price of the products or null if no price data is found.
	 *
	 * @since 1.0.0
	 */
	protected function get_total_price( $product_ids ) {
		if ( ! empty( $product_ids ) && is_array( $product_ids ) ) {
			//phpcs:disable
			global $wpdb;
			$product_ids = implode( ', ', array_fill( 0, count( $product_ids ), '%d' ) );
			return $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`meta_value`) FROM %i WHERE `post_id` IN($product_ids) AND `meta_key`='_price'", $wpdb->postmeta ) );
			//phpcs:enable
		}
		return 0;
	}

	/**
	 * Retrieves the settings of a recommendation engine from post meta.
	 *
	 * @param int $engine_id The ID of the recommendation engine.
	 *
	 * @return mixed The settings of the recommendation engine, or an empty string if not found.
	 *
	 * @since 1.0.0
	 */
	protected function get_engine_settings( $engine_id ) {
		return get_post_meta( $engine_id, '_rexprr_recommendation_engine_settings', true );
	}
}
