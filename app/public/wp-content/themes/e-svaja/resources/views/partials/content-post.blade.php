<?php
$post_used = ($post_loop ?? false ? $post_loop : get_the_ID());
?>

<article @php(post_class('col-md-6 card'))>
  <div class="card-body p-0 d-flex-column card-post">
    <div class="container-post-img p-rel mb-4 ovf-hidden ratio ratio-16x9 rounded-top">
      @if (has_post_thumbnail($post_used))
        {!! get_the_post_thumbnail( $post_used, 'full', ['class' => 'w-100']); !!}
      @endif
    </div>
    <div class="card-content t-dark mb-4">
      <h2>{!! get_the_title($post_used) !!}</h2>
      {!! apply_filters('the_content_lite', get_the_excerpt()) ?? '' !!}
    </div>
    <div class="card-footer mt-auto">
      <a href="{!! get_the_permalink($post_used) !!}" class="btn btn-secondary stretched-link">{!! __('Read More', 'sage') !!}</a>
    </div>
  </div>
</article>
