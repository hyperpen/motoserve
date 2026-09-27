<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'appointment_number',
        'customer_id',
        'motorcycle_id',
        'service_id',
        'mechanic_id',
        'appointment_date',
        'appointment_time',
        'problem_description',
        'status',
        'admin_notes',
        'mechanic_notes',
        'service_started_at',
        'service_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'service_started_at' => 'datetime',
            'service_completed_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(
            User::class,
            'customer_id'
        );
    }

    public function mechanic()
    {
        return $this->belongsTo(
            User::class,
            'mechanic_id'
        );
    }

    public function motorcycle()
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function parts()
    {
        return $this->hasMany(AppointmentPart::class);
    }
}