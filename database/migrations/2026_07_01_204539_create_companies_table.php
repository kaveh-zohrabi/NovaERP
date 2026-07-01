<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // ──────────────────────────────────────────────
            // Identity
            // ──────────────────────────────────────────────

            // Display name shown in UI (e.g., "Acme Corp")
            $table->string('name');

            // URL-friendly identifier (e.g., "acme-corp")
            $table->string('slug');

            // Legal name on official documents (e.g., "Acme Corporation LLC")
            $table->string('legal_name')->nullable();

            // ──────────────────────────────────────────────
            // Legal & Tax
            // ──────────────────────────────────────────────

            // Government registration number (e.g., "123456789")
            $table->string('registration_number')->nullable();

            // Tax identification number (e.g., "US-123456789")
            $table->string('tax_number')->nullable();

            // ──────────────────────────────────────────────
            // Contact
            // ──────────────────────────────────────────────

            // Primary contact email
            $table->string('email');

            // Contact phone number
            $table->string('phone')->nullable();

            // Company website
            $table->string('website')->nullable();

            // ──────────────────────────────────────────────
            // Address
            // ──────────────────────────────────────────────

            // Street address
            $table->text('address')->nullable();

            // City
            $table->string('city')->nullable();

            // State / Province
            $table->string('state')->nullable();

            // Country (ISO code, e.g., "US", "IR")
            $table->string('country')->nullable();

            // ZIP / Postal code
            $table->string('postal_code')->nullable();

            // ──────────────────────────────────────────────
            // Branding
            // ──────────────────────────────────────────────

            // Logo file path (stored in storage/app/public)
            $table->string('logo')->nullable();

            // ──────────────────────────────────────────────
            // Status
            // ──────────────────────────────────────────────

            // Active, inactive, or suspended
            $table->string('status')->default('active');

            // ──────────────────────────────────────────────
            // Settings
            // ──────────────────────────────────────────────

            // Company-specific settings (currency, timezone, etc.)
            $table->json('settings')->nullable();

            // ──────────────────────────────────────────────
            // Audit
            // ──────────────────────────────────────────────

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // ──────────────────────────────────────────────
            // Indexes
            // ──────────────────────────────────────────────

            $table->unique('slug');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
