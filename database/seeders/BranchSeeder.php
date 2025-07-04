<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Sucursal Centro',
                'description' => 'Oficina principal ubicada en el centro de la ciudad',
                'address' => 'Av. Principal #123, Col. Centro',
                'phone' => '+52 55 1234 5678',
                'email' => 'centro@empresa.com',
                'is_active' => true,
            ],
            [
                'name' => 'Sucursal Norte',
                'description' => 'Sucursal de atención al norte de la ciudad',
                'address' => 'Blvd. Norte #456, Col. Residencial Norte',
                'phone' => '+52 55 2345 6789',
                'email' => 'norte@empresa.com',
                'is_active' => true,
            ],
            [
                'name' => 'Sucursal Sur',
                'description' => 'Punto de servicio en la zona sur',
                'address' => 'Av. Sur #789, Col. Industrial Sur',
                'phone' => '+52 55 3456 7890',
                'email' => 'sur@empresa.com',
                'is_active' => true,
            ],
            [
                'name' => 'Sucursal Poniente',
                'description' => 'Oficina de servicios en zona poniente',
                'address' => 'Calle Poniente #321, Col. Moderna',
                'phone' => '+52 55 4567 8901',
                'email' => 'poniente@empresa.com',
                'is_active' => false, // Ejemplo de sucursal inactiva
            ],
        ];

        foreach ($branches as $branchData) {
            Branch::create($branchData);
        }
    }
}
