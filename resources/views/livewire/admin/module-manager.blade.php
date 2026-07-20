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
                <div class="card__title"><i class="ti ti-sitemap"></i> Modules &amp; child modules</div>
                <div class="card__subtitle">Category → Module → Child Module. This tree drives the left sidebar.</div>
            </div>
            <button type="button" class="btn btn-primary" wire:click="openCreate">
                <i class="ti ti-plus"></i> Add module
            </button>
        </div>

        <div class="tree">
            @forelse ($tree as $category)
                <div class="tree-category" wire:key="tree-cat-{{ $category->id }}">
                    <div class="tree-category__head">
                        <div class="tree-category__title">
                            @if ($category->icon)<i class="{{ $category->icon }}"></i>@endif
                            {{ $category->name }}
                            <span class="badge badge-muted">{{ $category->modules->count() }} module(s)</span>
                        </div>
                        <button class="btn btn-sm btn-ghost" wire:click="openCreate({{ $category->id }})">
                            <i class="ti ti-plus"></i> Add module here
                        </button>
                    </div>

                    <div class="tree-category__body">
                        @forelse ($category->modules as $module)
                            <div class="tree-module" wire:key="tree-mod-{{ $module->id }}">
                                <div class="tree-module__row">
                                    <div class="tree-module__title">
                                        @if ($module->icon)<i class="{{ $module->icon }}"></i>@endif
                                        {{ $module->label }}
                                        <code>{{ $module->module_key }}</code>
                                        @unless($module->status)<span class="badge badge-muted">Hidden</span>@endunless
                                    </div>
                                    <div class="tree-module__actions">
                                        <button class="btn btn-sm btn-ghost" wire:click="openCreate({{ $category->id }}, {{ $module->id }})">
                                            <i class="ti ti-plus"></i> Child
                                        </button>
                                        <label class="switch">
                                            <input type="checkbox" wire:click="toggleStatus({{ $module->id }})" @checked($module->status)>
                                            <span class="slider"></span>
                                        </label>
                                        <button class="btn btn-sm btn-ghost" wire:click="edit({{ $module->id }})"><i class="ti ti-edit"></i></button>
                                        <button class="btn btn-sm btn-ghost" wire:click="delete({{ $module->id }})"
                                                onclick="return confirm('Delete this module?')"><i class="ti ti-trash"></i></button>
                                    </div>
                                </div>

                                @foreach ($module->children as $child)
                                    <div class="tree-child" wire:key="tree-child-{{ $child->id }}">
                                        <div class="tree-child__title">
                                            @if ($child->icon)<i class="{{ $child->icon }}"></i>@endif
                                            {{ $child->label }}
                                            <code>{{ $child->module_key }}</code>
                                            @unless($child->status)<span class="badge badge-muted">Hidden</span>@endunless
                                        </div>
                                        <div class="tree-module__actions">
                                            <label class="switch">
                                                <input type="checkbox" wire:click="toggleStatus({{ $child->id }})" @checked($child->status)>
                                                <span class="slider"></span>
                                            </label>
                                            <button class="btn btn-sm btn-ghost" wire:click="edit({{ $child->id }})"><i class="ti ti-edit"></i></button>
                                            <button class="btn btn-sm btn-ghost" wire:click="delete({{ $child->id }})"
                                                    onclick="return confirm('Delete this child module?')"><i class="ti ti-trash"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="empty-state" style="padding:16px;"><i class="ti ti-box-off"></i> No modules in this category yet.</div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="empty-state"><i class="ti ti-folder-off"></i> Create a category first, then add modules under it.</div>
            @endforelse
        </div>
    </div>

    @if ($showModal)
        <div class="modal-backdrop" wire:click.self="closeModal">
            <div class="modal-panel">
                <div class="modal-panel__header">
                    <div class="modal-panel__title">{{ $editingId ? 'Edit module' : ($parent_id ? 'New child module' : 'New module') }}</div>
                    <button class="modal-panel__close" wire:click="closeModal"><i class="ti ti-x"></i></button>
                </div>

                <form wire:submit="save">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" wire:model.live="category_id">
                                <option value="">Select category…</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="form-error"><i class="ti ti-alert-circle"></i>{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Parent module <span style="font-weight:400;color:var(--text-muted)">(optional — makes this a child module)</span></label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" wire:model="parent_id" @disabled(!$category_id)>
                                <option value="">— top-level module —</option>
                                @foreach ($parentOptions as $p)
                                    <option value="{{ $p->id }}">{{ $p->label }}</option>
                                @endforeach
                            </select>
                            @error('parent_id') <div class="form-error"><i class="ti ti-alert-circle"></i>{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Module key <span style="font-weight:400;color:var(--text-muted)">(unique, used by permissions & routes)</span></label>
                        <input type="text" class="form-input @error('module_key') is-invalid @enderror"
                               wire:model="module_key" placeholder="e.g. courses.builder">
                        @error('module_key') <div class="form-error"><i class="ti ti-alert-circle"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Label</label>
                        <input type="text" class="form-input @error('label') is-invalid @enderror"
                               wire:model="label" placeholder="e.g. Curriculum Builder">
                        @error('label') <div class="form-error"><i class="ti ti-alert-circle"></i>{{ $message }}</div> @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Icon class</label>
                            <input type="text" class="form-input @error('icon') is-invalid @enderror" wire:model="icon" placeholder="ti ti-stack-2">
                            @error('icon') <div class="form-error"><i class="ti ti-alert-circle"></i>{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Route name</label>
                            <input type="text" class="form-input @error('route') is-invalid @enderror" wire:model="route" placeholder="admin.courses.builder">
                            @error('route') <div class="form-error"><i class="ti ti-alert-circle"></i>{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Sort order</label>
                            <input type="number" class="form-input" wire:model="sort_order">
                        </div>
                        <div class="form-group" style="display:flex;align-items:center;gap:10px;margin-top:26px;">
                            <label class="switch">
                                <input type="checkbox" wire:model="status">
                                <span class="slider"></span>
                            </label>
                            <span class="form-label" style="margin:0;">Active</span>
                        </div>
                    </div>

                    <div class="modal-panel__actions">
                        <button type="button" class="btn btn-ghost" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save"><i class="ti ti-check"></i> Save module</span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>