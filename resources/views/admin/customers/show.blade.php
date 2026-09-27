<x-admin-layout>

    <div class="mx-auto max-w-7xl p-6">

        <a
            href="{{ route('admin.customers.index') }}"
            class="text-sm text-blue-600"
        >
            ← Back to Customers
        </a>


        <div
            class="mt-5 rounded-xl border bg-white p-6 shadow-sm"
        >

            <div
                class="flex flex-col justify-between gap-4 md:flex-row md:items-center"
            >

                <div>

                    <h1 class="text-2xl font-bold">
                        {{ $customer->name }}
                    </h1>

                    <p class="text-gray-500">
                        {{ $customer->email }}
                    </p>

                    <p class="text-gray-500">
                        {{ $customer->phone ?? 'No phone number' }}
                    </p>

                </div>


                <span
                    class="rounded-full px-4 py-2 text-sm
                    {{
                        $customer->status === 'active'
                        ? 'bg-green-100 text-green-700'
                        : 'bg-red-100 text-red-700'
                    }}"
                >
                    {{ ucfirst($customer->status) }}
                </span>

            </div>

        </div>


        {{-- MOTORCYCLES --}}
        <div
            class="mt-6 rounded-xl border bg-white p-6"
        >

            <h2 class="mb-4 text-lg font-semibold">
                Motorcycles
            </h2>

            <div
                class="grid gap-4 md:grid-cols-3"
            >

                @forelse(
                    $customer->motorcycles
                    as $motorcycle
                )

                    <div
                        class="rounded-lg border p-4"
                    >

                        <p class="font-semibold">
                            {{ $motorcycle->brand }}
                            {{ $motorcycle->model }}
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            Year:
                            {{ $motorcycle->year ?? 'N/A' }}
                        </p>

                        <p class="text-sm text-gray-500">
                            Plate:
                            {{ $motorcycle->plate_number ?? 'N/A' }}
                        </p>

                        <p class="text-sm text-gray-500">
                            Color:
                            {{ $motorcycle->color ?? 'N/A' }}
                        </p>

                    </div>

                @empty

                    <p class="text-gray-500">
                        No motorcycles registered.
                    </p>

                @endforelse

            </div>

        </div>


        {{-- APPOINTMENTS --}}
        <div
            class="mt-6 rounded-xl border bg-white p-6"
        >

            <h2 class="mb-4 text-lg font-semibold">
                Appointment History
            </h2>


            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-gray-50">

                        <tr>
                            <th class="px-4 py-3">
                                Appointment
                            </th>

                            <th class="px-4 py-3">
                                Motorcycle
                            </th>

                            <th class="px-4 py-3">
                                Service
                            </th>

                            <th class="px-4 py-3">
                                Date
                            </th>

                            <th class="px-4 py-3">
                                Status
                            </th>
                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        @forelse(
                            $customer->customerAppointments
                            as $appointment
                        )

                            <tr>

                                <td class="px-4 py-3">

                                    <a
                                        href="{{ route(
                                            'admin.appointments.show',
                                            $appointment
                                        ) }}"
                                        class="font-medium text-blue-600"
                                    >
                                        {{ $appointment->appointment_number }}
                                    </a>

                                </td>


                                <td class="px-4 py-3">

                                    {{ $appointment->motorcycle->brand }}
                                    {{ $appointment->motorcycle->model }}

                                </td>


                                <td class="px-4 py-3">
                                    {{ $appointment->service->name }}
                                </td>


                                <td class="px-4 py-3">

                                    {{ $appointment
                                        ->appointment_date
                                        ->format('M d, Y')
                                    }}

                                </td>


                                <td class="px-4 py-3">

                                    {{ ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $appointment->status
                                        )
                                    ) }}

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="5"
                                    class="p-6 text-center text-gray-500"
                                >
                                    No appointments.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ORDERS --}}
        <div
            class="mt-6 rounded-xl border bg-white p-6"
        >

            <h2 class="mb-4 text-lg font-semibold">
                Orders
            </h2>

            @forelse($customer->orders as $order)

                <a
                    href="{{ route(
                        'admin.orders.show',
                        $order
                    ) }}"
                    class="mb-3 flex items-center justify-between rounded-lg border p-4 hover:bg-gray-50"
                >

                    <div>

                        <p class="font-semibold">
                            {{ $order->order_number }}
                        </p>

                        <p class="text-sm text-gray-500">
                            {{ $order->created_at->format(
                                'M d, Y'
                            ) }}
                        </p>

                    </div>


                    <div class="text-right">

                        <p class="font-semibold">
                            ₱{{ number_format(
                                $order->total_amount,
                                2
                            ) }}
                        </p>

                        <p class="text-sm text-gray-500">

                            {{ ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $order->status
                                )
                            ) }}

                        </p>

                    </div>

                </a>

            @empty

                <p class="text-gray-500">
                    No orders.
                </p>

            @endforelse

        </div>

    </div>

</x-admin-layout>