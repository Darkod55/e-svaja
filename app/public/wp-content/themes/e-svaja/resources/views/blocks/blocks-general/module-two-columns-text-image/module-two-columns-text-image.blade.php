<?php
$content      = App\Helpers()->getFieldDataOrBlockPlaceholder("content", $fields ?? '', $block ?? []);
$image        = App\Helpers()->getFieldDataOrBlockPlaceholder('image', $fields ?? '', $block ?? []);
$block_layout = ($fields->layout ?? false) ? $fields->layout : 'flex-row';
$img_radius   = ($fields->image_radius ?? false) ? $fields->image_radius : '';
if ($fields->columns === '7-5') {
  $col_1 = 'col-md-7';
  $col_2 = 'col-md-5 col-lg-4';
  if ($block_layout == 'flex-row') {
    $col_1_offset = '';
    $col_2_offset = 'offset-lg-1';
  } else {
    $col_1_offset = 'offset-lg-1';
    $col_2_offset = '';
  }
} elseif ($fields->columns === '8-4') {
  $col_1 = 'col-md-6 col-lg-8';
  $col_2 = 'col-md-6 col-lg-3';
  if ($block_layout == 'flex-lg-row-reverse') {
    $col_1_offset = 'offset-lg-1';
    $col_2_offset = '';
  } else {
    $col_1_offset = '';
    $col_2_offset = 'offset-lg-1';
  }
} else {
  $col_1 = 'col-md-6';
  $col_2 = 'col-md-6';
}
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif
  class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-xl-y"
  >
  <div class="container-xxl p-clamp-x">
    <div class="row {!! $block_layout ?? 'flex-row' !!} g-4">
      <div class="{!! $col_1 !!} {!! $col_1_offset ?? '' !!} {!! ($block_layout == 'flex-row') ? 'pe-md-5' : 'ps-md-5' !!} anm8 d-flex-column ai-start">
        {!! apply_filters('the_content_lite', $content) ?? '' !!}
      </div>
      <div class="{!! $col_2 !!} {!! $col_2_offset ?? '' !!} anm8">
        @if ($image ?? false)
          {!! wp_get_attachment_image($image, \App\home_image_size($fields ?? null), false, \App\home_image_attrs(['class' => 'w-100 ' . $img_radius], $fields ?? null)) !!}
        @endif
      </div>
    </div>
  </div>
</section>