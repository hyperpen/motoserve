<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MechanicController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\AuditLogController;

use App\Http\Controllers\Mechanic\DashboardController as MechanicDashboardController;
use App\Http\Controllers\Mechanic\AppointmentController as MechanicAppointmentController;


Route::view('/', 'welcome')->name('home');


Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | General Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Admin Dashboard
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/dashboard',
                [AdminDashboardController::class, 'index']
            )->name('dashboard');


            /*
            |--------------------------------------------------------------------------
            | Mechanics
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'mechanics',
                MechanicController::class
            )->except([
                'show',
                'destroy'
            ]);

            Route::patch(
                '/mechanics/{mechanic}/status',
                [MechanicController::class, 'toggleStatus']
            )->name('mechanics.status');


            /*
            |--------------------------------------------------------------------------
            | Customers
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/customers',
                [CustomerController::class, 'index']
            )->name('customers.index');

            Route::get(
                '/customers/{customer}',
                [CustomerController::class, 'show']
            )->name('customers.show');

            Route::patch(
                '/customers/{customer}/status',
                [CustomerController::class, 'toggleStatus']
            )->name('customers.status');


            /*
            |--------------------------------------------------------------------------
            | Services
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'services',
                ServiceController::class
            )->except([
                'show',
                'destroy'
            ]);

            Route::patch(
                '/services/{service}/status',
                [ServiceController::class, 'toggleStatus']
            )->name('services.status');


            /*
            |--------------------------------------------------------------------------
            | Appointments
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/appointments',
                [AdminAppointmentController::class, 'index']
            )->name('appointments.index');

            Route::get(
                '/appointments/{appointment}',
                [AdminAppointmentController::class, 'show']
            )->name('appointments.show');

            Route::patch(
                '/appointments/{appointment}/confirm',
                [AdminAppointmentController::class, 'confirm']
            )->name('appointments.confirm');

            Route::patch(
                '/appointments/{appointment}/assign',
                [AdminAppointmentController::class, 'assignMechanic']
            )->name('appointments.assign');

            Route::patch(
                '/appointments/{appointment}/reject',
                [AdminAppointmentController::class, 'reject']
            )->name('appointments.reject');


            /*
            |--------------------------------------------------------------------------
            | Categories
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'categories',
                CategoryController::class
            )->except([
                'show'
            ]);


            /*
            |--------------------------------------------------------------------------
            | Products
            |--------------------------------------------------------------------------
            */

            Route::resource(
                'products',
                ProductController::class
            )->except([
                'show',
                'destroy'
            ]);

            Route::patch(
                '/products/{product}/stock',
                [ProductController::class, 'stock']
            )->name('products.stock');

            Route::patch(
                '/products/{product}/status',
                [ProductController::class, 'toggleStatus']
            )->name('products.status');


            /*
            |--------------------------------------------------------------------------
            | Orders
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/orders',
                [AdminOrderController::class, 'index']
            )->name('orders.index');

            Route::get(
                '/orders/{order}',
                [AdminOrderController::class, 'show']
            )->name('orders.show');

            Route::patch(
                '/orders/{order}/status',
                [AdminOrderController::class, 'updateStatus']
            )->name('orders.status');

            Route::patch(
                '/orders/{order}/claim',
                [AdminOrderController::class, 'claim']
            )->name('orders.claim');


            /*
            |--------------------------------------------------------------------------
            | Audit Logs
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/audit-logs',
                [AuditLogController::class, 'index']
            )->name('audit-logs.index');
        });


    /*
    |--------------------------------------------------------------------------
    | Mechanic Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('mechanic')
        ->name('mechanic.')
        ->middleware('role:mechanic')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Mechanic Dashboard
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/dashboard',
                [MechanicDashboardController::class, 'index']
            )->name('dashboard');


            /*
            |--------------------------------------------------------------------------
            | Mechanic Appointments
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/appointments',
                [MechanicAppointmentController::class, 'index']
            )->name('appointments.index');

            Route::get(
                '/appointments/{appointment}',
                [MechanicAppointmentController::class, 'show']
            )->name('appointments.show');

            Route::patch(
                '/appointments/{appointment}/status',
                [MechanicAppointmentController::class, 'updateStatus']
            )->name('appointments.status');
        });
});


require __DIR__.'/settings.php';