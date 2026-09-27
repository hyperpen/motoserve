<x-admin-layout>

    <div class="mx-auto max-w-5xl p-6">

        <h1 class="mb-6 text-2xl font-bold">
            Add Product
        </h1>

        <div class="rounded-xl border bg-white p-6">

            <form
                method="POST"
                action="{{ route(
                    'admin.products.store'
                ) }}"
                enctype="multipart/form-data"
            >

                @include(
                    'admin.products._form'
                )

            </form>

        </div>

    </div>

</x-admin-layout>