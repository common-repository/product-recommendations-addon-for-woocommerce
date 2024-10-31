<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\PostTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use RexTheme\RexProductRecommendationsForWoocommerce\Abstracts\InternalPostType;

class RecommendationEnginePostType extends InternalPostType {

	public $post_type = 'rex-product-engine';

	/**
	 * @inheritDoc
	 */
	public function get_labels() {
		return array( 'name' => _x( 'Recommendation Engine', 'post type general name', 'product-recommendations-addon-for-woocommerce' ) );
	}

	/**
	 * @inheritDoc
	 */
	public function get_post_type_args( $labels ) {
		return array(
			'labels'              => $labels,
			'public'              => false,
			'query_var'           => true,
			'can_export'          => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'show_in_rest'        => true,
			'rewrite'             => array( 'slug' => 'rexprr-product-recommendation' ),
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => null,
			'supports'            => array(),
		);
	}
}
