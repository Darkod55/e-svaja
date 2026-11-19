<?php
if ($fields->use_default_or_custom ?? false) {
  $content  = App\Helpers()->checkIfEditor(get_field('newsletter_text', 'option') ?? '', $block['example']['attributes']['data']['content']);
  $form     = App\Helpers()->checkIfEditor(get_field('svaja_form_newsletter', 'option') ?? '', $block['example']['attributes']['data']['svaja_form']);
  $image    = App\Helpers()->checkIfEditor(get_field('newsletter_image', 'option') ?? '', $block['example']['attributes']['data']['image']);
} else {
  $content  = App\Helpers()->getFieldDataOrBlockPlaceholder("content", $fields ?? '', $block ?? []);
  $form     = App\Helpers()->getFieldDataOrBlockPlaceholder("svaja_form", $fields ?? '', $block ?? []);
  $image    = App\Helpers()->getFieldDataOrBlockPlaceholder("image", $fields ?? '', $block ?? []);
}
?>
<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif
  class="block-{!! App\Helpers()->kebabCase($block['title']) !!} ovf-hidden p-clamp-xl-y"
  >
  <div class="container-xxl p-clamp-x">
    <div class="row">
      <div class="col-md-6 pe-md-0">
        <div class="p-rel w-100 h-100 pt-sm">
          @if ($image ?? false)
            {!! wp_get_attachment_image($image, \App\home_image_size($fields ?? null), false, \App\home_image_attrs(['class' => 'bg-img-cover w-100 img-rounded rounded-start'], $fields ?? null)) !!}
          @endif
        </div>
      </div>
      <div class="col-md-6 t-white ps-md-0">
        @if ($content ?? false)
          <div class="block-content bg-secondary p-clamp-xl-x p-clamp-sm-y col-rounded rounded-end d-flex ai-center">
            <div class="module-content anm8">
              <hr class="border-top border-white border-large">
              {!! apply_filters('the_content_lite', $content) ?? '' !!}
              @if ($form ?? false)
              <div class="svaja-form newsletter-form t-white">
                @includeIf('components.form-newsletter')
              </div>
              @endif
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>