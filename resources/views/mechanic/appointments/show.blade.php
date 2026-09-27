<x-layouts.app>

    <div class="mx-auto max-w-5xl p-6">

        <a
            href="{{ route(
                'mechanic.appointments.index'
            ) }}"
            class="text-sm text-blue-600"
        >
            ← Back to My Jobs
        </a>


        <div
            class="mt-5 rounded-xl border bg-white p-6 shadow-sm"
        >

            <h1 class="text-2xl font-bold">
                {{ $appointment->appointment_number }}
            </h1>


            <div
                class="mt-6 grid gap-5 sm:grid-cols-2"
            >

                <div>
                    <p class="text-xs text-gray-500">
                        Customer
                    </p>

                    <p class="font-semibold">
                        {{ $appointment->customer->name }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-gray-500">
                        Motorcycle
                    </p>

                    <p class="font-semibold">
                        {{ $appointment->motorcycle->brand }}
                        {{ $appointment->motorcycle->model }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-gray-500">
                        Service
                    </p>

                    <p class="font-semibold">
                        {{ $appointment->service->name }}
                    </p>
                </div>


                <div>
                    <p class="text-xs text-gray-500">
                        Status
                    </p>

                    <p class="font-semibold">
                        {{ ucwords(
                            str_replace(
                                '_',
                                ' ',
                                $appointment->status
                            )
                        ) }}
                    </p>
                </div>


                <div class="sm:col-span-2">

                    <p class="text-xs text-gray-500">
                        Customer Concern
                    </p>

                    <p>
                        {{ $appointment->problem_description
                            ?: 'No concern provided.'
                        }}
                    </p>

                </div>

            </div>


            @php

                $nextStatus = match(
                    $appointment->status
                ) {
                    'mechanic_assigned'
                        => 'checked_in',

                    'checked_in'
                        => 'in_service',

                    'in_service'
                        => 'ready_for_release',

                    'ready_for_release'
                        => 'completed',

                    default => null,
                };

            @endphp


            @if($nextStatus)

                <form
                    method="POST"
                    action="{{ route(
                        'mechanic.appointments.status',
                        $appointment
                    ) }}"
                    class="mt-8"
                >

                    @csrf
                    @method('PATCH')


                    <input
                        type="hidden"
                        name="status"
                        value="{{ $nextStatus }}"
                    >


                    <label
                        class="mb-2 block text-sm font-medium"
                    >
                        Mechanic Notes
                    </label>

                    <textarea
                        name="mechanic_notes"
                        rows="4"
                        class="w-full rounded-lg border px-4 py-3"
                    >{{ old(
                        'mechanic_notes',
                        $appointment->mechanic_notes
                    ) }}</textarea>


                    <button
                        class="mt-4 rounded-lg bg-blue-600 px-5 py-3 font-medium text-white"
                    >

                        @switch($nextStatus)

                            @case('checked_in')
                                Mark as Checked In
                                @break

                            @case('in_service')
                                Start Service
                                @break

                            @case('ready_for_release')
                                Mark Ready for Release
                                @break

                            @case('completed')
                                Complete Service
                                @break

                        @endswitch

                    </button>

                </form>

            @else

                <div
                    class="mt-8 rounded-lg bg-green-100 p-4 text-green-700"
                >
                    This service has been completed.
                </div>

            @endif

        </div>

    </div>

</x-layouts.app>