<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'type',
        'date',
        'location',
        'address',
        'price',
        'total_tickets',
        'available_tickets',
        'image',
        'organizer_id',
        'status',
        'views',
        'avatar',
    ];

    protected $casts = [
        'date' => 'datetime',
        'price' => 'decimal:2',
    ];

    /**
     * Организатор мероприятия
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Заказы на это мероприятие
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'event_id');
    }

    /**
     * Оплаченные заказы
     */
    public function paidOrders()
    {
        return $this->hasMany(Order::class, 'event_id')
                    ->whereIn('status', ['paid', 'confirmed']);
    }

    /**
     * Отзывы на мероприятие
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'event_id');
    }

    // ============= МЕТОДЫ ДЛЯ ПРОВЕРКИ СТАТУСА =============

    /**
     * Проверка, можно ли покупать билеты
     */
    public function canBuyTickets()
    {
        return $this->status === 'active' 
        && $this->date > now() 
        && $this->available_tickets > 0;
    }

    /**
     * Проверка, началось ли мероприятие
     */
    public function hasStarted()
    {
        return $this->date <= now();
    }

    /**
     * Проверка, завершено ли мероприятие
     */
    public function isFinished()
    {
        return $this->date < now() || $this->status === 'completed';
    }

    /**
     * Активно ли мероприятие
     */
    public function isActive()
    {
        return $this->status === 'active' && $this->date > now();
    }

    /**
     * Отменено ли мероприятие
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Завершено ли мероприятие
     */
    public function isCompleted()
    {
        return $this->status === 'completed' || $this->date < now();
    }

    /**
     * Обновить статус на основе даты
     */
    public function updateStatusBasedOnDate()
    {
        if ($this->date < now() && $this->status === 'active') {
            $this->status = 'completed';
            $this->save();
            return true;
        }
        return false;
    }

    // ============= МЕТОДЫ ДЛЯ ПОДСЧЕТА БИЛЕТОВ =============

    /**
     * Количество проданных билетов
     */
    public function getSoldTicketsCount()
    {
        return $this->total_tickets - $this->available_tickets;
    }

    /**
     * Количество доступных билетов
     */
    public function getAvailableTicketsCount()
    {
        return $this->available_tickets;
    }

    /**
     * Проверка доступности билетов
     */
    public function hasAvailableTickets()
    {
        return $this->available_tickets > 0;
    }

    // ============= АКСЕССОРЫ =============

    /**
     * Количество проданных билетов (аксессор)
     */
    public function getSoldTicketsAttribute()
    {
        return $this->total_tickets - $this->available_tickets;
    }

    /**
     * Выручка с мероприятия (из оплаченных заказов)
     */
    public function getRevenueAttribute()
    {
        return $this->paidOrders()->sum('total_price');
    }

    /**
     * Процент заполненности
     */
    public function getFillPercentageAttribute()
    {
        if ($this->total_tickets === 0) return 0;
        return round(($this->getSoldTicketsCount() / $this->total_tickets) * 100, 2);
    }

    /**
     * Форматированная цена
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ') . ' ₽';
    }

    /**
     * Дата в формате дд.мм.гггг
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d.m.Y');
    }

    /**
     * Время мероприятия
     */
    public function getFormattedTimeAttribute()
    {
        return $this->date->format('H:i');
    }

    /**
     * Тип на русском
     */
    public function getTypeNameAttribute()
    {
        $types = [
            'concert' => 'Концерт',
            'festival' => 'Фестиваль',
            'other' => 'Другое',
        ];
        
        return $types[$this->type] ?? $this->type;
    }

    /**
     * Статус на русском
     */
    public function getStatusNameAttribute()
    {
        $statuses = [
            'active' => 'Активно',
            'cancelled' => 'Отменено',
            'completed' => 'Завершено',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Цвет статуса для CSS
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'active' => 'green',
            'cancelled' => 'red',
            'completed' => 'gray',
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Получить сообщение о статусе покупки
     */
    public function getBuyButtonMessageAttribute()
    {
        if ($this->status === 'cancelled') {
            return 'Мероприятие отменено';
        }
        
        if ($this->status === 'completed' || $this->date < now()) {
            return 'Мероприятие завершено';
        }
        
        if ($this->available_tickets === 0) {
            return 'Билеты закончились';
        }
        
        return 'Купить билет';
    }

    /**
     * Можно ли купить билеты (для шаблонов)
     */
    public function getCanBuyAttribute()
    {
        return $this->canBuyTickets();
    }

    // ============= SCOPES =============

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('date', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now())->orderBy('date', 'asc');
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', now())->orderBy('date', 'desc');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfOrganizer($query, $organizerId)
    {
        return $query->where('organizer_id', $organizerId);
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description', 'LIKE', "%{$searchTerm}%")
              ->orWhere('location', 'LIKE', "%{$searchTerm}%");
        });
    }

    public function scopeWithAvailableTickets($query)
    {
        return $query->where('available_tickets', '>', 0);
    }

    public function scopeAvailableForPurchase($query)
    {
        return $query->where('status', 'active')
                     ->where('date', '>', now())
                     ->where('available_tickets', '>', 0);
    }

    // ============= BOOT =============

    protected static function booted()
    {
        // При сохранении проверяем дату
        static::saving(function ($event) {
            if ($event->date < now() && $event->status === 'active') {
                $event->status = 'completed';
            }
        });

        // Ежечасно обновляем статусы (для консоли)
        static::retrieved(function ($event) {
            if ($event->date < now() && $event->status === 'active') {
                $event->status = 'completed';
                $event->saveQuietly(); // сохраняем без событий
            }
        });
    }


    /**
     * Проверка, скоро ли начнется мероприятие (в течение указанных часов)
     * 
     * @param int $hours Количество часов для проверки
     * @return bool
     */
    public function startsSoon(int $hours = 48): bool
    {
        // Если мероприятие уже прошло или отменено
        if ($this->isFinished() || $this->isCancelled()) {
            return false;
        }
        
        // Разница в часах между текущим временем и началом мероприятия
        $hoursUntilStart = now()->diffInHours($this->date, false);
        
        // Возвращаем true, если мероприятие начнется в ближайшие N часов
        // и еще не началось
        return $hoursUntilStart > 0 && $hoursUntilStart <= $hours;
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class)->orderBy('sort_order');
    }

    public function activeTicketTypes()
    {
        return $this->hasMany(TicketType::class)
            ->where('available', '>', 0)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favoritedBy()->count();
    }

    // В модели App\Models\Event
    public function getRussianDateAttribute()
    {
        // Массив названий месяцев на русском
        $months = [
            1 => 'января',
            2 => 'февраля',
            3 => 'марта',
            4 => 'апреля',
            5 => 'мая',
            6 => 'июня',
            7 => 'июля',
            8 => 'августа',
            9 => 'сентября',
            10 => 'октября',
            11 => 'ноября',
            12 => 'декабря'
        ];
        
        $day = $this->date->format('j'); // день без ведущего нуля
        $month = $months[$this->date->format('n')]; // номер месяца (1-12)
        
        return $day . ' ' . $month;
    }



    /**
     * Отмена мероприятия и всех связанных заказов
     */
    public function cancelEvent($reason = null)
    {
        // Меняем статус мероприятия
        $this->status = 'cancelled';
        $this->save();
        
        // Находим все активные заказы на это мероприятие
        $orders = $this->orders()
            ->whereIn('status', ['pending', 'paid', 'confirmed'])
            ->get();
        
        $cancelledCount = 0;
        
        foreach ($orders as $order) {
            // Отменяем заказ с причиной
            $order->cancel('Мероприятие отменено организатором' . ($reason ? ': ' . $reason : ''));
            $cancelledCount++;
            
            // TODO: Здесь можно добавить отправку email уведомления пользователю
            // Mail::to($order->user->email)->send(new EventCancelledMail($order, $reason));
        }
        
        return $cancelledCount;
    }
}