{{--
  Template Name: Woo Default Pages
--}}

@extends('layouts.app-wide')

@section('content')
<section class="woo-default-wrapper">
	<div class="container-xxl p-clamp-sm-x p-clamp-y">
		@include('partials.page-header')
		<div class="row">
			<div class="col-12">
  			{!! the_content() !!}
      </div>
		</div>
	</div>
</section>
@endsection
