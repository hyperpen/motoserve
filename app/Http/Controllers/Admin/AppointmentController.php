<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with([
            'customer',
            'motorcycle',
            'service',
            'mechanic',
        ]);

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $appointments = $query
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.appointments.index',
            compact('appointments')
        );
    }

    public function show(Appointment $appointment)
    {
        $appointment->load([
            'customer',
            'motorcycle',
            'service',
            'mechanic',
        ]);

        $mechanics = User::where(
            'role',
            'mechanic'
        )
            ->where(
                'status',
                'active'
            )
            ->orderBy('name')
            ->get();

        return view(
            'admin.appointments.show',
            compact(
                'appointment',
                'mechanics'
            )
        );
    }

    public function confirm(Appointment $appointment)
    {
        if ($appointment->status !== 'pending') {
            return back()->with(
                'error',
                'Only pending appointments can be confirmed.'
            );
        }

        $oldStatus = $appointment->status;

        $appointment->update([
            'status' => 'confirmed',
        ]);

        AuditLogger::log(
            action: 'confirmed_appointment',
            module: 'Appointments',
            record: $appointment,
            description:
                "Confirmed appointment {$appointment->appointment_number}.",
            oldValues: [
                'status' => $oldStatus,
            ],
            newValues: [
                'status' => 'confirmed',
            ]
        );

        return back()->with(
            'success',
            'Appointment confirmed.'
        );
    }

    public function assignMechanic(
        Request $request,
        Appointment $appointment
    ) {
        $validated = $request->validate([
            'mechanic_id' => [
                'required',
                'exists:users,id',
            ],
        ]);

        $mechanic = User::where(
            'id',
            $validated['mechanic_id']
        )
            ->where(
                'role',
                'mechanic'
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();

        if (!in_array(
            $appointment->status,
            [
                'confirmed',
                'mechanic_assigned',
            ]
        )) {
            return back()->with(
                'error',
                'Confirm the appointment first.'
            );
        }

        $conflict = Appointment::where(
            'mechanic_id',
            $mechanic->id
        )
            ->where(
                'appointment_date',
                $appointment->appointment_date
            )
            ->where(
                'appointment_time',
                $appointment->appointment_time
            )
            ->whereNotIn('status', [
                'completed',
                'cancelled',
                'rejected',
                'no_show',
            ])
            ->where(
                'id',
                '!=',
                $appointment->id
            )
            ->exists();

        if ($conflict) {
            return back()->with(
                'error',
                "{$mechanic->name} already has an appointment at this time."
            );
        }

        $oldValues = [
            'mechanic_id' =>
                $appointment->mechanic_id,

            'status' =>
                $appointment->status,
        ];

        $appointment->update([
            'mechanic_id' => $mechanic->id,
            'status' => 'mechanic_assigned',
        ]);

        AuditLogger::log(
            action: 'assigned_mechanic',
            module: 'Appointments',
            record: $appointment,
            description:
                "Assigned {$mechanic->name} to {$appointment->appointment_number}.",
            oldValues: $oldValues,
            newValues: [
                'mechanic_id' => $mechanic->id,
                'mechanic_name' => $mechanic->name,
                'status' => 'mechanic_assigned',
            ]
        );

        return back()->with(
            'success',
            "Mechanic {$mechanic->name} assigned successfully."
        );
    }

    public function reject(
        Request $request,
        Appointment $appointment
    ) {
        $validated = $request->validate([
            'admin_notes' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        if (!in_array(
            $appointment->status,
            [
                'pending',
                'confirmed',
            ]
        )) {
            return back()->with(
                'error',
                'This appointment cannot be rejected.'
            );
        }

        $appointment->update([
            'status' => 'rejected',
            'admin_notes' =>
                $validated['admin_notes'],
        ]);

        AuditLogger::log(
            action: 'rejected_appointment',
            module: 'Appointments',
            record: $appointment,
            description:
                "Rejected appointment {$appointment->appointment_number}."
        );

        return back()->with(
            'success',
            'Appointment rejected.'
        );
    }
}