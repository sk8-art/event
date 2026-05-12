<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'event_id',
        'ticket_type_id',
        'quantity',
        'unit_price',
        'total_price',
        'status',
        'payment_method',
        'payment_id',
        'ticket_data',
        'notes',
        'paid_at',
        'cancelled_at',
        'ticket_type_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'ticket_data' => 'array',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];


    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }
    // Статусы заказа
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_COMPLETED = 'completed';

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    /**
     * Связь с мероприятием
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Генерация уникального номера заказа
     */
    public static function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        
        $orderNumber = $prefix . $date . $random;
        
        while (self::where('order_number', $orderNumber)->exists()) {
            $random = strtoupper(substr(uniqid(), -6));
            $orderNumber = $prefix . $date . $random;
        }
        
        return $orderNumber;
    }

    /**
     * Создание нового заказа
     */
    public static function createOrder($userId, $eventId, $ticketTypeId, $quantity, $unitPrice, $ticketData = [])
    {
        $totalPrice = $quantity * $unitPrice;
    
        $order = self::create([
            'order_number' => self::generateOrderNumber(),
            'user_id' => $userId,
            'event_id' => $eventId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'status' => self::STATUS_PENDING,
            'ticket_data' => $ticketData,
        ]);
        
        // Запускаем задачу на отмену через 15 минут
        \App\Jobs\CancelOrderAfterDelay::dispatch($order)
            ->delay(Carbon::now()->addMinutes(15));
        
        return $order;
    }

    // ============= МЕТОДЫ ДЛЯ ПРОВЕРКИ =============

    /**
     * Проверка, можно ли отменить заказ
     */
    public function canBeCancelled()
    {
        // Заказ можно отменить если:
        // 1. Статус позволяет (pending, paid, confirmed)
        // 2. Мероприятие еще не началось (или есть запас времени)
        // 3. Заказ не был уже отменен
        
        $cancellableStatuses = [
            self::STATUS_PENDING,
            self::STATUS_PAID,
            self::STATUS_CONFIRMED
        ];
        
        return in_array($this->status, $cancellableStatuses) 
            && $this->event 
            && $this->event->date > now()->addHours(24); // Можно отменить за 24 часа
    }

    /**
     * Проверка, можно ли вернуть деньги
     */
    public function canBeRefunded()
    {
        return in_array($this->status, [self::STATUS_PAID, self::STATUS_CONFIRMED]) 
            && $this->event 
            && $this->event->date > now()->addHours(48); // Возврат за 48 часов
    }

    // ============= МЕТОДЫ ДЛЯ ДЕЙСТВИЙ =============

    /**
     * Отмена заказа
     */
    public function cancel($reason = null)
    {
        if (!$this->canBeCancelled() && $this->status !== self::STATUS_PENDING) {
            return false;
        }
        
        $oldStatus = $this->status;
        
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'notes' => $reason ? ($this->notes . "\nОтмена: " . $reason) : $this->notes,
        ]);
        
        if ($this->event) {
            $this->event->increment('available_tickets', $this->quantity);
        }
        
        return true;
    }

    /**
     * Подтверждение оплаты
     */
    public function markAsPaid($paymentMethod, $paymentId = null)
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'payment_method' => $paymentMethod,
            'payment_id' => $paymentId,
            'paid_at' => now(),
        ]);
        
        return $this;
    }

    /**
     * Подтверждение заказа
     */
    public function confirm()
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
        ]);
        
        return $this;
    }

    /**
     * Завершение заказа
     */
    public function complete()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
        ]);
        
        return $this;
    }

    // ============= АКСЕССОРЫ =============

    /**
     * Получить статус на русском
     */
    public function getStatusNameAttribute()
    {
        $statuses = [
            self::STATUS_PENDING => 'Ожидает оплаты',
            self::STATUS_PAID => 'Оплачен',
            self::STATUS_CONFIRMED => 'Подтвержден',
            self::STATUS_CANCELLED => 'Отменен',
            self::STATUS_REFUNDED => 'Возвращен',
            self::STATUS_COMPLETED => 'Завершен',
        ];
        
        // Если заказ отменен из-за отмены мероприятия, показываем специальный статус
        if ($this->status === self::STATUS_CANCELLED && 
            str_contains($this->notes ?? '', 'Мероприятие отменено')) {
            return 'Мероприятие отменено';
        }
        
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Получить цвет статуса для CSS
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_PAID => 'info',
            self::STATUS_CONFIRMED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_REFUNDED => 'secondary',
            self::STATUS_COMPLETED => 'primary',
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Получить метод оплаты на русском
     */
    public function getPaymentMethodNameAttribute()
    {
        $methods = [
            'cash' => 'Наличные',
            'card' => 'Карта',
            'online' => 'Онлайн',
        ];
        
        return $methods[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Получить отформатированную общую стоимость
     */
    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 0, ',', ' ') . ' ₽';
    }

    /**
     * Получить отформатированную цену за билет
     */
    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 0, ',', ' ') . ' ₽';
    }

    // ============= SCOPES =============

    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->whereIn('status', [self::STATUS_PAID, self::STATUS_CONFIRMED]);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    // ============= BOOT =============

    protected static function booted()
    {
        static::creating(function ($order) {
            // Уменьшаем количество доступных билетов
            $event = Event::find($order->event_id);
            if ($event && $event->available_tickets >= $order->quantity) {
                $event->decrement('available_tickets', $order->quantity);
            } else {
                return false;
            }
        });
    }

    /**
     * Проверка, истек ли срок оплаты (15 минут)
     */
    public function isExpired()
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }
        
        return $this->created_at->diffInMinutes(now()) >= 15;
    }

    /**
     * Проверка, можно ли оплатить (с учетом времени)
     */
    public function canBePaid()
    {
        return $this->status === self::STATUS_PENDING 
            && $this->event 
            && $this->event->date > now()
            && !$this->isExpired(); // Нельзя оплатить если срок истек
    }
    
    
}