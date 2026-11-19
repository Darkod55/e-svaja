<?php
$content  = (get_field('gift_card_content', 'option') ?? '');
$link     = (get_field('gift_card_link', 'option') ?? '');
$image    = (get_field('gift_card_image', 'option') ?? '');
?>

<section class="block-gift-card">
  <div class="container-xxl p-clamp-xl-y p-clamp-x">
    <div class="row g-4 flex-center">
      <div class="col-md-6 col-lg-5 anm8">
        @if ($image ?? false)
          {!! wp_get_attachment_image($image, 'full', '', ['class' => 'w-100 rounded']) !!}
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