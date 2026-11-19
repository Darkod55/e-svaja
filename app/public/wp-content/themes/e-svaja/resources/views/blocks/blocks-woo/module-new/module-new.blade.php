<?php 
$content      = App\Helpers()->getFieldDataOrBlockPlaceholder("content", $fields ?? '', $block ?? []);
$products     = App\Helpers()->getFieldDataOrBlockPlaceholder("products", $fields ?? '', $block ?? []);
$bg_image     = App\Helpers()->getFieldDataOrBlockPlaceholder("bg_image", $fields ?? '', $block ?? []);
?>
{{--
@if ($products ?? false)
<section class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-xl-y">
  <div class="container-xxl p-clamp-x p-rel">
    @if ($bg_image ?? false)
      {!! wp_get_attachment_image($bg_image, 'full', '', ['class' => 'bg-img-cover w-100 zi--1']) !!}
    @endif
    <div class="mw-lg-40 ms-md-auto text-md-end mb-5 anm8 d-flex-column ai-start align-items-md-end p-rel zi-1">
      {!! apply_filters('the_content_lite', $content) ?? '' !!}
    </div>
    <div class="row g-4">
      @foreach ($products as $product)
        <div class="col-md-6 col-lg-4">
          <div class="card card-new-products anm8">
            <div class="card-img ovf-hidden rounded">
              @php 
                $product_full = new WC_product($product);
                $attachment_ids = $product_full->get_gallery_image_ids();
              @endphp
              @if ($attachment_ids[0] ?? false) 
                {!! wp_get_attachment_image($attachment_ids[0], 'full', '', ['class' => 'w-100']) !!}
              @else 
                {!! wp_get_attachment_image($product_full->image_id, 'full', '', ['class' => 'w-100']) !!}
              @endif
            </div>
            <a href="{!! get_the_permalink($product) !!}" class="mt-4 stretched-link h6 text-dark">{!! get_the_title($product) !!}</a>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif
--}}