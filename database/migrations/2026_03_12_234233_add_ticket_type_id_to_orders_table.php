<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Добавляем поле ticket_type_id после event_id
            $table->foreignId('ticket_type_id')
                  ->nullable()
                  ->after('event_id')
                  ->constrained()
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Удаляем внешний ключ и само поле
            $table->dropForeign(['ticket_type_id']);
            $table->dropColumn('ticket_type_id');
        });
    }
};