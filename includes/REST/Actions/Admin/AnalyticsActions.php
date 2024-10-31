<?php
/**
 * RexTheme\RexProductRecommendationsForWoocommerce\REST\Actions\Admin\AnalyticsActions
 *
 * This class provides methods to retrieve analytics data for WooCommerce orders.
 *
 * @since 1.0.3
 * @package RexTheme\RexProductRecommendationsForWoocommerce
 */

namespace RexTheme\RexProductRecommendationsForWoocommerce\REST\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class AnalyticsActions
 *
 * This class provides methods to retrieve analytics data for WooCommerce orders.
 *
 * @since 1.0.3
 */
class AnalyticsActions {


	/**
	 * Retrieves analytics data based on the provided engine ID and filter.
	 *
	 * @param string $engine_id Engine ID. Pass 'all' for all engines.
	 * @param string $filter The type of filter ('weekly', 'monthly', 'yearly').
	 * @return array Analytics data.
	 * @since 1.0.3
	 */
	public function get_analytics_data( $engine_id, $filter ) {
		$date_frame = $this->get_date_frame( $filter );
		$start_date = strtotime( $date_frame['start_date'] );
		$end_date   = strtotime( $date_frame['end_date'] );

		$date_query = array(
			'after'     => gmdate( 'Y-m-d', $start_date ),
			'before'    => gmdate( 'Y-m-d', $end_date ),
			'inclusive' => true,
		);

		$offset     = 0;
		$batch_size = 50;
		do {
			$orders = wc_get_orders(
				array(
					'limit'      => $batch_size,
					'offset'     => $offset,
					'date_query' => array( $date_query ),
					'status'     => array( 'wc-processing', 'wc-completed' ),
				)
			);

			$batch_orders_count = count( $orders );
			$gross_sales        = 0;
			$total_orders       = 0;
			$total_products     = 0;
			$week_days          = array();
			$months_days        = array();
			$year_months        = array();
			foreach ( $orders as $order ) {
				$items      = $order->get_items();
				$order_flag = false;
				foreach ( $items as $item ) {
					$item_meta = wc_get_order_item_meta( $item->get_id(), 'rexprr_item_data', true );
					if ( ! empty( $item_meta ) && is_array( $item_meta ) ) {
						if ( 'all' === $engine_id ) {
							if ( 'weekly' === $filter ) {
								$order_day = gmdate( 'M j', strtotime( $order->get_date_created()->date( 'Y-m-d' ) ) );
								if ( ! isset( $week_days[ $order_day ] ) ) {
									$week_days[ $order_day ] = 0;
								}
								$week_days[ $order_day ] += $item_meta['price_total'];
							} elseif ( 'monthly' === $filter ) {
								$order_day = gmdate( 'j', strtotime( $order->get_date_created()->date( 'Y-m-d' ) ) );
								if ( ! isset( $months_days[ $order_day ] ) ) {
									$months_days[ $order_day ] = 0;
								}
								$months_days[ $order_day ] += $item_meta['price_total'];
							} elseif ( 'yearly' === $filter ) {
								$order_day = gmdate( 'M', strtotime( $order->get_date_created()->date( 'Y-m-d' ) ) );
								if ( ! isset( $year_months[ $order_day ] ) ) {
									$year_months[ $order_day ] = 0;
								}
								$year_months[ $order_day ] += $item_meta['price_total'];
							}
							$order_flag      = true;
							$gross_sales    += $item_meta['price_total'];
							$total_products += $item_meta['quantity'];
						} elseif ( isset( $item_meta['rexprr_engine_id'] ) && intval( $engine_id ) === $item_meta['rexprr_engine_id'] ) {
							$order_flag = true;
							if ( 'weekly' === $filter ) {
								$order_day = gmdate( 'M j', strtotime( $order->get_date_created()->date( 'Y-m-d' ) ) );
								if ( ! isset( $week_days[ $order_day ] ) ) {
									$week_days[ $order_day ] = 0;
								}
								$week_days[ $order_day ] += $item_meta['price_total'];
							} elseif ( 'monthly' === $filter ) {
								$order_day = gmdate( 'j', strtotime( $order->get_date_created()->date( 'Y-m-d' ) ) );
								if ( ! isset( $months_days[ $order_day ] ) ) {
									$months_days[ $order_day ] = 0;
								}
								$months_days[ $order_day ] += $item_meta['price_total'];
							} elseif ( 'yearly' === $filter ) {
								$order_day = gmdate( 'M', strtotime( $order->get_date_created()->date( 'Y-m-d' ) ) );
								if ( ! isset( $year_months[ $order_day ] ) ) {
									$year_months[ $order_day ] = 0;
								}
								$year_months[ $order_day ] += $item_meta['price_total'];
							}
							$gross_sales    += $item_meta['price_total'];
							$total_products += $item_meta['quantity'];
						}
					}
				}
				if ( $order_flag ) {
					++$total_orders;
				}
			}
			$offset += $batch_size;
		} while ( $batch_orders_count === $batch_size );
		return $this->generate_result_array(
			$total_orders,
			$gross_sales,
			$total_products,
			$filter,
            $start_date,
			$end_date,
			$engine_id,
			$week_days,
			$months_days,
			$year_months
		);
	}

