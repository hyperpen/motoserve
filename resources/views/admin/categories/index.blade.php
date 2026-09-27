<x-admin-layout>

    <div class="p-6">

        <div
            class="mb-6 flex items-center justify-between"
        >

            <div>
                <h1 class="text-2xl font-bold">
                    Categories
                </h1>

                <p class="text-gray-500">
                    Product categories.
                </p>
            </div>

            <a
                href="{{ route(
                    'admin.categories.create'
                ) }}"
                class="rounded-lg bg-blue-600 px-4 py-2 text-white"
            >
                + Add Category
            </a>

        </div>


        @if(session('success'))
            <div class="mb-4 rounded bg-green-100 p-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded bg-red-100 p-4">
                {{ session('error') }}
            </div>
        @endif


        <div
            class="overflow-hidden rounded-xl border bg-white"
        >

            <table class="w-full text-left">

                <thead class="bg-gray-50">

                    <tr>
                        <th class="px-6 py-4">
                            Category
                        </th>

                        <th class="px-6 py-4">
                            Products
                        </th>

                        <th class="px-6 py-4">
                            Actions
                        </th>
                    </tr>

                </thead>


                <tbody class="divide-y">

                    @forelse(
                        $categories as $category
                    )

                        <tr>

                            <td class="px-6 py-4">

                                <p class="font-semibold">
                                    {{ $category->name }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    {{ $category->description }}
                                </p>

                            </td>


                            <td class="px-6 py-4">

                                {{ $category->products_count }}

                            </td>


                            <td class="px-6 py-4">

                                <div class="flex gap-2">

                                    <a
                                        href="{{ route(
                                            'admin.categories.edit',
                                            $category
                                        ) }}"
                                        class="rounded border px-3 py-2"
                                    >
                                        Edit
                                    </a>


                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.categories.destroy',
                                            $category
                                        ) }}"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            onclick="return confirm('Delete this category?')"
                                            class="rounded bg-red-100 px-3 py-2 text-red-700"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="3"
                                class="p-10 text-center text-gray-500"
                            >
                                No categories yet.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-5">
            {{ $categories->links() }}
        </div>

    </div>

</x-admin-layout>