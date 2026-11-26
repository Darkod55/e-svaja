{{--
  Shared seasonal hero. Used by the homepage (default) and the Christmas Gifts page.
  Pass image_id, alt_fallback, eyebrow, offer, title, intro, buttons, lcp.
  Title lines: "|" or " – " splits the italic second line; otherwise the latter half of the words.
--}}
@php
$image_id = 33508; //isset($image_id) && is_numeric($image_id) ? (int) $image_id : 0;
$alt = '';
if ($image_id > 0 && function_exists('get_post_meta')) {
    $meta = get_post_meta($image_id, '_wp_attachment_image_alt', true);
    $alt = is_string($meta) ? trim($meta) : '';
}
if ($alt === '') {
    $alt = isset($alt_fallback) && is_string($alt_fallback) ? $alt_fallback : '';
}
$image_html = '';
if ($image_id > 0 && function_exists('wp_get_attachment_image')) {
    $attrs = [
        'class' => 'w-100 img-cover' . (! empty($lcp) ? ' home-hero-lcp' : ''),
        'alt' => $alt,
        'loading' => 'eager',
        'fetchpriority' => 'high',
        'decoding' => 'sync',
        'sizes' => '100vw',
    ];
    $rendered = wp_get_attachment_image($image_id, 'full', false, $attrs);
    $image_html = is_string($rendered) ? $rendered : '';
}
$eyebrow = isset($eyebrow) && is_string($eyebrow) ? trim($eyebrow) : '';
$offer = isset($offer) && is_string($offer) ? $offer : '';
$intro = isset($intro) && is_string($intro) ? $intro : '';
$title_html = \App\christmas_gifts_title_html(isset($title) && is_string($title) ? $title : '');
$buttons = isset($buttons) && is_array($buttons) ? $buttons : [];
@endphp

<section class="svaja-seasonal-hero p-rel ovf-hidden{{ $image_html === '' ? ' is-fallback' : '' }}">
  @if ($image_html !== '')
    {!! $image_html !!}
    <div class="svaja-seasonal-hero__shade" aria-hidden="true"></div>
  @endif
  <div class="svaja-seasonal-hero__copy zi-1">
    @if ($eyebrow !== '')
      <p class="svaja-seasonal-hero__eyebrow">{{ $eyebrow }}</p>
      <span class="svaja-seasonal-hero__rule" aria-hidden="true"></span>
    @endif
    @if ($offer !== '')
      <p class="svaja-seasonal-hero__offer">{{ $offer }}</p>
    @endif
    <h1>{!! $title_html !!}</h1>
    @if ($intro !== '')
      <p class="svaja-seasonal-hero__intro">{{ $intro }}</p>
    @endif
    @if ($buttons !== [])
      <div class="svaja-seasonal-hero__actions">
        @foreach ($buttons as $button)
          @php
            $label = is_array($button) && isset($button['label']) && is_string($button['label']) ? $button['label'] : '';
            $url = is_array($button) && isset($button['url']) && is_string($button['url']) ? $button['url'] : '';
            $secondary = is_array($button) && ! empty($button['secondary']);
          @endphp
          @if ($label !== '' && $url !== '')
            <a class="svaja-seasonal-hero__btn{{ $secondary ? ' is-secondary' : '' }}" href="{{ $url }}">{{ $label }}</a>
          @endif
        @endforeach
      </div>
    @endif
  </div>
</section>

@once
  <style>
    .svaja-seasonal-hero {
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

    .svaja-seasonal-hero > img {
      position: absolute;
      inset: 0;
      z-index: 0;
      width: 100%;
      height: 100%;
      max-width: none;
      object-fit: cover;
      object-position: center right;
    }

    .svaja-seasonal-hero__shade {
      position: absolute;
      inset: 0;
      z-index: 0;
      background: linear-gradient(90deg, rgba(0, 0, 0, 0.82) 0%, rgba(0, 0, 0, 0.62) 34%, rgba(0, 0, 0, 0.28) 62%, rgba(0, 0, 0, 0.08) 100%);
    }

    .svaja-seasonal-hero__copy {
      position: relative;
      z-index: 1;
      margin-left: 8%;
      max-width: 55%;
      padding: 4rem 1.5rem 4rem 0;
      text-align: left;
    }

    .svaja-seasonal-hero__eyebrow,
    .svaja-seasonal-hero__offer,
    .svaja-seasonal-hero__btn {
      font-family: Poppins, var(--bs-body-font-family), sans-serif;
      font-weight: 400;
      font-size: 0.9375rem;
      letter-spacing: 0.25em;
      line-height: 1.4;
      text-transform: uppercase;
    }

    .svaja-seasonal-hero__eyebrow,
    .svaja-seasonal-hero__offer {
      margin: 0;
      color: var(--bs-primary, #f1bd58);
    }

    .svaja-seasonal-hero__rule {
      display: block;
      width: 60px;
      height: 1px;
      margin: 0.9rem 0 1.15rem;
      background: var(--bs-primary, #f1bd58);
    }

    .svaja-seasonal-hero__offer {
      margin-bottom: 1rem;
      font-size: 0.8rem;
    }

    .svaja-seasonal-hero h1 {
      margin: 0 0 1rem;
      color: #f5efe6;
      font-family: Poppins, var(--bs-body-font-family), sans-serif;
      font-size: clamp(2.5rem, 6vw, 5rem);
      font-weight: 300;
      letter-spacing: -0.02em;
      line-height: 1.05;
    }

    .svaja-seasonal-hero h1 em {
      font-style: italic;
      font-weight: 300;
    }

    .svaja-seasonal-hero__intro {
      max-width: 520px;
      margin: 0;
      color: #f5efe6;
      font-family: Poppins, var(--bs-body-font-family), sans-serif;
      font-size: 1.1rem;
      font-weight: 400;
      line-height: 1.6;
    }

    .svaja-seasonal-hero__actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      margin-top: 1.5rem;
    }

    .svaja-seasonal-hero__btn {
      display: inline-block;
      padding: 16px 30px;
      border: 1px solid #f5efe6;
      background: transparent;
      color: #f5efe6;
      font-size: 0.8rem;
      text-decoration: none;
    }

    .svaja-seasonal-hero__btn.is-secondary {
      border-color: rgba(245, 239, 230, 0.7);
    }

    .svaja-seasonal-hero__btn:hover,
    .svaja-seasonal-hero__btn:focus-visible {
      background: #f5efe6;
      color: #212529;
    }

    @media (max-width: 767px) {
      .svaja-seasonal-hero {
        min-height: 36rem;
      }

      .svaja-seasonal-hero__shade {
        background: linear-gradient(90deg, rgba(0, 0, 0, 0.82) 0%, rgba(0, 0, 0, 0.68) 58%, rgba(0, 0, 0, 0.4) 100%);
      }

      .svaja-seasonal-hero__copy {
        margin-left: 0;
        max-width: 100%;
        padding: 3rem 1.25rem;
      }

      .svaja-seasonal-hero__eyebrow,
      .svaja-seasonal-hero__offer,
      .svaja-seasonal-hero__btn {
        font-size: 0.75rem;
        letter-spacing: 0.18em;
      }
    }
  </style>
@endonce
