<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'driver_id',
        'vehicle_id',
        'assigned_by',
        'assigned_at',
        'accepted_at',
        'started_at',
        'arrived_at',
        'completed_at',
        'status', // Assigned, Accepted, Rejected, In Transit, Filling Water, Completed, Cancelled
        'driver_reject_reason',
        'driver_issue_reason',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'accepted_at' => 'datetime',
            'started_at' => 'datetime',
            'arrived_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function locations()
    {
        return $this->hasMany(DeliveryLocation::class);
    }

    public function latestLocation()
    {
        return $this->hasOne(DeliveryLocation::class)->latestOfMany('recorded_at');
    }

    public function proof()
    {
        return $this->hasOne(DeliveryProof::class);
    }

    public function document()
    {
        return $this->hasOne(DeliveryDocument::class);
    }
}

