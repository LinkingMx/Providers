<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

class TestFilamentPasswordReset extends Command
{
    protected $signature = 'test:filament-password-reset {email}';
    protected $description = 'Test Filament password reset with custom notification';

    public function handle()
    {
        $email = $this->argument('email');
        
        // Find user by email
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $this->info("Testing Filament password reset for: {$email}");
        
        // Test 1: Direct Filament notification creation (simulating what Filament does internally)
        $this->info("Test 1: Creating Filament notification instance...");
        $filamentNotification = app(\Filament\Notifications\Auth\ResetPassword::class, ['token' => 'test-token-123']);
        $filamentNotification->url = route('filament.admin.auth.password-reset.reset', [
            'token' => 'test-token-123',
            'email' => $user->email
        ]);
        
        $this->info("Notification class resolved to: " . get_class($filamentNotification));
        
        // Send the notification
        $user->notify($filamentNotification);
        $this->info("Notification queued successfully!");
        
        // Test 2: Laravel's Password facade (what happens when someone uses the form)
        $this->info("\nTest 2: Using Laravel Password facade...");
        $status = Password::sendResetLink(['email' => $email]);
        $this->info("Password reset status: {$status}");
        
        $this->info("\nBoth tests completed. Check the queue with: php artisan queue:work");
        
        return 0;
    }
}
