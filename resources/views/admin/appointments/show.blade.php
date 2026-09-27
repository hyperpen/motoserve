<x-admin-layout>

    <div class="mx-auto max-w-6xl p-6">

        <div class="mb-6">

            <a
                href="{{ route('admin.appointments.index') }}"
                class="text-sm text-blue-600"
            >
                ← Back to Appointments
            </a>

            <h1 class="mt-3 text-2xl font-bold">
                {{ $appointment->appointment_number }}
            </h1>

            <p class="text-gray-500">
                Appointment Details
            </p>

        </div>


        @if(session('success'))

            <div
                class="mb-4 rounded-lg bg-green-100 p-4 text-green-800"
            >
                {{ session('success') }}
            </div>

        @endif


        @if(session('error'))

            <div
                class="mb-4 rounded-lg bg-red-100 p-4 text-red-800"
            >
                {{ session('error') }}
            </div>

        @endif


        <div class="grid gap-6 lg:grid-cols-3">

            <div class="lg:col-span-2">

                <div
                    class="rounded-xl border bg-white p-6 shadow-sm"
                >

                    <h2 class="mb-5 text-lg font-semibold">
                        Appointment Information
                    </h2>


                    <div
                        class="grid gap-5 sm:grid-cols-2"
                    >

                        <div>
                            <p class="text-xs text-gray-500">
                                Customer
                            </p>

                            <p class="font-medium">
                                {{ $appointment->customer->name }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs text-gray-500">
                                Service
                            </p>

                            <p class="font-medium">
                                {{ $appointment->service->name }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs text-gray-500">
                                Motorcycle
                            </p>

                            <p class="font-medium">
                                {{ $appointment->motorcycle->brand }}
                                {{ $appointment->motorcycle->model }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs text-gray-500">
                                Plate Number
                            </p>

                            <p class="font-medium">
                                {{ $appointment->motorcycle->plate_number
                                    ?? 'N/A'
                                }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs text-gray-500">
                                Date
                            </p>

                            <p class="font-medium">
                                {{ $appointment
                                    ->appointment_date
                                    ->format('F d, Y')
                                }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs text-gray-500">
                                Time
                            </p>

                            <p class="font-medium">
                                {{ \Carbon\Carbon::parse(
                                    $appointment->appointment_time
                                )->format('h:i A') }}
                            </p>
                        </div>


                        <div class="sm:col-span-2">

                            <p class="text-xs text-gray-500">
                                Customer Concern
                            </p>

                            <p class="mt-1">
                                {{ $appointment->problem_description
                                    ?: 'No description provided.'
                                }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <div>

                <div
                    class="rounded-xl border bg-white p-6 shadow-sm"
                >

                    <h2 class="mb-4 font-semibold">
                        Appointment Status
                    </h2>

                    <div class="mb-6">

                        <span
                            class="rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-700"
                        >
                            {{ ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $appointment->status
                                )
                            ) }}
                        </span>

                    </div>


                    @if($appointment->status === 'pending')

                        <form
                            method="POST"
                            action="{{ route(
                                'admin.appointments.confirm',
                                $appointment
                            ) }}"
                        >

                            @csrf
                            @method('PATCH')

                            <button
                                class="w-full rounded-lg bg-green-600 px-4 py-3 font-medium text-white"
                            >
                                Confirm Appointment
                            </button>

                        </form>

                    @endif


                    @if(
                        in_array(
                            $appointment->status,
                            [
                                'confirmed',
                                'mechanic_assigned'
                            ]
                        )
                    )

                        <form
                            method="POST"
                            action="{{ route(
                                'admin.appointments.assign',
                                $appointment
                            ) }}"
                            class="mt-4"
                        >

                            @csrf
                            @method('PATCH')


                            <label
                                class="mb-2 block text-sm font-medium"
                            >
                                Assign Mechanic
                            </label>


                            <select
                                name="mechanic_id"
                                class="w-full rounded-lg border px-3 py-3"
                                required
                            >

                                <option value="">
                                    Select mechanic
                                </option>


                                @foreach(
                                    $mechanics as $mechanic
                                )

                                    <option
                                        value="{{ $mechanic->id }}"
                                        @selected(
                                            $appointment->mechanic_id
                                            === $mechanic->id
                                        )
                                    >
                                        {{ $mechanic->name }}
                                    </option>

                                @endforeach

                            </select>


                            <button
                                class="mt-3 w-full rounded-lg bg-blue-600 px-4 py-3 font-medium text-white"
                            >
                                Assign Mechanic
                            </button>

                        </form>

                    @endif


                    @if($appointment->mechanic)

                        <div
                            class="mt-5 rounded-lg bg-gray-50 p-4"
                        >

                            <p class="text-xs text-gray-500">
                                Assigned Mechanic
                            </p>

                            <p class="font-semibold">
                                {{ $appointment->mechanic->name }}
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</x-admin-layout>