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
        Schema::create('user_profile_revisions', function (Blueprint $table) {
            $table->id();

            //Аватарка и баннер
            $table->string('avatar')->nullable();
            $table->string('banner')->nullable();

            //About
            $table->text('about')->nullable();
            $table->string('i_live_in')->nullable();
            $table->string('aka')->nullable();

            //внешние сайты
            $table->string('discord')->nullable();
            $table->string('discord_if_bot')->nullable();
            $table->string('vk')->nullable();
            $table->string('telegram')->nullable();
            $table->string('github')->nullable();

            //Внешние ключи
            $table->unsignedBigInteger('wiki_id');
            $table->unsignedBigInteger('user_id');

            $table->boolean('is_approved');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile_revisions');
    }
};
