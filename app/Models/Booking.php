<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'requester_name',
        'requester_office_id',
        'vehicle_id',
        'driver_id',
        'purpose',
        'destination',
        'start_datetime',
        'end_datetime',
        'start_odometer',
        'end_odometer',
        'status',
        'current_level',
        'approver_level_1_id',
        'approver_level_2_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'start_odometer' => 'integer',
            'end_odometer' => 'integer',
            'current_level' => 'integer',
        ];
    }

    public function requesterOffice(): BelongsTo
    {
        return $this->belongsTo(
            Office::class,
            'requester_office_id'
        );
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(BookingApproval::class);
    }

    public function approverLevelOne(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approver_level_1_id'
        );
    }

    public function approverLevelTwo(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approver_level_2_id'
        );
    }

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FuelLog::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isOngoing(): bool
    {
        return $this->status === 'ongoing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
    
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}