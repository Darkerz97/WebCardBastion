<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'min_stock')) {
                $table->unsignedInteger('min_stock')->default(0)->after('stock');
            }

            if (! Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type')->default('normal')->after('active');
            }

            if (! Schema::hasColumn('products', 'game')) {
                $table->string('game')->nullable()->after('product_type');
            }

            if (! Schema::hasColumn('products', 'card_name')) {
                $table->string('card_name')->nullable()->after('game');
            }

            if (! Schema::hasColumn('products', 'set_name')) {
                $table->string('set_name')->nullable()->after('card_name');
            }

            if (! Schema::hasColumn('products', 'set_code')) {
                $table->string('set_code')->nullable()->after('set_name');
            }

            if (! Schema::hasColumn('products', 'collector_number')) {
                $table->string('collector_number')->nullable()->after('set_code');
            }

            if (! Schema::hasColumn('products', 'finish')) {
                $table->string('finish')->nullable()->after('collector_number');
            }

            if (! Schema::hasColumn('products', 'language')) {
                $table->string('language')->nullable()->after('finish');
            }

            if (! Schema::hasColumn('products', 'card_condition')) {
                $table->string('card_condition')->nullable()->after('language');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = collect([
                'min_stock',
                'product_type',
                'game',
                'card_name',
                'set_name',
                'set_code',
                'collector_number',
                'finish',
                'language',
                'card_condition',
            ])->filter(fn (string $column) => Schema::hasColumn('products', $column))->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
