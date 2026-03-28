<?php

namespace App\Helpers;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class SystemLogHelper
{
    /**
     * Log a system action.
     *
     * @param string $action
     * @param string|null $module
     * @param string|null $description
     * @return void
     */
    public static function log(string $action, ?string $module = null, ?string $description = null): void
    {
        try {
            SystemLog::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to write system log: ' . $e->getMessage());
        }
    }
}
