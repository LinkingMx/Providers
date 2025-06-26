<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();

            // Foreign key to the users table - establishes one-to-one relationship
            // When a user is deleted, their provider profile will also be deleted automatically
            // This maintains referential integrity and prevents orphaned records
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // RFC (Registro Federal de Contribuyentes) - Mexican tax identification number
            // This field is unique to ensure each RFC can only be registered once in the system
            // Required for all providers doing business in Mexico
            $table->string('rfc')->unique();

            // Optional business name or company name for the provider
            // Can be different from the user's personal name when operating as a business entity
            // Nullable to allow individual providers who operate under their personal name
            $table->string('business_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};
