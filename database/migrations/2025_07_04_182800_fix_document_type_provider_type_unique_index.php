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
        Schema::table('document_type_provider_type', function (Blueprint $table) {
            // Drop the existing unique constraint if it exists (for rollback scenarios)
            try {
                $table->dropUnique(['document_type_id', 'provider_type_id']);
            } catch (Exception $e) {
                // Ignore if the constraint doesn't exist
            }

            // Add the unique constraint with a shorter, custom name
            $table->unique(['document_type_id', 'provider_type_id'], 'doc_type_provider_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_type_provider_type', function (Blueprint $table) {
            // Drop the custom named unique constraint
            $table->dropUnique('doc_type_provider_type_unique');

            // Restore the original (if needed, but this would have the same long name issue)
            // $table->unique(['document_type_id', 'provider_type_id']);
        });
    }
};
