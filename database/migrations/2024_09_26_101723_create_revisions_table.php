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
        Schema::create('revisions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('article_id');

            $table->string('title');
            $table->string('url_title');
            $table->text('content');

            $table->boolean('is_approved')->default(0);
            $table->boolean('is_patrolled')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->string('user_ip');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revisions');
    }
};
