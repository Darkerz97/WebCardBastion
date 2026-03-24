<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('barcode')->constrained()->nullOnDelete();
            $table->string('slug')->nullable()->after('name');
            $table->string('short_description', 280)->nullable()->after('description');
            $table->boolean('featured')->default(false)->after('active')->index();
            $table->boolean('publish_to_store')->default(true)->after('featured')->index();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn(['slug', 'short_description', 'featured', 'publish_to_store']);
        });
    }
};
