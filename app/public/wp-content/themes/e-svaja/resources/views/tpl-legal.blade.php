{{--
  Template Name: Legal
--}}

@extends('layouts.app-wide')

@section('content')
<section class="section-legal">
  <div class="container-xxl p-clamp">
    <h1>{!! $page_title !!}</h1>
    {!! the_content() !!}
  </div>
</section>
@endsection
