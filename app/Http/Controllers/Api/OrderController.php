<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(
            'items.product'
        )
            ->where(
                'customer_id',
                $request->user()->id
            )
            ->latest()
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function show(
        Request $request,
        Order $order
    ) {
        abort_unless(
            $order->customer_id ===
                $request->user()->id,
            403
        );

        $order->load('items.product');

        return response()->json([
            'order' => $order,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'pickup_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $order = DB::transaction(
            function () use ($request, $validated) {

                $order = Order::create([
                    'order_number' =>
                        $this->generateOrderNumber(),

                    'customer_id' =>
                        $request->user()->id,

                    'total_amount' => 0,

                    'pickup_date' =>
                        $validated['pickup_date'],

                    'pickup_time' =>
                        $validated['pickup_time']
                        ?? null,

                    'pickup_code' =>
                        (string) random_int(
                            100000,
                            999999
                        ),

                    'status' => 'pending',

                    'payment_method' =>
                        'pay_at_store',
                ]);

                $total = 0;

                foreach (
                    $validated['items']
                    as $item
                ) {
                    $product =
                        Product::lockForUpdate()
                            ->where(
                                'id',
                                $item['product_id']
                            )
                            ->where(
                                'is_active',
                                true
                            )
                            ->firstOrFail();

                    if (
                        $product->stock <
                        $item['quantity']
                    ) {
                        abort(
                            422,
                            "Not enough stock for {$product->name}."
                        );
                    }

                    $before =
                        $product->stock;

                    $subtotal =
                        $product->price *
                        $item['quantity'];

                    $order->items()->create([
                        'product_id' =>
                            $product->id,

                        'quantity' =>
                            $item['quantity'],

                        'unit_price' =>
                            $product->price,

                        'subtotal' =>
                            $subtotal,
                    ]);

                    $product->decrement(
                        'stock',
                        $item['quantity']
                    );

                    $product->refresh();

                    InventoryTransaction::create([
                        'product_id' =>
                            $product->id,

                        'user_id' =>
                            $request->user()->id,

                        'type' => 'order',

                        'quantity' =>
                            -$item['quantity'],

                        'stock_before' =>
                            $before,

                        'stock_after' =>
                            $product->stock,

                        'remarks' =>
                            "Reserved for order {$order->order_number}.",
                    ]);

                    $total += $subtotal;
                }

                $order->update([
                    'total_amount' => $total,
                ]);

                AuditLogger::log(
                    action: 'created_order',
                    module: 'Orders',
                    record: $order,
                    description:
                        "Created pickup order {$order->order_number}."
                );

                return $order;
            }
        );

        $order->load('items.product');

        return response()->json([
            'message' =>
                'Order placed successfully.',
            'order' => $order,
        ], 201);
    }

    public function cancel(
        Request $request,
        Order $order
    ) {
        abort_unless(
            $order->customer_id ===
                $request->user()->id,
            403
        );

        if (!in_array(
            $order->status,
            [
                'pending',
                'confirmed',
            ]
        )) {
            return response()->json([
                'message' =>
                    'This order can no longer be cancelled.'
            ], 422);
        }

        DB::transaction(
            function () use ($request, $order) {

                $order->load('items');

                foreach ($order->items as $item) {

                    $product =
                        Product::lockForUpdate()
                            ->findOrFail(
                                $item->product_id
                            );

                    $before = $product->stock;

                    $product->increment(
                        'stock',
                        $item->quantity
                    );

                    $product->refresh();

                    InventoryTransaction::create([
                        'product_id' =>
                            $product->id,

                        'user_id' =>
                            $request->user()->id,

                        'type' => 'adjustment',

                        'quantity' =>
                            $item->quantity,

                        'stock_before' =>
                            $before,

                        'stock_after' =>
                            $product->stock,

                        'remarks' =>
                            "Stock returned from cancelled order {$order->order_number}.",
                    ]);
                }

                $order->update([
                    'status' => 'cancelled',
                ]);
            }
        );

        return response()->json([
            'message' =>
                'Order cancelled successfully.'
        ]);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number =
                'ORD-' .
                now()->format('ymd') .
                '-' .
                strtoupper(
                    Str::random(4)
                );

        } while (
            Order::where(
                'order_number',
                $number
            )->exists()
        );

        return $number;
    }
}