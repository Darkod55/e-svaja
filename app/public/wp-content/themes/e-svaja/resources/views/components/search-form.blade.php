<form id="search-form" role="search" method="get" class="search-form m-0 border-0" action="{!! esc_url(home_url( '/' )) !!}">
  <label class="search-input-group p-rel d-flex ai-center">
    <input type="search" class="search-field form-control p-0 me-2" placeholder="{!! esc_attr_x( 'Search', 'placeholder' ) !!}" value="{!! get_search_query() !!}" name="s" />
    <button type="submit">@svgIcon('search|20|20')</button>
  </label>
</form>
