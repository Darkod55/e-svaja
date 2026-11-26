{{--
  Template Name: Christmas Gifts
--}}

@extends('layouts.app-wide')

@section('content')
  @while(have_posts()) @php(the_post())
    <div class="christmas-gifts">
      @include('partials.page-header')
      @include('partials.content-christmas-gifts')
    </div>
  @endwhile
@endsection
