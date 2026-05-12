<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Сначала создадим базовые роли
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'display_name' => 'Администратор',
                'description' => 'Полный доступ ко всем функциям',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'organizer',
                'display_name' => 'Организатор',
                'description' => 'Может создавать и управлять своими мероприятиями',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'user',
                'display_name' => 'Пользователь',
                'description' => 'Может просматривать и регистрироваться на мероприятия',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->default(3)->constrained();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
        
        DB::table('roles')->truncate();
    }
};