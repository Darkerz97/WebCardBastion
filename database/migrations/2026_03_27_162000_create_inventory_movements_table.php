<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('movement_type')->index();
            $table->string('direction')->index();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('stock_before');
            $table->unsignedInteger('stock_after');
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->string('reference')->nullable()->index();
            $table->text('notes')->nullable();
            $table->string('source')->index();
            $table->timestamp('occurred_at')->index();
            $table->unsignedBigInteger('sync_version')->default(1)->index();
            $table->timestamps();
            $table->index(['product_id', 'occurred_at']);
            $table->index(['sale_id', 'movement_type']);
            $table->index(['device_id', 'source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
