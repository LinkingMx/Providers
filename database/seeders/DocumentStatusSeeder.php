<?php

namespace Database\Seeders;

use App\Models\DocumentStatus;
use Illuminate\Database\Seeder;

class DocumentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the document statuses with their respective properties
        // Each status represents a different stage in the document lifecycle

        // Default status for new documents - indicates document is awaiting review
        DocumentStatus::create([
            'name' => 'Pendiente',
            'color' => 'gray',
            'icon' => 'heroicon-o-clock',
            'is_default' => true,
            'is_complete' => false,
        ]);

        // Status indicating document is currently being reviewed by administrators
        DocumentStatus::create([
            'name' => 'En Revisión',
            'color' => 'warning',
            'icon' => 'heroicon-o-document-magnifying-glass',
            'is_default' => false,
            'is_complete' => false,
        ]);

        // Final successful status indicating document has been approved
        DocumentStatus::create([
            'name' => 'Aprobado',
            'color' => 'success',
            'icon' => 'heroicon-o-check-circle',
            'is_default' => false,
            'is_complete' => true,
        ]);

        // Status indicating document has been rejected and needs correction
        DocumentStatus::create([
            'name' => 'Rechazado',
            'color' => 'danger',
            'icon' => 'heroicon-o-x-circle',
            'is_default' => false,
            'is_complete' => false,
        ]);

        // Status for documents that are not applicable or no longer needed
        DocumentStatus::create([
            'name' => 'No Aplica',
            'color' => 'info',
            'icon' => 'heroicon-o-minus-circle',
            'is_default' => false,
            'is_complete' => false,
        ]);

        // Retrieve the created status objects from the database
        // We need these objects to set up the transition relationships
        $pendiente = DocumentStatus::where('name', 'Pendiente')->first();
        $enRevision = DocumentStatus::where('name', 'En Revisión')->first();
        $aprobado = DocumentStatus::where('name', 'Aprobado')->first();
        $rechazado = DocumentStatus::where('name', 'Rechazado')->first();
        $noAplica = DocumentStatus::where('name', 'No Aplica')->first();

        // Define the allowed status transitions using the many-to-many relationship
        // This creates a workflow where documents must follow specific paths

        // From 'Pendiente' (initial state), documents can only move to review
        $pendiente->next_statuses()->sync([$enRevision->id]);

        // From 'En Revisión', documents can be approved or rejected
        $enRevision->next_statuses()->sync([$aprobado->id, $rechazado->id]);

        // From 'Aprobado', documents can go back to review (e.g., for renewal or updates)
        $aprobado->next_statuses()->sync([$enRevision->id]);

        // From 'Rechazado', documents can be sent back for review after corrections
        $rechazado->next_statuses()->sync([$enRevision->id]);

        // Note: 'No Aplica' has no outgoing transitions as it's typically a final state
        // for documents that are no longer relevant or applicable
    }
}
