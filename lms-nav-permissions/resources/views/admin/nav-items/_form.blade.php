@php
    $id = $mode === 'edit' ? "edit-{$navItem->id}" : 'nav-item-create';
    $action = $mode === 'edit' ? route('admin.nav-items.update', $navItem) : route('admin.nav-items.store');
@endphp

<div class="modal" id="{{ $id }}">
    <div class="modal__panel">
        <h3>{{ $mode === 'edit' ? 'Edit menu item' : 'New menu item' }}</h3>

        <form action="{{ $action }}" method="POST">
            @csrf
            @if ($mode === 'edit') @method('PUT') @endif

            <label>Parent (optional — leave blank for a top-level item)
                <select name="parent_id">
                    <option value="">— none —</option>
                    @foreach ($parents as $p)
                        @if (!$navItem || $p->id !== $navItem->id)
                            <option value="{{ $p->id }}" @selected($navItem && $navItem->parent_id == $p->id)>{{ $p->label }}</option>
                        @endif
                    @endforeach
                </select>
            </label>

            <label>Module key (unique, links to permissions)
                <input type="text" name="module_key" value="{{ $navItem->module_key ?? '' }}" required>
            </label>

            <label>Label
                <input type="text" name="label" value="{{ $navItem->label ?? '' }}" required>
            </label>

            <label>Icon class (e.g. ti ti-users)
                <input type="text" name="icon" value="{{ $navItem->icon ?? '' }}">
            </label>

            <label>Route name
                <input type="text" name="route" value="{{ $navItem->route ?? '' }}">
            </label>

            <label>Sort order
                <input type="number" name="sort_order" value="{{ $navItem->sort_order ?? 0 }}">
            </label>

            <label>
                <input type="checkbox" name="status" value="1" @checked(!$navItem || $navItem->status)>
                Active (visible in menu)
            </label>

            <div class="modal__actions">
                <button type="button" onclick="document.getElementById('{{ $id }}').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
