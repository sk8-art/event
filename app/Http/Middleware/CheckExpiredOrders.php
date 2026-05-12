<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Order;
use Carbon\Carbon;

class CheckExpiredOrders
{
    public function handle($request, Closure $next)
    {
        // Проверяем раз в 10 запросов (чтобы не нагружать)
        if (rand(1, 10) === 1) {
            $this->cancelExpiredOrders();
        }
        
        return $next($request);
    }
    
    private function cancelExpiredOrders()
    {
        $expiredOrders = Order::where('status', Order::STATUS_PENDING)
            ->where('created_at', '<=', Carbon::now()->subMinutes(15))
            ->get();
        
        foreach ($expiredOrders as $order) {
            $order->cancel('Автоматическая отмена: истекло время оплаты (15 минут)');
        }
    }
}