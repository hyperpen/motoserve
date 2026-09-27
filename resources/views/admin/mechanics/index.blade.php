<x-admin-layout>

    <div class="p-6">

        <div class="mb-6 flex items-center justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Mechanics
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage mechanic accounts.
                </p>
            </div>

            <a
                href="{{ route('admin.mechanics.create') }}"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
                + Add Mechanic
            </a>

        </div>

        @if(session('success'))
            <div
                class="mb-4 rounded-lg bg-green-100 p-4 text-green-800"
            >
                {{ session('success') }}
            </div>
        @endif


        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
        >

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead
                        class="bg-gray-50 text-xs uppercase text-gray-600 dark:bg-gray-800 dark:text-gray-300"
                    >
                        <tr>

                            <th class="px-6 py-4">
                                Mechanic
                            </th>

                            <th class="px-6 py-4">
                                Contact
                            </th>

                            <th class="px-6 py-4">
                                Status
                            </th>

                            <th class="px-6 py-4">
                                Created
                            </th>

                            <th class="px-6 py-4 text-right">
                                Actions
                            </th>

                        </tr>
                    </thead>


                    <tbody
                        class="divide-y divide-gray-200 dark:divide-gray-700"
                    >

                        @forelse($mechanics as $mechanic)

                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-gray-800"
                            >

                                <td class="px-6 py-4">

                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $mechanic->name }}
                                    </div>

                                    <div class="text-gray-500">
                                        {{ $mechanic->email }}
                                    </div>

                                </td>


                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $mechanic->phone ?? 'No phone' }}
                                </td>


                                <td class="px-6 py-4">

                                    @if($mechanic->status === 'active')

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


                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">

                                    {{ $mechanic->created_at->format('M d, Y') }}

                                </td>


                                <td class="px-6 py-4">

                                    <div class="flex justify-end gap-2">

                                        <a
                                            href="{{ route('admin.mechanics.edit', $mechanic) }}"
                                            class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-800"
                                        >
                                            Edit
                                        </a>


                                        <form
                                            action="{{ route('admin.mechanics.status', $mechanic) }}"
                                            method="POST"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="rounded-lg px-3 py-2 text-xs font-medium
                                                    {{ $mechanic->status === 'active'
                                                        ? 'bg-red-100 text-red-700 hover:bg-red-200'
                                                        : 'bg-green-100 text-green-700 hover:bg-green-200'
                                                    }}"
                                            >

                                                {{ $mechanic->status === 'active'
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
                                    No mechanics found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div class="mt-6">
            {{ $mechanics->links() }}
        </div>

    </div>

</x-admin-layout>