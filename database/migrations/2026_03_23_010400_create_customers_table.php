<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('phone')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->text('notes')->nullable();
            $table->decimal('credit_balance', 12, 2)->default(0);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['name', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
