@include('sections.header')
<div class="wrap container-fluid px-0" role="document">
  <div class="content">
    <main class="main {!! (!is_archive()) ? 'ovf-hidden' : '' !!}">
      @yield('content')
    </main>
  </div>
</div>
@include('sections.footer')