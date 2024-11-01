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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('wiki_id');

            $table->string('title');
            $table->string('url_title');

            $table->boolean('is_comments_enabled')->default(1);
            $table->boolean('is_comments_super_enabled')->default(0);

            $table->boolean('is_article_visible')->default(1);
            $table->boolean('is_article_super_visible')->default(1);

            $table->string('article_protection_level')->default('none');
            $table->string('comments_protection_level')->default('none');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
