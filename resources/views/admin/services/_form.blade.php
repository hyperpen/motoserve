@csrf

<div class="grid gap-6 md:grid-cols-2">

    <div>
        <label
            class="mb-2 block text-sm font-medium"
        >
            Service Name
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $service->name ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-3"
            placeholder="Example: Oil Change"
            required
        >

        @error('name')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    <div>
        <label
            class="mb-2 block text-sm font-medium"
        >
            Base Price
        </label>

        <input
            type="number"
            name="base_price"
            step="0.01"
            min="0"
            value="{{ old('base_price', $service->base_price ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-3"
            placeholder="0.00"
            required
        >

        @error('base_price')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    <div>
        <label
            class="mb-2 block text-sm font-medium"
        >
            Estimated Duration
        </label>

        <input
            type="number"
            name="estimated_minutes"
            min="1"
            value="{{ old(
                'estimated_minutes',
                $service->estimated_minutes ?? 60
            ) }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-3"
            required
        >

        <p class="mt-1 text-xs text-gray-500">
            Duration in minutes.
        </p>

        @error('estimated_minutes')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    <div>
        <label
            class="mb-2 block text-sm font-medium"
        >
            Status
        </label>

        <select
            name="is_active"
            class="w-full rounded-lg border border-gray-300 px-4 py-3"
        >

            <option
                value="1"
                @selected(
                    old(
                        'is_active',
                        isset($service)
                            ? (int) $service->is_active
                            : 1
                    ) === 1
                )
            >
                Active
            </option>

            <option
                value="0"
                @selected(
                    old(
                        'is_active',
                        isset($service)
                            ? (int) $service->is_active
                            : 1
                    ) === 0
                )
            >
                Inactive
            </option>

        </select>
    </div>


    <div class="md:col-span-2">

        <label
            class="mb-2 block text-sm font-medium"
        >
            Description
        </label>

        <textarea
            name="description"
            rows="4"
            class="w-full rounded-lg border border-gray-300 px-4 py-3"
            placeholder="Describe the service..."
        >{{ old(
            'description',
            $service->description ?? ''
        ) }}</textarea>

        @error('description')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror

    </div>

</div>


<div class="mt-8 flex justify-end gap-3">

    <a
        href="{{ route('admin.services.index') }}"
        class="rounded-lg border border-gray-300 px-5 py-2.5 hover:bg-gray-100"
    >
        Cancel
    </a>


    <button
        type="submit"
        class="rounded-lg bg-blue-600 px-5 py-2.5 font-medium text-white hover:bg-blue-700"
    >
        {{ isset($service)
            ? 'Update Service'
            : 'Create Service'
        }}
    </button>

</div>