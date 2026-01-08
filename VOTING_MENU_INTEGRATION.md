# 📌 INTEGRAÇÃO DO MENU - Passo a Passo

## Arquivo a editar:
`Modules/Frontend/Resources/views/components/partials/horizontal-nav.blade.php`

## Adicione este código ANTES da closing `</ul>`:

```blade
<li class="nav-item">
    <a class="nav-link" href="{{ route('voting.index') }}">
        <i class="ph-fill ph-heart me-2"></i>
        <span class="item-name">
            @if(userHasVotingAccess())
                Votação
            @else
                <i class="ph ph-lock"></i> Votação
            @endif
        </span>
    </a>
</li>
```

## Resultado esperado:
- ✅ Item "Votação" aparece ao lado de "Em Breve"
- ✅ Com acesso: Icone de coração ❤️
- ✅ Sem acesso: Icone de cadeado 🔒
- ✅ Ao clicar: Vai para `/voting`

## Código completo do arquivo após adição:

```blade
<!-- Horizontal Menu Start -->
<nav id="navbar_main" class="offcanvas mobile-offcanvas nav navbar navbar-expand-xl hover-nav horizontal-nav py-xl-0">
  <div class="container-fluid p-lg-0">
    <div class="offcanvas-header">
      <div class="navbar-brand p-0">
        <!--Logo -->
        @include('frontend::components.partials.logo')

      </div>
      <button type="button" class="btn-close p-0" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <ul class="navbar-nav iq-nav-menu  list-unstyled" id="header-menu">
      <li class="nav-item">
        <a class="nav-link"  href="{{route('user.login')}}">
          <span class="item-name">{{__('frontend.home')}}</span>
        </a>
      </li>
      @if(isenablemodule('movie'))
      <li class="nav-item">
        <a class="nav-link"  href="{{ route('movies') }}">
          <span class="item-name">{{__('frontend.movies')}}</span>
        </a>
      </li>
      @endif

      <li class="nav-item">
        <a class="nav-link"  href="{{ route('movie-castcrew-list',['id' => 'all' ,'type' => 'actor']) }}">
          <span class="item-name">{{__('frontend.personality')}}</span>
        </a>
      </li>

      {{-- @if(isenablemodule('tvshow'))
      <li class="nav-item">
        <a class="nav-link"  href="{{ route('tv-shows') }}">
          <span class="item-name">{{__('frontend.tvshows')}}</span>
        </a>
      </li>
      @endif --}}
      @if(isenablemodule('video'))
      <li class="nav-item">
        <a class="nav-link"  href="{{ route('videos') }}">
          <span class="item-name">{{__('frontend.video')}}</span>
        </a>
      </li>
      @endif
      <li class="nav-item">
        <a class="nav-link"  href="{{ route('comingsoon') }}">
          <span class="item-name">{{__('frontend.coming_soon')}}</span>
        </a>
      </li>

      <!-- NOVO ITEM: Votação da Comunidade -->
      <li class="nav-item">
        <a class="nav-link" href="{{ route('voting.index') }}">
          <span class="item-name">
            @if(!userHasVotingAccess())
              <i class="ph ph-lock me-2"></i>
            @else
              <i class="ph-fill ph-heart me-2"></i>
            @endif
            Votação
          </span>
        </a>
      </li>
      <!-- FIM DO NOVO ITEM -->

      @if(isenablemodule('livetv'))
      <li class="nav-item">
        <a class="nav-link"  href="{{route('livetv')}}">
          <span class="item-name">{{__('frontend.livetv')}}</span>
        </a>
      </li>
      @endif
    </ul>
  </div>
  <!-- container-fluid.// -->
</nav>
<!-- Horizontal Menu End -->
```

---

## Próximos passos:

1. Editar o arquivo mencionado acima e adicionar o código
2. Limpar cache: `php artisan optimize:clear`
3. Recarregar a página no navegador
4. Item "Votação" deve aparecer no menu!

Pronto! A feature está completa e integrada! 🚀
