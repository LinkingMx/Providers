<?php

namespace App\Jobs;

use App\Models\DocumentStatus;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AssignProviderDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->user->fresh(); // Ensure the user model is fresh, especially for roles

        // Check if the user now has the 'Provider' role
        if (! $user->hasRole('Provider')) {
            Log::info('[AssignProviderDocumentsJob] User does not have Provider role, skipping document assignment.', ['user_id' => $user->id]);
            return;
        }

        // Check if documents are already assigned to avoid re-processing
        if ($user->documentRequirements()->count() > 0) {
            Log::info('[AssignProviderDocumentsJob] User already has documents assigned, skipping.', ['user_id' => $user->id]);
            return;
        }

        $defaultStatusId = DocumentStatus::where('is_default', true)->firstOrFail()->id;

        // Create provider profile if it doesn't exist
        if (! $user->providerProfile) {
            $user->providerProfile()->create([
                'business_name' => $user->name,
            ]);
        }

        // Refrescar el modelo para asegurar que providerProfile esté actualizado
        $user->refresh();

        // Obtener el tipo de proveedor del usuario
        $providerTypeId = $user->providerProfile->provider_type_id ?? null;
        Log::info('[AssignProviderDocumentsJob] provider_type_id para asignación:', ['user_id' => $user->id, 'provider_type_id' => $providerTypeId]);
        if (! $providerTypeId) {
            Log::info('[AssignProviderDocumentsJob] No se asignan documentos porque no hay provider_type_id.', ['user_id' => $user->id]);
            return;
        }

        // Obtener los tipos de documento activos que aplican a ese tipo de proveedor
        $documentTypes = DocumentType::where('is_active', true)
            ->whereHas('providerTypes', function ($query) use ($providerTypeId) {
                $query->where('provider_types.id', $providerTypeId);
            })
            ->pluck('id')
            ->toArray();
        Log::info('[AssignProviderDocumentsJob] DocumentTypes encontrados para asignar:', ['user_id' => $user->id, 'ids' => $documentTypes]);

        if (empty($documentTypes)) {
            Log::warning('[AssignProviderDocumentsJob] No se encontraron documentos activos para el tipo de proveedor', ['user_id' => $user->id, 'provider_type_id' => $providerTypeId]);
        }

        $pivotData = [];
        foreach ($documentTypes as $documentTypeId) {
            $pivotData[$documentTypeId] = [
                'document_status_id' => $defaultStatusId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $user->documentRequirements()->syncWithoutDetaching($pivotData);
        Log::info('[AssignProviderDocumentsJob] Documentos asignados al usuario:', ['user_id' => $user->id, 'pivotData' => $pivotData]);
    }
}