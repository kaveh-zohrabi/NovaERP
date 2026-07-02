<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Marks this as the user's default company.
            // Used for single-company mode and as default in multi-company.
            $table->boolean('is_default')->default(false);

            $table->timestamps();

            // One user can only belong to a company once
            $table->unique(['company_id', 'user_id']);

            // Fast lookup by user
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_user');
    }
};
