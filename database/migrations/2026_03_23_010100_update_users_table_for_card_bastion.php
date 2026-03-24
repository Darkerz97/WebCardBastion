<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->foreignId('role_id')->nullable()->after('password')->constrained()->nullOnDelete();
            $table->boolean('active')->default(true)->after('role_id');
            $table->timestamp('last_login_at')->nullable()->after('active');
            $table->softDeletes();
            $table->index(['active', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropIndex(['active', 'role_id']);
            $table->dropSoftDeletes();
            $table->dropColumn(['uuid', 'phone', 'active', 'last_login_at']);
        });
    }
};
