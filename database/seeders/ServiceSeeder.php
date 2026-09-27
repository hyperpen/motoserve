<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Oil Change',
                'description' =>
                    'Engine oil replacement and basic inspection.',
                'base_price' => 250,
                'estimated_minutes' => 30,
            ],

            [
                'name' => 'Preventive Maintenance',
                'description' =>
                    'General motorcycle inspection and maintenance.',
                'base_price' => 500,
                'estimated_minutes' => 90,
            ],

            [
                'name' => 'CVT Cleaning',
                'description' =>
                    'Cleaning and inspection of the CVT system.',
                'base_price' => 450,
                'estimated_minutes' => 60,
            ],

            [
                'name' => 'Brake Inspection',
                'description' =>
                    'Inspection of brake pads, discs, and brake system.',
                'base_price' => 250,
                'estimated_minutes' => 30,
            ],

            [
                'name' => 'Engine Diagnosis',
                'description' =>
                    'Diagnosis of engine problems and abnormal behavior.',
                'base_price' => 350,
                'estimated_minutes' => 60,
            ],

            [
                'name' => 'Electrical Diagnosis',
                'description' =>
                    'Inspection of battery, lights, wiring, and electrical components.',
                'base_price' => 350,
                'estimated_minutes' => 60,
            ],

            [
                'name' => 'Tire Replacement',
                'description' =>
                    'Removal and installation of motorcycle tires.',
                'base_price' => 200,
                'estimated_minutes' => 30,
            ],

            [
                'name' => 'General Repair',
                'description' =>
                    'General inspection and repair of motorcycle issues.',
                'base_price' => 300,
                'estimated_minutes' => 60,
            ],
        ];


        foreach ($services as $service) {

            Service::updateOrCreate(
                [
                    'name' => $service['name']
                ],
                [
                    ...$service,
                    'is_active' => true,
                ]
            );

        }
    }
}