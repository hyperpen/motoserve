<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()
            ->paginate(10);

        return view(
            'admin.services.index',
            compact('services')
        );
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:services,name',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'base_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'estimated_minutes' => [
                'required',
                'integer',
                'min:1',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $service = Service::create($validated);

        AuditLogger::log(
            action: 'created_service',
            module: 'Services',
            record: $service,
            description:
                "Created service: {$service->name}.",
            newValues: [
                'name' => $service->name,
                'base_price' => $service->base_price,
                'estimated_minutes' =>
                    $service->estimated_minutes,
                'is_active' => $service->is_active,
            ]
        );

        return redirect()
            ->route('admin.services.index')
            ->with(
                'success',
                'Service created successfully.'
            );
    }

    public function edit(Service $service)
    {
        return view(
            'admin.services.edit',
            compact('service')
        );
    }

    public function update(
        Request $request,
        Service $service
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:services,name,' . $service->id,
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'base_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'estimated_minutes' => [
                'required',
                'integer',
                'min:1',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $oldValues = [
            'name' => $service->name,
            'description' => $service->description,
            'base_price' => $service->base_price,
            'estimated_minutes' =>
                $service->estimated_minutes,
            'is_active' => $service->is_active,
        ];

        $service->update($validated);

        AuditLogger::log(
            action: 'updated_service',
            module: 'Services',
            record: $service,
            description:
                "Updated service: {$service->name}.",
            oldValues: $oldValues,
            newValues: [
                'name' => $service->name,
                'description' => $service->description,
                'base_price' => $service->base_price,
                'estimated_minutes' =>
                    $service->estimated_minutes,
                'is_active' => $service->is_active,
            ]
        );

        return redirect()
            ->route('admin.services.index')
            ->with(
                'success',
                'Service updated successfully.'
            );
    }

    public function toggleStatus(Service $service)
    {
        $oldStatus = $service->is_active;

        $service->update([
            'is_active' => !$service->is_active,
        ]);

        AuditLogger::log(
            action: 'changed_service_status',
            module: 'Services',
            record: $service,
            description:
                "Changed {$service->name} service status.",
            oldValues: [
                'is_active' => $oldStatus,
            ],
            newValues: [
                'is_active' => $service->is_active,
            ]
        );

        return back()->with(
            'success',
            'Service status updated.'
        );
    }
}