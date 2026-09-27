<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\MotorcycleController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AppointmentUpdateController;

Route::prefix('customer')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/register',
        [AuthController::class, 'register']
    );

    Route::post(
        '/login',
        [AuthController::class, 'login']
    );


    /*
    |--------------------------------------------------------------------------
    | Shop
    |--------------------------------------------------------------------------
    | Public shop browsing
    */

    Route::get(
        '/shop/categories',
        [ShopController::class, 'categories']
    );

    Route::get(
        '/shop/products',
        [ShopController::class, 'products']
    );

    Route::get(
        '/shop/products/{product}',
        [ShopController::class, 'show']
    );


    /*
    |--------------------------------------------------------------------------
    | Authenticated Customer Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Profile / Authentication
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/me',
                [AuthController::class, 'me']
            );

            Route::post(
                '/logout',
                [AuthController::class, 'logout']
            );


            /*
            |--------------------------------------------------------------------------
            | Motorcycles
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/motorcycles',
                [MotorcycleController::class, 'index']
            );

            Route::post(
                '/motorcycles',
                [MotorcycleController::class, 'store']
            );

            Route::put(
                '/motorcycles/{motorcycle}',
                [MotorcycleController::class, 'update']
            );

            Route::delete(
                '/motorcycles/{motorcycle}',
                [MotorcycleController::class, 'destroy']
            );


            /*
            |--------------------------------------------------------------------------
            | Services
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/services',
                [ServiceController::class, 'index']
            );


            /*
            |--------------------------------------------------------------------------
            | Appointments
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/appointments',
                [AppointmentController::class, 'index']
            );

            Route::post(
                '/appointments',
                [AppointmentController::class, 'store']
            );

            Route::get(
                '/appointments/{appointment}',
                [AppointmentController::class, 'show']
            );

            Route::patch(
                '/appointments/{appointment}/cancel',
                [AppointmentController::class, 'cancel']
            );


            /*
            |--------------------------------------------------------------------------
            | Orders
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/orders',
                [OrderController::class, 'index']
            );

            Route::post(
                '/orders',
                [OrderController::class, 'store']
            );

            Route::get(
                '/orders/{order}',
                [OrderController::class, 'show']
            );

            Route::patch(
                '/orders/{order}/cancel',
                [OrderController::class, 'cancel']
            );

            Route::patch('me', [ProfileController::class, 'update']);
Route::put('me/password', [ProfileController::class, 'changePassword']);
Route::put('appointments/{appointment}', AppointmentUpdateController::class);
        });
});