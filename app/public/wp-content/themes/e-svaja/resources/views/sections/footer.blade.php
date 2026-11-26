<footer class="p-clamp-y border-top">
  <div class="container-xxl p-clamp-x">
    <div class="row jc-between g-4">
      <div class="col-lg-3">
        <div class="border-bottom border-branded-horizontal pb-5 h-100">
          {!! $site_logo !!}
        </div>
      </div>
      <div class="col-md-6 col-lg-2">
        @if (has_nav_menu('footer_navigation'))
          <h2 class="h6 fw-bold tx-uc">{!! __('Pages', 'sage') !!}</h2>
          <nav class="nav-footer">
            {!! wp_nav_menu(['theme_location' => 'footer_navigation', 'menu_class' => 'nav d-flex flex-column']) !!}
          </nav>
        @endif
      </div>
      <div class="col-md-6 col-lg-2">
        @if (is_nav_menu('Menu About'))
        <h2 class="h6 fw-bold tx-uc">{!! __('About Svaja', 'sage') !!}</h2>
          <nav class="nav-about">
            {!! wp_nav_menu(['menu' => 'Menu About', 'menu_class' => 'nav d-flex-column']) !!}
          </nav>
        @endif
      </div>
      <div class="col-md-6 col-lg-2">
        <h2 class="h6 fw-bold tx-uc">{!! __('Contact Us', 'sage') !!}</h2>
        @if ($tel = get_field('phone', 'option') ?? false)
          <div class="phone"><a class="t-dark" href="tel:{!! App\helpers()->getSanitisedTel($tel) !!}">{!! $tel !!}</a></div>
        @endif
        @if ($email = get_field('email', 'option') ?? false)
          <div class="phone"><a class="t-dark" href="mailto:{!! $email !!}">{!! $email !!}</a></div>
        @endif
        @if (has_nav_menu('social_navigation'))
        <div class="social mt-3">
          <nav class="nav-social">
            {!! wp_nav_menu(['theme_location' => 'social_navigation', 'menu_class' => 'nav d-flex']) !!}
          </nav>
        </div>
        @endif
      </div>
      <div class="col-md-6 col-lg-2">
      <h2 class="h6 fw-bold tx-uc">{!! __('Company Details', 'sage') !!}</h2>
        @if ($address = get_field('address', 'option') ?? false)
          <address class="mb-0">
            {!! apply_filters('the_content_lite', $address) ?? '' !!}
          </address>          
        @endif
      </div>
    </div>
  </div>
</footer>
<p class="text-center bg-dark py-2 t-white mb-0 small">
  {!! __('© Svaja 2026. All rights reserved.', 'sage') !!}
  {!! __('A shop by ', 'sage') !!} <a href="https://metapixels.co.uk/" target="_blank" style="color: white !important; text-decoration: underline !important;">MetaPixels Ltd</a>
</p>
@includeIf('components.footer-icons')
