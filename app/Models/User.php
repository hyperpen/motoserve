<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function motorcycles()
    {
        return $this->hasMany(Motorcycle::class);
    }

    public function customerAppointments()
    {
        return $this->hasMany(
            Appointment::class,
            'customer_id'
        );
    }

    public function mechanicAppointments()
    {
        return $this->hasMany(
            Appointment::class,
            'mechanic_id'
        );
    }

    public function orders()
    {
        return $this->hasMany(
            Order::class,
            'customer_id'
        );
    }
}