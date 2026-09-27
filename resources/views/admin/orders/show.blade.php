<x-admin-layout>

    <div class="mx-auto max-w-5xl p-6">

        <a
            href="{{ route('admin.orders.index') }}"
            class="text-blue-600"
        >
            ← Back to Orders
        </a>


        <div
            class="mt-5 rounded-xl border bg-white p-6"
        >

            <h1 class="text-2xl font-bold">
                {{ $order->order_number }}
            </h1>

            <p class="mt-1 text-gray-500">
                {{ $order->customer->name }}
            </p>


            <div class="mt-6 overflow-hidden rounded-lg border">

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr>
                            <th class="px-4 py-3 text-left">
                                Product
                            </th>

                            <th class="px-4 py-3">
                                Qty
                            </th>

                            <th class="px-4 py-3">
                                Price
                            </th>

                            <th class="px-4 py-3">
                                Subtotal
                            </th>
                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        @foreach($order->items as $item)

                            <tr>

                                <td class="px-4 py-3">
                                    {{ $item->product->name }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    {{ $item->quantity }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    ₱{{ number_format(
                                        $item->unit_price,
                                        2
                                    ) }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    ₱{{ number_format(
                                        $item->subtotal,
                                        2
                                    ) }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            <div class="mt-5 text-right">

                <p class="text-lg font-bold">

                    Total:
                    ₱{{ number_format(
                        $order->total_amount,
                        2
                    ) }}

                </p>

            </div>


            <div
                class="mt-7 rounded-lg bg-gray-50 p-5"
            >

                <p>
                    <strong>Status:</strong>

                    {{ ucwords(
                        str_replace(
                            '_',
                            ' ',
                            $order->status
                        )
                    ) }}
                </p>

                <p class="mt-2">
                    <strong>Pickup Date:</strong>

                    {{ $order->pickup_date->format(
                        'F d, Y'
                    ) }}
                </p>

            </div>


            @php

                $nextStatus = match($order->status) {
                    'pending' => 'confirmed',
                    'confirmed' => 'preparing',
                    'preparing' => 'ready_for_pickup',
                    default => null,
                };

            @endphp


            @if($nextStatus)

                <form
                    method="POST"
                    action="{{ route(
                        'admin.orders.status',
                        $order
                    ) }}"
                    class="mt-6"
                >

                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="status"
                        value="{{ $nextStatus }}"
                    >

                    <button
                        class="rounded-lg bg-blue-600 px-5 py-3 text-white"
                    >

                        @switch($nextStatus)

                            @case('confirmed')
                                Confirm Order
                                @break

                            @case('preparing')
                                Start Preparing
                                @break

                            @case('ready_for_pickup')
                                Ready for Pickup
                                @break

                        @endswitch

                    </button>

                </form>

            @endif


            @if(
                $order->status ===
                'ready_for_pickup'
            )

                <div
                    class="mt-8 rounded-xl border p-5"
                >

                    <h2 class="font-semibold">
                        Verify Pickup
                    </h2>

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.orders.claim',
                            $order
                        ) }}"
                        class="mt-4 flex max-w-md gap-2"
                    >

                        @csrf
                        @method('PATCH')

                        <input
                            type="text"
                            name="pickup_code"
                            placeholder="Enter customer's pickup code"
                            class="flex-1 rounded-lg border px-4 py-3"
                            required
                        >

                        <button
                            class="rounded-lg bg-green-600 px-4 py-3 text-white"
                        >
                            Claim
                        </button>

                    </form>

                </div>

            @endif

        </div>

    </div>

</x-admin-layout>