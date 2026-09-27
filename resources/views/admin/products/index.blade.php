<x-admin-layout>

    <div class="p-6">

        <div
            class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >

            <div>

                <h1 class="text-2xl font-bold">
                    Products & Inventory
                </h1>

                <p class="text-gray-500">
                    Manage motorcycle parts and stock.
                </p>

            </div>


            <div class="flex gap-2">

                <a
                    href="{{ route(
                        'admin.categories.index'
                    ) }}"
                    class="rounded-lg border px-4 py-2"
                >
                    Categories
                </a>

                <a
                    href="{{ route(
                        'admin.products.create'
                    ) }}"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white"
                >
                    + Add Product
                </a>

            </div>

        </div>


        <form
            method="GET"
            class="mb-5 flex max-w-xl gap-2"
        >

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search product..."
                class="flex-1 rounded-lg border px-4 py-2"
            >

            <button
                class="rounded-lg bg-gray-900 px-5 py-2 text-white"
            >
                Search
            </button>

        </form>


        @if(session('success'))

            <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>

        @endif


        <div
            class="overflow-hidden rounded-xl border bg-white"
        >

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-gray-50">

                        <tr>
                            <th class="px-5 py-4">
                                Product
                            </th>

                            <th class="px-5 py-4">
                                Category
                            </th>

                            <th class="px-5 py-4">
                                Price
                            </th>

                            <th class="px-5 py-4">
                                Stock
                            </th>

                            <th class="px-5 py-4">
                                Status
                            </th>

                            <th class="px-5 py-4">
                                Actions
                            </th>
                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        @forelse(
                            $products as $product
                        )

                            <tr>

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        @if($product->image)

                                            <img
                                                src="{{ asset(
                                                    'storage/' .
                                                    $product->image
                                                ) }}"
                                                class="h-12 w-12 rounded-lg object-cover"
                                            >

                                        @endif


                                        <div>

                                            <p class="font-semibold">
                                                {{ $product->name }}
                                            </p>

                                            <p class="text-xs text-gray-500">
                                                {{ $product->sku }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                <td class="px-5 py-4">
                                    {{ $product->category?->name
                                        ?? 'None'
                                    }}
                                </td>


                                <td class="px-5 py-4">

                                    ₱{{ number_format(
                                        $product->price,
                                        2
                                    ) }}

                                </td>


                                <td class="px-5 py-4">

                                    <span
                                        class="{{
                                            $product->stock <=
                                            $product->minimum_stock
                                                ? 'font-semibold text-red-600'
                                                : ''
                                        }}"
                                    >
                                        {{ $product->stock }}
                                    </span>

                                    @if(
                                        $product->stock <=
                                        $product->minimum_stock
                                    )

                                        <span
                                            class="ml-1 text-xs text-red-500"
                                        >
                                            Low
                                        </span>

                                    @endif

                                </td>


                                <td class="px-5 py-4">

                                    {{ $product->is_active
                                        ? 'Active'
                                        : 'Inactive'
                                    }}

                                </td>


                                <td class="px-5 py-4">

                                    <div class="flex flex-wrap gap-2">

                                        <a
                                            href="{{ route(
                                                'admin.products.edit',
                                                $product
                                            ) }}"
                                            class="rounded border px-3 py-2"
                                        >
                                            Edit
                                        </a>


                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.products.stock',
                                                $product
                                            ) }}"
                                            class="flex gap-1"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="operation"
                                                value="add"
                                            >

                                            <input
                                                type="number"
                                                name="quantity"
                                                min="1"
                                                value="1"
                                                class="w-16 rounded border px-2"
                                            >

                                            <button
                                                class="rounded bg-green-100 px-3 py-2 text-green-700"
                                            >
                                                + Stock
                                            </button>

                                        </form>


                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.products.status',
                                                $product
                                            ) }}"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                class="rounded bg-gray-100 px-3 py-2"
                                            >
                                                {{ $product->is_active
                                                    ? 'Disable'
                                                    : 'Enable'
                                                }}
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="p-10 text-center text-gray-500"
                                >
                                    No products found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div class="mt-5">
            {{ $products->links() }}
        </div>

    </div>

</x-admin-layout>