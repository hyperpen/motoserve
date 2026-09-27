<x-admin-layout>

    <div class="mx-auto max-w-4xl p-6">

        <h1 class="mb-6 text-2xl font-bold">
            Edit Category
        </h1>

        <div class="rounded-xl border bg-white p-6">

            <form
                method="POST"
                action="{{ route(
                    'admin.categories.update',
                    $category
                ) }}"
            >

                @method('PUT')

                @include(
                    'admin.categories._form'
                )

            </form>

        </div>

    </div>

</x-admin-layout>