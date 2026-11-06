<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TariffSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'water_price_per_liter',
        'delivery_price_per_km',
        'base_fee',
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'depot_latitude',
        'depot_longitude',
    ];

    public static function current(): self
    {
        return self::firstOrCreate([], [
            'water_price_per_liter' => 50.00,
            'delivery_price_per_km' => 15000.00,
            'base_fee' => 50000.00,
            'company_name' => 'Tirta Segar Mandiri Tanker Logistics',
            'company_address' => 'Jl. Industri Distribusi Air No. 88, Kawasan Pergudangan, Jakarta',
            'company_phone' => '+62 812-3456-7890',
            'company_email' => 'operasional@tirtamandiri.co.id',
            'depot_latitude' => -6.2088,
            'depot_longitude' => 106.8456,
        ]);
    }
}

