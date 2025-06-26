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
        Schema::create('provider_documents', function (Blueprint $table) {
            $table->id();

            // Foreign key to the users table - identifies which user owns this document
            // Cascade delete ensures that when a user is removed, their documents are also removed
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Foreign key to the document_types table - defines what type of document this is
            // Restrict delete to prevent orphaning documents when document types are modified
            $table->foreignId('document_type_id')
                ->constrained('document_types')
                ->onDelete('restrict');

            // Foreign key to the document_statuses table - tracks the current status of the document
            // Uses our dynamic status system (Pendiente, En Revisión, Aprobado, Rechazado, etc.)
            // Restrict delete to maintain status integrity and audit trail
            $table->foreignId('document_status_id')
                ->constrained('document_statuses')
                ->onDelete('restrict');

            // Path to the uploaded file in storage (relative to storage disk)
            // Nullable for cases where document entry exists but file hasn't been uploaded yet
            $table->string('file_path')->nullable();

            // Timestamp when the document file was actually uploaded
            // Separate from created_at to track the file upload event specifically
            $table->dateTime('uploaded_at')->nullable();

            // Date when this document expires (calculated from document_type validity_days)
            // Nullable for document types that don't have expiration (validity_days = 0)
            $table->date('expires_at')->nullable();

            // Detailed reason for rejection when document status is 'Rechazado'
            // Helps providers understand what needs to be corrected for resubmission
            $table->text('rejection_reason')->nullable();

            // Composite unique constraint to ensure each user can only have one document per type
            // This prevents duplicate submissions and maintains data integrity
            $table->unique(['user_id', 'document_type_id'], 'unique_user_document_type');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_documents');
    }
};
