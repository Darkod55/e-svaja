<a class="btn btn-dark d-block d-lg-none mb-4" data-bs-toggle="offcanvas" href="#catsNav" role="button" aria-controls="catsNav">{{ __('View Categories', 'sage') }}</a>
<aside class="product_cat offcanvas offcanvas-start-lg" tabindex="-1" id="catsNav" aria-labelledby="catsNavLabel">
  <button type="button" class="btn-close text-reset d-block d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  <ul>
    <li class="cat-item {{ is_shop() ? 'current-cat' : '' }}"><a href="{{ get_permalink(wc_get_page_id( 'shop')) }}">{{ __('All Products', 'sage') }}</a></li>
    <li class="cat-item {{ is_page() ? 'current-cat' : '' }}"><a href="{{ home_url('collections') }}">{{ __('All Collections', 'sage') }}</a></li>
    {!! wp_list_categories( array('taxonomy' => 'product_cat', 'title_li' => '') ); !!}
  </ul>
</aside>