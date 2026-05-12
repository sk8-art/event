<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', // Добавили role_id
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Связь с ролью
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Проверка ролей
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isOrganizer()
    {
        return $this->hasRole('organizer');
    }

    public function isUser()
    {
        return $this->hasRole('user');
    }

    // Проверка нескольких ролей
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }
        
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }

    /* Заказы пользователя
    */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Оплаченные заказы пользователя
     */
    public function paidOrders()
    {
        return $this->hasMany(Order::class)
                    ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_CONFIRMED]);
    }

    /**
     * Сумма потраченная пользователем
     */
    public function getTotalSpentAttribute()
    {
        return $this->paidOrders()->sum('total_price');
    }


    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteEvents()
    {
        return $this->belongsToMany(Event::class, 'favorites')->withTimestamps();
    }

    public function hasInFavorites($eventId)
    {
        return $this->favorites()->where('event_id', $eventId)->exists();
    }
}