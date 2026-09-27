<x-layouts.app>
    <div class="p-5 sm:p-6">
        <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mechanic Dashboard</h1>
                <p class="mt-1 text-gray-500">Welcome back, {{ auth()->user()->name }}.</p>
            </div>

            <a href="{{ route('mechanic.appointments.index') }}"
               class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                View My Jobs
            </a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Active Jobs</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $activeJobs }}</p>
            </div>

            @foreach (['today' => 'Completed Today', 'week' => 'This Week', 'month' => 'This Month', 'year' => 'This Year'] as $key => $label)
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">{{ $label }}</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $completedCounts[$key] }}</p>
                </div>
            @endforeach
        </div>

        <section class="mt-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="completed-jobs-chart-title">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 id="completed-jobs-chart-title" class="text-lg font-semibold text-gray-900">My Completed Jobs</h2>
                    <p class="mt-1 text-sm text-gray-500">Only jobs assigned to you, based on completion time in the Philippines.</p>
                </div>

                <nav aria-label="Chart period" class="flex flex-wrap gap-2">
                    @foreach (['today' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $option => $label)
                        <a href="{{ route('mechanic.dashboard', ['period' => $option]) }}"
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
        </section>

        <section class="mt-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="my-jobs-title">
            <div class="flex items-center justify-between gap-3">
                <h2 id="my-jobs-title" class="text-lg font-semibold text-gray-900">Current Jobs</h2>
                <a href="{{ route('mechanic.appointments.index') }}" class="text-sm font-medium text-blue-600 hover:underline">See all jobs</a>
            </div>

            @forelse ($recentJobs as $job)
                <a href="{{ route('mechanic.appointments.show', $job) }}" class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4 hover:text-blue-700">
                    <div>
                        <p class="font-semibold">{{ $job->appointment_number }} · {{ $job->service?->name ?? 'Service unavailable' }}</p>
                        <p class="text-sm text-gray-500">{{ $job->customer?->name ?? 'Customer unavailable' }} · {{ $job->motorcycle?->brand }} {{ $job->motorcycle?->model }}</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $job->appointment_date->format('M j, Y') }} · {{ ucwords(str_replace('_', ' ', $job->status)) }}
                    </div>
                </a>
            @empty
                <p class="mt-4 text-sm text-gray-500">No active jobs assigned to you right now.</p>
            @endforelse
        </section>
    </div>
</x-layouts.app>
