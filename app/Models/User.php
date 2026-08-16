<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'position',
        'approval_level',
        'office_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approval_level' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active
            && in_array($this->role, [
                'admin',
                'approver',
            ], true);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function createdBookings(): HasMany
    {
        return $this->hasMany(
            Booking::class,
            'created_by'
        );
    }

    public function bookingApprovals(): HasMany
    {
        return $this->hasMany(
            BookingApproval::class,
            'approver_id'
        );
    }

    public function recordedFuelLogs(): HasMany
    {
        return $this->hasMany(
            FuelLog::class,
            'recorded_by'
        );
    }

    public function serviceSchedules(): HasMany
    {
        return $this->hasMany(
            ServiceSchedule::class,
            'created_by'
        );
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isApprover(): bool
    {
        return $this->role === 'approver';
    }

    public function isLevelOneApprover(): bool
    {
        return $this->isApprover()
            && $this->approval_level === 1;
    }

    public function isLevelTwoApprover(): bool
    {
        return $this->isApprover()
            && $this->approval_level === 2;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasApprovalLevel(int $level): bool
    {
        return $this->isApprover()
            && $this->approval_level === $level;
    }
}