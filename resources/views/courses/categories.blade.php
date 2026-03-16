@extends('layouts.app')

@section('content')
    <style>
        .category-shell { display: grid; gap: 16px; }
        .category-shell > .card {
            border: 1px solid var(--line);
            background:
                linear-gradient(140deg, rgba(20, 95, 209, 0.08), rgba(20, 95, 209, 0) 55%),
                var(--card);
        }
        .category-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
        }
        .category-head h1 {
            font-size: 30px;
            line-height: 1.05;
            letter-spacing: -0.02em;
        }
        .category-head .muted {
            margin-top: 8px;
            font-size: 13px;
        }
        .category-create-btn {
            border: 1px solid #cfd7e4;
            background: #f7f9fc;
            color: #1f2f48;
            box-shadow: none;
            border-radius: 10px;
            padding: 7px 12px;
            font-weight: 600;
            letter-spacing: 0;
            transition: 160ms ease;
        }
        .category-create-btn:hover {
            border-color: #bcc8d9;
            background: #eef3f9;
            transform: translateY(-2px);
            box-shadow: none;
        }
        .category-create-btn:focus-visible {
            outline: none;
            box-shadow:
                0 0 0 3px rgba(20, 95, 209, 0.14);
        }
        .category-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .category-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--card);
            overflow: hidden;
            box-shadow: var(--shadow);
            display: grid;
            grid-template-rows: auto auto 1fr auto;
            transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
        }
        .category-card:hover {
            transform: translateY(-3px);
            border-color: #b9cfee;
            box-shadow: 0 24px 40px rgba(11, 34, 66, 0.14);
        }
        .category-thumb {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 1px solid var(--line-soft);
            background: linear-gradient(135deg, rgba(145, 161, 185, 0.16), rgba(145, 161, 185, 0.04));
            transition: transform 300ms ease;
        }
        .category-card:hover .category-thumb {
            transform: scale(1.04);
        }
        .category-thumb-placeholder {
            width: 100%;
            height: 200px;
            border-bottom: 1px solid var(--line-soft);
            background:
                radial-gradient(circle at 30% 20%, rgba(20, 95, 209, 0.16), rgba(20, 95, 209, 0) 45%),
                linear-gradient(135deg, rgba(145, 161, 185, 0.16), rgba(145, 161, 185, 0.04));
            display: grid;
            place-content: center;
            color: var(--muted);
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .category-main {
            padding: 14px 14px 12px;
            border-bottom: 1px solid var(--line-soft);
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
        }
        .category-main h3 {
            font-size: 24px;
            line-height: 1.08;
            margin: 0;
            letter-spacing: -0.01em;
        }
        .category-main p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.45;
        }
        .count-pill {
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text);
            background: var(--primary-soft);
            white-space: nowrap;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.3);
        }
        .sub-list {
            display: grid;
            align-content: start;
            background: linear-gradient(180deg, rgba(20, 95, 209, 0.02), rgba(20, 95, 209, 0));
        }
        .sub-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: center;
            padding: 11px 14px;
            border-bottom: 1px solid var(--line-soft);
        }
        .sub-item:last-child { border-bottom: 0; }
        .sub-name {
            font-weight: 700;
            font-size: 13px;
        }
        .sub-actions {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .icon-action {
            width: 32px;
            height: 32px;
            border: 1px solid var(--line);
            border-radius: 9px;
            background: var(--card);
            color: var(--muted);
            display: grid;
            place-content: center;
            cursor: pointer;
            transition: 180ms ease;
        }
        .icon-action:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: var(--primary-soft);
            transform: translateY(-1px);
        }
        .icon-action.danger:hover {
            color: var(--danger);
            border-color: var(--danger);
            background: rgba(197, 58, 58, 0.08);
        }
        .card-tools {
            padding: 12px;
            border-top: 1px solid var(--line-soft);
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            background: linear-gradient(180deg, rgba(20, 95, 209, 0), rgba(20, 95, 209, 0.04));
        }
        .card-tools form {
            margin: 0;
            display: inline-flex;
        }
        .btn-mini {
            border: 1px solid #cfd7e4;
            border-radius: 10px;
            background: #f7f9fc;
            color: #1f2f48;
            padding: 7px 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            line-height: 1;
            transition: 160ms ease;
        }
        .btn-mini:hover {
            border-color: #bcc8d9;
            background: #eef3f9;
            transform: translateY(-1px);
        }
        .btn-mini.danger:hover {
            border-color: #d8b8b8;
            color: #b34747;
            background: #f9f1f1;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(8, 15, 28, 0.56);
            backdrop-filter: blur(3px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            z-index: 120;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            width: min(700px, 100%);
            max-height: calc(100vh - 36px);
            overflow: auto;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: var(--card);
            box-shadow: 0 28px 56px rgba(0, 0, 0, 0.25);
        }
        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line-soft);
        }
        .modal-head h3 { margin: 0; font-size: 24px; }
        .modal-close {
            border: 0;
            background: transparent;
            color: var(--muted);
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
        }
        .modal-body { padding: 14px 16px 16px; }
        .modal-body .form-premium {
            padding: 16px;
            border-radius: 14px;
        }
        .modal-body .actions-row {
            justify-content: flex-end;
            margin-top: 4px;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            border-top: 1px solid var(--line-soft);
            padding: 12px 16px;
        }

        .pagination {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 8px;
            flex-wrap: wrap;
        }
        .page-btn {
            min-width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--text);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 10px;
            font-size: 13px;
            font-weight: 600;
            transition: 160ms ease;
        }
        .page-btn:hover:not(.active):not(.disabled) {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-soft);
        }
        .page-btn.active { background: var(--primary); border-color: var(--primary); color: #fff; }
        .page-btn.disabled { opacity: 0.5; pointer-events: none; }

        @media (max-width: 1140px) {
            .category-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 760px) {
            .category-head {
                flex-direction: column;
                align-items: flex-start;
            }
            .category-head h1 { font-size: 24px; }
            .category-grid { grid-template-columns: 1fr; }
            .form-grid, .form-grid-2 { grid-template-columns: 1fr; }
        }
    </style>

    <div class="category-shell">
        <section class="card">
            <div class="category-head">
                <div>
                    <h1>All Category ({{ $allCategories->count() }})</h1>
                    <p class="muted">Manage main categories and subcategories from one place.</p>
                </div>
                @if ($canManage)
                    <button type="button" class="btn category-create-btn" data-modal-open="modal-main-create">+ Add new category</button>
                @endif
            </div>
        </section>

        <section>
            <div class="category-grid">
                @forelse ($categories as $category)
                    <article class="category-card">
                        @if ($category->thumbnail)
                            <img class="category-thumb" src="{{ $category->thumbnail_url }}" alt="{{ $category->name }}" loading="lazy">
                        @else
                            <div class="category-thumb-placeholder">No Thumbnail</div>
                        @endif

                        <div class="category-main">
                            <div>
                                <h3>{{ $category->name }}</h3>
                                <p>{{ $category->description ?: 'No description' }}</p>
                            </div>
                            <span class="count-pill">{{ $category->children_count }} sub</span>
                        </div>

                        <div class="sub-list">
                            @forelse ($category->children as $child)
                                <div class="sub-item">
                                    <span class="sub-name">{{ $child->name }}</span>
                                    @if ($canManage)
                                        <div class="sub-actions">
                                            <button type="button" class="icon-action" title="Edit subcategory" aria-label="Edit subcategory" data-modal-open="modal-sub-edit-{{ $child->id }}">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                                </svg>
                                            </button>
                                            <form method="POST" action="{{ route('course-categories.destroy', $child) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="icon-action danger" title="Delete subcategory" aria-label="Delete subcategory" onclick="return confirm('Delete this subcategory?')">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6l-1 14H6L5 6"></path>
                                                        <path d="M10 11v6"></path>
                                                        <path d="M14 11v6"></path>
                                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="sub-item">
                                    <span class="muted">No subcategories yet.</span>
                                </div>
                            @endforelse
                        </div>

                        @if ($canManage)
                           <div class="card-tools">

    <!-- Add Subcategory -->
    <button type="button"
        class="btn-mini"
        data-modal-open="modal-sub-create-{{ $category->id }}">
        <svg width="14" height="14" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Subcategory
    </button>

    <!-- Edit Category -->
    <button type="button"
        class="btn-mini"
        data-modal-open="modal-main-edit-{{ $category->id }}">
        <svg width="14" height="14" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9"></path>
            <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
        </svg>
        Edit
    </button>

    <!-- Delete Category -->
    <form method="POST" action="{{ route('course-categories.destroy', $category) }}">
        @csrf
        @method('DELETE')

        <button
            class="btn-mini danger"
            type="submit"
            onclick="return confirm('Delete this category?')">

            <svg width="14" height="14" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6l-1 14H6L5 6"></path>
                <path d="M10 11v6"></path>
                <path d="M14 11v6"></path>
                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
            </svg>

            Delete
        </button>
    </form>

</div>
                        @endif
                    </article>
                @empty
                    <article class="card">
                        <p class="muted">No categories found.</p>
                    </article>
                @endforelse
            </div>

            @if ($categories->hasPages())
                <div class="pagination">
                    <a class="page-btn {{ $categories->onFirstPage() ? 'disabled' : '' }}" href="{{ $categories->previousPageUrl() ?: '#' }}">Prev</a>
                    @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                        <a class="page-btn {{ $page === $categories->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    @endforeach
                    <a class="page-btn {{ $categories->hasMorePages() ? '' : 'disabled' }}" href="{{ $categories->nextPageUrl() ?: '#' }}">Next</a>
                </div>
            @endif
        </section>
    </div>

    @if ($canManage)
        <div class="modal-overlay" id="modal-main-create" aria-hidden="true">
            <div class="modal" role="dialog" aria-modal="true">
                <div class="modal-head">
                    <h3>Add new category</h3>
                    <button type="button" class="modal-close" data-modal-close="modal-main-create" aria-label="Close">x</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('course-categories.store') }}" enctype="multipart/form-data" class="stack form-premium">
                        @csrf
                        <div class="form-grid">
                            <div class="field">
                                <label>Name</label>
                                <input type="text" name="name" placeholder="Enter category name" required>
                            </div>
                            <div class="field">
                                <label>Description</label>
                                <input type="text" name="description" placeholder="Enter description">
                            </div>
                            <div class="field">
                                <label>Thumbnail</label>
                                <input type="file" name="thumbnail" accept="image/*">
                            </div>
                            <div class="field">
                                <label>Parent Category (optional for subcategory)</label>
                                <select name="parent_id">
                                    <option value="">None (Main Category)</option>
                                    @foreach ($allCategories->whereNull('parent_id') as $parentCategory)
                                        <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="actions-row">
                            <button class="btn" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft" data-modal-close="modal-main-create">Close</button>
                </div>
            </div>
        </div>

        @foreach ($categories as $category)
            <div class="modal-overlay" id="modal-main-edit-{{ $category->id }}" aria-hidden="true">
                <div class="modal" role="dialog" aria-modal="true">
                    <div class="modal-head">
                        <h3>Edit category</h3>
                        <button type="button" class="modal-close" data-modal-close="modal-main-edit-{{ $category->id }}" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('course-categories.update', $category) }}" enctype="multipart/form-data" class="stack form-premium">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="parent_id" value="">
                            <div class="form-grid-2">
                                <div class="field">
                                    <label>Name</label>
                                    <input type="text" name="name" value="{{ $category->name }}" required>
                                </div>
                                <div class="field">
                                    <label>Description</label>
                                    <input type="text" name="description" value="{{ $category->description }}" placeholder="Description">
                                </div>
                            </div>
                            <div class="field">
                                <label>Thumbnail</label>
                                <input type="file" name="thumbnail" accept="image/*">
                            </div>
                            <div class="actions-row">
                                <button class="btn" type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-soft" data-modal-close="modal-main-edit-{{ $category->id }}">Close</button>
                    </div>
                </div>
            </div>

            <div class="modal-overlay" id="modal-sub-create-{{ $category->id }}" aria-hidden="true">
                <div class="modal" role="dialog" aria-modal="true">
                    <div class="modal-head">
                        <h3>Add subcategory</h3>
                        <button type="button" class="modal-close" data-modal-close="modal-sub-create-{{ $category->id }}" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('course-categories.store') }}" enctype="multipart/form-data" class="stack form-premium">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $category->id }}">
                            <div class="form-grid-2">
                                <div class="field">
                                    <label>Name</label>
                                    <input type="text" name="name" placeholder="Subcategory name" required>
                                </div>
                                <div class="field">
                                    <label>Description</label>
                                    <input type="text" name="description" placeholder="Description">
                                </div>
                            </div>
                            <div class="field">
                                <label>Thumbnail</label>
                                <input type="file" name="thumbnail" accept="image/*">
                            </div>
                            <div class="actions-row">
                                <button class="btn" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-soft" data-modal-close="modal-sub-create-{{ $category->id }}">Close</button>
                    </div>
                </div>
            </div>

            @foreach ($category->children as $child)
                <div class="modal-overlay" id="modal-sub-edit-{{ $child->id }}" aria-hidden="true">
                    <div class="modal" role="dialog" aria-modal="true">
                        <div class="modal-head">
                            <h3>Edit subcategory</h3>
                            <button type="button" class="modal-close" data-modal-close="modal-sub-edit-{{ $child->id }}" aria-label="Close">x</button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('course-categories.update', $child) }}" enctype="multipart/form-data" class="stack form-premium">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="parent_id" value="{{ $category->id }}">
                                <div class="form-grid-2">
                                    <div class="field">
                                        <label>Name</label>
                                        <input type="text" name="name" value="{{ $child->name }}" required>
                                    </div>
                                    <div class="field">
                                        <label>Description</label>
                                        <input type="text" name="description" value="{{ $child->description }}" placeholder="Description">
                                    </div>
                                </div>
                                <div class="field">
                                    <label>Thumbnail</label>
                                    <input type="file" name="thumbnail" accept="image/*">
                                </div>
                                <div class="actions-row">
                                    <button class="btn" type="submit">Save</button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-soft" data-modal-close="modal-sub-edit-{{ $child->id }}">Close</button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
<script src="{{ asset('js/category.js') }}" defer></script>
    @endif
@endsection
