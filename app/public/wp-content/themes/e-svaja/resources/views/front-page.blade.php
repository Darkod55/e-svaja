@extends('layouts.app-wide')

@section('content')
  @php
    $homepage = \App\homepage_sections();
    // 'seasonal' (default) or 'classic'. See homepage_hero_variant().
    $hero_variant = \App\homepage_hero_variant();
  @endphp

  @if ($hero_variant === 'classic')
    @include('partials.hero-homepage-classic', [
      'fields' => $homepage['hero']['fields'],
      'block' => $homepage['hero']['block'],
    ])
  @else
    @include('partials.hero-seasonal', [
      'image_id' => 33507,
      'alt_fallback' => 'Hand-blown glass art sculptures and gifts by Svaja',
      'eyebrow' => '',
      'offer' => '',
      'title' => 'Hand-Blown Glass Art Sculptures & Gifts - Svaja',
      'intro' => "Kristina's Private Edit: five glass pieces for autumn, chosen by Kristina.",
      'lcp' => true,
      'buttons' => [
        ['label' => 'Shop Now', 'url' => home_url('/collections/')],
        ['label' => 'See the five', 'url' => '#private-edit', 'secondary' => true],
      ],
    ])
  @endif

  @include('blocks.blocks-woo.module-products.module-products', [
    'fields' => $homepage['products']['fields'],
    'block' => $homepage['products']['block'],
  ])

  @include('blocks.blocks-general.module-offer.module-offer', [
    'fields' => $homepage['offer']['fields'],
    'block' => $homepage['offer']['block'],
  ])

  @include('blocks.blocks-general.module-newsletter.module-newsletter', [
    'fields' => $homepage['newsletter']['fields'],
    'block' => $homepage['newsletter']['block'],
  ])

  @include('blocks.blocks-woo.module-categories.module-categories', [
    'block' => $homepage['categories']['block'],
    'slider_id' => $homepage['categories']['slider_id'],
  ])
  <script type="text/javascript">var sliderId = @json($homepage['categories']['slider_id']);</script>

  @include('blocks.blocks-general.module-gift-card.module-gift-card', [
    'fields' => $homepage['gift']['fields'],
    'block' => $homepage['gift']['block'],
  ])

  @include('blocks.blocks-general.module-testimonials.module-testimonials', [
    'fields' => $homepage['testimonials']['fields'],
    'block' => $homepage['testimonials']['block'],
  ])

  @include('blocks.blocks-general.module-two-columns-text-image.module-two-columns-text-image', [
    'fields' => $homepage['story']['fields'],
    'block' => $homepage['story']['block'],
  ])
@endsection
