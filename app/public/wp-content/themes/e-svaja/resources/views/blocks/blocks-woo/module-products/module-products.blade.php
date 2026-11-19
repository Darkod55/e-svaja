<?php
$content  = App\Helpers()->getFieldDataOrBlockPlaceholder("content", $fields ?? '', $block ?? []);
$products = App\Helpers()->getFieldDataOrBlockPlaceholder("products", $fields ?? '', $block ?? []);
$products = is_array($products) ? $products : [];
$columns  = count($products);
?>

@if ($columns)
<section
  id="private-edit"
  class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-xl-y"
  style="--module-products-cols: {{ $columns }};"
>
  <div class="container-xxl p-clamp-x">
    {!! apply_filters('the_content_lite', $content) ?? '' !!}
    <div class="row">
      <div class="col-12 col-for-products">
        {{-- .woocommerce wrapper is required for star-rating CSS (scoped as .woocommerce .star-rating). --}}
        <div class="woocommerce">
          @php
            wc_set_loop_prop('columns', $columns);
            woocommerce_product_loop_start();

            foreach ($products as $product_id) {
              $product = wc_get_product((int) $product_id);

              if (! $product) {
                continue;
              }

              // Variations are catalog-hidden by default; still show when explicitly selected.
              if ($product->is_type('variation')) {
                if (! $product->variation_is_visible()) {
                  continue;
                }
              } elseif (! $product->is_visible()) {
                continue;
              }

              $post_object = get_post($product->get_id());
              if (! $post_object) {
                continue;
              }

              setup_postdata($GLOBALS['post'] = $post_object);
              $GLOBALS['product'] = $product;

              // Bypass content-product.php is_visible() for selected variations.
              $visibility_filter = static function ($visible, $id) use ($product) {
                return (int) $id === (int) $product->get_id() ? true : $visible;
              };
              add_filter('woocommerce_product_is_visible', $visibility_filter, 10, 2);

              wc_get_template_part('content', 'product');

              remove_filter('woocommerce_product_is_visible', $visibility_filter, 10);
            }

            woocommerce_product_loop_end();
            wp_reset_postdata();
          @endphp
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Desktop only: column count matches selected products. Mobile/tablet keep global grid (col-md-6). --}}
<style>
  @media (min-width: 992px) {
    .block-module-products .col-for-products .products > [class*="col-"] {
      flex: 0 0 auto;
      width: calc(100% / var(--module-products-cols, 4));
      max-width: calc(100% / var(--module-products-cols, 4));
    }
  }
</style>
@endif