	/**
	 * Generates an array of result data based on various parameters.
	 *
	 * Generates an array containing total orders, total sales, total products, and line chart data
	 * based on the specified parameters such as filter, start date, end date, etc.
	 *
	 * @param int    $total_orders The total number of orders.
	 * @param float  $gross_sales The total gross sales.
	 * @param int    $total_products The total number of products.
	 * @param string $filter The filter type ('weekly', 'monthly', or 'yearly').
	 * @param string $start_date The start date for filtering.
	 * @param string $end_date The end date for filtering.
	 * @param string $engine_id The engine ID for filtering.
	 * @param array  $week_days An array containing days of the week.
	 * @param array  $months_days An array containing days of the month.
	 * @param array  $year_months An array containing months of the year.
	 * @return array An array of result data including total orders, total sales, total products,
	 *               and line chart data.
	 *
	 * @since 1.0.3
	 */
	private function generate_result_array( $total_orders, $gross_sales, $total_products, $filter, $start_date = '', $end_date = '', $engine_id = '', $week_days = array(), $months_days = array(), $year_months = array() ) {
		$line_chart_data = array();
		if ( 'weekly' === $filter ) {
			$line_chart_data = $this->get_weekly_data( $start_date, $week_days );
		} elseif ( 'monthly' === $filter ) {
			$line_chart_data = $this->get_monthly_data( $months_days );
		} elseif ( 'yearly' === $filter ) {
			$line_chart_data = $this->get_yearly_data( $year_months );
		}
		return array(
			'total_orders'    => $total_orders,
			'total_sales'     => $this->price_format_with_wc_currency( $gross_sales ),
			'total_products'  => $total_products,
			'line_chart_data' => $line_chart_data,
		);
	}

	/**
	 * Formats data for a line chart.
	 *
	 * Formats the given result and days into a format suitable for display in a line chart.
	 *
	 * @param array $result The result data to be formatted.
	 * @param array $days An array containing days or periods.
	 * @return array Formatted data for a line chart.
	 *
	 * @since 1.0.3
	 */
	public function get_formatted_line_chart( $result, $days ) {
		$values = array();
		$label  = array();
		if ( is_array( $result ) && count( $result ) > 0 ) {
			foreach ( $result as $key => $value ) {
				if ( array_key_exists( $key, $days ) ) {
					$days[ $key ] = $value;
				}
			}
		}
		if ( is_array( $days ) && count( $days ) > 0 ) {
			foreach ( $days as $key => $value ) {
				$label[]  = $key;
				$values[] = $value;
			}
		}

		return array(
			'amount' => array(
				'label'  => $label,
				'values' => $values,
				'max'    => max( $values ),
				'min'    => min( $values ),
			),
		);
	}


	/**
	 * Retrieves weekly data formatted for a line chart.
	 *
	 * Retrieves data for the specified weekly days and formats it for display in a line chart.
	 *
	 * @param string $start_date The start date of the week.
	 * @param array  $week_days An array containing days of the week.
	 * @return array Formatted data for a line chart.
	 *
	 * @since 1.0.3
	 */
	public function get_weekly_data( $start_date, $week_days ) {
		$days_array = $this->get_week_days( $start_date );
		return $this->get_formatted_line_chart( $week_days, $days_array );
	}


	/**
	 * Retrieves monthly data formatted for a line chart.
	 *
	 * Retrieves data for the specified monthly days and formats it for display in a line chart.
	 *
	 * @param array $monthly_days An array containing days of the month.
	 * @return array Formatted data for a line chart.
	 *
	 * @since 1.0.3
	 */
	public function get_monthly_data( $monthly_days ) {
		$monthly_days_array = $this->get_monthly_days();
		return $this->get_formatted_line_chart( $monthly_days, $monthly_days_array );
	}

	/**
	 * Retrieves yearly data formatted for a line chart.
	 *
	 * Retrieves data for the specified year months and formats it for display in a line chart.
	 *
	 * @param array $year_months An array containing months of the year.
	 * @return array Formatted data for a line chart.
	 *
	 * @since 1.0.3
	 */
	public function get_yearly_data( $year_months ) {
		$months_array = $this->get_months_array();
		return $this->get_formatted_line_chart( $year_months, $months_array );
	}

	/**
	 * Get an array with days of the week starting from the provided start of the week.
	 *
	 * @param int $start_of_week Unix timestamp representing the start of the week.
	 * @return array Associative array with days of the week as keys and initial values as 0.
	 * @since 1.0.3
	 */
	public function get_week_days( $start_of_week ) {
		$week_days = array();
		$interval  = 0;

		while ( $interval < 7 ) {
			$label               = gmdate( 'M j', strtotime( '+' . $interval . ' day', $start_of_week ) );
			$week_days[ $label ] = 0;
			++$interval;
		}

		return $week_days;
	}

