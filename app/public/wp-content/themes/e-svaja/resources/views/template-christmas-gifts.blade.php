{{--
  Template Name: Christmas Gifts
--}}

@extends('layouts.app-wide')

@section('content')
  @while(have_posts()) @php(the_post())
    <div class="christmas-gifts">
      <div class="container-xxl p-clamp-x">
        @include('partials.page-header')
        @php(\App\christmas_gifts_render())
        @include('partials.content-christmas-gifts')
      </div>
    </div>
  @endwhile

  <style>
    .christmas-gifts .christmas-gifts-line {
      margin: 0.25rem 0 0;
      font-size: 1rem;
      line-height: 1.45;
    }
  </style>
@endsection
