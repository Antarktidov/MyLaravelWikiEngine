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
            $table->string('avatar');
            $table->string('banner');
            $table->string('banner_bg_color');

            //About
            $table->text('about');
            $table->string('i_live_in');
            $table->string('aka');

            //внешние сайты
            $table->string('discord');
            $table->string('discord_if_bot');
            $table->string('vk');
            $table->string('telegram');
            $table->string('github');

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
