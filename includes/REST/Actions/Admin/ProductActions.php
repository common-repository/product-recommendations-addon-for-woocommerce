<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\REST\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ProductActions {

	/**
	 * Searches for product taxonomies based on keywords.
	 *
	 * @param string $keywords The keywords to search for in category names.
	 * @return array An array containing term_id as 'value' and name as 'label' for matching categories.
	 * @since 1.0.0
	 */
	private static function search_taxonomies( $keywords, $taxonomy ) {
		global $wpdb;
		if ( ! empty( $keywords ) ) {
			$query = $wpdb->prepare( '
			SELECT t.term_id AS `value`, t.name AS `label`
			FROM %i AS t INNER JOIN %i AS tx ON t.term_id = tx.term_id
			WHERE tx.taxonomy = %s AND t.name LIKE %s
			',
				$wpdb->terms,
				$wpdb->term_taxonomy,
				$taxonomy,
				"%{$wpdb->esc_like( $keywords )}%"
			);

			return $wpdb->get_results( $query, ARRAY_A ); //phpcs:ignore
		}
		return array();
	}

	/**
	 * Retrieves product categories based on keywords.
	 *
	 * @param string $keywords The keywords to search for in category names.
	 * @return array An array containing term_id as 'value' and name as 'label' for matching categories.
	 * @since 1.0.0
	 */
	public static function get_categories( $keywords ) {
		if ( ! empty( $keywords ) ) {
			return self::search_taxonomies( $keywords, 'product_cat' );
		}
		return array();
	}

	/**
	 * Retrieves product tags based on keywords.
	 *
	 * @param string $keywords The keywords to search for in category names.
	 * @return array An array containing term_id as 'value' and name as 'label' for matching categories.
	 * @since 1.0.0
	 */
	public static function get_tags( $keywords ) {
		if ( ! empty( $keywords ) ) {
			return self::search_taxonomies( $keywords, 'product_tag' );
		}
		return array();
	}
}
