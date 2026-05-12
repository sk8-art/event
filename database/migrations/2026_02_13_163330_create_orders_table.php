<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Уникальный номер заказа
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->integer('quantity'); // Количество билетов
            $table->decimal('unit_price', 10, 2); // Цена за один билет
            $table->decimal('total_price', 10, 2); // Общая стоимость
            $table->enum('status', [
                'pending',      // Ожидает оплаты
                'paid',         // Оплачен
                'confirmed',    // Подтвержден
                'cancelled',    // Отменен пользователем
                'refunded',     // Возвращен
                'completed'     // Завершен (мероприятие прошло)
            ])->default('pending');
            $table->enum('payment_method', [
                'cash',
                'card',
                'online'
            ])->nullable();
            $table->string('payment_id')->nullable(); // ID транзакции
            $table->json('ticket_data')->nullable(); // Данные билетов (места, имена и т.д.)
            $table->text('notes')->nullable(); // Примечания к заказу
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index('order_number');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['event_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};