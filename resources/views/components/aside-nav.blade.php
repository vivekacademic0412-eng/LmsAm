<aside class="sidebar" id="sidebar" role="complementary" aria-label="Main navigation">

    {{-- ── Brand ──────────────────────────────────── --}}
    <div class="sb-brand">
        <a href="{{ route('dashboard') }}" class="sb-logo" aria-label="Academic Mantra LMS — Home">
            <div class="sb-brand-text">
                <img src="{{ asset('theme/images/am35.png') }}" alt="LIVE Skills" class="brand-logo"
                     title="LIVE Skills Training Programs" loading="lazy">
            </div>
        </a>
    </div>

    {{-- ── Navigation ──────────────────────────────── --}}
    <nav class="sb-nav" role="navigation" aria-label="Main navigation">

        @php
            $path = request()->path();

            $isActive = function (?string $routeName) use ($path): bool {
                if (!$routeName || !\Illuminate\Support\Facades\Route::has($routeName)) {
                    return false;
                }
                $routePath = trim(parse_url(route($routeName), PHP_URL_PATH), '/');
                return $path === $routePath || str_starts_with($path, $routePath . '/');
            };

            $navUrl = fn (?string $routeName) => $routeName && \Illuminate\Support\Facades\Route::has($routeName)
                ? route($routeName)
                : '#';

            $slug = fn (string $key) => 'grp-' . str_replace('_', '-', $key);
        @endphp

        @forelse ($categories as $category)

            <div class="sb-section" aria-hidden="true">{{ $category->name }}</div>

            @foreach ($category->modules as $item)

                @if ($item->children->isNotEmpty())

                    {{-- ── Group with sub-items ─────────────── --}}
                    @php
                        $groupId = $slug($item->module_key);
                        $groupActive = $item->children->contains(fn ($c) => $isActive($c->route));
                    @endphp

                    <div class="sb-group {{ $groupActive ? 'open' : '' }}" id="{{ $groupId }}">
                        <button type="button"
                                class="sb-group-trigger {{ $groupActive ? 'has-active' : '' }}"
                                onclick="toggleGroup('{{ $groupId }}')"
                                aria-expanded="{{ $groupActive ? 'true' : 'false' }}"
                                aria-controls="sub-{{ $item->module_key }}">
                            @if ($item->icon)
                                <i class="{{ $item->icon }} item-icon" aria-hidden="true"></i>
                            @endif
                            <span class="item-label">{{ $item->label }}</span>
                            <i class="ti ti-chevron-down sb-chevron" aria-hidden="true"></i>
                        </button>
                        <div class="sb-sub" id="sub-{{ $item->module_key }}">
                            <div class="sb-sub-inner">
                                @foreach ($item->children as $child)
                                    @php $childActive = $isActive($child->route); @endphp
                                    <a href="{{ $navUrl($child->route) }}"
                                       class="sb-sub-item {{ $childActive ? 'active' : '' }}"
                                       {{ $childActive ? 'aria-current=page' : '' }}>
                                        @if ($child->icon)
                                            <i class="{{ $child->icon }}" aria-hidden="true"></i>
                                        @endif
                                        {{ $child->label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                @else

                    {{-- ── Plain leaf item ──────────────────── --}}
                    @php $active = $isActive($item->route); @endphp
                    <a href="{{ $navUrl($item->route) }}"
                       class="sb-item {{ $active ? 'active' : '' }}"
                       {{ $active ? 'aria-current=page' : '' }}>
                        @if ($item->icon)
                            <i class="{{ $item->icon }} item-icon" aria-hidden="true"></i>
                        @endif
                        <span class="item-label">{{ $item->label }}</span>
                    </a>

                @endif

            @endforeach

            @if (!$loop->last)
                <div class="sb-divider"></div>
            @endif

        @empty
            <div class="sb-section" aria-hidden="true">Menu</div>
            <div class="sb-item" style="opacity:.6;cursor:default;">
                <span class="item-label">No menu items for your role yet.</span>
            </div>
        @endforelse

    </nav>

    {{-- ── Bottom User Card ────────────────────────── --}}
    <div class="sb-bottom">

        <a href="{{ route('profile.edit') }}"
           class="sb-user"
           aria-label="Go to profile — {{ $user->name }}">
            <div class="sb-avatar" aria-hidden="true">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}"
                         alt="{{ $user->name }}"
                         style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div class="sb-user-info">
                <div class="sb-user-name">{{ $user->name }}</div>
                <div class="sb-user-role">{{ $roleLabels[$user->role] ?? 'User' }}</div>
            </div>
            <i class="ti ti-chevron-right sb-user-chevron" aria-hidden="true"></i>
        </a>

        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="sb-logout" aria-label="Sign out of Academic Mantra LMS">
                <i class="ti ti-logout" aria-hidden="true"></i>
                <span>Sign Out</span>
            </button>
        </form>

    </div>

</aside>


{{-- ── Sidebar collapse toggle (desktop) ──────── --}}
<button class="sb-toggle"
        id="sbToggleBtn"
        aria-label="Collapse sidebar"
        aria-expanded="true"
        aria-controls="sidebar"
        onclick="toggleSidebar()">
    <i class="ti ti-chevron-left"></i>
</button>

{{-- ── Mobile overlay ──────────────────────────── --}}
<div class="sb-overlay"
     id="sbOverlay"
     aria-hidden="true"
     onclick="closeMobileSidebar()"></div>