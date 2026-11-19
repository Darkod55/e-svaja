<?php 
$product_title  = App\Helpers()->getFieldDataOrBlockPlaceholder('block_title', $fields ?? '', $block ?? []);
$testimonials   = App\Helpers()->getFieldDataOrBlockPlaceholder('testimonials', $fields ?? '', $block ?? []);
$image          = App\Helpers()->getFieldDataOrBlockPlaceholder('image', $fields ?? '', $block ?? []);
$content        = App\Helpers()->getFieldDataOrBlockPlaceholder('content', $fields ?? '', $block ?? []); 
$link           = App\Helpers()->getFieldDataOrBlockPlaceholder('link', $fields ?? '', $block ?? []); 
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif
  class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-xl-y"
  >
  <div class="container-xxl p-clamp-x">
    <div class="row g-4">
      <div class="col-md-6 col-lg-5">
        <div class="block-content h-100 anm8">
          @if ($product_title ?? false)
            <h2>{!! $product_title !!}</h2>
          @endif
          <hr class="border-top border-branded-horizontal border-large w-50 mb-5">
          @if ($testimonials ?? false)
            @foreach ($testimonials as $t)
              <div class="card border border-dark rounded p-4 {!! (!$loop->last) ? 'mb-4' : '' !!}">
                @svgIcon('quotes|30|30')
                {!! apply_filters('the_content_lite', $t['testimonial']) ?? '' !!}
              </div>
            @endforeach
          @endif
        </div> 
      </div>
      <div class="col-md-6 offset-lg-1 t-white ovf-hidden">
        <div class="card bg-dark rounded ovf-hidden h-100">
          @if ($image ?? false)
            <div class="img-card-top p-rel pt-50">
              {!! wp_get_attachment_image($image, 'full', '', ['class' => 'bg-img-cover w-100']) !!}
            </div>
            <div class="box-info p-clamp-xl-x p-clamp-y">
              {!! apply_filters('the_content_lite', $content) ?? '' !!}
              {!! App\Helpers()->getButtonHtml($link, 'btn btn-primary' ) !!}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>