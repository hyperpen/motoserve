<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where(
            'role',
            'customer'
        );

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'email',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'phone',
                    'like',
                    "%{$search}%"
                );

            });
        }

        $customers = $query
            ->withCount([
                'motorcycles',
                'customerAppointments',
                'orders',
            ])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.customers.index',
            compact('customers')
        );
    }


    public function show(User $customer)
    {
        abort_unless(
            $customer->role === 'customer',
            404
        );

        $customer->load([
            'motorcycles',

            'customerAppointments' => function ($query) {
                $query->with([
                    'service',
                    'motorcycle',
                    'mechanic',
                ])
                ->latest();
            },

            'orders' => function ($query) {
                $query->latest();
            },
        ]);

        return view(
            'admin.customers.show',
            compact('customer')
        );
    }


    public function toggleStatus(User $customer)
    {
        abort_unless(
            $customer->role === 'customer',
            404
        );

        $oldStatus = $customer->status;

        $customer->update([
            'status' =>
                $customer->status === 'active'
                    ? 'inactive'
                    : 'active',
        ]);

        AuditLogger::log(
            action: 'changed_customer_status',
            module: 'Customers',
            record: $customer,

            description:
                "Changed customer {$customer->name} status from {$oldStatus} to {$customer->status}.",

            oldValues: [
                'status' => $oldStatus,
            ],

            newValues: [
                'status' => $customer->status,
            ]
        );

        return back()->with(
            'success',
            'Customer status updated.'
        );
    }
}