<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Catat aktivitas audit ke database.
     */
    public static function log(string $action, string $details, ?User $user = null, ?string $ip = null): AuditLog
    {
        $currentUser = $user ?? Auth::user();
        $ipAddress = $ip ?? request()->ip();

        return AuditLog::create([
            'user_id' => $currentUser?->id,
            'user_nama' => $currentUser?->name ?? 'Sistem / Anonim',
            'role' => $currentUser?->role ?? 'guest',
            'action' => $action,
            'details' => $details,
            'ip_address' => $ipAddress,
        ]);
    }
}
