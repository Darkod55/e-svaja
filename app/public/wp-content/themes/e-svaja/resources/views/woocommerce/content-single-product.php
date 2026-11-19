<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'container-xxl p-clamp', $product ); ?>>
  <div class="row">
    <div class="col-12">
      <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
    </div>
  </div>
  <div class="row g-5">
    <div class="col-md-6 p-rel">
      <?php
      /**
       * Hook: woocommerce_before_single_product_summary.
       *
       * @hooked woocommerce_show_product_sale_flash - 10
       * @hooked woocommerce_show_product_images - 20
       */
      do_action( 'woocommerce_before_single_product_summary' );
      ?>
    </div>
    <div class="col-md-6">
      <div class="summary entry-summary">
        <?php
        /**
         * Hook: woocommerce_single_product_summary.
         *
         * @hooked woocommerce_template_single_title - 5
         * @hooked woocommerce_template_single_rating - 10
         * @hooked woocommerce_template_single_price - 10
         * @hooked woocommerce_template_single_excerpt - 20
         * @hooked woocommerce_template_single_add_to_cart - 30
         * @hooked woocommerce_template_single_meta - 40
         * @hooked woocommerce_template_single_sharing - 50
         * @hooked WC_Structured_Data::generate_product_data() - 60
         */
        do_action( 'woocommerce_single_product_summary' );
        ?>
      </div>
    </div>
  </div>
  <div class="row g-5">
    <div class="col-md-6">
      <?php 
        if ($product->get_gallery_image_ids() ?? false) {
          echo '<h2>Product Gallery</h2>';
          echo '<div class="product-imgs mb-5">';
          foreach ($product->get_gallery_image_ids() as $img) {
            echo "<a class='e-svaja-lightbox' href='" . wp_get_attachment_image_url($img, 'full') . "' data-gallery='e-svaja-gallery'>";
            echo wp_get_attachment_image($img, 'full', '', ['class' => 'w-100']);
            echo "</a>";
          }
          echo "</div>";
        }

		// Display full description
		if ($product->get_description() ?? false) : ?>
		  <div class="prod-desc">
			<?php the_content(); ?>
		  </div>
		<?php endif; ?>
    </div>
    <div class="col-md-6">
      <?php
      /**
       * Hook: woocommerce_after_single_product_summary.
       *
       * @hooked woocommerce_output_product_data_tabs - 10
       * @hooked woocommerce_upsell_display - 15
       * @hooked woocommerce_output_related_products - 20
       */
      do_action( 'woocommerce_after_single_product_summary' );
      ?>
    </div>
  </div>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
<div class="related-products container-xxl p-clamp">
  <div class="row">
    <div class="col-12 col-for-products">
      <?= do_shortcode( '[related_products limit="4"]' ) ?>
    </div>
  </div>
</div>