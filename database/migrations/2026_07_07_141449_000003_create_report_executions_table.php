<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_definition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('executed_by')->constrained('users')->cascadeOnDelete();
            $table->json('filters')->nullable();
            $table->string('status')->default('pending');
            $table->text('generated_file')->nullable();
            $table->timestamps();

            $table->index('report_definition_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_executions');
    }
};
