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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();

            // The name of the document type (e.g., "Passport", "Driver's License", "Medical Certificate")
            // This field is unique to prevent duplicate document type names
            $table->string('name')->unique();

            // Optional detailed description of what this document type represents
            // Can include requirements, purpose, or any additional information about the document
            $table->text('description')->nullable();

            // JSON array containing allowed file extensions or MIME types for this document type
            // Example: ["pdf", "jpg", "png"] or ["application/pdf", "image/jpeg", "image/png"]
            // This helps enforce file type restrictions during document uploads
            $table->json('allowed_file_types');

            // Number of days this document type remains valid after issuance or upload
            // Used to calculate expiration dates and send renewal reminders
            // Set to 0 for documents that don't expire
            $table->integer('validity_days');

            // Indicates whether this document type is currently active and available for use
            // Inactive types won't appear in selection lists but existing documents remain accessible
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
