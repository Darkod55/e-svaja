<?php
$content_left      = App\Helpers()->getFieldDataOrBlockPlaceholder("content_left", $fields ?? '', $block ?? []);
$content_right  = App\Helpers()->getFieldDataOrBlockPlaceholder('content_right', $fields ?? '', $block ?? []);
$icon_left = ($fields->icon_left['icons'] ?? 'values');
$icon_right = ($fields->icon_right['icons'] ?? 'values');
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif
  class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-xl-y"
  >
  <div class="container-xxl p-clamp-x">
    <div class="row g-4">
      @if ($content_left ?? false)
      <div class="col-lg-6 anm8">
        <div class="border rounded-start p-clamp-xl h-100 border-dark">
          {!! App\helpers()->getSvg([ 'icon' => $icon_left, 'width' => 65, 'height' => 65]) !!}
          <div class="mt-4">
            {!! apply_filters('the_content_lite', $content_left) ?? '' !!}
          </div>
        </div>
      </div>
      @endif
      @if ($content_right ?? false)
      <div class="col-lg-6 anm8">
        <div class="border rounded-end p-clamp-xl h-100 border-dark">
          {!! App\helpers()->getSvg([ 'icon' => $icon_right, 'width' => 65, 'height' => 65]) !!}
          <div class="mt-4">
            {!! apply_filters('the_content_lite', $content_right) ?? '' !!}
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</section>