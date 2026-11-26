@extends('layouts.app-wide')

<?php 
$vip_form   = (get_field('vip_form', 'option') ?? '');
?>

@section('content')
  <section class="post-single-grid p-clamp-y">
    <div class="container-xxl p-clamp-x">
       @if (has_post_thumbnail(get_the_ID()))
        <div class="ratio ratio-21x9">
          {!! get_the_post_thumbnail( get_the_ID(), 'full', ['class' => 'w-100 rounded-top img-cover']); !!}
        </div>
       @endif
      <div class="row g-5 p-clamp-t">
        <div class="col-md-8 col-lg-9">
          <hr class="border-top border-branded-horizontal border-large w-50">
          <h1 class="anm8">{!! the_title() !!}</h1>
          {!! the_content() !!}
          @include('partials.author-card')
        </div>
        <div class="col-md-4 col-lg-3 pt-5">
          <div class="recent-posts anm8">
            @php
              $args = ['numberposts' => 3, 'post_type' => 'post', 'fields' => 'ids'];
              $recent_posts = get_posts($args);
            @endphp
            @if ($recent_posts ?? false)
              <h2 class="h4">{{ __('Other News', 'sage') }}</h2>
              <ul class="ps-0" style="list-style-type: none;">
              @foreach ($recent_posts as $p)
                <li><a class="h5 t-dark d-block border-bottom pb-3" href="{!! get_the_permalink($p) !!}">{!! get_the_title($p) !!}</a></li>
              @endforeach
              </ul>
            @endif
          </div>
          @if ($vip_form ?? false)
            <div class="vip-form anm8">
              <h2 class="h4">{{ __('Join the VIP Club', 'sage') }}</h2>
              <div class="svaja-form">
                {!! apply_filters('the_content_lite', $vip_form) ?? '' !!}
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>
  @includeIf('woocommerce.module-newsletter')
@endsection