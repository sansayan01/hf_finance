<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emandates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('borrower_id')->constrained()->onDelete('cascade');
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->string('mandate_id')->unique();
            $table->string('reference_number');
            $table->string('account_holder_name');
            $table->string('account_number');
            $table->string('ifsc_code');
            $table->string('bank_name');
            $table->string('account_type');
            $table->string('phone');
            $table->string('email');
            $table->decimal('max_amount', 15, 2);
            $table->enum('frequency', ['asandwhen', 'monthly', 'weekly', 'daily']);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'active', 'suspended', 'cancelled', 'expired'])->default('pending');
            $table->string('umrn')->nullable();
            $table->json('npci_response')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['borrower_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emandates');
    }
};
