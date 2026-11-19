<?php
$content      = App\Helpers()->getFieldDataOrBlockPlaceholder("content", $fields ?? '', $block ?? []);
$image        = App\Helpers()->getFieldDataOrBlockPlaceholder("image", $fields ?? '', $block ?? []);
$icon         = ($fields->icon['icons'] ?? 'values');
$block_layout = ($fields->layout ?? false) ? $fields->layout : 'flex-row';
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif
  class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-xl-y"
  >
  <div class="container-xxl p-clamp-x">
    <div class="row {!! $block_layout ?? 'flex-row' !!} g-4">
      @if ($content ?? false)
      <div class="col-lg-6 anm8">
        <div class="border {!! $block_layout == 'flex-row' ? 'rounded-start' : 'rounded-end' !!} p-clamp-xl h-100 border-dark">
          {!! App\helpers()->getSvg([ 'icon' => $icon, 'width' => 65, 'height' => 65]) !!}
          @svgIcon($icon|65|65)
          <div class="mt-4">
            {!! apply_filters('the_content_lite', $content) ?? '' !!}
          </div>
        </div>
      </div>
      @endif
      @if ($image ?? false)
      <div class="col-lg-6 anm8">
        <div class="ratio ratio-1x1 {!! $block_layout == 'flex-row' ? 'rounded-end' : 'rounded-start' !!} ovf-hidden">
          {!! wp_get_attachment_image($image, 'full', '', ['class' => 'w-100 img-cover']) !!}
        </div>
      </div>
      @endif
    </div>
  </div>
</section>