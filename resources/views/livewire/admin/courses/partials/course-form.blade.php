{{-- resources/views/livewire/admin/courses/partials/course-form.blade.php --}}
<div class="cc-form-grid">

    <div class="cc-field">
        <label class="cc-label">Title <span class="req">*</span></label>
        <input type="text" wire:model="title" class="cc-input" placeholder="Course title">
        @error('title') <span class="cc-error">{{ $message }}</span> @enderror
    </div>

    <div class="cc-field">
        <label class="cc-label">Category <span class="req">*</span></label>
        <select wire:model="form_category_id" class="cc-select">
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        @error('form_category_id') <span class="cc-error">{{ $message }}</span> @enderror
    </div>

    <div class="cc-field">
        <label class="cc-label">Subcategory</label>
        <select wire:model="form_subcategory_id" class="cc-select">
            <option value="">No subcategory</option>
            @foreach ($categories as $category)
                @foreach ($category->children as $sub)
                    <option value="{{ $sub->id }}">{{ $category->name }} / {{ $sub->name }}</option>
                @endforeach
            @endforeach
        </select>
        @error('form_subcategory_id') <span class="cc-error">{{ $message }}</span> @enderror
    </div>

    <div class="cc-field">
        <label class="cc-label">Level</label>
        <select wire:model="course_level_id" class="cc-select">
            <option value="">Select level</option>
            @foreach ($levels as $level)
                <option value="{{ $level->id }}">{{ $level->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="cc-field">
        <label class="cc-label">Type</label>
        <select wire:model="course_type_id" class="cc-select">
            <option value="">Select type</option>
            @foreach ($types as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="cc-field">
        <label class="cc-label">Language</label>
        <input type="text" wire:model="language" class="cc-input" placeholder="e.g. English">
    </div>

    <div class="cc-field">
        <label class="cc-label">Duration (hours) <span class="req">*</span></label>
        <input type="number" min="1" wire:model="duration_hours" class="cc-input">
        @error('duration_hours') <span class="cc-error">{{ $message }}</span> @enderror
    </div>

    <div class="cc-field">
        <label class="cc-label">Original Price</label>
        <input type="number" step="0.01" min="0" wire:model="original_price" class="cc-input" placeholder="0.00">
        @error('original_price') <span class="cc-error">{{ $message }}</span> @enderror
    </div>

    <div class="cc-field">
        <label class="cc-label">Price</label>
        <input type="number" step="0.01" min="0" wire:model="price" class="cc-input" placeholder="0.00">
        @error('price') <span class="cc-error">{{ $message }}</span> @enderror
    </div>

    <div class="cc-field">
        <label class="cc-label">GST (%)</label>
        <input type="number" step="0.01" min="0" wire:model="gst" class="cc-input" placeholder="0.00">
        @error('gst') <span class="cc-error">{{ $message }}</span> @enderror
    </div>

    <div class="cc-field cc-field-full">
        <label class="cc-label">Short Description</label>
        <input type="text" wire:model="short_description" class="cc-input" placeholder="One-line summary">
    </div>

    <div class="cc-field cc-field-full">
        <label class="cc-label">Description</label>
        <textarea wire:model="description" rows="4" class="cc-textarea" placeholder="Full course description"></textarea>
    </div>

    {{-- ── Thumbnail: preview existing image, allow replacing, live-preview new pick ── --}}
    <div class="cc-field cc-field-full">
        <label class="cc-label">Thumbnail</label>

        <div class="cc-thumb-row">
            @if ($thumbnail)
                {{-- newly picked file, not saved yet --}}
                <img src="{{ $thumbnail->temporaryUrl() }}" class="cc-thumb-preview">
            @elseif ($existing_thumbnail)
                {{-- current saved image when editing --}}
                <img src="{{ $existing_thumbnail }}" class="cc-thumb-preview">
            @else
                <div class="cc-thumb-preview cc-thumb-empty">No image</div>
            @endif

            <div>
                <input type="file" wire:model="thumbnail" accept="image/*" class="cc-file-input">
                <div wire:loading wire:target="thumbnail" class="cc-uploading">Uploading…</div>
                <p class="cc-hint">JPG/PNG, max 2MB. Leave empty to keep the current image.</p>
                @error('thumbnail') <span class="cc-error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

</div>