<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public static function log(
        string $action,
        string $module,
        ?Model $record = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        AuditLog::create([
            'user_id' => auth()->id(),

            'role' => auth()->check()
                ? auth()->user()->role
                : null,

            'action' => $action,

            'module' => $module,

            'record_type' => $record
                ? get_class($record)
                : null,

            'record_id' => $record?->getKey(),

            'description' => $description,

            'old_values' => $oldValues,

            'new_values' => $newValues,

            'ip_address' => request()->ip(),

            'user_agent' => request()->userAgent(),
        ]);
    }
}