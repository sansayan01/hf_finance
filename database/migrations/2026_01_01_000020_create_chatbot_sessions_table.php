<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('borrower_id')->nullable()->constrained();
            $table->string('session_id')->unique();
            $table->string('phone');
            $table->enum('platform', ['whatsapp', 'sms', 'telegram', 'other']);
            $table->enum('status', ['active', 'closed', 'expired'])->default('active');
            $table->timestamp('started_at');
            $table->timestamp('last_activity_at');
            $table->timestamp('ended_at')->nullable();
            $table->json('context')->nullable();
            $table->integer('message_count')->default(0);
            $table->timestamps();
        });

        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('chatbot_sessions')->onDelete('cascade');
            $table->enum('direction', ['inbound', 'outbound']);
            $table->text('message');
            $table->enum('message_type', ['text', 'image', 'document', 'location', 'template']);
            $table->json('metadata')->nullable();
            $table->string('external_message_id')->nullable();
            $table->enum('status', ['sent', 'delivered', 'read', 'failed'])->default('sent');
            $table->timestamp('sent_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('chatbot_sessions');
    }
};
