<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogService
{
    public function log(
        User $user,
        string $module,
        string $action,
        string $description
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $user->id,
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}