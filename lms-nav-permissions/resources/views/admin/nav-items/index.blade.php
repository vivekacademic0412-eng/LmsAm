@extends('admin.layout')

@section('content')
<div class="page-header">
    <h1>Navigation builder</h1>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('nav-item-create').classList.add('open')">
        + Add menu item
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="admin-table">
    <thead>
        <tr>
            <th>Sort</th>
            <th>Label</th>
            <th>Module key</th>
            <th>Route</th>
            <th>Icon</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($navItems as $item)
            <tr>
                <td>{{ $item->sort_order }}</td>
                <td>{{ $item->label }}</td>
                <td><code>{{ $item->module_key }}</code></td>
                <td>{{ $item->route }}</td>
                <td>{{ $item->icon }}</td>
                <td>
                    <span class="badge {{ $item->status ? 'badge-success' : 'badge-muted' }}">
                        {{ $item->status ? 'Active' : 'Hidden' }}
                    </span>
                </td>
                <td>
                    <button type="button" onclick="document.getElementById('edit-{{ $item->id }}').classList.add('open')">Edit</button>
                    <form action="{{ route('admin.nav-items.destroy', $item) }}" method="POST" style="display:inline"
                          onsubmit="return confirm('Delete this menu item?')">
                        @csrf @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>

            @foreach ($item->children as $child)
                <tr class="admin-table__child">
                    <td>{{ $child->sort_order }}</td>
                    <td>— {{ $child->label }}</td>
                    <td><code>{{ $child->module_key }}</code></td>
                    <td>{{ $child->route }}</td>
                    <td>{{ $child->icon }}</td>
                    <td>
                        <span class="badge {{ $child->status ? 'badge-success' : 'badge-muted' }}">
                            {{ $child->status ? 'Active' : 'Hidden' }}
                        </span>
                    </td>
                    <td>
                        <button type="button" onclick="document.getElementById('edit-{{ $child->id }}').classList.add('open')">Edit</button>
                        <form action="{{ route('admin.nav-items.destroy', $child) }}" method="POST" style="display:inline"
                              onsubmit="return confirm('Delete this menu item?')">
                            @csrf @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach

            @include('admin.nav-items._form', ['mode' => 'edit', 'navItem' => $item, 'parents' => $parents])
            @foreach ($item->children as $child)
                @include('admin.nav-items._form', ['mode' => 'edit', 'navItem' => $child, 'parents' => $parents])
            @endforeach
        @endforeach
    </tbody>
</table>

@include('admin.nav-items._form', ['mode' => 'create', 'navItem' => null, 'parents' => $parents])
@endsection
