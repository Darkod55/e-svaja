@extends('layouts.app-wide')

@section('content')
  <section class="mvh-lg-100 d-flex-center">
    <div class="container-xxl p-clamp">
      <div class="row ai-center">
        <div class="col-md-6">
          <h1 class="title404">404</span>
        </div>
        <div class="col-md-6 lead">
          <h2 class="t-primary mb-4">{!! __('Page not found', 'sage') !!}</h2>
          <p>{!! __('It looks like nothing was found at this location. <br>Maybe try one of the links in the navigation bar?', 'sage') !!}</p>
        </div>
      </div>
    </div>
  </section>
@endsection
