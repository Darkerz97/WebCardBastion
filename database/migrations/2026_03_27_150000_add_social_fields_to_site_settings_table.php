<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->string('social_heading')->nullable()->after('announcement_text');
            $table->text('social_description')->nullable()->after('social_heading');
            $table->string('facebook_url')->nullable()->after('social_description');
            $table->text('facebook_embed')->nullable()->after('facebook_url');
            $table->string('instagram_url')->nullable()->after('facebook_embed');
            $table->text('instagram_embed')->nullable()->after('instagram_url');
            $table->string('tiktok_url')->nullable()->after('instagram_embed');
            $table->text('tiktok_embed')->nullable()->after('tiktok_url');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'social_heading',
                'social_description',
                'facebook_url',
                'facebook_embed',
                'instagram_url',
                'instagram_embed',
                'tiktok_url',
                'tiktok_embed',
            ]);
        });
    }
};
