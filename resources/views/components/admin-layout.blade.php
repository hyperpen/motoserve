<x-layouts.app>

    <div class="min-h-screen bg-gray-100">

        <div class="flex">

            {{-- SIDEBAR --}}
            <aside
                class="hidden min-h-screen w-64 shrink-0 border-r border-gray-200 bg-white lg:block"
            >
                <div class="sticky top-0 flex min-h-screen flex-col p-5">

                    <div class="mb-8">
                        <h1 class="text-2xl font-bold">
                            MotoServe
                        </h1>

                        <p class="text-xs text-gray-500">
                            Admin Panel
                        </p>
                    </div>


                    <nav class="space-y-2">

                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="block rounded-lg px-4 py-3
                            {{
                                request()->routeIs('admin.dashboard')
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-700 hover:bg-gray-100'
                            }}"
                        >
                            Dashboard
                        </a>


                        <a
                            href="{{ route('admin.appointments.index') }}"
                            class="block rounded-lg px-4 py-3
                            {{
                                request()->routeIs('admin.appointments.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-700 hover:bg-gray-100'
                            }}"
                        >
                            Appointments
                        </a>


                        <a
                            href="{{ route('admin.orders.index') }}"
                            class="block rounded-lg px-4 py-3
                            {{
                                request()->routeIs('admin.orders.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-700 hover:bg-gray-100'
                            }}"
                        >
                            Pickup Orders
                        </a>


                        <a
                            href="{{ route('admin.products.index') }}"
                            class="block rounded-lg px-4 py-3
                            {{
                                request()->routeIs('admin.products.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-700 hover:bg-gray-100'
                            }}"
                        >
                            Products
                        </a>


                        <a
                            href="{{ route('admin.categories.index') }}"
                            class="block rounded-lg px-4 py-3
                            {{
                                request()->routeIs('admin.categories.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-700 hover:bg-gray-100'
                            }}"
                        >
                            Categories
                        </a>


                        <a
                            href="{{ route('admin.services.index') }}"
                            class="block rounded-lg px-4 py-3
                            {{
                                request()->routeIs('admin.services.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-700 hover:bg-gray-100'
                            }}"
                        >
                            Services
                        </a>


                        <a
                            href="{{ route('admin.mechanics.index') }}"
                            class="block rounded-lg px-4 py-3
                            {{
                                request()->routeIs('admin.mechanics.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-700 hover:bg-gray-100'
                            }}"
                        >
                            Mechanics
                        </a>


                        <a
                            href="{{ route('admin.customers.index') }}"
                            class="block rounded-lg px-4 py-3
                            {{
                                request()->routeIs('admin.customers.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-700 hover:bg-gray-100'
                            }}"
                        >
                            Customers
                        </a>


                        <a
                            href="{{ route('admin.audit-logs.index') }}"
                            class="block rounded-lg px-4 py-3
                            {{
                                request()->routeIs('admin.audit-logs.*')
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-700 hover:bg-gray-100'
                            }}"
                        >
                            Audit Logs
                        </a>

                    </nav>

                    <form method="POST" action="{{ route('logout') }}" class="mt-auto border-t border-gray-200 pt-4">
                        @csrf
                        <button
                            type="submit"
                            class="block w-full rounded-lg px-4 py-3 text-left text-red-700 hover:bg-red-50"
                        >
                            Log out
                        </button>
                    </form>

                </div>

            </aside>


            {{-- MAIN CONTENT --}}
            <main class="min-w-0 flex-1">

                {{-- MOBILE NAV --}}
                <div
                    class="border-b bg-white p-4 lg:hidden"
                >

                    <div class="flex gap-2 overflow-x-auto">

                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="whitespace-nowrap rounded-lg border px-3 py-2 text-sm"
                        >
                            Dashboard
                        </a>

                        <a
                            href="{{ route('admin.appointments.index') }}"
                            class="whitespace-nowrap rounded-lg border px-3 py-2 text-sm"
                        >
                            Appointments
                        </a>

                        <a
                            href="{{ route('admin.products.index') }}"
                            class="whitespace-nowrap rounded-lg border px-3 py-2 text-sm"
                        >
                            Products
                        </a>

                        <a
                            href="{{ route('admin.orders.index') }}"
                            class="whitespace-nowrap rounded-lg border px-3 py-2 text-sm"
                        >
                            Orders
                        </a>

                        <a
                            href="{{ route('admin.mechanics.index') }}"
                            class="whitespace-nowrap rounded-lg border px-3 py-2 text-sm"
                        >
                            Mechanics
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="whitespace-nowrap rounded-lg border border-red-200 px-3 py-2 text-sm text-red-700 hover:bg-red-50"
                            >
                                Log out
                            </button>
                        </form>

                    </div>

                </div>


                {{ $slot }}

            </main>

        </div>

    </div>

</x-layouts.app>
