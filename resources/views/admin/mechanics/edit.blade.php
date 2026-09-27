<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMechanics = User::where(
            'role',
            'mechanic'
        )->count();

        $activeMechanics = User::where(
            'role',
            'mechanic'
        )
            ->where('status', 'active')
            ->count();

        $totalCustomers = User::where(
            'role',
            'customer'
        )->count();

        $pendingAppointments = Appointment::where(
            'status',
            'pending'
        )->count();

        $activeServices = Appointment::whereIn(
            'status',
            [
                'mechanic_assigned',
                'checked_in',
                'in_service',
                'ready_for_release',
            ]
        )->count();

        $pendingOrders = Order::whereIn(
            'status',
            [
                'pending',
                'confirmed',
                'preparing',
            ]
        )->count();

        $lowStockProducts = Product::whereColumn(
            'stock',
            '<=',
            'minimum_stock'
        )->count();

        return view(
            'admin.dashboard',
            compact(
                'totalMechanics',
                'activeMechanics',
                'totalCustomers',
                'pendingAppointments',
                'activeServices',
                'pendingOrders',
                'lowStockProducts'
            )
        );
    }
}