<?php 
$testimonials   = App\Helpers()->getFieldDataOrBlockPlaceholder('testimonials', $fields ?? '', $block ?? []);
$content        = App\Helpers()->getFieldDataOrBlockPlaceholder('content', $fields ?? '', $block ?? []); 
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif
  class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-y"
  >
  <div class="container-xxl p-clamp-x">
    <div class="mw-lg-40 ms-md-auto text-md-end mb-md-5 anm8 d-flex-column ai-start align-items-md-end">
      {!! apply_filters('the_content_lite', $content) ?? '' !!}
    </div>
    <div class="row g-4">
      @if ($testimonials ?? false)
        @foreach ($testimonials as $t)
        <div class="col-md-4">
          <div class="card border rounded p-4 h-100 anm8">
            <div class="icon-box mb-4">
              @svgIcon('quotes|30|30')
            </div>
            {!! apply_filters('the_content_lite', $t['testimonial']) ?? '' !!}
          </div>
        </div> 
        @endforeach
      @endif
    </div>
  </div>
</section>