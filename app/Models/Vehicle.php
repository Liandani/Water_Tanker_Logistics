<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'capacity',
        'vehicle_type',
        'brand_model',
        'status',
        'current_latitude',
        'current_longitude',
        'notes',
    ];

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(Assignment::class)
            ->whereIn('status', ['Assigned', 'Accepted', 'In Transit', 'Filling Water'])
            ->latestOfMany();
    }
}

