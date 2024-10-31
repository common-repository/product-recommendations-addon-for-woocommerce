<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\REST\Actions\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class RecommendationActions {


	/**
	 * Creates a new engine if the visibility location is not '-1'.
	 *
	 * If the provided visibility location is not '-1', formats the engine data and inserts it as a new post.
	 * Also updates the post meta for the recommendation engine visibility location.
	 *
	 * @param array $engine_data The data to create the engine.
	 *
	 * @return int|null Returns the ID of the created engine if successful; otherwise, returns null.
	 * @since 1.0.0
	 */
	public static function create_engine( $engine_data ) {
		$metadata = $engine_data['metadata'] ?? array();

		if (
			! empty( $metadata['engine_visibility_location']['value'] )
			&& '-1' !== $metadata['engine_visibility_location']['value']
			&& ! empty( $metadata['engine_settings'] )
		) {
			$engine_data = self::format_engine_data( $engine_data );

			if ( ! empty( $engine_data ) ) {
				$engine_id = wp_insert_post( $engine_data );
				if ( ! empty( $engine_id ) ) {
					self::update_engine_metadata( $engine_id, $metadata );
				}
			}
		}
		return $engine_id ?? null;
	}

	/**
	 * Updates an existing engine if the visibility location is not '-1'.
	 *
	 * If the provided visibility location is not '-1', formats the engine data and updates the existing post.
	 * Also updates the post meta for the recommendation engine visibility location.
	 *
	 * @param int   $engine_id   The ID of the engine to update.
	 * @param array $engine_data The updated data for the engine.
	 *
	 * @return int|null Returns the updated engine ID if successful; otherwise, returns null.
	 * @since 1.0.0
	 */
	public static function update_engine( $engine_id, $engine_data ) {
		$metadata = $engine_data['metadata'] ?? array();

		if (
			! empty( $engine_id )
			&& ! empty( $metadata['engine_visibility_location']['value'] )
			&& '-1' !== $metadata['engine_visibility_location']['value']
			&& ! empty( $metadata['engine_settings'] )
		) {
			$engine_data = self::format_engine_data( $engine_data );
			if ( ! empty( $engine_data ) ) {
				$engine_data['ID'] = $engine_id;
				$engine_id         = wp_update_post( $engine_data );
				self::update_engine_metadata( $engine_id, $metadata );
				return $engine_id;
			}
		}
		return null;
	}

	/**
	 * Updates metadata for a recommendation engine with the given engine ID.
	 *
	 * @param int   $engine_id The ID of the recommendation engine to update.
	 * @param array $metadata  An associative array of metadata to be updated.
	 *
	 * @since 1.0.0
	 */
	private static function update_engine_metadata( $engine_id, $metadata ) {
		if ( ! empty( $engine_id ) && ! empty( $metadata ) && is_array( $metadata ) ) {
			foreach ( $metadata as $key => $meta_value ) {
				update_post_meta( $engine_id, "_rexprr_recommendation_{$key}", $meta_value );
			}
		}
	}

	/**
	 * Formats the provided data into an array suitable for creating or updating the recommendation engine post.
	 *
	 * @param array $data The data to format for the recommendation engine post.
	 *
	 * @return array Returns an array with formatted post data or an empty array if data requirements are not met.
	 * @since 1.0.0
	 */
	private static function format_engine_data( $data ) {
		if ( ! empty( $data['engine_type']['value'] ) && '-1' !== $data['engine_type']['value'] && ! empty( $data['engine_title'] ) ) {
			$engine_filters = ! empty( $data['engine_filters'] ) && is_array( $data['engine_filters'] ) ? serialize( $data['engine_filters'] ) : ''; // phpcs:ignore

			return array(
				'post_type'    => 'rex-product-engine',
				'post_status'  => ! empty( $data['engine_status'] ) ? 'publish' : 'draft',
				'post_excerpt' => serialize( $data['engine_type'] ), // phpcs:ignore
				'post_title'   => $data['engine_title'],
				'post_content' => $engine_filters,
			);
		}
		return array();
	}

	/**
	 * Retrieves specific engine data via the REST API.
	 *
	 * Retrieves engine data based on the provided engine ID from the REST API request.
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 *
	 * @return \WP_REST_Response|null Returns a REST API response containing the retrieved engine data if successful,
	 *                               otherwise returns null.
	 * @since 1.0.0
	 */
	public static function get_engine_data( $id ) {
		global $wpdb;
		// phpcs:disable
		return $wpdb->get_row(
			$wpdb->prepare(
				'
				SELECT `post_title` AS `engine_title`,
				`post_status` AS `engine_status`,
				`post_excerpt` AS `engine_type`,
				`post_content` AS `engine_filters`,
				MAX(CASE WHEN `meta_key` = %s THEN `meta_value` END) AS `visibility_location`,
    			MAX(CASE WHEN `meta_key` = %s THEN `meta_value` END) AS `engine_settings`
				FROM %1s JOIN %1s ON `ID`=`post_id`
				WHERE `ID`=%d
				',
				array(
					'_rexprr_recommendation_engine_visibility_location',
					'_rexprr_recommendation_engine_settings',
					$wpdb->posts,
					$wpdb->postmeta,
					$id,
				)
			),
			ARRAY_A
		);
		// phpcs:enable
	}

	/**
	 * Retrieves all engine data via the REST API.
	 *
	 * Retrieves all available engine data via the REST API.
	 *
	 * @return array Returns a REST API response containing all engine data if successful,
	 *                               otherwise returns null.
	 * @since 1.0.0
	 */
	public static function get_all_engine_data( $per_page = 5, $offset = 0 ) {
		global $wpdb;
		//phpcs:disable
		$data = $wpdb->get_results(
			$wpdb->prepare(
				'
                SELECT `ID` AS `id`, `post_title` AS `engine_title`,
                `post_excerpt` AS `engine_type`,
                `post_content` AS `engine_filter`,
                `post_status` AS `engine_status`,
                `meta_value` AS `visibility_location`
                FROM %i JOIN %i ON `ID`=`post_id`
                WHERE `meta_key`=%s AND `post_type` = %s
                LIMIT %d, %d
                ',
				array(
					$wpdb->posts,
					$wpdb->postmeta,
					'_rexprr_recommendation_engine_visibility_location',
					'rex-product-engine',
					$offset,
					$per_page,
				)
			),
			ARRAY_A
		);

		$total_engine = $wpdb->get_var(
			$wpdb->prepare(
				'
				SELECT COUNT(DISTINCT `ID`) FROM %1s
				WHERE `post_type` = %s
				',
				array( $wpdb->posts, 'rex-product-engine' )
			)
		);
		//phpcs:enable

		return array(
			'data'  => $data,
			'total' => $total_engine,
		);
	}

	/**
	 * Deletes specific engine data via the REST API.
	 *
	 * Deletes engine data based on the provided engine ID from the REST API request.
	 *
	 * @param \WP_REST_Request $request The REST API request object.
	 *
	 * @return array|false|\WP_Post|null Returns a REST API response indicating the success of the delete operation if successful,
	 *                               otherwise returns null.
	 * @since 1.0.0
	 */
	public static function delete_engine( $engine_id ) {
		if ( ! empty( $engine_id ) ) {
			return wp_delete_post( $engine_id );
		}
		return false;
	}

	/**
	 * Unserialize specific fields within the given engine data array and its nested data array.
	 *
	 * @param array $engine_data The engine data array containing serialized fields.
	 *
	 * @return array The engine data array with specified fields unserialized, including nested data arrays.
	 * @since 1.0.0
	 */
	public static function get_unserialized_data( $engine_data ) {
		if ( ! empty( $engine_data['engine_type'] ) ) {
			$engine_data['engine_type'] = @unserialize( $engine_data['engine_type'] ); // phpcs:ignore
		}
		if ( ! empty( $engine_data['visibility_location'] ) ) {
			$engine_data['visibility_location'] = @unserialize( $engine_data['visibility_location'] ); // phpcs:ignore
		}
		if ( ! empty( $engine_data['engine_filters'] ) ) {
			$engine_data['engine_filters'] = @unserialize( $engine_data['engine_filters'] ); // phpcs:ignore
		}
		if ( ! empty( $engine_data['engine_settings'] ) ) {
			$engine_data['engine_settings'] = @unserialize( $engine_data['engine_settings'] ); // phpcs:ignore
		}

		if ( ! empty( $engine_data['data'] ) && is_array( $engine_data['data'] ) ) {
			foreach ( $engine_data['data'] as $key => $data ) {
				if ( ! empty( $engine_data['data'][ $key ]['engine_type'] ) ) {
					$engine_data['data'][ $key ]['engine_type'] = @unserialize( $engine_data['data'][ $key ]['engine_type'] ); // phpcs:ignore
				}
				if ( ! empty( $engine_data['data'][ $key ]['visibility_location'] ) ) {
					$engine_data['data'][ $key ]['visibility_location'] = @unserialize( $engine_data['data'][ $key ]['visibility_location'] ); // phpcs:ignore
				}
				if ( ! empty( $engine_data['data'][ $key ]['engine_filter'] ) ) {
					$engine_data['data'][ $key ]['engine_filter'] = @unserialize( $engine_data['data'][ $key ]['engine_filter'] ); // phpcs:ignore
				}
			}
		}
		return $engine_data;
	}

	/**
	 * Retrieves all recommendation engines with their IDs and titles.
	 *
	 * @return array An array containing IDs as keys and titles as values.
	 *
	 * @since 1.0.3
	 */
	public static function get_all_engines() {
		global $wpdb;
		//phpcs:disable
		$all_engines     = $wpdb->get_results(
			$wpdb->prepare(
				'
                SELECT `ID` AS `id`, `post_title` AS `engine_title`
                FROM %i where `post_type` = %s and `post_status` = %s
                ',
				array(
					$wpdb->posts,
					'rex-product-engine',
					'publish',
				)
			),
			ARRAY_A
		);
		//phpcs:enable
		$formatted_array = array();
		if ( ! empty( $all_engines ) && count( $all_engines ) > 0 ) {
			foreach ( $all_engines as $key => $engine ) {
				$formatted_array[ $engine['id'] ] = $engine['engine_title'];
			}
		}
		return array( 'data' => $formatted_array );
	}
}
