<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'identity_number',
        'availability_status',
        'verification_status',
        'current_latitude',
        'current_longitude',
        'last_location_update',
    ];

    protected function casts(): array
    {
        return [
            'last_location_update' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function averageRating(): float
    {
        return round((float) $this->ratings()->avg('rating'), 1) ?: 5.0;
    }

    public function totalCompletedDeliveries(): int
    {
        return $this->assignments()->where('status', 'Completed')->count();
    }
}

