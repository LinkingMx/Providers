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
        Schema::create('status_transitions', function (Blueprint $table) {
            // Foreign key to the document_statuses table representing the source status
            // This is the status that a document is transitioning FROM
            $table->foreignId('from_status_id')
                ->constrained('document_statuses')
                ->onDelete('cascade');

            // Foreign key to the document_statuses table representing the target status
            // This is the status that a document is transitioning TO
            $table->foreignId('to_status_id')
                ->constrained('document_statuses')
                ->onDelete('cascade');

            // Set composite primary key using both foreign keys
            // This ensures that each transition pair (from -> to) is unique
            // and prevents duplicate transition definitions
            $table->primary(['from_status_id', 'to_status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_transitions');
    }
};
