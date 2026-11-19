<?php 
$content      = App\Helpers()->getFieldDataOrBlockPlaceholder('content', $fields ?? '', $block ?? []);
$form         = App\Helpers()->getFieldDataOrBlockPlaceholder('svaja_form', $fields ?? '', $block ?? []);
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif
  class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-xl-y"
  >
  <div class="container-xxl p-clamp-x">
    <div class="row g-5">
      <div class="col-md-6 anm8">
        {!! apply_filters('the_content_lite', $content) ?? '' !!}
      </div>
      <div class="col-md-6 d-flex-column ai-start anm8">
        @if ($form ?? false)
          <div class="svaja-form w-100">
            @includeIf('components.form-vip')
            {{-- {!! App\helpers()->getShortcodeContactForm7($form) !!} --}}
          </div>
        @endif
      </div>
    </div>
  </div>
</section>