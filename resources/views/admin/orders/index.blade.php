<x-admin-layout>

    <div class="p-6">

        <div class="mb-6">

            <h1 class="text-2xl font-bold">
                Pickup Orders
            </h1>

            <p class="text-gray-500">
                Manage customer parts orders.
            </p>

        </div>


        <div
            class="overflow-hidden rounded-xl border bg-white"
        >

            <table class="w-full text-left text-sm">

                <thead class="bg-gray-50">

                    <tr>
                        <th class="px-6 py-4">
                            Order
                        </th>

                        <th class="px-6 py-4">
                            Customer
                        </th>

                        <th class="px-6 py-4">
                            Pickup
                        </th>

                        <th class="px-6 py-4">
                            Total
                        </th>

                        <th class="px-6 py-4">
                            Status
                        </th>

                        <th class="px-6 py-4"></th>
                    </tr>

                </thead>


                <tbody class="divide-y">

                    @forelse($orders as $order)

                        <tr>

                            <td class="px-6 py-4 font-semibold">
                                {{ $order->order_number }}
                            </td>


                            <td class="px-6 py-4">
                                {{ $order->customer->name }}
                            </td>


                            <td class="px-6 py-4">
                                {{ $order->pickup_date->format(
                                    'M d, Y'
                                ) }}
                            </td>


                            <td class="px-6 py-4">

                                ₱{{ number_format(
                                    $order->total_amount,
                                    2
                                ) }}

                            </td>


                            <td class="px-6 py-4">

                                {{ ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $order->status
                                    )
                                ) }}

                            </td>


                            <td class="px-6 py-4">

                                <a
                                    href="{{ route(
                                        'admin.orders.show',
                                        $order
                                    ) }}"
                                    class="rounded bg-blue-600 px-3 py-2 text-white"
                                >
                                    View
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="6"
                                class="p-10 text-center text-gray-500"
                            >
                                No pickup orders.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="mt-5">
            {{ $orders->links() }}
        </div>

    </div>

</x-admin-layout>
