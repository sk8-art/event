<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug'); // vip, fan, standard
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->integer('available')->default(0);
            $table->json('benefits')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Составной уникальный ключ - событие + slug
            $table->unique(['event_id', 'slug']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_types');
    }
};