<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer');

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $orders = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.orders.index',
            compact('orders')
        );
    }

    public function show(Order $order)
    {
        $order->load([
            'customer',
            'items.product',
        ]);

        return view(
            'admin.orders.show',
            compact('order')
        );
    }

    public function updateStatus(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:confirmed,preparing,ready_for_pickup',
            ],
        ]);

        $allowed = [
            'pending' => [
                'confirmed',
            ],

            'confirmed' => [
                'preparing',
            ],

            'preparing' => [
                'ready_for_pickup',
            ],
        ];

        if (
            !isset($allowed[$order->status]) ||
            !in_array(
                $validated['status'],
                $allowed[$order->status]
            )
        ) {
            return back()->with(
                'error',
                'Invalid order status transition.'
            );
        }

        $oldStatus = $order->status;

        $order->update([
            'status' => $validated['status'],
        ]);

        AuditLogger::log(
            action: 'updated_order_status',
            module: 'Orders',
            record: $order,
            description:
                "Changed {$order->order_number} from {$oldStatus} to {$order->status}.",
            oldValues: [
                'status' => $oldStatus,
            ],
            newValues: [
                'status' => $order->status,
            ]
        );

        return back()->with(
            'success',
            'Order status updated.'
        );
    }

    public function claim(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([
            'pickup_code' => [
                'required',
                'string',
            ],
        ]);

        if (
            $order->status !==
            'ready_for_pickup'
        ) {
            return back()->with(
                'error',
                'Order is not ready for pickup.'
            );
        }

        if (
            $validated['pickup_code'] !==
            $order->pickup_code
        ) {
            return back()->with(
                'error',
                'Invalid pickup code.'
            );
        }

        $order->update([
            'status' => 'claimed',
        ]);

        AuditLogger::log(
            action: 'claimed_order',
            module: 'Orders',
            record: $order,
            description:
                "Order {$order->order_number} was claimed."
        );

        return back()->with(
            'success',
            'Order successfully claimed.'
        );
    }
}