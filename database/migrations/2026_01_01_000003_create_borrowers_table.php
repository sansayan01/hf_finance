<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_officer_id')->nullable()->constrained('users');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('alternate_phone')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('national_id')->nullable();
            $table->string('national_id_type')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('pincode')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('occupation')->nullable();
            $table->string('employer_name')->nullable();
            $table->decimal('monthly_income', 15, 2)->default(0);
            $table->enum('employment_type', ['salaried', 'self_employed', 'business', 'unemployed'])->nullable();
            $table->enum('kyc_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->json('kyc_documents')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active');
            $table->timestamp('blacklisted_at')->nullable();
            $table->text('blacklist_reason')->nullable();
            $table->integer('credit_score')->nullable();
            $table->decimal('ai_risk_score', 5, 2)->nullable();
            $table->json('ai_risk_factors')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'phone']);
            $table->index(['organization_id', 'national_id']);
            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowers');
    }
};
