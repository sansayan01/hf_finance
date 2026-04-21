<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->morphs('keyable');
            $table->string('name');
            $table->string('key', 64)->unique();
            $table->string('secret', 128);
            $table->json('permissions')->nullable();
            $table->json('allowed_ips')->nullable();
            $table->integer('rate_limit')->default(1000);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active', 'revoked', 'expired'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['key', 'status']);
        });

        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->nullable()->constrained();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('method', 10);
            $table->string('endpoint', 500);
            $table->json('request_body')->nullable();
            $table->integer('response_status');
            $table->json('response_body')->nullable();
            $table->integer('duration_ms');
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->timestamp('requested_at');
            $table->timestamps();

            $table->index(['organization_id', 'requested_at']);
            $table->index(['api_key_id', 'requested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('api_keys');
    }
};
