<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('format')->default('swiss');
            $table->string('status')->default('draft')->index();
            $table->decimal('entry_fee', 12, 2)->default(0);
            $table->unsignedInteger('max_players')->nullable();
            $table->unsignedInteger('rounds_count')->default(3);
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('registration_closes_at')->nullable();
            $table->boolean('published')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('tournament_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('registered')->index();
            $table->unsignedInteger('points')->default(0);
            $table->unsignedInteger('wins')->default(0);
            $table->unsignedInteger('draws')->default(0);
            $table->unsignedInteger('losses')->default(0);
            $table->unsignedInteger('bye_rounds')->default(0);
            $table->decimal('opponent_win_rate', 5, 2)->default(0);
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
            $table->unique(['tournament_id', 'user_id']);
        });

        Schema::create('tournament_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('round_number');
            $table->string('status')->default('pending')->index();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['tournament_id', 'round_number']);
        });

        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_round_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_one_registration_id')->constrained('tournament_registrations')->cascadeOnDelete();
            $table->foreignId('player_two_registration_id')->nullable()->constrained('tournament_registrations')->nullOnDelete();
            $table->unsignedTinyInteger('table_number')->nullable();
            $table->unsignedTinyInteger('player_one_score')->default(0);
            $table->unsignedTinyInteger('player_two_score')->default(0);
            $table->foreignId('winner_registration_id')->nullable()->constrained('tournament_registrations')->nullOnDelete();
            $table->boolean('is_draw')->default(false);
            $table->boolean('is_bye')->default(false);
            $table->string('status')->default('pending')->index();
            $table->timestamp('reported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_matches');
        Schema::dropIfExists('tournament_rounds');
        Schema::dropIfExists('tournament_registrations');
        Schema::dropIfExists('tournaments');
    }
};
