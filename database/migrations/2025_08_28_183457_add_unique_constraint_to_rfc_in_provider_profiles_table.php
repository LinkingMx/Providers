<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero verificar si ya existe la restricción unique
        $uniqueIndexExists = false;
        
        try {
            // Para SQLite verificar si existe el índice
            if (config('database.default') === 'sqlite') {
                $indices = DB::select("PRAGMA index_list(provider_profiles)");
                foreach ($indices as $index) {
                    if ($index->name === 'provider_profiles_rfc_unique' && $index->unique == 1) {
                        $uniqueIndexExists = true;
                        break;
                    }
                }
            } else {
                // Para MySQL/PostgreSQL verificar constraints
                $constraints = DB::select("SHOW INDEX FROM provider_profiles WHERE Key_name = 'provider_profiles_rfc_unique'");
                $uniqueIndexExists = !empty($constraints);
            }
        } catch (Exception $e) {
            // Si hay error verificando, asumir que no existe
            $uniqueIndexExists = false;
        }

        // Solo proceder si no existe la restricción unique
        if (!$uniqueIndexExists) {
            // Paso 1: Identificar y resolver RFC duplicados
            $duplicatedRfcs = DB::table('provider_profiles')
                ->select('rfc')
                ->whereNotNull('rfc')
                ->where('rfc', '!=', '')
                ->groupBy('rfc')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('rfc');

            if ($duplicatedRfcs->isNotEmpty()) {
                echo "Encontrados " . $duplicatedRfcs->count() . " RFC duplicados. Resolviendo...\n";
                
                foreach ($duplicatedRfcs as $rfc) {
                    // Obtener todos los registros con este RFC
                    $profiles = DB::table('provider_profiles')
                        ->where('rfc', $rfc)
                        ->orderBy('created_at', 'asc')
                        ->get();
                    
                    // Mantener el primer registro, modificar los demás
                    $first = true;
                    foreach ($profiles as $profile) {
                        if ($first) {
                            $first = false;
                            continue; // Mantener el primer registro sin cambios
                        }
                        
                        // Generar un RFC temporal único para los duplicados
                        $newRfc = $rfc . '_DUP_' . $profile->id . '_' . time();
                        
                        DB::table('provider_profiles')
                            ->where('id', $profile->id)
                            ->update(['rfc' => $newRfc]);
                        
                        echo "RFC duplicado {$rfc} para perfil ID {$profile->id} cambiado a: {$newRfc}\n";
                    }
                }
            }

            // Paso 2: Agregar la restricción unique de forma segura
            Schema::table('provider_profiles', function (Blueprint $table) {
                $table->unique('rfc', 'provider_profiles_rfc_unique');
            });

            echo "✅ Restricción unique agregada exitosamente al campo RFC.\n";
        } else {
            echo "ℹ️  La restricción unique para RFC ya existe. No se requiere acción.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la restricción unique si existe
        try {
            Schema::table('provider_profiles', function (Blueprint $table) {
                $table->dropUnique('provider_profiles_rfc_unique');
            });
            echo "✅ Restricción unique del RFC eliminada.\n";
        } catch (Exception $e) {
            echo "⚠️  No se pudo eliminar la restricción unique: " . $e->getMessage() . "\n";
        }
    }
};
