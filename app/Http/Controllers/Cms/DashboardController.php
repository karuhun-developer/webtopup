<?php

namespace App\Http\Controllers\Cms;

use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        $giftOrderQuery = Order::query()
            ->whereHas('product', function ($q) {
                $q->where('provider', 'gift')
                    ->orWhere('provider', 'manual_topup');
            });

        return inertia('Dashboard', [
            'stats' => inertia()->defer(function () use ($giftOrderQuery) {
                // Stats: Not Processed
                $notProcessedCount = (clone $giftOrderQuery)
                    ->where(function ($q) {
                        $q->where('submited->admin_add_friend_timestamp', '=', '')
                            ->orWhereNull('submited->admin_add_friend_timestamp');
                    })
                    ->where(function ($q) {
                        $q->where('submited->user_confirm_friend_timestamp', '=', '')
                            ->orWhereNull('submited->user_confirm_friend_timestamp');
                    })
                    ->where('payment_status', PaymentStatusEnum::SETTLEMENT)
                    ->where(function ($q) {
                        $q->where('submited->gift_send', false)
                            ->orWhere('submited->gift_send', 'false')
                            ->orWhere('submited->gift_send', 0)
                            ->orWhere('submited->gift_send', '0');
                    })
                    ->count();

                // Stats: Gifted
                $giftedCount = (clone $giftOrderQuery)
                    ->where(function ($q) {
                        $q->where('submited->gift_send', true)
                            ->orWhere('submited->gift_send', 'true')
                            ->orWhere('submited->gift_send', 1)
                            ->orWhere('submited->gift_send', '1');
                    })
                    ->count();

                // Stats: Waiting vs Ready
                $pendingGiftOrders = (clone $giftOrderQuery)
                    ->where('submited->admin_add_friend_timestamp', '!=', '')
                    ->where('submited->user_confirm_friend_timestamp', '!=', '')
                    ->where('payment_status', PaymentStatusEnum::SETTLEMENT)
                    ->where(function ($q) {
                        $q->where('submited->gift_send', false)
                            ->orWhere('submited->gift_send', 'false')
                            ->orWhere('submited->gift_send', 0)
                            ->orWhere('submited->gift_send', '0');
                    })
                    ->get();

                $waitingCount = 0;
                $readyCount = 0;

                foreach ($pendingGiftOrders as $order) {
                    $timestamp = $order->submited['user_confirm_friend_timestamp'];
                    $readyDate = Carbon::parse($timestamp)->addDays(7);

                    if (floor(now()->diffInDays($readyDate)) < 1) {
                        $readyCount++;
                    } else {
                        $waitingCount++;
                    }
                }

                // Stats: Revenue
                $revenue = (clone $giftOrderQuery)
                    ->whereHas('payment', fn ($q) => $q->whereNotNull('paid_at'))
                    ->with(['payment'])
                    ->get()
                    ->sum(function ($order) {
                        return $order->payment->amount;
                    });

                // Stats: Payment Review (Manual & Pending)
                $paymentReviewCount = Order::query()
                    ->where('payment_status', 0)
                    ->count();

                return [
                    'notProcessed' => $notProcessedCount,
                    'waiting' => $waitingCount,
                    'ready' => $readyCount,
                    'gifted' => $giftedCount,
                    'revenue' => $revenue,
                    'paymentReview' => $paymentReviewCount,
                ];
            }),
            'ordersWaiting' => inertia()->defer(function () use ($giftOrderQuery) {
                $ordersWaiting = (clone $giftOrderQuery)
                    ->with('brand', 'product', 'payment', 'user')
                    ->where('submited->admin_add_friend_timestamp', '!=', '')
                    ->where('submited->user_confirm_friend_timestamp', '!=', '')
                    ->where(function ($q) {
                        $q->where('submited->gift_send', false)
                            ->orWhere('submited->gift_send', 'false')
                            ->orWhere('submited->gift_send', 0)
                            ->orWhere('submited->gift_send', '0');
                    })
                    ->where('payment_status', PaymentStatusEnum::SETTLEMENT)
                    ->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(submited, '$.user_confirm_friend_timestamp')) AS DATETIME) ASC")
                    ->take(10)
                    ->get();

                $ordersWaiting->map(function ($order) {
                    if ($order->payment && $order->payment->driver === 'manual') {
                        $order->payment->image = $order->payment->getFirstMediaUrl('image');
                    }

                    return $order;
                });

                return $ordersWaiting;
            }),
            'ordersNotProcessed' => inertia()->defer(function () use ($giftOrderQuery) {
                $ordersNotProcessed = (clone $giftOrderQuery)
                    ->with('brand', 'product', 'payment', 'user')
                    ->where(function ($q) {
                        $q->where('submited->admin_add_friend_timestamp', '=', '')
                            ->orWhereNull('submited->admin_add_friend_timestamp');
                    })
                    ->where(function ($q) {
                        $q->where('submited->user_confirm_friend_timestamp', '=', '')
                            ->orWhereNull('submited->user_confirm_friend_timestamp');
                    })
                    ->where(function ($q) {
                        $q->where('submited->gift_send', false)
                            ->orWhere('submited->gift_send', 'false')
                            ->orWhere('submited->gift_send', 0)
                            ->orWhere('submited->gift_send', '0');
                    })
                    ->where('payment_status', PaymentStatusEnum::SETTLEMENT)
                    ->orderBy('created_at', 'asc')
                    ->take(10)
                    ->get();

                $ordersNotProcessed->map(function ($order) {
                    if ($order->payment && $order->payment->driver === 'manual') {
                        $order->payment->image = $order->payment->getFirstMediaUrl('image');
                    }

                    return $order;
                });

                return $ordersNotProcessed;
            }),
            'ordersPaymentReview' => inertia()->defer(function () {
                $ordersPaymentReview = Order::with('brand', 'product', 'payment', 'user')
                    ->whereHas('payment', fn ($q) => $q->where('driver', 'manual'))
                    ->where('payment_status', false)
                    ->orderBy('created_at', 'asc')
                    ->take(10)
                    ->get();

                $ordersPaymentReview->map(function ($order) {
                    if ($order->payment && $order->payment->driver === 'manual') {
                        $order->payment->image = $order->payment->getFirstMediaUrl('image');
                    }

                    return $order;
                });

                return $ordersPaymentReview;
            }),
        ]);
    }
}
