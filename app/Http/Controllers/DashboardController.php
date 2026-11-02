<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Render the desktop dashboard.
     *
     * The mobile app lives in its own project and talks to the API, so this
     * shell only has to distinguish the Electron window from a browser session.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $roles = $user->roles->sortBy('name')->values();
        $permissions = $user->effectivePermissions()->sortBy('slug')->values();
        $permissionGroups = $permissions->groupBy(fn ($permission) => $permission->group ?: 'general');

        $modules = config('navigation.modules', []);
        $liveModules = count(array_filter($modules, fn ($module) => $module['status'] === 'live'));

        $isNative = (bool) config('nativephp-internal.running');

        $hour = (int) now()->format('G');

        return view('dashboard.desktop', [
            'user' => $user,
            'isNative' => $isNative,
            'greeting' => $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening'),
            'roles' => $roles,
            'permissions' => $permissions,
            'permissionGroups' => $permissionGroups,
            'permissionGroupCount' => $permissionGroups->count(),
            'modules' => $modules,
            'liveModules' => $liveModules,
            'stats' => [
                [
                    'label' => 'Roles',
                    'value' => $roles->count(),
                    'caption' => $roles->isEmpty() ? 'unassigned' : $roles->pluck('slug')->implode(' · '),
                ],
                [
                    'label' => 'Permissions',
                    'value' => $permissions->count(),
                    'caption' => $permissionGroups->count().' '.Str::plural('group', $permissionGroups->count()),
                ],
                [
                    'label' => 'Modules',
                    'value' => count($modules),
                    'caption' => $liveModules.' live · '.(count($modules) - $liveModules).' scaffolded',
                ],
                [
                    'label' => 'Runtime',
                    'value' => $isNative ? 'Native' : 'Web',
                    'caption' => $isNative ? 'electron shell' : 'browser session',
                ],
            ],
        ]);
    }
}
