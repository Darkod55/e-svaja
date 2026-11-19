@extends('layouts.app-wide')

@section('content')
<section class="section-search">
  <div class="container-xxl p-clamp">
    <div class="border-bottom border-tertiary pb-5">
      <h1>{!! $page_title !!}</h1>
    </div>
  </div>
</section>
<section>
  <div class="container-xxl p-clamp-x p-clamp-b">
    @if (! have_posts())
    <div class="row lead section">
      <x-alert type="warning">
        {!! __('Sorry, no results were found. Please search for something else.', 'sage') !!}
      </x-alert>
    </div>
    @else 
    <div class="row">
      @while(have_posts()) @php(the_post())
        @include('partials.content-search')
      @endwhile

      {!! get_the_posts_navigation() !!}
    </div>
    @endif
  </div>
</section>
@endsection