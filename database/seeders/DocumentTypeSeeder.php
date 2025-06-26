<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

/**
 * Document Type Seeder
 *
 * Seeds the database with initial document types required for provider
 * onboarding and compliance management. Creates standard document types
 * with appropriate validation rules and expiration periods.
 */
class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates the standard document types used in the provider
     * management system with appropriate file type restrictions
     * and validity periods.
     */
    public function run(): void
    {
        $documentTypes = [
            [
                'name' => 'Cédula de Identidad',
                'description' => 'Documento oficial de identificación personal emitido por el gobierno.',
                'allowed_file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'validity_days' => 0, // No expira
                'is_active' => true,
            ],
            [
                'name' => 'Certificado de Antecedentes Penales',
                'description' => 'Certificado que acredita la ausencia de antecedentes penales.',
                'allowed_file_types' => ['pdf'],
                'validity_days' => 180, // 6 meses
                'is_active' => true,
            ],
            [
                'name' => 'Licencia Profesional',
                'description' => 'Licencia o certificación profesional requerida para ejercer la actividad.',
                'allowed_file_types' => ['pdf', 'jpg', 'jpeg'],
                'validity_days' => 365, // 1 año
                'is_active' => true,
            ],
            [
                'name' => 'Comprobante de Domicilio',
                'description' => 'Documento que acredita el domicilio actual del proveedor.',
                'allowed_file_types' => ['pdf', 'jpg', 'jpeg', 'png'],
                'validity_days' => 90, // 3 meses
                'is_active' => true,
            ],
        ];

        foreach ($documentTypes as $documentType) {
            DocumentType::firstOrCreate(
                ['name' => $documentType['name']],
                $documentType
            );
        }

        $this->command->info('Document types seeded successfully.');
    }
}
