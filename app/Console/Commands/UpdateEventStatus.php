<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class UpdateEventStatus extends Command
{
    protected $signature = 'events:update-status';
    protected $description = 'Update event status based on date';

    public function handle()
    {
        $updated = 0;
        
        // Находим все активные мероприятия, которые уже прошли
        $events = Event::where('status', 'active')
            ->where('date', '<', now())
            ->get();
        
        foreach ($events as $event) {
            $event->status = 'completed';
            $event->save();
            $updated++;
        }
        
        $this->info("Updated {$updated} events to completed status.");
    }
}