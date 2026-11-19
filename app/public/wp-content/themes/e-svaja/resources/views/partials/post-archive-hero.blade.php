@if ($blog_content ?? false)
<section class="block-module-hero p-clamp-y">
  <div class="container-xxl p-clamp-x">
    <div class="row ai-end g-5">  
      <div class="col-md-6">
        <div class="anm8 hero-title ps-4 ps-md-5 border-start border-branded-vertical">
          {!! apply_filters('the_content_lite', $blog_content) ?? '' !!}
        </div>
      </div>
      <div class="col-md-6">
        @if ($blog_img ?? false)
          <div class="ratio ratio-4x3">
            {!! wp_get_attachment_image($blog_img, 'full', '', ['class' => 'w-100 rounded-end img-cover', 'loading' => 'eager']) !!}
          </div>
        @endif
      </div>
    </div>
  </div>
</section>
@endif