@csrf

<div class="space-y-5">

    <div>
        <label class="mb-2 block font-medium">
            Category Name
        </label>

        <input
            type="text"
            name="name"
            value="{{ old(
                'name',
                $category->name ?? ''
            ) }}"
            class="w-full rounded-lg border px-4 py-3"
            required
        >

        @error('name')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    <div>
        <label class="mb-2 block font-medium">
            Description
        </label>

        <textarea
            name="description"
            rows="4"
            class="w-full rounded-lg border px-4 py-3"
        >{{ old(
            'description',
            $category->description ?? ''
        ) }}</textarea>
    </div>

</div>


<div class="mt-6 flex gap-3">

    <button
        class="rounded-lg bg-blue-600 px-5 py-3 text-white"
    >
        Save Category
    </button>

    <a
        href="{{ route(
            'admin.categories.index'
        ) }}"
        class="rounded-lg border px-5 py-3"
    >
        Cancel
    </a>

</div>