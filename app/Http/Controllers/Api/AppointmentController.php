<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Motorcycle;
use App\Models\Service;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $appointments = Appointment::with([
            'motorcycle', 'service', 'mechanic:id,name',
        ])
            ->where('customer_id', $request->user()->id)
            ->latest('appointment_date')
            ->latest('appointment_time')
            ->get();

        return response()->json(['appointments' => $appointments]);
    }

    public function show(Request $request, Appointment $appointment)
    {
        abort_unless(
            (int) $appointment->customer_id === (int) $request->user()->id,
            403
        );

        $appointment->load([
            'motorcycle', 'service', 'mechanic:id,name', 'parts.product',
        ]);

        return response()->json(['appointment' => $appointment]);
    }

    public function store(Request $request)
    {
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

        $duplicateBooking = Appointment::query()
            ->where('customer_id', $request->user()->id)
            ->where('motorcycle_id', $motorcycle->id)
            ->where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->whereNotIn('status', [
                'cancelled', 'rejected', 'completed', 'no_show',
            ])
            ->exists();

        if ($duplicateBooking) {
            return response()->json([
                'message' => 'You already have an appointment for this motorcycle at the selected date and time.',
            ], 422);
        }

        $appointment = Appointment::create([
            'appointment_number' => $this->generateAppointmentNumber(),
            'customer_id' => $request->user()->id,
            'motorcycle_id' => $motorcycle->id,
            'service_id' => $service->id,
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'problem_description' => $validated['problem_description'] ?? null,
            'status' => 'pending',
        ]);

        AuditLogger::log(
            action: 'created_appointment',
            module: 'Appointments',
            record: $appointment,
            description: "Created appointment {$appointment->appointment_number}.",
            newValues: [
                'service' => $service->name,
                'date' => $appointment->appointment_date,
                'time' => $appointment->appointment_time,
                'status' => $appointment->status,
            ]
        );

        return response()->json([
            'message' => 'Appointment booked successfully.',
            'appointment' => $appointment->load(['motorcycle', 'service']),
        ], 201);
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        abort_unless(
            (int) $appointment->customer_id === (int) $request->user()->id,
            403
        );

        if (!in_array(
            $appointment->status,
            ['pending', 'confirmed', 'mechanic_assigned'],
            true
        )) {
            return response()->json([
                'message' => 'This appointment can no longer be cancelled.',
            ], 422);
        }

        $oldStatus = $appointment->status;
        $appointment->update(['status' => 'cancelled']);

        AuditLogger::log(
            action: 'cancelled_appointment',
            module: 'Appointments',
            record: $appointment,
            description: "Cancelled appointment {$appointment->appointment_number}.",
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => 'cancelled']
        );

        return response()->json([
            'message' => 'Appointment cancelled successfully.',
        ]);
    }

    private function generateAppointmentNumber(): string
    {
        do {
            $number = 'APT-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (Appointment::where('appointment_number', $number)->exists());

        return $number;
    }
}
