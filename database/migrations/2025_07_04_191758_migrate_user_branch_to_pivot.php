<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, migrar datos existentes a la tabla pivot
        $users = DB::table('users')->whereNotNull('branch_id')->get();

        foreach ($users as $user) {
            DB::table('branch_user')->insert([
                'branch_id' => $user->branch_id,
                'user_id' => $user->id,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Luego, eliminar la columna branch_id de users
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar la columna branch_id
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
        });

        // Migrar datos de vuelta (solo la sucursal principal)
        $branchUsers = DB::table('branch_user')->where('is_primary', true)->get();

        foreach ($branchUsers as $branchUser) {
            DB::table('users')->where('id', $branchUser->user_id)->update([
                'branch_id' => $branchUser->branch_id,
            ]);
        }
    }
};
