@extends('layouts.app-wide')
<?php
$blog_content   = (get_field('blog_hero_content', 'option') ?? '');
$blog_img       = (get_field('blog_hero_img', 'option') ?? '');
?>

@section('content')
@includeIf('partials.post-archive-hero', ['blog_content' => $blog_content, 'blog_img' => $blog_img])
<section class="blog-archive-grid p-clamp-y">
    <div class="container-xxl p-clamp-x">
      <div class="row g-5">
        @if (have_posts())
          @while(have_posts()) @php(the_post())
            @includeFirst(['partials.content-post', 'partials.content'])
          @endwhile
        @else 
          <p class="lead">{!! __('Currently there are no posts available.', 'sage') !!}</p>
        @endif
        {{-- <div class="pagination pagination-secondary jc-center">
          {!! paginate_links() !!}
        </div> --}}
      </div>
    </div>
  </section>
  @includeIf('woocommerce.module-newsletter')
@endsection
