<?php

use App\Http\Controllers\Admin\NavItemController;
use App\Http\Controllers\Admin\RolePermissionController;
use Illuminate\Support\Facades\Route;

// Wrap with your existing admin auth + role middleware, e.g.:
// Route::middleware(['auth', 'role:superadmin,admin'])->group(function () { ... })

Route::prefix('admin')->name('admin.')->group(function () {

    // Navigation Builder module
    Route::get('nav-items', [NavItemController::class, 'index'])->name('nav-items.index');
    Route::post('nav-items', [NavItemController::class, 'store'])->name('nav-items.store');
    Route::put('nav-items/{navItem}', [NavItemController::class, 'update'])->name('nav-items.update');
    Route::delete('nav-items/{navItem}', [NavItemController::class, 'destroy'])->name('nav-items.destroy');

    // Roles & Permissions module
    Route::get('permissions', [RolePermissionController::class, 'index'])->name('permissions.index');
    Route::put('permissions', [RolePermissionController::class, 'update'])->name('permissions.update');
});
