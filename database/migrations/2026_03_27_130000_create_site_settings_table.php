<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('site_tagline')->nullable();
            $table->string('home_kicker')->nullable();
            $table->string('home_headline');
            $table->text('home_description');
            $table->string('catalog_heading');
            $table->text('catalog_description');
            $table->string('benefit_one_title');
            $table->text('benefit_one_description');
            $table->string('benefit_two_title');
            $table->text('benefit_two_description');
            $table->string('benefit_three_title');
            $table->text('benefit_three_description');
            $table->string('announcement_text')->nullable();
            $table->timestamps();
        });

        SiteSetting::query()->create(SiteSetting::defaults());
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
