<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanProviderDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'provider:clean-documents {email? : Email of specific user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove document requirements from users who no longer have Provider role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            // Clean specific user
            $user = User::where('email', $email)->first();
            if (! $user) {
                $this->error("User with email {$email} not found.");

                return 1;
            }

            if ($user->hasRole('Provider')) {
                $this->error("User {$email} still has Provider role. Cannot clean documents.");

                return 1;
            }

            $this->cleanUserDocuments($user);
            $this->info("Documents cleaned for {$user->email}");
        } else {
            // Clean all users who have documents but no Provider role
            $usersWithDocuments = User::whereHas('documentRequirements')
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Provider');
                })
                ->get();

            foreach ($usersWithDocuments as $user) {
                $this->cleanUserDocuments($user);
                $this->info("Documents cleaned for {$user->email}");
            }

            $this->info("Processed {$usersWithDocuments->count()} users.");
        }

        return 0;
    }

    private function cleanUserDocuments(User $user): void
    {
        // Remove all document requirements
        $user->documentRequirements()->detach();

        // Delete provider profile if exists
        $user->providerProfile()?->delete();
    }
}
