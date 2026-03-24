<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained()->nullOnDelete();
            $table->string('entity_type')->index();
            $table->uuid('entity_uuid')->nullable()->index();
            $table->string('action')->index();
            $table->string('status')->index();
            $table->json('payload_json')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('synced_at')->nullable()->index();
            $table->timestamps();
            $table->index(['device_id', 'entity_type', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
