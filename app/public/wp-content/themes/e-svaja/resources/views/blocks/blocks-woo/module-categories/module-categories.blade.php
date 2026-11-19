<?php 
$categories = get_terms( 'product_cat');
?>
@if ($categories ?? false)
<section class="block-{!! App\Helpers()->kebabCase($block['title']) ?? 'categories' !!} ovf-hidden p-clamp-xl-y">
  <div class="container-xxl p-clamp-x">
    <h2>Find what you are looking for</h2>
    <div class="row p-rel">
      <div id="icon-slick-left">@svgIcon('chevron-left|20|20')</div>  
      <div id="slider-categories-{{ $slider_id }}" class="slider-cats col-12 col-for-products{{ (function_exists('is_front_page') && is_front_page()) ? ' home-cat-scroll' : '' }}" @if (!(function_exists('is_front_page') && is_front_page())) data-slick="{{ json_encode(['slidesToShow' => 6, 'slidesToScroll' => 1,'dots' => false, 'arrows' => false, 'responsive' => [['breakpoint' => 1024, 'settings' => ['slidesToShow' => 3]], ['breakpoint' => 767, 'settings' => ['slidesToShow' => 1]]]]) }}" @endif>
        @foreach ($categories as $cat)
          @php
            $thumb_id = get_woocommerce_term_meta($cat->term_id, 'thumbnail_id', true);
            $thumb_alt = $thumb_id ? trim((string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true)) : '';
            $thumb_attrs = [];
            if ($thumb_alt === '') {
              $thumb_attrs['alt'] = trim($cat->name . ' collection');
            }
            if (function_exists('is_front_page') && is_front_page()) {
              $thumb_attrs['loading'] = 'lazy';
              $thumb_attrs['decoding'] = 'async';
              $thumb_attrs['fetchpriority'] = 'low';
              // Six-up in the xxl container is ~220px; 33vw at tablet; nearly full width on a phone.
              $thumb_attrs['sizes'] = '(min-width: 1024px) 220px, (min-width: 768px) 33vw, 90vw';
            }
          @endphp
          <div class="cat-box p-rel text-center d-flex flex-column flex-center">
            {!! wp_get_attachment_image($thumb_id, (function_exists('is_front_page') && is_front_page()) ? 'medium_large' : 'full', false, $thumb_attrs) !!}
            <a href="{!! get_term_link($cat->term_id) !!}" class="h6 stretched-link card-title text-black">{!! $cat->name !!}</a>
          </div>
        @endforeach
      </div>
      <div id="icon-slick-right">@svgIcon('chevron-right|20|20')</div>  
    </div>
  </div>
</section>
@endif