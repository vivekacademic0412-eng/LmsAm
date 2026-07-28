<div class="cc-grid-2">
    <div class="cc-field">
        <label class="cc-label">Main Category</label>
        <select wire:model.live="form_category_id" class="cc-select">
            <option value="">Select main category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        @error('form_category_id') <span class="cc-error">{{ $message }}</span> @enderror
    </div>

    <div class="cc-field">
        <label class="cc-label">Subcategory</label>
        <select wire:model="form_subcategory_id" class="cc-select">
            <option value="">Select subcategory (optional)</option>
            @foreach ($categories as $category)
                @foreach ($category->children as $sub)
                    <option value="{{ $sub->id }}">{{ $category->name }} / {{ $sub->name }}</option>
                @endforeach
            @endforeach
        </select>
    </div>

    <div class="cc-field" style="grid-column: span 2;">
        <label class="cc-label">Course Title</label>
        <input type="text" wire:model="title" class="cc-input">
        @error('title') <span class="cc-error">{{ $message }}</span> @enderror
    </div>

    <div class="cc-field">
        <label class="cc-label">Course Language</label>
        <input type="text" wire:model="language" class="cc-input" placeholder="English / Hindi / Gujarati">
    </div>

    <div class="cc-field">
        <label class="cc-label">Duration (hours)</label>
        <input type="number" min="1" wire:model="duration_hours" class="cc-input">
        @error('duration_hours') <span class="cc-error">{{ $message }}</span> @enderror
    </div>

    <div class="cc-field" style="grid-column: span 2;">
        <label class="cc-label">Thumbnail Image</label>
        <input type="file" wire:model="thumbnail" accept="image/*" class="cc-input">
        @error('thumbnail') <span class="cc-error">{{ $message }}</span> @enderror
        @if ($thumbnail)
            <img src="{{ $thumbnail->temporaryUrl() }}" class="cc-thumb-preview">
        @endif
        <div wire:loading wire:target="thumbnail" class="cc-hint">Uploading...</div>
    </div>

    <div class="cc-field" style="grid-column: span 2;">
        <label class="cc-label">Short Description</label>
        <textarea wire:model="short_description" rows="2" class="cc-input"></textarea>
    </div>

    <div class="cc-field" style="grid-column: span 2;">
        <label class="cc-label">Description</label>
        <textarea wire:model="description" rows="3" class="cc-input"></textarea>
    </div>
</div>