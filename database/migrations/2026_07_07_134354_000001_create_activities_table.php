<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('subjectable_type');
            $table->unsignedBigInteger('subjectable_id');
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('due_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->datetime('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index(['subjectable_type', 'subjectable_id']);
            $table->index('assigned_to');
            $table->index('type');
            $table->index('is_completed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
