<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['concert', 'festival', 'other'])->default('other');
            $table->datetime('date');
            $table->string('location');
            $table->string('address');
            $table->decimal('price', 10, 2);
            $table->integer('total_tickets');
            $table->integer('available_tickets');
            $table->string('image');
            $table->foreignId('organizer_id')->constrained('users');
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};