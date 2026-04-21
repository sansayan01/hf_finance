<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_restructurings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->enum('restructure_type', ['tenure_extension', 'rate_reduction', 'emi_reduction', 'balloon_payment', 'combined']);
            $table->text('reason');
            $table->integer('old_tenure_months');
            $table->integer('new_tenure_months');
            $table->decimal('old_interest_rate', 8, 4);
            $table->decimal('new_interest_rate', 8, 4);
            $table->decimal('old_emi_amount', 15, 2);
            $table->decimal('new_emi_amount', 15, 2);
            $table->decimal('outstanding_principal', 15, 2);
            $table->decimal('outstanding_interest', 15, 2);
            $table->date('moratorium_start_date')->nullable();
            $table->date('moratorium_end_date')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_restructurings');
    }
};
