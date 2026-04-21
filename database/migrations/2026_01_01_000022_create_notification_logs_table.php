<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('borrower_id')->nullable()->constrained();
            $table->foreignId('loan_id')->nullable()->constrained();
            $table->string('notification_type');
            $table->enum('channel', ['email', 'sms', 'whatsapp', 'push']);
            $table->string('recipient');
            $table->text('subject')->nullable();
            $table->text('content');
            $table->json('template_data')->nullable();
            $table->string('template_id')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'bounced'])->default('pending');
            $table->json('provider_response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->string('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();

            $table->index(['organization_id', 'sent_at']);
            $table->index(['borrower_id', 'notification_type']);
            $table->index(['status', 'retry_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
