<?php
$content  = (get_field('newsletter_text', 'option') ?? '');
$form     = (get_field('svaja_form_newsletter', 'option') ?? '');
$image    = (get_field('newsletter_image', 'option') ?? '');
?>
<section class="block-newsletter ovf-hidden">
  <div class="container-xxl p-clamp">
    <div class="row">
      <div class="col-md-6 pe-md-0">
        <div class="p-rel w-100 h-100 pt-sm">
          @if ($image ?? false)
            {!! wp_get_attachment_image($image, 'full', '', ['class' => 'bg-img-cover w-100 img-rounded rounded-start']) !!}
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
                {{--
                {!! App\helpers()->getShortcodeContactForm7($form) !!}
                --}}
              </div>
              @endif
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>