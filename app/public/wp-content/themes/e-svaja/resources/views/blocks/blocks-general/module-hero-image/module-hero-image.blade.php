<?php
$content     = App\Helpers()->getFieldDataOrBlockPlaceholder("content", $fields ?? '', $block ?? []);
$image       = App\Helpers()->getFieldDataOrBlockPlaceholder("image", $fields ?? '', $block ?? []);
$link        = App\Helpers()->getFieldDataOrBlockPlaceholder("link", $fields ?? '', $block ?? []);
$second_link = App\Helpers()->getFieldDataOrBlockPlaceholder("second_link", $fields ?? '', $block ?? []);
$hero_attrs  = \App\home_image_attrs([
  'class' => 'w-100 img-cover',
  'loading' => 'eager',
  'decoding' => 'async',
], $fields ?? null);
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif class="block-{!! App\Helpers()->kebabCase($block['title']) !!} mxh-lg-100 p-rel module-layout-2 ovf-hidden{!! (!empty($block['className']) && $block['className'] === 'home-hero') ? ' home-hero' : '' !!}">
  @if ($image ?? false)
    {!! wp_get_attachment_image($image, 'full', false, $hero_attrs) !!}
  @endif
  <div class="container-xxl p-clamp zi-1 video-content">
    <div class="d-flex jc-between flex-column flex-md-row ai-start align-items-md-center content-box">
      <div class="hero-title t-white border-start ps-4 ps-md-5 border-branded-vertical">
        {!! apply_filters('the_content_lite', $content) ?? '' !!}
      </div>
      <div class="d-flex flex-column mt-4 mt-md-0 align-self-stretch align-self-md-auto">
        {!! App\Helpers()->getButtonHtml($link, 'btn btn-lg btn-secondary') !!}
        {!! App\Helpers()->getButtonHtml($second_link, 'btn btn-lg btn-secondary mt-3') !!}
      </div>
    </div>
  </div>
</section>
