@csrf

<div class="grid gap-6 md:grid-cols-2">

    <div>

        <label class="mb-2 block text-sm font-medium">
            Full Name
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $mechanic->name ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 dark:border-gray-600 dark:bg-gray-800"
            required
        >

        @error('name')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror

    </div>


    <div>

        <label class="mb-2 block text-sm font-medium">
            Email Address
        </label>

        <input
            type="email"
            name="email"
            value="{{ old('email', $mechanic->email ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 dark:border-gray-600 dark:bg-gray-800"
            required
        >

        @error('email')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror

    </div>


    <div>

        <label class="mb-2 block text-sm font-medium">
            Phone Number
        </label>

        <input
            type="text"
            name="phone"
            value="{{ old('phone', $mechanic->phone ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 dark:border-gray-600 dark:bg-gray-800"
        >

        @error('phone')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror

    </div>


    <div>

        <label class="mb-2 block text-sm font-medium">
            Status
        </label>

        <select
            name="status"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 dark:border-gray-600 dark:bg-gray-800"
        >

            <option
                value="active"
                @selected(
                    old(
                        'status',
                        $mechanic->status ?? 'active'
                    ) === 'active'
                )
            >
                Active
            </option>

            <option
                value="inactive"
                @selected(
                    old(
                        'status',
                        $mechanic->status ?? ''
                    ) === 'inactive'
                )
            >
                Inactive
            </option>

        </select>

    </div>


    <div>

        <label class="mb-2 block text-sm font-medium">

            Password

            @isset($mechanic)
                <span class="text-xs text-gray-500">
                    (leave blank to keep current)
                </span>
            @endisset

        </label>

        <input
            type="password"
            name="password"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 dark:border-gray-600 dark:bg-gray-800"
            @required(!isset($mechanic))
        >

        @error('password')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror

    </div>


    <div>

        <label class="mb-2 block text-sm font-medium">
            Confirm Password
        </label>

        <input
            type="password"
            name="password_confirmation"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 dark:border-gray-600 dark:bg-gray-800"
            @required(!isset($mechanic))
        >

    </div>

</div>


<div class="mt-8 flex justify-end gap-3">

    <a
        href="{{ route('admin.mechanics.index') }}"
        class="rounded-lg border border-gray-300 px-5 py-2.5 hover:bg-gray-100 dark:border-gray-600"
    >
        Cancel
    </a>


    <button
        type="submit"
        class="rounded-lg bg-blue-600 px-5 py-2.5 font-medium text-white hover:bg-blue-700"
    >
        {{ isset($mechanic)
            ? 'Update Mechanic'
            : 'Create Mechanic'
        }}
    </button>

</div>