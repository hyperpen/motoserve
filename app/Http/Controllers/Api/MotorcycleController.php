<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class MotorcycleController extends Controller
{
    public function index(Request $request)
    {
        $motorcycles = $request->user()
            ->motorcycles()
            ->latest()
            ->get();

        return response()->json([
            'motorcycles' => $motorcycles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => [
                'required',
                'string',
                'max:100',
            ],

            'model' => [
                'required',
                'string',
                'max:100',
            ],

            'year' => [
                'nullable',
                'integer',
                'min:1950',
                'max:' . now()->year,
            ],

            'plate_number' => [
                'nullable',
                'string',
                'max:30',
            ],

            'color' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        $motorcycle = $request->user()
            ->motorcycles()
            ->create($validated);

        AuditLogger::log(
            action: 'created_motorcycle',
            module: 'Motorcycles',
            record: $motorcycle,
            description:
                "Added motorcycle {$motorcycle->brand} {$motorcycle->model}.",
            newValues: $motorcycle->only([
                'brand',
                'model',
                'year',
                'plate_number',
                'color',
            ])
        );

        return response()->json([
            'message' => 'Motorcycle added successfully.',
            'motorcycle' => $motorcycle,
        ], 201);
    }

    public function update(
        Request $request,
        Motorcycle $motorcycle
    ) {
        abort_unless(
            $motorcycle->user_id === $request->user()->id,
            403
        );

        $validated = $request->validate([
            'brand' => [
                'required',
                'string',
                'max:100',
            ],

            'model' => [
                'required',
                'string',
                'max:100',
            ],

            'year' => [
                'nullable',
                'integer',
                'min:1950',
                'max:' . now()->year,
            ],

            'plate_number' => [
                'nullable',
                'string',
                'max:30',
            ],

            'color' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        $oldValues = $motorcycle->only([
            'brand',
            'model',
            'year',
            'plate_number',
            'color',
        ]);

        $motorcycle->update($validated);

        AuditLogger::log(
            action: 'updated_motorcycle',
            module: 'Motorcycles',
            record: $motorcycle,
            description:
                "Updated motorcycle {$motorcycle->brand} {$motorcycle->model}.",
            oldValues: $oldValues,
            newValues: $motorcycle->only([
                'brand',
                'model',
                'year',
                'plate_number',
                'color',
            ])
        );

        return response()->json([
            'message' => 'Motorcycle updated successfully.',
            'motorcycle' => $motorcycle,
        ]);
    }

    public function destroy(
        Request $request,
        Motorcycle $motorcycle
    ) {
        abort_unless(
            $motorcycle->user_id === $request->user()->id,
            403
        );

        $hasActiveAppointment =
            $motorcycle->appointments()
                ->whereNotIn('status', [
                    'completed',
                    'cancelled',
                    'rejected',
                    'no_show',
                ])
                ->exists();

        if ($hasActiveAppointment) {
            return response()->json([
                'message' =>
                    'Motorcycle cannot be deleted while it has an active appointment.'
            ], 422);
        }

        $motorcycle->delete();

        return response()->json([
            'message' => 'Motorcycle deleted successfully.'
        ]);
    }
}