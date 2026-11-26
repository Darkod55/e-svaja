{{--
  Template Name: Christmas Gifts
--}}

@extends('layouts.app-wide')

@section('content')
  @while(have_posts()) @php(the_post())
    <div class="christmas-gifts">
      @include('partials.hero-seasonal', [
        'image_id' => 33498,
        'alt_fallback' => 'SVAJA Christmas gifts',
        'eyebrow' => 'Christmas at SVAJA',
        'offer' => \App\christmas_gifts_black_friday_line(),
        'title' => get_the_title(),
        'intro' => 'Every SVAJA piece is designed in the UK and handmade by skilled glassmakers in European studios, so the gift you choose is individual. Here is our Christmas selection, by budget.',
        'buttons' => [
          ['label' => 'Shop Christmas Gifts', 'url' => '#under-100'],
        ],
      ])
      <div class="container-xxl p-clamp-x">
        @php(\App\christmas_gifts_render())
        @include('partials.content-christmas-gifts')
      </div>
    </div>
  @endwhile

  <style>
    .christmas-gifts .christmas-gifts-section {
      margin-bottom: 3rem;
    }

    .christmas-gifts .christmas-gifts-band {
      background: #f9f9f9;
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
      scrollbar-width: none;
    }

    .christmas-gifts .christmas-gifts-track::-webkit-scrollbar {
      display: none;
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

    .christmas-gifts .christmas-gifts-track {
      align-items: stretch;
    }

    .christmas-gifts .christmas-gifts-track > [class*="col-"] {
      display: flex;
      align-items: stretch;
      align-self: stretch;
    }

    .christmas-gifts .christmas-gifts-track .card {
      display: flex;
      flex: 1 1 auto;
      flex-direction: column;
      gap: 16px;
      width: 100%;
      height: auto;
    }

    .christmas-gifts .christmas-gifts-track img {
      display: block;
      width: 100%;
      margin: 0;
    }

    .christmas-gifts .shop-card {
      display: flex;
      flex: 1 1 auto;
      flex-direction: column;
      min-height: 0;
      padding: 0 1.5rem 1.5rem;
    }

    .christmas-gifts .shop-card > a.woocommerce-LoopProduct-link {
      display: flex;
      flex: 1 1 auto;
      flex-direction: column;
      min-height: 0;
    }

    .christmas-gifts .title-wrap {
      display: flex;
      flex: 1 1 auto;
      flex-direction: column;
      gap: 12px;
      min-height: 0;
    }

    .christmas-gifts .prod-cat {
      display: inline-block;
      align-self: flex-start;
      width: auto;
      max-width: 100%;
      margin: 0 !important;
    }

    .christmas-gifts .woocommerce-loop-product__title,
    .christmas-gifts .christmas-gifts-line,
    .christmas-gifts .price,
    .christmas-gifts .rating-price-add .btn {
      margin: 0 !important;
    }

    .christmas-gifts .christmas-gifts-line {
      flex: 1 1 auto;
      color: #212529;
      font-size: 0.9375rem;
      font-weight: 400;
      line-height: 1.5;
    }

    .christmas-gifts a .christmas-gifts-line {
      color: #212529;
    }

    .christmas-gifts .rating-price-add {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-top: auto !important;
    }

    .christmas-gifts .rating-price-add > a.woocommerce-LoopProduct-link {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 12px;
    }

    .christmas-gifts .rating-price-add > a.woocommerce-LoopProduct-link:not(:has(.star-rating))::before {
      content: "";
      display: block;
      height: 1.25rem;
    }

    .christmas-gifts .star-rating {
      display: block;
      position: relative;
      float: none;
      overflow: hidden;
      height: 1.25rem;
      margin: 0 !important;
      font-size: 1.25rem;
      line-height: 1;
    }

    .christmas-gifts .rating-price-add .btn {
      align-self: flex-start;
    }

    .christmas-gifts .christmas-gifts-cta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 2.5rem;
      max-width: 100%;
      margin: 0.5rem auto 3rem;
      padding: 56px 64px;
      border-radius: 24px;
      background: #212529;
      color: #f5efe6;
    }

    .christmas-gifts .christmas-gifts-cta-copy {
      max-width: 36rem;
    }

    .christmas-gifts .christmas-gifts-cta h2 {
      margin: 0 0 0.75rem;
      color: #f5efe6;
      font-family: Poppins, var(--bs-body-font-family), sans-serif;
      font-size: clamp(1.75rem, 3vw, 2.75rem);
      font-weight: 300;
      letter-spacing: -0.02em;
      line-height: 1.15;
    }

    .christmas-gifts .christmas-gifts-cta h2 em {
      color: #f1bd58;
      font-style: italic;
      font-weight: 300;
    }

    .christmas-gifts .christmas-gifts-cta p {
      margin: 0;
      color: #f5efe6;
      font-family: Poppins, var(--bs-body-font-family), sans-serif;
      font-size: 1.05rem;
      line-height: 1.5;
    }

    .christmas-gifts .christmas-gifts-cta-actions {
      display: flex;
      flex: 0 0 auto;
      flex-direction: column;
      gap: 0.75rem;
    }

    .christmas-gifts .christmas-gifts-cta-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 999px;
      padding: 14px 28px;
      font-family: Poppins, var(--bs-body-font-family), sans-serif;
      font-size: 0.95rem;
      line-height: 1.2;
      text-align: center;
      text-decoration: none;
      white-space: nowrap;
    }

    .christmas-gifts .christmas-gifts-cta-btn-primary {
      border: 1px solid #f5efe6;
      background: #f5efe6;
      color: #212529;
    }

    .christmas-gifts .christmas-gifts-cta-btn-secondary {
      border: 1px solid rgba(245, 239, 230, 0.85);
      background: transparent;
      color: #f5efe6;
    }

    .christmas-gifts .christmas-gifts-cta-btn-primary:hover,
    .christmas-gifts .christmas-gifts-cta-btn-primary:focus-visible {
      background: #fff;
      color: #212529;
    }

    .christmas-gifts .christmas-gifts-cta-btn-secondary:hover,
    .christmas-gifts .christmas-gifts-cta-btn-secondary:focus-visible {
      background: rgba(245, 239, 230, 0.12);
      color: #f5efe6;
    }

    @media (max-width: 767px) {
      .christmas-gifts .christmas-gifts-cta {
        flex-direction: column;
        align-items: stretch;
        padding: 32px 24px;
      }

      .christmas-gifts .christmas-gifts-cta-btn {
        width: 100%;
        white-space: normal;
      }
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

      var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      var paused = false;
      var timer = null;
      var looping = false;
      var setWidth = 0;
      var scrollTimer = null;

      function gap() {
        var styles = window.getComputedStyle(track);
        return parseFloat(styles.columnGap || styles.gap) || 0;
      }

      function realCards() {
        return Array.prototype.filter.call(track.children, function (el) {
          return !el.hasAttribute('data-clone');
        });
      }

      function step() {
        var card = realCards()[0];
        return card ? card.getBoundingClientRect().width + gap() : track.clientWidth;
      }

      function measureSet() {
        var cards = realCards();
        if (!cards.length) {
          return 0;
        }
        var first = cards[0];
        var last = cards[cards.length - 1];
        return last.offsetLeft + last.offsetWidth - first.offsetLeft + gap();
      }

      function silence(node) {
        node.setAttribute('data-clone', '1');
        node.setAttribute('aria-hidden', 'true');
        if ('inert' in node) {
          node.inert = true;
        }
        node.querySelectorAll('a, button, input, select, textarea, [tabindex]').forEach(function (el) {
          el.setAttribute('tabindex', '-1');
        });
      }

      function clearClones() {
        track.querySelectorAll('[data-clone]').forEach(function (node) {
          node.remove();
        });
      }

      function jump(left) {
        var behavior = track.style.scrollBehavior;
        track.style.scrollBehavior = 'auto';
        track.scrollLeft = left;
        track.style.scrollBehavior = behavior;
      }

      function normalize() {
        if (!looping || !setWidth) {
          return;
        }
        var left = track.scrollLeft;
        if (left < setWidth) {
          jump(left + setWidth);
        } else if (left >= setWidth * 2) {
          jump(left - setWidth);
        }
      }

      function go(direction) {
        if (!looping) {
          track.scrollBy({ left: direction * step(), behavior: reduce ? 'auto' : 'smooth' });
          return;
        }
        track.scrollBy({ left: direction * step(), behavior: reduce ? 'auto' : 'smooth' });
      }

      function stop() {
        if (timer) {
          window.clearInterval(timer);
          timer = null;
        }
      }

      function start() {
        stop();
        if (!looping || reduce || paused || document.hidden) {
          return;
        }
        timer = window.setInterval(function () {
          if (paused || document.hidden || !looping) {
            return;
          }
          go(1);
        }, 4000);
      }

      function setup() {
        clearClones();
        var cards = realCards();
        var width = 0;
        cards.forEach(function (card) {
          width += card.getBoundingClientRect().width;
        });
        width += gap() * Math.max(0, cards.length - 1);
        var overflow = cards.length > 1 && width > track.clientWidth + 4;
        slider.classList.toggle('is-static', !overflow);
        if (prev) {
          prev.hidden = !overflow;
        }
        if (next) {
          next.hidden = !overflow;
        }
        looping = overflow;
        if (!overflow) {
          stop();
          jump(0);
          return;
        }
        var before = document.createDocumentFragment();
        var after = document.createDocumentFragment();
        cards.forEach(function (card) {
          var lead = card.cloneNode(true);
          var tail = card.cloneNode(true);
          silence(lead);
          silence(tail);
          before.appendChild(lead);
          after.appendChild(tail);
        });
        track.insertBefore(before, track.firstChild);
        track.appendChild(after);
        setWidth = measureSet();
        jump(setWidth);
        start();
      }

      track.addEventListener('scroll', function () {
        window.clearTimeout(scrollTimer);
        scrollTimer = window.setTimeout(normalize, 120);
      });
      slider.addEventListener('mouseenter', function () { paused = true; stop(); });
      slider.addEventListener('mouseleave', function () { paused = false; start(); });
      slider.addEventListener('focusin', function () { paused = true; stop(); });
      slider.addEventListener('focusout', function (event) {
        if (!slider.contains(event.relatedTarget)) {
          paused = false;
          start();
        }
      });
      track.addEventListener('pointerdown', function () { paused = true; stop(); });
      window.addEventListener('pointerup', function () {
        if (paused && !slider.matches(':hover') && !slider.contains(document.activeElement)) {
          paused = false;
          start();
        }
      });
      document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
          stop();
        } else {
          start();
        }
      });
      if (prev) {
        prev.addEventListener('click', function () { go(-1); });
      }
      if (next) {
        next.addEventListener('click', function () { go(1); });
      }
      window.addEventListener('resize', setup);
      window.addEventListener('load', setup);
      setup();
    });
  </script>
@endsection
