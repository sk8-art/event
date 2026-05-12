<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'quantity',
        'total_price',
        'status', // pending, confirmed, cancelled, completed
        'payment_method',
        'payment_id',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getStatusNameAttribute()
    {
        $statuses = [
            'pending' => 'Ожидает подтверждения',
            'confirmed' => 'Подтверждено',
            'cancelled' => 'Отменено',
            'completed' => 'Завершено',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }
}   