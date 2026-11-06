<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'assignment_id',
        'driver_id',
        'photo_path',
        'recipient_name',
        'notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}

