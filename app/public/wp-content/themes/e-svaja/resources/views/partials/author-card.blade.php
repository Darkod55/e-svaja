{{--
  Author card for single posts. Renders nothing when the biographical info is empty.
--}}
@php
  $post_id = function_exists('get_the_ID') ? get_the_ID() : 0;
  $post_id = is_numeric($post_id) ? (int) $post_id : 0;
  $post_type = ($post_id > 0 && function_exists('get_post_type')) ? get_post_type($post_id) : '';
  $post_type = is_string($post_type) ? $post_type : '';
  $author_id = ($post_id > 0 && function_exists('get_post_field')) ? get_post_field('post_author', $post_id) : 0;
  $author_id = is_numeric($author_id) ? (int) $author_id : 0;
  $author_name = '';
  $author_bio = '';
  if ($post_type === 'post' && $author_id > 0 && function_exists('get_the_author_meta')) {
      $name = get_the_author_meta('display_name', $author_id);
      $bio = get_the_author_meta('description', $author_id);
      $author_name = is_string($name) ? $name : '';
      $author_bio = is_string($bio) ? trim($bio) : '';
  }
  $author_bio_html = '';
  if ($author_bio !== '' && function_exists('wpautop') && function_exists('wp_kses_post')) {
      $author_bio_html = wp_kses_post(wpautop($author_bio));
      $plain = function_exists('wp_strip_all_tags') ? wp_strip_all_tags($author_bio_html) : $author_bio_html;
      if (! is_string($plain) || trim($plain) === '') {
          $author_bio_html = '';
      }
  }
  $author_avatar = '';
  if ($author_bio_html !== '' && $author_id > 0 && function_exists('get_avatar')) {
      $avatar = get_avatar($author_id, 200, '', $author_name, [
          'class' => 'author-card__avatar',
          'loading' => 'lazy',
          'decoding' => 'async',
      ]);
      $author_avatar = is_string($avatar) ? $avatar : '';
  }
@endphp

@if ($author_bio_html !== '')
  <aside class="author-card">
    @if ($author_avatar !== '')
      <div class="author-card__media">{!! $author_avatar !!}</div>
    @endif
    <div class="author-card__body">
      @if ($author_name !== '')
        <h2 class="author-card__name">{{ $author_name }}</h2>
        <span class="author-card__rule" aria-hidden="true"></span>
      @endif
      <div class="author-card__bio">{!! $author_bio_html !!}</div>
    </div>
  </aside>

  @once
    <style>
      .author-card {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        width: 100%;
        margin: 2.5rem 0 0;
        padding: 1.5rem 1.75rem;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 8px 28px rgba(33, 37, 41, 0.08);
        color: #212529;
      }

      .author-card__media {
        flex: 0 0 100px;
      }

      .author-card__avatar {
        display: block;
        width: 100px;
        height: 100px;
        max-width: none;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 6px 16px rgba(33, 37, 41, 0.16);
      }

      .author-card__body {
        min-width: 0;
      }

      .author-card__name {
        margin: 0;
        color: #212529;
        font-family: Poppins, var(--bs-body-font-family), sans-serif;
        font-size: 1.125rem;
        font-weight: 400;
        line-height: 1.3;
      }

      .author-card__rule {
        display: block;
        width: 40px;
        height: 2px;
        margin: 0.55rem 0 0.75rem;
        background: #f1bd58;
      }

      .author-card__bio {
        color: #212529;
        font-family: Poppins, var(--bs-body-font-family), sans-serif;
        font-size: 0.975rem;
        font-weight: 300;
        line-height: 1.55;
      }

      .author-card__bio p {
        margin: 0 0 0.5rem;
      }

      .author-card__bio p:last-child {
        margin-bottom: 0;
      }

      @media (max-width: 575.98px) {
        .author-card {
          flex-direction: column;
          align-items: center;
          padding: 1.5rem 1.25rem;
          text-align: center;
        }

        .author-card__rule {
          margin-right: auto;
          margin-left: auto;
        }
      }
    </style>
  @endonce
@endif
