<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'assignment_id',
        'document_number',
        'revision',
        'pdf_path',
        'issued_at',
        'issued_by',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    public static function generateDocumentNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $count = self::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;
        $serial = str_pad($count, 4, '0', STR_PAD_LEFT);
        return "SJJ/{$year}/{$month}/{$serial}";
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}

