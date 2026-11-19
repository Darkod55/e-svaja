<?php
$content    = App\Helpers()->getFieldDataOrBlockPlaceholder("content", $fields ?? '', $block ?? []);
$form       = App\Helpers()->getFieldDataOrBlockPlaceholder("svaja_form", $fields ?? '', $block ?? []);
$image       = App\Helpers()->getFieldDataOrBlockPlaceholder("image", $fields ?? '', $block ?? []);
$address    = get_field('address', 'option') ?? '';
$phone      = get_field('phone', 'option') ?? '';
$email      = get_field('email', 'option') ?? '';;
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif
  class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-xl-y"
  >
  <div class="container-xxl p-clamp-x">
    <div class="row g-4">
      <div class="col-md-6 anm8 p-rel">
        @if ($image ?? false)
          {!! wp_get_attachment_image($image, 'full', '', ['class' => 'bg-img-cover w-100 img-layout-2']) !!}
        @endif
        <hr class="border-top border-branded-horizontal border-large w-50">
        @if ($content ?? false)
        <div class="module-content p-rel zi-1 mb-5">
          {!! apply_filters('the_content_lite', $content) ?? '' !!}
        </div>
        @endif
        <div class="contact-info zi-1 p-rel">
          @if ($address ?? false)
            <p class="mb-0">Address:</p>
            <address class="mb-4">
              {!! apply_filters('the_content_lite', $address) ?? '' !!}
            </address>  
          @endif
          @if ($phone ?? false)
            <p class="mb-0">Telephone:</p>
            <a class="t-dark mb-4 d-block" href="tel:{!! App\helpers()->getSanitisedTel($phone) !!}">{!! $phone !!}</a>
          @endif
          @if ($email ?? false)
            <p class="mb-0">Email:</p>
            <a class="t-dark" href="mailto:{!! $email !!}">{!! $email !!}</a>
          @endif
        </div>
      </div>
      <div class="col-md-6 anm8">
        @if ($form ?? false)
        <div class="border border-dark rounded p-clamp-y p-clamp-xl-x svaja-form">
          @includeIf('components.form-contact')
          {{--
          {!! App\helpers()->getShortcodeContactForm7($form) !!}
          --}}
        </div>
        @endif
      </div>
    </div>
  </div>
</section>