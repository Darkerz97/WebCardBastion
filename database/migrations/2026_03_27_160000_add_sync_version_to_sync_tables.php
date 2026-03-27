<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->unsignedBigInteger('sync_version')->default(1)->after('updated_at')->index();
        });

        Schema::table('customers', function (Blueprint $table): void {
            $table->unsignedBigInteger('sync_version')->default(1)->after('updated_at')->index();
        });

        Schema::table('sales', function (Blueprint $table): void {
            $table->unsignedBigInteger('sync_version')->default(1)->after('updated_at')->index();
        });

        Schema::table('devices', function (Blueprint $table): void {
            $table->unsignedBigInteger('sync_version')->default(1)->after('updated_at')->index();
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table): void {
            $table->dropColumn('sync_version');
        });

        Schema::table('sales', function (Blueprint $table): void {
            $table->dropColumn('sync_version');
        });

        Schema::table('customers', function (Blueprint $table): void {
            $table->dropColumn('sync_version');
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn('sync_version');
        });
    }
};
