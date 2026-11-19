<?php
$content  = App\Helpers()->getFieldDataOrBlockPlaceholder("content", $fields ?? '', $block ?? []);
$image    = App\Helpers()->getFieldDataOrBlockPlaceholder("image", $fields ?? '', $block ?? []);
$link     = App\Helpers()->getFieldDataOrBlockPlaceholder("link", $fields ?? '', $block ?? []);
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-y">
  <div class="container-xxl p-clamp-x">
    <div class="row ai-end {!! ($image ?? false) ? 'g-5' : '' !!}">
      <div class="col-md-6">
        <div class="anm8 hero-title ps-4 ps-md-5 border-start border-branded-vertical">
          {!! apply_filters('the_content_lite', $content) ?? '' !!}
          {!! App\Helpers()->getButtonHtml($link, 'btn btn-secondary' ) !!}
        </div>
      </div>
      <div class="col-md-6">
        @if ($image ?? false)
          <div class="ratio ratio-4x3">
            {!! wp_get_attachment_image($image, 'full', '', ['class' => 'w-100 rounded-end img-cover img-pos-top', 'loading' => 'eager']) !!}
          </div>
        @endif
      </div>
    </div>
  </div>
</section>
