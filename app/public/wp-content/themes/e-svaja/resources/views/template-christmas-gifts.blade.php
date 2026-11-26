{{--
  Template Name: Christmas Gifts
--}}

@extends('layouts.app-wide')

@section('content')
  @while(have_posts()) @php(the_post())
    <div class="christmas-gifts">
      @php(\App\christmas_gifts_render_hero())
      <div class="container-xxl p-clamp-x">
        @php(\App\christmas_gifts_render())
        @include('partials.content-christmas-gifts')
      </div>
    </div>
  @endwhile

  <style>
    .christmas-gifts .christmas-gifts-hero {
      position: relative;
      display: flex;
      align-items: center;
      overflow: hidden;
      width: 100%;
      min-height: max(32rem, min(50vw, 70vh));
      margin-bottom: 3rem;
      background: #212529;
      color: #f5efe6;
    }

    .christmas-gifts .christmas-gifts-hero > img {
      position: absolute;
      inset: 0;
      z-index: 0;
      width: 100%;
      height: 100%;
      max-width: none;
      object-fit: cover;
      object-position: center right;
    }

    .christmas-gifts .christmas-gifts-hero-shade {
      position: absolute;
      inset: 0;
      z-index: 0;
      background: linear-gradient(90deg, rgba(0, 0, 0, 0.82) 0%, rgba(0, 0, 0, 0.62) 34%, rgba(0, 0, 0, 0.28) 62%, rgba(0, 0, 0, 0.08) 100%);
    }

    .christmas-gifts .christmas-gifts-hero-copy {
      position: relative;
      z-index: 1;
      margin-left: 8%;
      max-width: 55%;
      padding: 4rem 1.5rem 4rem 0;
      text-align: left;
    }

    .christmas-gifts .christmas-gifts-eyebrow,
    .christmas-gifts .christmas-gifts-offer,
    .christmas-gifts .christmas-gifts-hero-btn {
      font-family: Poppins, var(--bs-body-font-family), sans-serif;
      font-weight: 400;
      font-size: 0.9375rem;
      letter-spacing: 0.25em;
      line-height: 1.4;
      text-transform: uppercase;
    }

    .christmas-gifts .christmas-gifts-eyebrow,
    .christmas-gifts .christmas-gifts-offer {
      margin: 0;
      color: var(--bs-primary, #f1bd58);
    }

    .christmas-gifts .christmas-gifts-rule {
      display: block;
      width: 60px;
      height: 1px;
      margin: 0.9rem 0 1.15rem;
      background: var(--bs-primary, #f1bd58);
    }

    .christmas-gifts .christmas-gifts-offer {
      margin-bottom: 1rem;
      font-size: 0.8rem;
    }

    .christmas-gifts .christmas-gifts-hero h1 {
      margin: 0 0 1rem;
      color: #f5efe6;
      font-family: Poppins, var(--bs-body-font-family), sans-serif;
      font-size: clamp(2.5rem, 6vw, 5rem);
      font-weight: 300;
      letter-spacing: -0.02em;
      line-height: 1.05;
    }

    .christmas-gifts .christmas-gifts-hero h1 em {
      font-style: italic;
      font-weight: 300;
    }

    .christmas-gifts .christmas-gifts-intro {
      max-width: 520px;
      margin: 0;
      color: #f5efe6;
      font-family: Poppins, var(--bs-body-font-family), sans-serif;
      font-size: 1.1rem;
      font-weight: 400;
      line-height: 1.6;
    }

    .christmas-gifts .christmas-gifts-hero-btn {
      display: inline-block;
      margin-top: 1.5rem;
      padding: 16px 30px;
      border: 1px solid #f5efe6;
      background: transparent;
      color: #f5efe6;
      font-size: 0.8rem;
      text-decoration: none;
    }

    .christmas-gifts .christmas-gifts-hero-btn:hover,
    .christmas-gifts .christmas-gifts-hero-btn:focus-visible {
      background: #f5efe6;
      color: #212529;
    }

    @media (max-width: 767px) {
      .christmas-gifts .christmas-gifts-hero {
        min-height: 36rem;
      }

      .christmas-gifts .christmas-gifts-hero-shade {
        background: linear-gradient(90deg, rgba(0, 0, 0, 0.82) 0%, rgba(0, 0, 0, 0.68) 58%, rgba(0, 0, 0, 0.4) 100%);
      }

      .christmas-gifts .christmas-gifts-hero-copy {
        margin-left: 0;
        max-width: 100%;
        padding: 3rem 1.25rem;
      }

      .christmas-gifts .christmas-gifts-eyebrow,
      .christmas-gifts .christmas-gifts-offer,
      .christmas-gifts .christmas-gifts-hero-btn {
        font-size: 0.75rem;
        letter-spacing: 0.18em;
      }
    }

    .christmas-gifts .christmas-gifts-section {
      margin-bottom: 3rem;
    }

    .christmas-gifts .christmas-gifts-band {
      background: var(--bs-highlight-bg, #fcf2de);
      width: 100vw;
      margin-left: calc(50% - 50vw);
      margin-bottom: 3rem;
      padding: 3rem 0;
    }

    .christmas-gifts .christmas-gifts-slider {
      position: relative;
    }

    .christmas-gifts .christmas-gifts-track {
      display: flex;
      gap: 1.25rem;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      scroll-behavior: smooth;
      -webkit-overflow-scrolling: touch;
      padding-bottom: 0.5rem;
    }

    .christmas-gifts .christmas-gifts-track > [class*="col-"] {
      flex: 0 0 85%;
      width: 85%;
      max-width: 85%;
      scroll-snap-align: start;
    }

    @media (min-width: 768px) {
      .christmas-gifts .christmas-gifts-track > [class*="col-"] {
        flex-basis: 46%;
        width: 46%;
        max-width: 46%;
      }
    }

    @media (min-width: 992px) {
      .christmas-gifts .christmas-gifts-track > [class*="col-"] {
        flex-basis: calc((100% - 3.75rem) / 4);
        width: calc((100% - 3.75rem) / 4);
        max-width: calc((100% - 3.75rem) / 4);
      }
    }

    .christmas-gifts .christmas-gifts-nav {
      position: absolute;
      top: 38%;
      z-index: 2;
      width: 2.5rem;
      height: 2.5rem;
      border: 0;
      border-radius: 50%;
      background: #fff;
      color: #212529;
      box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.15);
      font-size: 1.75rem;
      line-height: 1;
    }

    .christmas-gifts .christmas-gifts-prev { left: 0.25rem; }
    .christmas-gifts .christmas-gifts-next { right: 0.25rem; }

    .christmas-gifts .christmas-gifts-slider.is-static .christmas-gifts-nav {
      display: none;
    }

    .christmas-gifts .christmas-gifts-line {
      margin: 0.25rem 0 0;
      font-size: 1rem;
      line-height: 1.45;
    }

    .christmas-gifts .christmas-gifts-cta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 1.5rem;
      margin-top: 1rem;
    }

    .christmas-gifts .christmas-gifts-cta-img {
      width: 220px;
      max-width: 100%;
      height: auto;
    }

    .christmas-gifts .christmas-gifts-cta-copy p {
      margin-bottom: 1rem;
    }
  </style>
  <script>
    document.querySelectorAll('[data-christmas-slider]').forEach(function (slider) {
      var track = slider.querySelector('.christmas-gifts-track');
      var prev = slider.querySelector('.christmas-gifts-prev');
      var next = slider.querySelector('.christmas-gifts-next');
      if (!track) {
        return;
      }
      function step() {
        var card = track.querySelector('[class*="col-"]');
        var styles = window.getComputedStyle(track);
        var gap = parseFloat(styles.columnGap || styles.gap) || 0;
        return card ? card.getBoundingClientRect().width + gap : track.clientWidth;
      }
      function update() {
        var overflow = track.scrollWidth > track.clientWidth + 4;
        slider.classList.toggle('is-static', !overflow);
        if (prev) {
          prev.hidden = !overflow;
        }
        if (next) {
          next.hidden = !overflow;
        }
      }
      if (prev) {
        prev.addEventListener('click', function () {
          track.scrollBy({ left: -step(), behavior: 'smooth' });
        });
      }
      if (next) {
        next.addEventListener('click', function () {
          track.scrollBy({ left: step(), behavior: 'smooth' });
        });
      }
      update();
      window.addEventListener('resize', update);
    });
  </script>
@endsection
