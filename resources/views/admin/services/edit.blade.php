<x-admin-layout>

    <div class="mx-auto max-w-5xl p-6">

        <div class="mb-6">
            <h1 class="text-2xl font-bold">
                Edit Service
            </h1>

            <p class="mt-1 text-gray-500">
                Update service details.
            </p>
        </div>


        <div
            class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
        >

            <form
                method="POST"
                action="{{ route(
                    'admin.services.update',
                    $service
                ) }}"
            >

                @method('PUT')

                @include('admin.services._form')

            </form>

        </div>

    </div>

</x-admin-layout>