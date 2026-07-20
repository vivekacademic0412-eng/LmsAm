<div>
    @if (session('success'))
        <div class="alert alert-success"><i class="ti ti-check"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger"><i class="ti ti-alert-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card__header">
            <div>
                <div class="card__title"><i class="ti ti-folders"></i> Module categories</div>
                <div class="card__subtitle">Top level of the navigation tree — categories group your modules.</div>
            </div>
            <button type="button" class="btn btn-primary" wire:click="openCreate">
                <i class="ti ti-plus"></i> Add category
            </button>
        </div>

        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:60px">Order</th>
                        <th>Category</th>
                        <th>Modules</th>
                        <th>Status</th>
                        <th style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr wire:key="cat-{{ $category->id }}">
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                <div class="tree-module__title">
                                    @if ($category->icon)<i class="{{ $category->icon }}"></i>@endif
                                    {{ $category->name }}
                                </div>
                            </td>
                            <td><span class="chip">{{ $category->modules_count }} module(s)</span></td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" wire:click="toggleStatus({{ $category->id }})" @checked($category->status)>
                                    <span class="slider"></span>
                                </label>
                            </td>
                            <td>
                                <div class="tree-module__actions">
                                    <button class="btn btn-sm btn-ghost" wire:click="edit({{ $category->id }})"><i class="ti ti-edit"></i></button>
                                    <button class="btn btn-sm btn-ghost" wire:click="delete({{ $category->id }})"
                                            onclick="return confirm('Delete this category?')"><i class="ti ti-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">
                            <div class="empty-state"><i class="ti ti-folder-off"></i> No categories yet — add your first one.</div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($showModal)
        <div class="modal-backdrop" wire:click.self="closeModal">
            <div class="modal-panel">
                <div class="modal-panel__header">
                    <div class="modal-panel__title">{{ $editingId ? 'Edit category' : 'New category' }}</div>
                    <button class="modal-panel__close" wire:click="closeModal"><i class="ti ti-x"></i></button>
                </div>

                <form wire:submit="save">
                    <div class="form-group">
                        <label class="form-label">Category name</label>
                        <input type="text" class="form-input @error('name') is-invalid @enderror"
                               wire:model="name" placeholder="e.g. Academics">
                        @error('name') <div class="form-error"><i class="ti ti-alert-circle"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Icon class</label>
                            <input type="text" class="form-input @error('icon') is-invalid @enderror"
                                   wire:model="icon" placeholder="ti ti-book">
                            @error('icon') <div class="form-error"><i class="ti ti-alert-circle"></i>{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sort order</label>
                            <input type="number" class="form-input @error('sort_order') is-invalid @enderror"
                                   wire:model="sort_order">
                            @error('sort_order') <div class="form-error"><i class="ti ti-alert-circle"></i>{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                        <label class="switch">
                            <input type="checkbox" wire:model="status">
                            <span class="slider"></span>
                        </label>
                        <span class="form-label" style="margin:0;">Active</span>
                    </div>

                    <div class="modal-panel__actions">
                        <button type="button" class="btn btn-ghost" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save"><i class="ti ti-check"></i> Save category</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>