<x-layouts.app>

    <div class="p-6">

        <div class="mb-6">

            <h1 class="text-2xl font-bold">
                My Jobs
            </h1>

            <p class="mt-1 text-gray-500">
                View your assigned customer appointments.
            </p>

        </div>


        <div
            class="overflow-hidden rounded-xl border bg-white shadow-sm"
        >

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

                                {{ $appointment
                                    ->appointment_date
                                    ->format('M d, Y')
                                }}

                            </td>


                            <td class="px-6 py-4">

                                {{ ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $appointment->status
                                    )
                                ) }}

                            </td>


                            <td class="px-6 py-4">

                                <a
                                    href="{{ route(
                                        'mechanic.appointments.show',
                                        $appointment
                                    ) }}"
                                    class="rounded bg-blue-600 px-3 py-2 text-xs text-white"
                                >
                                    Open
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-10 text-center text-gray-500"
                            >
                                No assigned jobs.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="mt-5">
            {{ $appointments->links() }}
        </div>

    </div>

</x-layouts.app>