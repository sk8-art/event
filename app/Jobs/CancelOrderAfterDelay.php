<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class CancelOrderAfterDelay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        // Проверяем, не оплачен ли уже заказ
        if ($this->order->status === Order::STATUS_PENDING) {
            $this->order->cancel('Автоматическая отмена: истекло время оплаты (15 минут)');
        }
    }
}