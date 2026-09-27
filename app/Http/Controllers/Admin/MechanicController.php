<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MechanicController extends Controller
{
    public function index()
    {
        $mechanics = User::where('role', 'mechanic')
            ->latest()
            ->paginate(10);

        return view('admin.mechanics.index', compact('mechanics'));
    }

    public function create()
    {
        return view('admin.mechanics.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $mechanic = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'mechanic',
            'status' => $validated['status'],
        ]);

        AuditLogger::log(
            action: 'created_mechanic',
            module: 'Mechanics',
            record: $mechanic,
            description: "Created mechanic account for {$mechanic->name}.",
            newValues: [
                'name' => $mechanic->name,
                'email' => $mechanic->email,
                'phone' => $mechanic->phone,
                'role' => $mechanic->role,
                'status' => $mechanic->status,
            ]
        );

        return redirect()
            ->route('admin.mechanics.index')
            ->with('success', 'Mechanic account created successfully.');
    }

    public function edit(User $mechanic)
    {
        $this->ensureMechanic($mechanic);

        return view(
            'admin.mechanics.edit',
            compact('mechanic')
        );
    }

    public function update(
        Request $request,
        User $mechanic
    ) {
        $this->ensureMechanic($mechanic);

        $oldValues = [
            'name' => $mechanic->name,
            'email' => $mechanic->email,
            'phone' => $mechanic->phone,
            'status' => $mechanic->status,
        ];

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($mechanic),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $mechanic->name = $validated['name'];
        $mechanic->email = $validated['email'];
        $mechanic->phone = $validated['phone'] ?? null;
        $mechanic->status = $validated['status'];

        if (!empty($validated['password'])) {
            $mechanic->password = Hash::make(
                $validated['password']
            );
        }

        $mechanic->save();

        AuditLogger::log(
            action: 'updated_mechanic',
            module: 'Mechanics',
            record: $mechanic,
            description: "Updated mechanic account for {$mechanic->name}.",
            oldValues: $oldValues,
            newValues: [
                'name' => $mechanic->name,
                'email' => $mechanic->email,
                'phone' => $mechanic->phone,
                'status' => $mechanic->status,
            ]
        );

        return redirect()
            ->route('admin.mechanics.index')
            ->with('success', 'Mechanic updated successfully.');
    }

    public function toggleStatus(User $mechanic)
    {
        $this->ensureMechanic($mechanic);

        $oldStatus = $mechanic->status;

        $mechanic->status =
            $mechanic->status === 'active'
                ? 'inactive'
                : 'active';

        $mechanic->save();

        AuditLogger::log(
            action: 'changed_mechanic_status',
            module: 'Mechanics',
            record: $mechanic,
            description:
                "Changed {$mechanic->name}'s status from {$oldStatus} to {$mechanic->status}.",
            oldValues: [
                'status' => $oldStatus,
            ],
            newValues: [
                'status' => $mechanic->status,
            ]
        );

        return back()->with(
            'success',
            'Mechanic status updated.'
        );
    }

    private function ensureMechanic(User $mechanic): void
    {
        abort_unless(
            $mechanic->role === 'mechanic',
            404
        );
    }
}