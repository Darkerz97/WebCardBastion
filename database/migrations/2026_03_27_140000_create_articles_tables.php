<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('cover_image_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->boolean('allow_comments')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('article_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->text('body');
            $table->boolean('is_approved')->default(true);
            $table->timestamp('approved_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_comments');
        Schema::dropIfExists('articles');
    }
};
