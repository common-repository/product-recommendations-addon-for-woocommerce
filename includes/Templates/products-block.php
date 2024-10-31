<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( $product_ids ) : ?>

	<section class="rex-product-recommendations-product-block products
    <?php
    if ( ! empty( $custom_class ) ) :
		echo esc_attr( $custom_class );
	endif;
	?>
    ">

		<?php if ( $engine_title ) : ?>
			<h2><?php echo esc_html( $engine_title ); ?></h2>
		<?php endif; ?>

		<?php do_action( 'rexprr_before_product_loop' ); ?>

		<?php woocommerce_product_loop_start(); ?>

		<?php foreach ( $product_ids as $product_id ) : ?>

			<?php
			$post_object = get_post( $product_id );

			setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

			wc_get_template_part( 'content', 'product' );
			?>

		<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

		<?php do_action( 'rexprr_after_product_loop' ); ?>

	</section>
		<?php
endif;

wp_reset_postdata();
