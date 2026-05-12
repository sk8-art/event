<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
         $schedule->command('orders:cancel-expired')
                 ->everyMinute()
                 ->withoutOverlapping() // Чтобы не запускать одновременно несколько экземпляров
                 ->appendOutputTo(storage_path('logs/orders-cancel.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected $middleware = [
        \App\Http\Middleware\CheckExpiredOrders::class,
    ];
}
