<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('report_type', ['borrower', 'loan', 'payment', 'collection', 'portfolio', 'custom']);
            $table->json('filters');
            $table->json('columns');
            $table->json('sorting');
            $table->enum('format', ['pdf', 'excel', 'csv', 'json'])->default('pdf');
            $table->enum('frequency', ['on_demand', 'daily', 'weekly', 'monthly'])->default('on_demand');
            $table->string('schedule_day')->nullable();
            $table->string('schedule_time')->nullable();
            $table->json('email_recipients')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->boolean('is_scheduled')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('report_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('custom_reports');
            $table->foreignId('executed_by')->nullable()->constrained('users');
            $table->enum('status', ['queued', 'processing', 'completed', 'failed'])->default('queued');
            $table->json('parameters')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('record_count')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_executions');
        Schema::dropIfExists('custom_reports');
    }
};
