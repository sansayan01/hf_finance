<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blacklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained();
            $table->enum('scope', ['organization', 'global'])->default('organization');
            $table->morphs('blacklistable');
            $table->string('identifier_type');
            $table->string('identifier_value');
            $table->string('name');
            $table->string('reason');
            $table->text('details')->nullable();
            $table->foreignId('blacklisted_by')->constrained('users');
            $table->foreignId('removed_by')->nullable()->constrained('users');
            $table->timestamp('removed_at')->nullable();
            $table->text('removal_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['identifier_type', 'identifier_value']);
            $table->index(['organization_id', 'scope']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blacklists');
    }
};
