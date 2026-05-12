<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email'); // путь к фото
            $table->string('phone')->nullable()->after('avatar');
            $table->date('birth_date')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('birth_date');
            $table->string('city')->nullable()->after('bio');
            $table->string('telegram')->nullable()->after('city');
            $table->string('vk')->nullable()->after('telegram');
            $table->boolean('email_notifications')->default(true)->after('vk');
            $table->boolean('sms_notifications')->default(false)->after('email_notifications');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'phone',
                'birth_date',
                'bio',
                'city',
                'telegram',
                'vk',
                'email_notifications',
                'sms_notifications'
            ]);
        });
    }
};