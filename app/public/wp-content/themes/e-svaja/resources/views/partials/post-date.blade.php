{{--
  Published date for post type `post` only. Uses Settings > General date format.
  Pass date_post (ID or WP_Post) when the card is not the current global post.
--}}
@php
  $date_post = $date_post ?? null;
  if ($date_post === false || $date_post === '' || $date_post === 0 || $date_post === '0') {
      $date_post = null;
  }
  $date_type = function_exists('get_post_type') ? get_post_type($date_post) : '';
  $date_type = is_string($date_type) ? $date_type : '';
  $date_iso = '';
  $date_label = '';
  if ($date_type === 'post' && function_exists('get_the_date')) {
      $iso = get_the_date('c', $date_post);
      $label = get_the_date('', $date_post);
      $date_iso = is_string($iso) ? $iso : '';
      $date_label = is_string($label) ? $label : '';
  }
@endphp

@if ($date_label !== '')
  <time class="post-date" datetime="{{ $date_iso }}">{{ $date_label }}</time>
  @once
    <style>
      .post-date {
        display: block;
        margin: 0.35rem 0 0.75rem;
        color: rgba(33, 37, 41, 0.7);
        font-family: Poppins, var(--bs-body-font-family), sans-serif;
        font-size: 0.875rem;
        font-weight: 300;
        line-height: 1.4;
      }

      .card-content > h2:has(+ .post-date),
      .post-single-grid h1:has(+ .post-date) {
        margin-bottom: 0;
      }
    </style>
  @endonce
@endif
