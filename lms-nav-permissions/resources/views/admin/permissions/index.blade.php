@extends('admin.layout')

@section('content')
<div class="page-header">
    <h1>Roles &amp; permissions</h1>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.permissions.update') }}" method="POST">
    @csrf
    @method('PUT')

    @foreach ($roles as $role)
        <h3 class="permissions-role-title">{{ ucfirst(str_replace('_', ' ', $role)) }}</h3>

        <table class="admin-table permissions-table">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>View</th>
                    <th>Create</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($modules as $i => $module)
                    @php
                        $row = $existing->get($role)?->get($module->module_key);
                        $prefix = "permissions[{$role}_{$module->module_key}]";
                    @endphp
                    <tr>
                        <td>
                            {{ $module->label }}
                            <input type="hidden" name="{{ $prefix }}[role]" value="{{ $role }}">
                            <input type="hidden" name="{{ $prefix }}[module_key]" value="{{ $module->module_key }}">
                        </td>
                        <td><input type="checkbox" name="{{ $prefix }}[can_view]" value="1" @checked($row?->can_view)></td>
                        <td><input type="checkbox" name="{{ $prefix }}[can_create]" value="1" @checked($row?->can_create)></td>
                        <td><input type="checkbox" name="{{ $prefix }}[can_edit]" value="1" @checked($row?->can_edit)></td>
                        <td><input type="checkbox" name="{{ $prefix }}[can_delete]" value="1" @checked($row?->can_delete)></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <button type="submit" class="btn btn-primary">Save all permissions</button>
</form>
@endsection
