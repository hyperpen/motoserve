<x-admin-layout>

    <div class="p-6">

        <div
            class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >

            <div>
                <h1 class="text-2xl font-bold">
                    Appointments
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage customer service appointments.
                </p>
            </div>


            <form
                method="GET"
                class="flex gap-2"
            >

                <select
                    name="status"
                    class="rounded-lg border border-gray-300 px-4 py-2"
                >
                    <option value="">
                        All Statuses
                    </option>

                    @foreach([
                        'pending',
                        'confirmed',
                        'mechanic_assigned',
                        'checked_in',
                        'in_service',
                        'ready_for_release',
                        'completed',
                        'cancelled',
                        'rejected',
                        'no_show',
                    ] as $status)

                        <option
                            value="{{ $status }}"
                            @selected(
                                request('status') === $status
                            )
                        >
                            {{ ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $status
                                )
                            ) }}
                        </option>

                    @endforeach

                </select>

                <button
                    class="rounded-lg bg-gray-900 px-4 py-2 text-white"
                >
                    Filter
                </button>

            </form>

        </div>


        @if(session('success'))
            <div
                class="mb-4 rounded-lg bg-green-100 p-4 text-green-800"
            >
                {{ session('success') }}
            </div>
        @endif


        <div
            class="overflow-hidden rounded-xl border bg-white shadow-sm"
        >

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-gray-50">

                        <tr>
                            <th class="px-6 py-4">
                                Appointment
                            </th>

                            <th class="px-6 py-4">
                                Customer
                            </th>

                            <th class="px-6 py-4">
                                Motorcycle
                            </th>

                            <th class="px-6 py-4">
                                Schedule
                            </th>

                            <th class="px-6 py-4">
                                Mechanic
                            </th>

                            <th class="px-6 py-4">
                                Status
                            </th>

                            <th class="px-6 py-4"></th>
                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        @forelse(
                            $appointments as $appointment
                        )

                            <tr>

                                <td class="px-6 py-4">

                                    <p class="font-semibold">
                                        {{ $appointment->appointment_number }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        {{ $appointment->service->name }}
                                    </p>

                                </td>


                                <td class="px-6 py-4">

                                    {{ $appointment->customer->name }}

                                </td>


                                <td class="px-6 py-4">

                                    {{ $appointment->motorcycle->brand }}
                                    {{ $appointment->motorcycle->model }}

                                </td>


                                <td class="px-6 py-4">

                                    <p>
                                        {{ $appointment
                                            ->appointment_date
                                            ->format('M d, Y')
                                        }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse(
                                            $appointment->appointment_time
                                        )->format('h:i A') }}
                                    </p>

                                </td>


                                <td class="px-6 py-4">

                                    {{ $appointment->mechanic?->name
                                        ?? 'Not assigned'
                                    }}

                                </td>


                                <td class="px-6 py-4">

                                    <span
                                        class="rounded-full bg-gray-100 px-3 py-1 text-xs"
                                    >
                                        {{ ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $appointment->status
                                            )
                                        ) }}
                                    </span>

                                </td>


                                <td class="px-6 py-4 text-right">

                                    <a
                                        href="{{ route(
                                            'admin.appointments.show',
                                            $appointment
                                        ) }}"
                                        class="rounded-lg bg-blue-600 px-3 py-2 text-xs text-white"
                                    >
                                        View
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="px-6 py-12 text-center text-gray-500"
                                >
                                    No appointments found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div class="mt-5">
            {{ $appointments->links() }}
        </div>

    </div>

</x-admin-layout>