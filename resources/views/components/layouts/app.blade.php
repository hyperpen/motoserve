<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'MotoServe') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-100">

    @if (auth()->check() && auth()->user()->role === 'mechanic')
        <div class="min-h-screen bg-gray-100 lg:flex">
            <aside class="hidden min-h-screen w-64 shrink-0 border-r border-gray-200 bg-white lg:block">
                <div class="sticky top-0 flex min-h-screen flex-col p-5">
                    <div class="mb-8">
                        <a href="{{ route('mechanic.dashboard') }}" class="text-2xl font-bold text-gray-900">MotoServe</a>
                        <p class="text-xs text-gray-500">Mechanic Panel</p>
                    </div>

                    <nav class="space-y-2" aria-label="Mechanic navigation">
                        <a href="{{ route('mechanic.dashboard') }}"
                           @if (request()->routeIs('mechanic.dashboard')) aria-current="page" @endif
                           class="block rounded-lg px-4 py-3 {{ request()->routeIs('mechanic.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('mechanic.appointments.index') }}"
                           @if (request()->routeIs('mechanic.appointments.*')) aria-current="page" @endif
                           class="block rounded-lg px-4 py-3 {{ request()->routeIs('mechanic.appointments.*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            My Jobs
                        </a>
                    </nav>

                    <form method="POST" action="{{ route('logout') }}" class="mt-auto border-t border-gray-200 pt-4">
                        @csrf
                        <button type="submit" class="block w-full rounded-lg px-4 py-3 text-left text-red-700 hover:bg-red-50">
                            Log out
                        </button>
                    </form>
                </div>
            </aside>

            <main class="min-w-0 flex-1">
                <nav class="flex flex-wrap items-center gap-2 border-b bg-white p-4 lg:hidden" aria-label="Mechanic navigation">
                    <a href="{{ route('mechanic.dashboard') }}" class="rounded-lg border px-3 py-2 text-sm">Dashboard</a>
                    <a href="{{ route('mechanic.appointments.index') }}" class="rounded-lg border px-3 py-2 text-sm">My Jobs</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm text-red-700">Log out</button>
                    </form>
                </nav>

                {{ $slot }}
            </main>
        </div>
    @else
        {{ $slot }}
    @endif

    @livewireScripts
</body>
</html>
