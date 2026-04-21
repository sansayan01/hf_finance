<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_id')->nullable()->constrained();
            $table->foreignId('borrower_id')->nullable()->constrained();
            $table->string('alert_type');
            $table->string('severity');
            $table->text('description');
            $table->json('detected_patterns');
            $table->decimal('fraud_score', 5, 2);
            $table->json('related_data')->nullable();
            $table->enum('status', ['new', 'investigating', 'confirmed', 'false_positive', 'resolved'])->default('new');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['borrower_id', 'alert_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_alerts');
    }
};
