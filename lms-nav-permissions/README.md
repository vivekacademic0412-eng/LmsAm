# Left navigation + role permissions modules

Two modules, drop-in ready:

1. **Navigation Builder** — admin table to CRUD sidebar menu items (`nav_items`), plus a reusable
   `<x-aside-nav />` Blade component that renders the sidebar filtered by the logged-in user's role.
2. **Roles & Permissions** — admin table (matrix: role × module × view/create/edit/delete) backed by
   `role_permissions`, saved in one bulk upsert.

## Copy into your Laravel app

```
database/migrations/2026_07_20_000001_create_nav_items_table.php
database/migrations/2026_07_20_000002_create_role_permissions_table.php
database/seeders/NavPermissionSeeder.php
app/Models/NavItem.php
app/Models/RolePermission.php
app/Traits/HasModulePermissions.php
app/Http/Controllers/Admin/NavItemController.php
app/Http/Controllers/Admin/RolePermissionController.php
app/View/Components/AsideNav.php
resources/views/components/aside-nav.blade.php
resources/views/admin/nav-items/index.blade.php
resources/views/admin/nav-items/_form.blade.php
resources/views/admin/permissions/index.blade.php
routes/admin.php   -> append its contents into your existing routes/web.php (or require it there)
```

## Fast setup (in order)

```bash
php artisan migrate
php artisan db:seed --class=Database\\Seeders\\NavPermissionSeeder
```

Two migrations, one seeder call — nav_items and the full role_permissions matrix are ready in one go.

## Wire it up

1. **User model** — add the trait so you can permission-check anywhere:
   ```php
   use App\Traits\HasModulePermissions;

   class User extends Authenticatable
   {
       use HasModulePermissions;
       // ...
   }
   ```
   Then: `if ($request->user()->hasPermission('courses', 'edit')) { ... }`

2. **Routes** — merge `routes/admin.php` into your route loading (require it from `RouteServiceProvider`
   or `bootstrap/app.php`, or paste its `Route::prefix('admin')...` block into `web.php`). Wrap the group
   with your real auth + role middleware.

3. **Layout** — drop the sidebar into your admin layout:
   ```blade
   <x-aside-nav />
   ```
   It reads `Auth::user()->role`, pulls only the `nav_items` whose `module_key` has `can_view = 1` for
   that role in `role_permissions`, and renders top-level items with nested children.

4. **Route guard (important)** — the hidden menu item is not security. Add a middleware, e.g.:
   ```php
   // app/Http/Middleware/CheckModulePermission.php
   public function handle($request, Closure $next, string $module, string $action = 'view')
   {
       abort_unless($request->user()->hasPermission($module, $action), 403);
       return $next($request);
   }
   ```
   then `Route::get('courses', ...)->middleware('module:courses,view')`.

## Adjusting the default matrix

Edit the `$roleAccess` array in `NavPermissionSeeder` for your real per-role rules, or just use the
**Roles & Permissions** admin page after seeding — it's editable there, no redeploy needed.

## Adding a new page to the sidebar

Use the **Navigation Builder** admin page (`/admin/nav-items`) — add a row with a unique `module_key`,
label, icon, and route name. Then flip it on for whichever roles need it in **Roles & Permissions**.
No code change required for new menu items going forward.
