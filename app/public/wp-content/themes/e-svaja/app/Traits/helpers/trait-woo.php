<?php

namespace App\Helpers\Traits;

trait woo
{
  public function addCatName() {
    global $product;

    if (! $product) {
      return;
    }

    // Variations inherit categories from the parent product.
    $product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();
    $categories = wp_get_post_terms($product_id, 'product_cat');

    if (!empty($categories) && ! is_wp_error($categories)) {
        $category = $categories[0]; // Assuming only one category per product
        echo '<div class="prod-cat mb-2 text-black">' . esc_html($category->name) . '</div>';
      }
    }
}