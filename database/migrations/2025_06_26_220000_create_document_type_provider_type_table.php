<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_type_provider_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['document_type_id', 'provider_type_id'], 'doc_type_provider_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_type_provider_type');
    }
};
