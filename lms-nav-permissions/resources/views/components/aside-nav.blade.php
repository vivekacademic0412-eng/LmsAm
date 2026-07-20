<aside class="aside-nav">
    <nav>
        <ul class="aside-nav__list">
            @forelse ($items as $item)
                <li class="aside-nav__item @if($item->children->isNotEmpty()) aside-nav__item--parent @endif">
                    <a href="{{ $item->route ? route($item->route) : '#' }}" class="aside-nav__link">
                        @if ($item->icon)
                            <i class="{{ $item->icon }}"></i>
                        @endif
                        <span>{{ $item->label }}</span>
                    </a>

                    @if ($item->children->isNotEmpty())
                        <ul class="aside-nav__sublist">
                            @foreach ($item->children as $child)
                                <li class="aside-nav__subitem">
                                    <a href="{{ $child->route ? route($child->route) : '#' }}">
                                        @if ($child->icon)
                                            <i class="{{ $child->icon }}"></i>
                                        @endif
                                        <span>{{ $child->label }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @empty
                <li class="aside-nav__empty">No menu items for your role yet.</li>
            @endforelse
        </ul>
    </nav>
</aside>
