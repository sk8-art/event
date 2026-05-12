<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отмена неоплаченных заказов через 15 минут';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Поиск просроченных заказов...');
        
        // Находим все заказы со статусом pending, созданные более 15 минут назад
        $expiredOrders = Order::where('status', Order::STATUS_PENDING)
            ->where('created_at', '<=', Carbon::now()->subMinutes(15))
            ->get();
        
        $count = 0;
        
        foreach ($expiredOrders as $order) {
            // Отменяем заказ
            $order->cancel('Автоматическая отмена: истекло время оплаты (15 минут)');
            
            // Возвращаем билеты (это уже делает метод cancel)
            // $order->event->increment('available_tickets', $order->quantity);
            
            $count++;
            
            $this->line("Заказ #{$order->order_number} отменен");
        }
        
        $this->info("Отменено {$count} просроченных заказов.");
        
        return Command::SUCCESS;
    }
}