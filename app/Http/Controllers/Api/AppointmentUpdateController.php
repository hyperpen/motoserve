<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Motorcycle;
use App\Models\Service;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentUpdateController extends Controller
{
    public function __invoke(Request $request, Appointment $appointment)
    {
        abort_unless(
            (int) $appointment->customer_id === (int) $request->user()->id,
            403
        );

        $validated = $request->validate([
            'motorcycle_id' => ['required', 'integer', 'exists:motorcycles,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'problem_description' => ['nullable', 'string', 'max:2000'],
        ]);

        $motorcycle = Motorcycle::query()
            ->whereKey($validated['motorcycle_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $service = Service::query()
            ->whereKey($validated['service_id'])
            ->where('is_active', true)
            ->firstOrFail();

        return DB::transaction(function () use ($request, $appointment, $validated, $motorcycle, $service) {
            $appointment = Appointment::query()
                ->lockForUpdate()
                ->findOrFail($appointment->id);

            abort_unless(
                (int) $appointment->customer_id === (int) $request->user()->id,
                403
            );

            if (!in_array($appointment->status, ['pending', 'confirmed'], true)) {
                return response()->json([
                    'message' => 'This appointment can no longer be edited.',
                ], 422);
            }

            $duplicate = Appointment::query()
                ->where('id', '!=', $appointment->id)
                ->where('customer_id', $request->user()->id)
                ->where('motorcycle_id', $motorcycle->id)
                ->where('appointment_date', $validated['appointment_date'])
                ->where('appointment_time', $validated['appointment_time'])
                ->whereNotIn('status', ['cancelled', 'rejected', 'completed', 'no_show'])
                ->exists();

            if ($duplicate) {
                return response()->json([
                    'message' => 'You already have an appointment for this motorcycle at the selected date and time.',
                ], 422);
            }

            $oldValues = $appointment->only([
                'motorcycle_id', 'service_id', 'appointment_date',
                'appointment_time', 'problem_description',
            ]);

            $appointment->update([
                'motorcycle_id' => $motorcycle->id,
                'service_id' => $service->id,
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $validated['appointment_time'],
                'problem_description' => $validated['problem_description'] ?? null,
            ]);

            AuditLogger::log(
                action: 'updated_appointment',
                module: 'Appointments',
                record: $appointment,
                description: "Updated appointment {$appointment->appointment_number}.",
                oldValues: $oldValues,
                newValues: $appointment->only([
                    'motorcycle_id', 'service_id', 'appointment_date',
                    'appointment_time', 'problem_description',
                ])
            );

            return response()->json([
                'message' => 'Appointment updated successfully.',
                'appointment' => $appointment->load(['motorcycle', 'service', 'mechanic:id,name']),
            ]);
        });
    }
}
