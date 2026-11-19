<article @php(post_class('col-lg-12 card card-search'))>
  <div class="card-body border-bottom border-tertiary py-5 px-0">
    <div class="row ai-center">
      <div class="col-md-8 col-lg-9">
        <div class="card-content mb-4 t-dark">
          <h2 class="mb-2">{!! $title !!}</h4>
          {!! apply_filters('the_content_lite', the_excerpt()) !!}
        </div>
        <div class="card-footer mt-auto px-0">
          <a href="{!! get_permalink() !!}" class="btn btn-secondary stretched-link">
            @if (get_post_type(get_the_ID()) === 'product')
              {{ __('View Product', 'sage') }}
            @else 
              {{ __('Find Out More', 'sage') }}
            @endif
          </a>
        </div>
      </div>
      <div class="col-md-4 col-lg-3">
        @if (has_post_thumbnail(get_the_ID()))
          {!! get_the_post_thumbnail( get_the_ID(), 'full', ['class' => 'w-100 mt-3 mt-md-0']); !!}
        @endif
      </div>
    </div>
  </div>
</article>