	/**
	 * Get an array with days of the current month as keys and initial values as 0.
	 *
	 * @return array Associative array with days of the current month as keys and initial values as 0.
	 * @since 1.0.3
	 */
	public function get_monthly_days() {
		$current_datetime = current_datetime();
		$current_month    = date_format( $current_datetime, 'n' );

		if ( 2 === (int) $current_month ) {
			$days = 28;
		} elseif ( 8 === (int) $current_month || ( 0 !== (int) $current_month % 2 && 9 > (int) $current_month ) || ( 0 === (int) $current_month % 2 && 9 < (int) $current_month ) ) {
			$days = 31;
		} else {
			$days = 30;
		}

		$monthly_days = array();

		for ( $day = 1; $day <= $days; $day++ ) {
			$monthly_days[ $day ] = 0;
		}

		return $monthly_days;
	}

	/**
	 * Get an array with month names as keys and initial values as 0.
	 *
	 * @return array Associative array with month names as keys and initial values as 0.
	 * @since 1.0.3
	 */
	public static function get_months_array() {
		return array(
			'Jan' => 0,
			'Feb' => 0,
			'Mar' => 0,
			'Apr' => 0,
			'May' => 0,
			'Jun' => 0,
			'Jul' => 0,
			'Aug' => 0,
			'Sep' => 0,
			'Oct' => 0,
			'Nov' => 0,
			'Dec' => 0,
		);
	}


	/**
	 * Checks if WooCommerce plugin is active.
	 *
	 * @return bool True if WooCommerce is active, false otherwise.
	 * @since 1.0.3
	 */
	public function is_wc_active() {
		if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) { //phpcs:ignore
			return true;
		} elseif ( function_exists( 'is_plugin_active' ) ) {
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Formats price with WooCommerce currency.
	 *
	 * @param float $price The price to format.
	 * @return string Formatted price with currency symbol.
	 * @since 1.0.3
	 */
	public function price_format_with_wc_currency( $price ) {
		$is_active = $this->is_wc_active();
		$format    = '';
		if ( $is_active ) {
			$symbol             = html_entity_decode(get_woocommerce_currency_symbol()); //phpcs:ignore
			$currency_pos       = get_option( 'woocommerce_currency_pos', 'left' );
			$minor_unit         = wc_get_price_decimals();
			$decimal_separator  = wc_get_price_decimal_separator();
			$thousand_separator = wc_get_price_thousand_separator();

			$price = number_format( (float) ( $price ), $minor_unit, $decimal_separator, $thousand_separator );

			switch ( $currency_pos ) {
				case 'left_space':
					$format = $symbol . ' ' . $price;
					break;
				case 'left':
					$format = $symbol . $price;
					break;
				case 'right_space':
					$format = $price . ' ' . $symbol;
					break;
				case 'right':
					$format = $price . $symbol;
					break;
			}
		}
		return $format;
	}


	/**
	 * Get the date and time of the store's first order.
	 *
	 * @since 1.0.3
	 *
	 * @return string The formatted date and time of the first order or a default value ('1970-01-01 00:00:00').
	 */
	public function get_store_first_order_date() {
		return '1970-01-01 00:00:00';
	}

	/**
	 * Get the start and end dates based on the specified date frame.
	 *
	 * @since 1.0.3
	 *
	 * @param string $date The date frame identifier ('all', 'weekly', 'monthly', 'yearly').
	 *
	 * @return array An associative array containing the start and end dates in the format:
	 *               - 'start_date' The start date.
	 *               - 'end_date'   The end date.
	 */
	public function get_date_frame( $filter ) {
		$current_date = new \DateTime();
		$start_date   = '';
		$end_date     = '';
		if ( 'all' === $filter ) {
			$start_date = $this->get_store_first_order_date();
			$end_date   = $current_date->format( 'Y-m-d 23:59:59' );
		} elseif ( 'weekly' === $filter ) {
			$start_of_week_option = get_option( 'start_of_week' );
			$current_day          = gmdate( 'w' );
			$offset               = ( $current_day - $start_of_week_option + 7 ) % 7;
			$start_of_week        = strtotime( '-' . $offset . ' days', strtotime( 'today' ) );
			$start_date           = gmdate( 'Y-m-d 00:00:00', $start_of_week );
			$end_date             = gmdate( 'Y-m-d 23:59:59', strtotime( '+6 days', $start_of_week ) );
		} elseif ( 'monthly' === $filter ) {
			$start_date = gmdate( 'Y-m-01 00:00:00' );
			$end_date   = gmdate( 'Y-m-t 23:59:59' );
		} elseif ( 'yearly' === $filter ) {
			$current_year = gmdate( 'Y' );
			$start_date   = gmdate( "$current_year-01-01 00:00:00" );
			$end_date     = gmdate( "$current_year-12-31 23:59:59" );
		}

		return array(
			'start_date' => $start_date,
			'end_date'   => $end_date,
		);
	}
}
