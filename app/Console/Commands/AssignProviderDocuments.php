<?php

namespace App\Console\Commands;

use App\Models\DocumentStatus;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Console\Command;

class AssignProviderDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'provider:assign-documents {email? : Email of specific user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign document requirements to providers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            // Assign to specific user
            $user = User::where('email', $email)->first();
            if (! $user) {
                $this->error("User with email {$email} not found.");

                return 1;
            }

            if (! $user->hasRole('Provider')) {
                $this->error("User {$email} does not have Provider role.");

                return 1;
            }

            $this->assignDocumentsToProvider($user);
            $this->info("Documents assigned to {$user->email}");
        } else {
            // Assign to all providers without documents
            $providers = User::role('Provider')
                ->whereDoesntHave('documentRequirements')
                ->get();

            foreach ($providers as $provider) {
                $this->assignDocumentsToProvider($provider);
                $this->info("Documents assigned to {$provider->email}");
            }

            $this->info("Processed {$providers->count()} providers.");
        }

        return 0;
    }

    private function assignDocumentsToProvider(User $user): void
    {
        // Create provider profile if doesn't exist
        if (! $user->providerProfile) {
            $user->providerProfile()->create([
                'business_name' => $user->name,
            ]);
        }

        // Get default status and document types
        $defaultStatus = DocumentStatus::where('is_default', true)->firstOrFail();
        $documentTypes = DocumentType::where('is_active', true)->get();

        // Assign documents
        foreach ($documentTypes as $docType) {
            if (! $user->documentRequirements()->where('document_type_id', $docType->id)->exists()) {
                $user->documentRequirements()->attach($docType->id, [
                    'document_status_id' => $defaultStatus->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
