<x-admin-layout>

    <div class="p-6">

        <div class="mb-8">

            <h1
                class="text-3xl font-bold text-gray-900 dark:text-white"
            >
                MotoServe Dashboard
            </h1>

            <p class="mt-1 text-gray-500">
                Welcome back,
                {{ auth()->user()->name }}.
            </p>

        </div>


        <div
            class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4"
        >

            <div
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900"
            >

                <p class="text-sm text-gray-500">
                    Total Mechanics
                </p>

                <div class="mt-2 flex items-end justify-between">

                    <p class="text-3xl font-bold">
                        {{ $totalMechanics }}
                    </p>

                    <span
                        class="rounded-full bg-green-100 px-2 py-1 text-xs text-green-700"
                    >
                        {{ $activeMechanics }} active
                    </span>

                </div>

            </div>


            <div
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900"
            >

                <p class="text-sm text-gray-500">
                    Customers
                </p>

                <p class="mt-2 text-3xl font-bold">
                    {{ $totalCustomers }}
                </p>

            </div>


            <div
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900"
            >

                <p class="text-sm text-gray-500">
                    Pending Appointments
                </p>

                <p class="mt-2 text-3xl font-bold">
                    {{ $pendingAppointments }}
                </p>

            </div>


            <div
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900"
            >

                <p class="text-sm text-gray-500">
                    Active Services
                </p>

                <p class="mt-2 text-3xl font-bold">
                    {{ $activeServices }}
                </p>

            </div>


            <div
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900"
            >

                <p class="text-sm text-gray-500">
                    Pending Pickup Orders
                </p>

                <p class="mt-2 text-3xl font-bold">
                    {{ $pendingOrders }}
                </p>

            </div>


            <div
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900"
            >

                <p class="text-sm text-gray-500">
                    Low Stock Products
                </p>

                <p class="mt-2 text-3xl font-bold">
                    {{ $lowStockProducts }}
                </p>

            </div>

        </div>


        <section class="mt-8" aria-labelledby="completed-work-title">
            <div class="mb-4">
                <h2 id="completed-work-title" class="text-xl font-semibold text-gray-900">Completed Service Work</h2>
                <p class="mt-1 text-sm text-gray-500">Across all mechanics, based on completion time in the Philippines.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach (['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month', 'year' => 'This Year'] as $key => $label)
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-sm text-gray-500">{{ $label }}</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $completedCounts[$key] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Completed Jobs Trend</h3>
                        <p class="mt-1 text-sm text-gray-500">All mechanics · completed appointments</p>
                    </div>

                    <nav aria-label="Chart period" class="flex flex-wrap gap-2">
                        @foreach (['today' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $option => $label)
                            <a href="{{ route('admin.dashboard', ['period' => $option]) }}"
                               @if ($period === $option) aria-current="page" @endif
                               class="rounded-lg px-3 py-2 text-sm font-medium {{ $period === $option ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </nav>
                </div>

                <figure class="mt-6 overflow-x-auto">
                    <div class="flex min-w-[480px] items-end gap-3 border-b border-gray-200 pb-1 sm:min-w-0" role="img" aria-label="Completed jobs by {{ $period }}">
                        @foreach ($chart as $point)
                            <div class="min-w-0 flex-1 text-center">
                                <span class="block text-sm font-semibold text-gray-900">{{ $point['count'] }}</span>
                                <div class="relative mt-2 h-44 rounded-t-md bg-blue-50">
                                    <div class="absolute inset-x-2 bottom-0 rounded-t-md bg-blue-600"
                                         style="height: {{ $point['count'] === 0 ? 0 : max(8, (int) round($point['count'] / $chartMax * 100)) }}%"></div>
                                </div>
                                <span class="mt-2 block truncate text-xs text-gray-500" title="{{ $point['label'] }}">{{ $point['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <figcaption class="mt-4 text-xs text-gray-500">
                        {{ ['today' => 'Last 7 days', 'week' => 'Last 8 weeks', 'month' => 'Last 12 months', 'year' => 'Last 5 years'][$period] }}
                    </figcaption>
                </figure>
            </div>
        </section>

        <div class="mt-8">

            <h2 class="mb-4 text-lg font-semibold">
                Quick Actions
            </h2>


            <div class="flex flex-wrap gap-3">

                <a
                    href="{{ route('admin.mechanics.index') }}"
                    class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-medium text-white hover:bg-blue-700"
                >
                    Manage Mechanics
                </a>
                <a
                    href="{{ route('admin.services.index') }}"
                    class="rounded-lg bg-gray-900 px-5 py-3 text-sm font-medium text-white hover:bg-gray-800"
                >
                    Manage Services
                </a>
                <a
                    href="{{ route('admin.appointments.index') }}"
                    class="rounded-lg bg-orange-600 px-5 py-3 text-sm font-medium text-white hover:bg-orange-700"
                >
                    Appointments
                </a>
                <a
    href="{{ route('admin.products.index') }}"
    class="rounded-lg bg-purple-600 px-5 py-3 text-sm font-medium text-white"
>
    Products & Inventory
</a>

<a
    href="{{ route('admin.orders.index') }}"
    class="rounded-lg bg-green-600 px-5 py-3 text-sm font-medium text-white"
>
    Pickup Orders
</a>

            </div>

        </div>

    </div>

</x-admin-layout>
