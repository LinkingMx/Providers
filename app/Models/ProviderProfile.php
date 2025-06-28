<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ProviderProfile Model
 *
 * Represents the business profile information for providers in the system.
 * This model extends user accounts with provider-specific data including
 * tax identification and business information required for service provision.
 *
 * @property int $id Primary key
 * @property int $user_id Foreign key to the users table
 * @property string $rfc Mexican tax identification number (RFC)
 * @property string|null $business_name Optional business or company name
 * @property \Illuminate\Support\Carbon $created_at Creation timestamp
 * @property \Illuminate\Support\Carbon $updated_at Last update timestamp
 * @property-read \App\Models\User $user The associated user account
 */
class ProviderProfile extends Model
{
    /** @use HasFactory<\Database\Factories\ProviderProfileFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * Includes the user relationship and provider-specific information
     * required for creating and updating provider profiles.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'rfc',
        'business_name',
        'provider_type_id', // Add this line
    ];

    /**
     * Get the user that owns this provider profile.
     *
     * This defines the inverse of the one-to-one relationship between
     * providers and users. Each provider profile belongs to exactly one user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the provider type for this profile.
     */
    public function providerType(): BelongsTo
    {
        return $this->belongsTo(ProviderType::class);
    }

    /**
     * Get the display name for this provider.
     *
     * Returns the business name if available, otherwise falls back to the user's name.
     * This provides a consistent way to display the provider's identity.
     *
     * @return string The provider's display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->business_name ?? $this->user->name;
    }

    /**
     * Format the RFC for display purposes.
     *
     * Converts the RFC to uppercase for consistent presentation.
     *
     * @return string The formatted RFC
     */
    public function getFormattedRfcAttribute(): string
    {
        return strtoupper($this->rfc);
    }
}
