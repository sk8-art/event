<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'description',
        'price',
        'quantity',
        'available',
        'benefits',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'benefits' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function hasAvailable()
    {
        return $this->available > 0 && $this->is_active;
    }

    public function getBenefitsListAttribute()
    {
        return is_array($this->benefits) ? $this->benefits : [];
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ') . ' ₽';
    }
}