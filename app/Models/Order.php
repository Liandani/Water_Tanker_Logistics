<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'volume',
        'fleet_category_recommendation',
        'trips_count',
        'delivery_address',
        'latitude',
        'longitude',
        'delivery_date',
        'recipient_name',
        'recipient_phone',
        'notes',
        'estimated_distance',
        'water_price_rate',
        'delivery_price_rate',
        'water_price',
        'delivery_price',
        'total_price',
        'status', // Pending, Diproses, Dikirim, Selesai, Dibatalkan, Pending Review
        'payment_status', // Unpaid, Down Payment, Paid
        'pending_reason',
        'rejection_reason',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'estimated_distance' => 'decimal:2',
            'water_price' => 'decimal:2',
            'delivery_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public static function determineFleetCategory(int $volume): array
    {
        if ($volume <= 7000) {
            return [
                'category' => 'Tangki 7.000 L',
                'trips' => 1,
                'min_capacity' => 7000,
            ];
        } elseif ($volume <= 8000) {
            return [
                'category' => 'Tangki 8.000 L',
                'trips' => 1,
                'min_capacity' => 8000,
            ];
        } elseif ($volume <= 16000) {
            return [
                'category' => 'Tangki 16.000 L',
                'trips' => 1,
                'min_capacity' => 16000,
            ];
        } elseif ($volume <= 20000) {
            return [
                'category' => 'Tangki 20.000 L',
                'trips' => 1,
                'min_capacity' => 20000,
            ];
        } else {
            $trips = (int) ceil($volume / 20000);
            return [
                'category' => "XXL / Beberapa Trip ({$trips} Trip)",
                'trips' => $trips,
                'min_capacity' => 20000,
            ];
        }
    }

    public static function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        return round($distance, 2);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function activeAssignment()
    {
        return $this->hasOne(Assignment::class)
            ->whereNotIn('status', ['Cancelled', 'Rejected'])
            ->latestOfMany();
    }

    public function proof()
    {
        return $this->hasOne(DeliveryProof::class);
    }

    public function document()
    {
        return $this->hasOne(DeliveryDocument::class)->latestOfMany();
    }

    public function documents()
    {
        return $this->hasMany(DeliveryDocument::class);
    }

    public function financialTransaction()
    {
        return $this->hasOne(FinancialTransaction::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'Diproses';
    }

    public function isInTransit(): bool
    {
        return $this->status === 'Dikirim';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Selesai';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'Dibatalkan';
    }

    public function isPendingReview(): bool
    {
        return $this->status === 'Pending Review';
    }
}

