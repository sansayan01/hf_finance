<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('borrower_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('collector_id')->constrained('users');
            $table->date('visit_date');
            $table->dateTime('visited_at');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->string('address_verified')->nullable();
            $table->text('borrower_status_notes')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_number')->nullable();
            $table->enum('borrower_availability', ['available', 'not_available', 'moved', 'refused']);
            $table->decimal('amount_collected', 15, 2)->default(0);
            $table->enum('payment_method', ['cash', 'online', 'promise_to_pay'])->nullable();
            $table->date('promised_payment_date')->nullable();
            $table->decimal('promised_amount', 15, 2)->nullable();
            $table->text('collection_notes')->nullable();
            $table->json('photos')->nullable();
            $table->enum('next_action', ['follow_up', 'legal_notice', 'skip_tracing', 'none'])->default('follow_up');
            $table->date('next_visit_date')->nullable();
            $table->decimal('ai_priority_score', 5, 2)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'visit_date']);
            $table->index(['loan_id', 'status']);
            $table->index(['collector_id', 'visit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_visits');
    }
};
