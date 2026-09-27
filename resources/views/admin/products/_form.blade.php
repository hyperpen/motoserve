@csrf

<div class="grid gap-5 md:grid-cols-2">

    <div>

        <label class="mb-2 block font-medium">
            Product Name
        </label>

        <input
            type="text"
            name="name"
            value="{{ old(
                'name',
                $product->name ?? ''
            ) }}"
            class="w-full rounded-lg border px-4 py-3"
            required
        >

    </div>


    <div>

        <label class="mb-2 block font-medium">
            SKU
        </label>

        <input
            type="text"
            name="sku"
            value="{{ old(
                'sku',
                $product->sku ?? ''
            ) }}"
            class="w-full rounded-lg border px-4 py-3"
            required
        >

    </div>


    <div>

        <label class="mb-2 block font-medium">
            Category
        </label>

        <select
            name="category_id"
            class="w-full rounded-lg border px-4 py-3"
        >

            <option value="">
                No Category
            </option>

            @foreach($categories as $category)

                <option
                    value="{{ $category->id }}"
                    @selected(
                        old(
                            'category_id',
                            $product->category_id ?? ''
                        )
                        == $category->id
                    )
                >
                    {{ $category->name }}
                </option>

            @endforeach

        </select>

    </div>


    <div>

        <label class="mb-2 block font-medium">
            Brand
        </label>

        <input
            type="text"
            name="brand"
            value="{{ old(
                'brand',
                $product->brand ?? ''
            ) }}"
            class="w-full rounded-lg border px-4 py-3"
        >

    </div>


    <div>

        <label class="mb-2 block font-medium">
            Price
        </label>

        <input
            type="number"
            name="price"
            step="0.01"
            min="0"
            value="{{ old(
                'price',
                $product->price ?? ''
            ) }}"
            class="w-full rounded-lg border px-4 py-3"
            required
        >

    </div>


    @if(!isset($product))

        <div>

            <label class="mb-2 block font-medium">
                Initial Stock
            </label>

            <input
                type="number"
                name="stock"
                min="0"
                value="{{ old('stock', 0) }}"
                class="w-full rounded-lg border px-4 py-3"
                required
            >

        </div>

    @endif


    <div>

        <label class="mb-2 block font-medium">
            Minimum Stock
        </label>

        <input
            type="number"
            name="minimum_stock"
            min="0"
            value="{{ old(
                'minimum_stock',
                $product->minimum_stock ?? 5
            ) }}"
            class="w-full rounded-lg border px-4 py-3"
            required
        >

    </div>


    <div>

        <label class="mb-2 block font-medium">
            Status
        </label>

        <select
            name="is_active"
            class="w-full rounded-lg border px-4 py-3"
        >

            <option value="1">
                Active
            </option>

            <option
                value="0"
                @selected(
                    isset($product) &&
                    !$product->is_active
                )
            >
                Inactive
            </option>

        </select>

    </div>


    <div>

        <label class="mb-2 block font-medium">
            Product Image
        </label>

        <input
            type="file"
            name="image"
            accept="image/*"
            class="w-full rounded-lg border px-4 py-3"
        >

    </div>


    <div class="md:col-span-2">

        <label class="mb-2 block font-medium">
            Compatible Motorcycle Models
        </label>

        <input
            type="text"
            name="compatible_models"
            value="{{ old(
                'compatible_models',
                $product->compatible_models ?? ''
            ) }}"
            placeholder="Honda Click 125, Click 150, Click 160"
            class="w-full rounded-lg border px-4 py-3"
        >

    </div>


    <div class="md:col-span-2">

        <label class="mb-2 block font-medium">
            Description
        </label>

        <textarea
            name="description"
            rows="4"
            class="w-full rounded-lg border px-4 py-3"
        >{{ old(
            'description',
            $product->description ?? ''
        ) }}</textarea>

    </div>

</div>


<div class="mt-7 flex gap-3">

    <button
        class="rounded-lg bg-blue-600 px-5 py-3 text-white"
    >
        Save Product
    </button>

    <a
        href="{{ route('admin.products.index') }}"
        class="rounded-lg border px-5 py-3"
    >
        Cancel
    </a>

</div>