
@php
$footerData = getFooterData();
@endphp

<footer class="footer">
  <div class="footer-bottom">
    <div class="container-fluid">
      <div class="text-center">
        © {{ now()->year }}<span class="text-primary"> {{ config('app.name', 'Laravel') }} </span>. {{__('frontend.all_rights_reserved')}}.
      </div>
    </div>
  </div>
</footer>
<!-- sticky footer -->
  @include('frontend::components.partials.footer-sticky-menu')
