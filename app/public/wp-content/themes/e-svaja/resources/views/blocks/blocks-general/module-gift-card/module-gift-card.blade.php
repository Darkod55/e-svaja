<?php
if ($fields->add_custom_content ?? false) {
  $content  = App\Helpers()->checkIfEditor(get_field('gift_card_content', 'option') ?? '', $block['example']['attributes']['data']['content']);
  $link     = App\Helpers()->checkIfEditor(get_field('gift_card_link', 'option') ?? '', $block['example']['attributes']['data']['link']);
  $image    = App\Helpers()->checkIfEditor(get_field('gift_card_image', 'option') ?? '', $block['example']['attributes']['data']['image']);
} else {
  $content   = App\Helpers()->getFieldDataOrBlockPlaceholder("content", $fields ?? '', $block ?? []);
  $image     = App\Helpers()->getFieldDataOrBlockPlaceholder('image', $fields ?? '', $block ?? []);
  $link      = App\Helpers()->getFieldDataOrBlockPlaceholder('link', $fields ?? '', $block ?? []);
}
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif
  class="block-{!! App\Helpers()->kebabCase($block['title'] ?? 'gift-card') !!} p-clamp-xl-y"
  >
  <div class="container-xxl p-clamp-x">
    <div class="row g-4 flex-center">
      <div class="col-md-6 col-lg-5 anm8">
        @if ($image ?? false)
          {!! wp_get_attachment_image($image, 'full', false, \App\home_image_attrs(['class' => 'w-100 rounded'], $fields ?? null)) !!}
        @endif
      </div>
      <div class="col-md-6 col-lg-4 offset-lg-1 anm8 text-center">
        @svgIcon('gift|75|75')
        <div class="gift-card-content mt-3">
          {!! apply_filters('the_content_lite', $content) ?? '' !!}
        </div>
        {!! App\Helpers()->getButtonHtml($link, 'btn btn-secondary mt-4' ) !!}
      </div>
    </div>
  </div>
</section>