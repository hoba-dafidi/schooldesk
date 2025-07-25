<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- App Brand -->
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                @include('_partials.macros', ['width' => 25, 'withbg' => 'var(--bs-primary)'])
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">
                {{ config('variables.templateName') }}
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        $user = Auth::user();
    @endphp

    <ul class="menu-inner py-1">
        @foreach ($menuData[0]->menu as $menu)
            @php
                // Normalize 'type' to array if it exists
                $types = isset($menu->type) ? (array) $menu->type : null;
            @endphp

            {{-- Skip this menu item if type is defined and user does not have any of the roles --}}
            @if ($types && !$user->hasAnyRole($types))
                @continue
            @endif

            {{-- Menu Header --}}
            @if (isset($menu->menuHeader))
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                </li>

                {{-- Menu Item --}}
            @else
                <li class="menu-item {{ isActiveMenu($menu->slug) }}">
                    <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                        class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                        @if (!empty($menu->target)) target="_blank" @endif>
                        @isset($menu->icon)
                            <i class="{{ $menu->icon }}"></i>
                        @endisset
                        <div>{{ __($menu->name ?? '') }}</div>
                        @isset($menu->badge)
                            <div class="badge rounded-pill bg-{{ $menu->badge[0] }} text-uppercase ms-auto">
                                {{ $menu->badge[1] }}
                            </div>
                        @endisset
                    </a>

                    @isset($menu->submenu)
                        @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
                    @endisset
                </li>
            @endif
        @endforeach
    </ul>
</aside>
