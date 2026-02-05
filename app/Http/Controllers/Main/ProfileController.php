<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\Order\Order;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Response;

class ProfileController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Get user's transactions with pagination
        $transactions = Order::with('payment', 'product', 'brand')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($request->get('paginate', 10));

        return inertia('main/Profile', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'transactions' => $transactions,
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
