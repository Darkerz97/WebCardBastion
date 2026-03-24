<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('uuid')->constrained()->nullOnDelete()->unique();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('order_channel')->default('pos')->after('sale_number')->index();
            $table->string('contact_name')->nullable()->after('order_channel');
            $table->string('contact_email')->nullable()->after('contact_name')->index();
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->text('notes')->nullable()->after('contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['order_channel', 'contact_name', 'contact_email', 'contact_phone', 'notes']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
