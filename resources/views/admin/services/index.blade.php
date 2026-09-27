<x-admin-layout>

    <div class="p-6">

        <div
            class="mb-6 flex items-center justify-between"
        >

            <div>
                <h1 class="text-2xl font-bold">
                    Services
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage motorcycle services.
                </p>
            </div>


            <a
                href="{{ route('admin.services.create') }}"
                class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700"
            >
                + Add Service
            </a>

        </div>


        @if(session('success'))

            <div
                class="mb-5 rounded-lg bg-green-100 p-4 text-green-800"
            >
                {{ session('success') }}
            </div>

        @endif


        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
        >

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-gray-50">
                        <tr>

                            <th class="px-6 py-4">
                                Service
                            </th>

                            <th class="px-6 py-4">
                                Price
                            </th>

                            <th class="px-6 py-4">
                                Duration
                            </th>

                            <th class="px-6 py-4">
                                Status
                            </th>

                            <th class="px-6 py-4 text-right">
                                Actions
                            </th>

                        </tr>
                    </thead>


                    <tbody class="divide-y">

                        @forelse($services as $service)

                            <tr>

                                <td class="px-6 py-4">

                                    <p class="font-semibold">
                                        {{ $service->name }}
                                    </p>

                                    <p
                                        class="mt-1 max-w-md text-xs text-gray-500"
                                    >
                                        {{ $service->description
                                            ?: 'No description'
                                        }}
                                    </p>

                                </td>


                                <td class="px-6 py-4">

                                    ₱{{ number_format(
                                        $service->base_price,
                                        2
                                    ) }}

                                </td>


                                <td class="px-6 py-4">

                                    {{ $service->estimated_minutes }}
                                    minutes

                                </td>


                                <td class="px-6 py-4">

                                    @if($service->is_active)

                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700"
                                        >
                                            Active
                                        </span>

                                    @else

                                        <span
                                            class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700"
                                        >
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    <div
                                        class="flex justify-end gap-2"
                                    >

                                        <a
                                            href="{{ route(
                                                'admin.services.edit',
                                                $service
                                            ) }}"
                                            class="rounded-lg border px-3 py-2 text-xs hover:bg-gray-100"
                                        >
                                            Edit
                                        </a>


                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.services.status',
                                                $service
                                            ) }}"
                                        >

                                            @csrf
                                            @method('PATCH')


                                            <button
                                                type="submit"
                                                class="rounded-lg px-3 py-2 text-xs
                                                {{ $service->is_active
                                                    ? 'bg-red-100 text-red-700'
                                                    : 'bg-green-100 text-green-700'
                                                }}"
                                            >

                                                {{ $service->is_active
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
                                    colspan="5"
                                    class="px-6 py-10 text-center text-gray-500"
                                >
                                    No services available.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div class="mt-5">
            {{ $services->links() }}
        </div>

    </div>

</x-admin-layout>