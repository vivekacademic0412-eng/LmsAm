<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureCanView($request);

        return view('courses.categories', [
            'categories' => CourseCategory::with(['parent:id,name', 'children:id,name,parent_id'])
                ->withCount(['courses', 'children'])
                ->whereNull('parent_id')
                ->orderBy('name')
                ->paginate(8)
                ->withQueryString(),
            'allCategories' => CourseCategory::orderBy('name')->get(['id', 'name', 'parent_id']),
            'canManage' => $this->canManage($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->canManage($request), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:course_categories,name'],
            'description' => ['nullable', 'string', 'max:800'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'parent_id' => ['nullable', 'integer', 'exists:course_categories,id'],
        ]);

        $parentId = $data['parent_id'] ?? null;
        if ($parentId !== null) {
            $parentIsChild = CourseCategory::query()->whereKey($parentId)->whereNotNull('parent_id')->exists();
            if ($parentIsChild) {
                return back()
                    ->withInput()
                    ->withErrors(['parent_id' => 'Subcategory cannot be selected as parent.']);
            }
        }

        CourseCategory::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(4)),
            'description' => $data['description'] ?? null,
            'thumbnail' => $request->hasFile('thumbnail')
                ? $request->file('thumbnail')->store('category-thumbnails', 'public')
                : null,
            'parent_id' => $parentId,
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, CourseCategory $category): RedirectResponse
    {
        abort_unless($this->canManage($request), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('course_categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:800'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'parent_id' => ['nullable', 'integer', 'exists:course_categories,id', Rule::notIn([$category->id])],
        ]);

        $parentId = $data['parent_id'] ?? null;
        if ($parentId !== null) {
            $parentIsChild = CourseCategory::query()->whereKey($parentId)->whereNotNull('parent_id')->exists();
            if ($parentIsChild) {
                return back()
                    ->withInput()
                    ->withErrors(['parent_id' => 'Subcategory cannot be selected as parent.']);
            }
        }

        $thumbnailPath = $category->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($thumbnailPath && !Str::startsWith($thumbnailPath, ['http://', 'https://']) && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            $thumbnailPath = $request->file('thumbnail')->store('category-thumbnails', 'public');
        }

        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.$category->id,
            'description' => $data['description'] ?? null,
            'thumbnail' => $thumbnailPath,
            'parent_id' => $parentId,
        ]);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, CourseCategory $category): RedirectResponse
    {
        abort_unless($this->canManage($request), 403);

        if ($category->thumbnail && !Str::startsWith($category->thumbnail, ['http://', 'https://']) && Storage::disk('public')->exists($category->thumbnail)) {
            Storage::disk('public')->delete($category->thumbnail);
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

    private function ensureCanView(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_MANAGER_HR, User::ROLE_IT], true),
            403,
            'You do not have access to this page.'
        );
    }

    private function canManage(Request $request): bool
    {
        return in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true);
    }
}
