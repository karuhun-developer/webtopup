<?php

namespace App\Http\Middleware;

use App\Models\Menu\Menu;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        // User and role
        $user = $request->user();
        $role = $user ? $user->roles()->first() : null;

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $user ? Cache::flexible('auth:user:'.$user->id, [300, 600], function () use ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'permissions' => $user->getAllPermissions()->pluck('name'),
                        'roles' => $user->getRoleNames(),
                    ];
                }) : null,
                'menus' => ($user && $role) ? Cache::flexible('menus:role:'.$role->id, [300, 600], function () use ($role) {
                    return Menu::with('subMenu')->where('role_id', $role->id)->orderBy('order', 'asc')->get();
                }) : [],
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
