<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuthGates
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! app()->runningInConsole() && $user) {
            $user->load('roles');

            $permissionsArray = [];

            $roles = Role::with('permissions')
                ->where('is_active', true)
                ->get();

            foreach ($roles as $role) {
                foreach ($role->permissions as $permission) {
                    if ($permission->is_active) {
                        $permissionsArray[$permission->title][] = $role->id;
                    }
                }
            }

            foreach ($permissionsArray as $title => $roleIds) {
                Gate::define($title, function (User $user) use ($roleIds) {
                    return count(array_intersect(
                        $user->roles->pluck('id')->toArray(),
                        $roleIds
                    )) > 0;
                });
            }
        }

        return $next($request);
    }
}
