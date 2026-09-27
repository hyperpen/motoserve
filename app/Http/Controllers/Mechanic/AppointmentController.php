<?php

namespace App\Http\Controllers\Mechanic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $appointments = Appointment::with([
            'customer',
            'motorcycle',
            'service',
        ])
            ->where(
                'mechanic_id',
                $request->user()->id
            )
            ->whereNotIn('status', [
                'cancelled',
                'rejected',
            ])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->paginate(15);

        return view(
            'mechanic.appointments.index',
            compact('appointments')
        );
    }

    public function show(
        Request $request,
        Appointment $appointment
    ) {
        abort_unless(
            $appointment->mechanic_id ===
                $request->user()->id,
            403
        );

        $appointment->load([
            'customer',
            'motorcycle',
            'service',
        ]);

        return view(
            'mechanic.appointments.show',
            compact('appointment')
        );
    }

    public function updateStatus(
        Request $request,
        Appointment $appointment
    ) {
        abort_unless(
            $appointment->mechanic_id ===
                $request->user()->id,
            403
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:checked_in,in_service,ready_for_release,completed',
            ],

            'mechanic_notes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ]);

        $allowedTransitions = [
            'mechanic_assigned' => [
                'checked_in',
            ],

            'checked_in' => [
                'in_service',
            ],

            'in_service' => [
                'ready_for_release',
            ],

            'ready_for_release' => [
                'completed',
            ],
        ];

        if (
            !isset(
                $allowedTransitions[
                    $appointment->status
                ]
            ) ||
            !in_array(
                $validated['status'],
                $allowedTransitions[
                    $appointment->status
                ]
            )
        ) {
            return back()->with(
                'error',
                'Invalid status transition.'
            );
        }

        $oldStatus = $appointment->status;

        $data = [
            'status' => $validated['status'],
            'mechanic_notes' =>
                $validated['mechanic_notes'] ??
                $appointment->mechanic_notes,
        ];

        if (
            $validated['status'] ===
            'in_service'
        ) {
            $data['service_started_at'] =
                now();
        }

        if (
            $validated['status'] ===
            'completed'
        ) {
            $data['service_completed_at'] =
                now();
        }

        $appointment->update($data);

        AuditLogger::log(
            action: 'updated_appointment_status',
            module: 'Appointments',
            record: $appointment,
            description:
                "Changed {$appointment->appointment_number} from {$oldStatus} to {$appointment->status}.",
            oldValues: [
                'status' => $oldStatus,
            ],
            newValues: [
                'status' => $appointment->status,
            ]
        );

        return back()->with(
            'success',
            'Appointment status updated.'
        );
    }
}