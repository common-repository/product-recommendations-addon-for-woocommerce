<?php

namespace RexTheme\RexProductRecommendationsForWoocommerce\Abstracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class InternalPostType
 *
 * @package RexTheme\RexDynamicDiscount\Abstracts
 */
abstract class InternalPostType {

	/**
	 * The internal post type name.
	 *
	 * @var string
	 */
	public $post_type = '';

	/**
	 * InternalPostType constructor.
	 * Hooks into the WordPress initialization to register the custom post type.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Registers the custom post type with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$labels = $this->get_labels();
		$args   = $this->get_post_type_args( $labels );
		register_post_type( $this->post_type, $args );
	}

	/**
	 * Abstract method to be implemented by subclasses to provide labels for the custom post type.
	 *
	 * @return array An array of labels for the custom post type.
	 *
	 * @since 1.0.0
	 */
	abstract public function get_labels();

	/**
	 * Abstract method to be implemented by subclasses to provide arguments for registering the custom post type.
	 *
	 * @param array $labels An array of labels for the custom post type.
	 *
	 * @return array An array of arguments for registering the custom post type.
	 *
	 * @since 1.0.0
	 */
	abstract public function get_post_type_args( $labels );
}
