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
        Schema::create('document_statuses', function (Blueprint $table) {
            $table->id();

            // The name of the document status (e.g., "Draft", "Under Review", "Approved", "Rejected")
            // This field is unique to prevent duplicate status names
            $table->string('name')->unique();

            // Color code for UI display purposes (e.g., "#FF0000", "red", "danger")
            // Used to visually distinguish different statuses in the interface
            $table->string('color');

            // Optional icon identifier for the status (e.g., "check-circle", "clock", "x-circle")
            // Can be used with icon libraries like FontAwesome, Heroicons, etc.
            $table->string('icon')->nullable();

            // Indicates if this is the default status assigned to new documents
            // Only one status should typically have this set to true
            $table->boolean('is_default')->default(false);

            // Indicates if this status represents a successful completion state
            // Used to identify when a document has reached its final successful state
            $table->boolean('is_complete')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_statuses');
    }
};
