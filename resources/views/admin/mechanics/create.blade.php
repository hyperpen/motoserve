<x-admin-layout>

    <div class="mx-auto max-w-5xl p-6">

        <div class="mb-6">

            <h1 class="text-2xl font-bold">
                Add Mechanic
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Create a new mechanic account.
            </p>

        </div>


        <div
            class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900"
        >

            <form
                action="{{ route('admin.mechanics.store') }}"
                method="POST"
            >

                @include('admin.mechanics._form')

            </form>

        </div>

    </div>

</x-admin-layout>