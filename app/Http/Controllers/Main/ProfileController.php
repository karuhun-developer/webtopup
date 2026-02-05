<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\Order\Order;
use App\Traits\WithGetFilterData;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    use WithGetFilterData;

    public function index(Request $request)
    {
        $user = $request->user();

        $order = $request?->order ?? 'desc';
        $orderBy = $request?->orderBy ?? 'created_at';
        $paginate = $request?->paginate ?? 10;
        $searchBySpecific = $request?->searchBySpecific ?? '';
        $search = $request?->search ?? '';

        return inertia('main/Profile', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'transactions' => $this->getDataWithFilter(
                model: Order::with('payment', 'product', 'brand')->where('user_id', $user->id),
                searchBy: [
                    'reference',
                ],
                order: $order,
                orderBy: $orderBy,
                paginate: $paginate,
                searchBySpecific: $searchBySpecific,
                s: $search,
            ),
            'balance' => $user->balance ?? 0,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        Cache::forget('auth:user:'.$request->user()->id);

        return to_route('main.profile.index');
    }
}
