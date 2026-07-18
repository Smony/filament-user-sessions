<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('filament-user-sessions.known_devices_table', 'session_known_devices'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('fingerprint');
            $table->timestamp('first_seen_at');
            $table->timestamp('last_seen_at');
            $table->unique(['user_id', 'fingerprint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('filament-user-sessions.known_devices_table', 'session_known_devices'));
    }
};
