<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id')->unique();
            $table->unsignedBigInteger('sync_version')->default(1)->after('updated_at')->index();
        });

        Schema::table('site_settings', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id')->unique();
            $table->unsignedBigInteger('sync_version')->default(1)->after('updated_at')->index();
        });

        collect(DB::table('categories')->select('id')->get())->each(function (object $row): void {
            DB::table('categories')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        });

        collect(DB::table('site_settings')->select('id')->get())->each(function (object $row): void {
            DB::table('site_settings')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
        });

    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn(['uuid', 'sync_version']);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn(['uuid', 'sync_version']);
        });
    }
};
