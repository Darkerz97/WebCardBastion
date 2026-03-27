<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('sales', function (Blueprint $table): void {
            $table->timestamp('client_generated_at')->nullable()->after('sold_at')->index();
            $table->timestamp('received_at')->nullable()->after('client_generated_at')->index();
        });

        Schema::table('inventory_movements', function (Blueprint $table): void {
            $table->timestamp('client_generated_at')->nullable()->after('occurred_at')->index();
            $table->timestamp('received_at')->nullable()->after('client_generated_at')->index();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table): void {
            $table->dropColumn(['client_generated_at', 'received_at']);
        });

        Schema::table('sales', function (Blueprint $table): void {
            $table->dropColumn(['client_generated_at', 'received_at']);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });
    }
};
