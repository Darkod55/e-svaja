@includeIf('components.header-icons')
<section id="preheader" class="py-1 d-none d-md-block bg-tertiary">
  <div class="container-xxl d-flex jc-between ai-center p-clamp-x">
    @if (has_nav_menu('social_navigation'))
      <div class="social">
        <nav class="nav-social">
          {!! wp_nav_menu(['theme_location' => 'social_navigation', 'menu_class' => 'nav d-flex']) !!}
        </nav>
      </div>
    @endif
    @if (is_nav_menu('Menu Preheader'))
      <nav class="nav-preheader">
        {!! wp_nav_menu(['menu' => 'Menu Preheader', 'menu_class' => 'nav d-flex ai-center']) !!}
      </nav>
    @endif
  </div>
</section>

<header id="global-header" class="py-3 sticky-top bg-white ovf-hidden">
  <div class="container-xxl p-clamp-x">
    <div class="row ai-center">
      <div class="col-md-3">
        {!! $site_logo !!}
      </div>
      <div class="col-md-9 d-flex jc-end ai-center col-icons">
        <div class="menu-header d-flex ai-center">
          <div class="navbar navbar-expand-lg py-0">
            @includeIf('components.header-offcanvas-toggler')
            <div class="offcanvas offcanvas-start-lg" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
              <div class="offcanvas-body ai-center">
                @includeIf('components.header-offcanvas-nav')
              </div>
            </div>
          </div>
        </div>
        <div class="search-vip d-flex">
          @includeIf('components.search-form')
          @if ($vip = get_field('vip', 'option') ?? false)
            <a href="{!! $vip['url'] !!}" class="btn btn-primary btn-header ms-4 d-none d-lg-block">{!! $vip['title'] !!} @svgIcon('crown|18|18')</a>
          @endif
        </div>
        <div class="woo-box d-flex ai-center border-start border-dark ms-md-4 ps-md-4">
          <a href="{!! is_user_logged_in() ? '/my-account' : '/login' !!}" class="user-icon me-3">@svgIcon('user|20|20')</a>
          <a class="p-rel d-flex-column flex-center cart @if(WC()->cart->get_cart_contents_count() != 0) basket-full @endif" href="{{ wc_get_cart_url() }}">@svgIcon('cart|20|20') <span class="cart-count">{!! WC()->cart->get_cart_contents_count() !!}</span></a>
        </div>
      </div>
    </div>
  </div>
</header>
@if (get_field('notification_message', 'option') ?? false)
<aside id="notf-bar" class="bg-secondary text-center p-2 t-dark text-notification">
  {!! get_field('notification_message', 'option') !!}
</aside>
@endif