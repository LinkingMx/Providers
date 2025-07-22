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
        // Esta migración fue movida a migrate_user_branch_to_pivot.php
        // Dejamos esta vacía para evitar conflictos
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hacer nada
    }
};