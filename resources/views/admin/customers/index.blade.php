<x-admin-layout>

    <div class="p-6">

        <div class="mb-6">

            <h1 class="text-2xl font-bold">
                Customers
            </h1>

            <p class="mt-1 text-gray-500">
                View and manage registered customers.
            </p>

        </div>


        @if(session('success'))

            <div
                class="mb-5 rounded-lg bg-green-100 p-4 text-green-800"
            >
                {{ session('success') }}
            </div>

        @endif


        <form
            method="GET"
            class="mb-5 flex max-w-xl gap-2"
        >

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search customer..."
                class="flex-1 rounded-lg border px-4 py-3"
            >

            <button
                class="rounded-lg bg-gray-900 px-5 text-white"
            >
                Search
            </button>

        </form>


        <div
            class="overflow-hidden rounded-xl border bg-white shadow-sm"
        >

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-4">
                                Customer
                            </th>

                            <th class="px-6 py-4">
                                Phone
                            </th>

                            <th class="px-6 py-4">
                                Motorcycles
                            </th>

                            <th class="px-6 py-4">
                                Appointments
                            </th>

                            <th class="px-6 py-4">
                                Orders
                            </th>

                            <th class="px-6 py-4">
                                Status
                            </th>

                            <th class="px-6 py-4"></th>

                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        @forelse(
                            $customers as $customer
                        )

                            <tr>

                                <td class="px-6 py-4">

                                    <p class="font-semibold">
                                        {{ $customer->name }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        {{ $customer->email }}
                                    </p>

                                </td>


                                <td class="px-6 py-4">
                                    {{ $customer->phone ?? 'N/A' }}
                                </td>


                                <td class="px-6 py-4">
                                    {{ $customer->motorcycles_count }}
                                </td>


                                <td class="px-6 py-4">
                                    {{ $customer->customer_appointments_count }}
                                </td>


                                <td class="px-6 py-4">
                                    {{ $customer->orders_count }}
                                </td>


                                <td class="px-6 py-4">

                                    @if(
                                        $customer->status === 'active'
                                    )

                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs text-green-700"
                                        >
                                            Active
                                        </span>

                                    @else

                                        <span
                                            class="rounded-full bg-red-100 px-3 py-1 text-xs text-red-700"
                                        >
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    <div class="flex gap-2">

                                        <a
                                            href="{{ route(
                                                'admin.customers.show',
                                                $customer
                                            ) }}"
                                            class="rounded-lg bg-blue-600 px-3 py-2 text-xs text-white"
                                        >
                                            View
                                        </a>


                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.customers.status',
                                                $customer
                                            ) }}"
                                        >

                                            @csrf
                                            @method('PATCH')


                                            <button
                                                class="rounded-lg bg-gray-100 px-3 py-2 text-xs"
                                            >
                                                {{ $customer->status === 'active'
                                                    ? 'Deactivate'
                                                    : 'Activate'
                                                }}
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="px-6 py-12 text-center text-gray-500"
                                >
                                    No customers found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div class="mt-5">
            {{ $customers->links() }}
        </div>

    </div>

</x-admin-layout>