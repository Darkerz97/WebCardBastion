<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preorders', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('preorder_number')->unique();
            $table->string('status', 50)->default('pending')->index();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('amount_due', 10, 2)->default(0);
            $table->timestamp('expected_release_date')->nullable()->index();
            $table->text('notes')->nullable();
            $table->string('source', 50)->default('server')->index();
            $table->unsignedBigInteger('sync_version')->default(1);
            $table->timestamps();
        });

        Schema::create('preorder_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('preorder_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('product_uuid')->nullable()->index();
            $table->string('product_sku')->nullable()->index();
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });

        Schema::create('preorder_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('preorder_id')->constrained()->cascadeOnDelete();
            $table->string('method', 50);
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preorder_payments');
        Schema::dropIfExists('preorder_items');
        Schema::dropIfExists('preorders');
    }
};
