<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return match ($request->user()->role) {
            'admin' =>
                redirect()->route('admin.dashboard'),

            'mechanic' =>
                redirect()->route('mechanic.dashboard'),

            'customer' =>
                redirect('/'),

            default =>
                abort(403),
        };
    }
}