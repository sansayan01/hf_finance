<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guarantors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('borrower_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('relationship');
            $table->string('national_id');
            $table->text('address');
            $table->decimal('monthly_income', 15, 2)->nullable();
            $table->string('employer_name')->nullable();
            $table->string('photo')->nullable();
            $table->json('documents')->nullable();
            $table->enum('status', ['active', 'released'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guarantors');
    }
};
