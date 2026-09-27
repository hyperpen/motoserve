<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'action',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'module',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                );

            });
        }

        if ($request->filled('role')) {
            $query->where(
                'role',
                $request->role
            );
        }

        if ($request->filled('module')) {
            $query->where(
                'module',
                $request->module
            );
        }

        $logs = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $modules = AuditLog::query()
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view(
            'admin.audit-logs.index',
            compact(
                'logs',
                'modules'
            )
        );
    }
}