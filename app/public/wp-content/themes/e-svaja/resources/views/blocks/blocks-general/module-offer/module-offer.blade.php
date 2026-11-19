<?php 
$image          = App\Helpers()->getFieldDataOrBlockPlaceholder('image', $fields ?? '', $block ?? []);
$content        = App\Helpers()->getFieldDataOrBlockPlaceholder('content', $fields ?? '', $block ?? []); 
$link           = App\Helpers()->getFieldDataOrBlockPlaceholder('link', $fields ?? '', $block ?? []); 
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif
  class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-xl-y"
  >
  <div class="container-xxl p-clamp-x">
    <div class="row flex-md-row-reverse">
      <div class="col-md-6 ps-md-0">
        <div class="p-rel h-100">
          @if ($image ?? false)
              {!! wp_get_attachment_image($image, \App\home_image_size($fields ?? null), false, \App\home_image_attrs(['class' => 'bg-img-cover w-100 rounded-end img-sm-rel'], $fields ?? null)) !!}
          @endif
        </div>
      </div>
      <div class="col-md-6 pe-md-0">
        <div class="col-offer p-clamp-xl bg-branded rounded-start t-white">
          {!! apply_filters('the_content_lite', $content) ?? '' !!}
          {!! App\Helpers()->getButtonHtml($link, 'btn btn-white' ) !!}
        </div>
      </div>
    </div>
  </div>
</section>