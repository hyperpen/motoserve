<x-admin-layout>

    <div class="p-6">

        <div class="mb-6">
            <h1 class="text-2xl font-bold">
                Audit Logs
            </h1>

            <p class="mt-1 text-gray-500">
                Track important activities performed in MotoServe.
            </p>
        </div>


        <form
            method="GET"
            class="mb-6 grid gap-3 rounded-xl border bg-white p-4 md:grid-cols-4"
        >

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search action..."
                class="rounded-lg border px-4 py-2"
            >


            <select
                name="role"
                class="rounded-lg border px-4 py-2"
            >

                <option value="">
                    All Roles
                </option>

                <option
                    value="admin"
                    @selected(request('role') === 'admin')
                >
                    Admin
                </option>

                <option
                    value="mechanic"
                    @selected(request('role') === 'mechanic')
                >
                    Mechanic
                </option>

                <option
                    value="customer"
                    @selected(request('role') === 'customer')
                >
                    Customer
                </option>

            </select>


            <select
                name="module"
                class="rounded-lg border px-4 py-2"
            >

                <option value="">
                    All Modules
                </option>

                @foreach($modules as $module)

                    <option
                        value="{{ $module }}"
                        @selected(
                            request('module') === $module
                        )
                    >
                        {{ $module }}
                    </option>

                @endforeach

            </select>


            <button
                class="rounded-lg bg-gray-900 px-5 py-2 text-white"
            >
                Filter
            </button>

        </form>


        <div
            class="overflow-hidden rounded-xl border bg-white shadow-sm"
        >

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-5 py-4">
                                Date
                            </th>

                            <th class="px-5 py-4">
                                User
                            </th>

                            <th class="px-5 py-4">
                                Role
                            </th>

                            <th class="px-5 py-4">
                                Module
                            </th>

                            <th class="px-5 py-4">
                                Action
                            </th>

                            <th class="px-5 py-4">
                                Description
                            </th>

                            <th class="px-5 py-4">
                                IP
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        @forelse($logs as $log)

                            <tr>

                                <td class="whitespace-nowrap px-5 py-4">

                                    {{ $log->created_at->format('M d, Y') }}

                                    <p class="text-xs text-gray-500">
                                        {{ $log->created_at->format('h:i A') }}
                                    </p>

                                </td>


                                <td class="px-5 py-4">
                                    {{ $log->user?->name ?? 'System' }}
                                </td>


                                <td class="px-5 py-4">
                                    {{ ucfirst($log->role ?? 'system') }}
                                </td>


                                <td class="px-5 py-4">
                                    {{ $log->module ?? 'N/A' }}
                                </td>


                                <td class="px-5 py-4">

                                    {{ ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $log->action
                                        )
                                    ) }}

                                </td>


                                <td class="px-5 py-4">
                                    {{ $log->description ?? 'No description' }}
                                </td>


                                <td
                                    class="whitespace-nowrap px-5 py-4 text-xs text-gray-500"
                                >
                                    {{ $log->ip_address ?? 'N/A' }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="p-12 text-center text-gray-500"
                                >
                                    No audit logs found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div class="mt-5">
            {{ $logs->links() }}
        </div>

    </div>

</x-admin-layout>