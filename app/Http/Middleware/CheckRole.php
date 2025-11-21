<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$params)
    {
        // 1) Xác định GUARD theo route middleware "auth:*"
        $guard = null;
        $routeMiddlewares = collect($request->route()->gatherMiddleware());
        $authGuards = $routeMiddlewares
            ->filter(fn ($m) => str_starts_with($m, 'auth:'))
            ->map(fn ($m) => explode(':', $m, 2)[1])
            ->values();

        if ($authGuards->isNotEmpty()) {
            // nếu có nhiều cái, lấy cái cuối (gần route nhất)
            $guard = $authGuards->last();
        }

        // 2) Fallback nếu không tìm thấy từ auth:*
        if (!$guard) {
            if (Auth::guard('admin')->check()) $guard = 'admin';
            elseif (Auth::guard('web')->check()) $guard = 'web';
            else $guard = 'admin'; // ưu tiên admin trong khu vực /admin
        }

        // 3) Nếu chưa đăng nhập theo guard đã chọn -> 401
        if (!Auth::guard($guard)->check()) {
            abort(401, 'Unauthenticated ('.$guard.')');
        }

        $user = Auth::guard($guard)->user();

        // --- Parse params ---
        // checkrole:<rolesParam>,<abilitiesParam>
        // ví dụ: checkrole:admin,create  => roles=['admin'], abilities=['create']
        $rolesParam = $params[0] ?? '';
        $abParam    = $params[1] ?? '';

        $split = static fn (string $s) => array_filter(array_map('trim', preg_split('/[|,]/', (string) $s)));

        $roles     = $split($rolesParam);
        $abilities = $split($abParam);

        // 4) Check ROLE
        if (!empty($roles)) {
            $okRole = true;

            if (method_exists($user, 'hasAnyRole')) {
                // Spatie\Permission
                $okRole = $user->hasAnyRole($roles);
            } elseif (method_exists($user, 'roles')) {
                $okRole = $user->roles()->whereIn('name', $roles)->exists();
            } else {
                $okRole = in_array($user->role ?? null, $roles, true);
            }

            if (!$okRole) {
                abort(403, 'Bạn không có quyền truy cập (role).');
            }
        }

        // 5) Check PERMISSION/ABILITY (nếu có)
        if (!empty($abilities)) {
            $okPerm = false;

            // a) Thử trực tiếp theo danh sách abilities (ví dụ 'create', 'update'…)
            if (method_exists($user, 'hasAnyPermission')) {
                if ($user->hasAnyPermission($abilities)) {
                    $okPerm = true;
                }
            } else {
                foreach ($abilities as $ab) {
                    if ($user->can($ab)) { $okPerm = true; break; }
                }
            }

            // b) Nếu chưa OK, thử map theo module từ route name: "admin.products.update"
            if (!$okPerm) {
                $routeName = (string) $request->route()->getName(); // ví dụ: "admin.products.update"
                $parts = explode('.', $routeName);
                $module = $parts[1] ?? null; // "products", "services", "brands", ...

                if ($module) {
                    $derived = [];
                    foreach ($abilities as $ab) {
                        $derived[] = $module.'.'.$ab; // "products.update"
                    }

                    if (method_exists($user, 'hasAnyPermission')) {
                        if ($user->hasAnyPermission($derived)) {
                            $okPerm = true;
                        }
                    } else {
                        foreach ($derived as $d) {
                            if ($user->can($d)) { $okPerm = true; break; }
                        }
                    }
                }
            }

            if (!$okPerm) {
                abort(403, 'Bạn không có quyền truy cập (permission).');
            }
        }

        return $next($request);
    }
}
