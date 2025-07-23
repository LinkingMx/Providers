<?php

namespace App\Models;

use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the provider profile associated with this user.
     *
     * This defines a one-to-one relationship where a user can have
     * at most one provider profile. This relationship is optional,
     * as not all users are necessarily providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<ProviderProfile>
     */
    public function providerProfile(): HasOne
    {
        return $this->hasOne(ProviderProfile::class);
    }

    /**
     * Check if this user has a provider profile.
     *
     * This is a convenience method to quickly determine if the user
     * is registered as a provider in the system.
     *
     * @return bool True if the user has a provider profile, false otherwise
     */
    public function isProvider(): bool
    {
        return $this->providerProfile()->exists();
    }

    /**
     * Get the document requirements for this user (provider documents).
     *
     * This defines a many-to-many relationship between users and document types
     * through the provider_documents pivot table. This relationship tracks
     * all document requirements for a provider including their status,
     * file information, and expiration dates.
     *
     * The pivot model (ProviderDocument) provides additional functionality
     * for managing document lifecycle, status transitions, and business logic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<DocumentType>
     */
    public function documentRequirements(): BelongsToMany
    {
        return $this->belongsToMany(DocumentType::class, 'provider_documents')
            ->using(ProviderDocument::class)
            ->withPivot([
                'document_status_id',
                'file_path',
                'uploaded_at',
                'expires_at',
                'rejection_reason',
            ])
            ->withTimestamps();
    }

    /**
     * Get the provider documents directly (pivot records).
     *
     * This provides direct access to the ProviderDocument pivot records
     * for this user, which is useful for widgets and other components
     * that need to work directly with the pivot data.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ProviderDocument>
     */
    public function providerDocuments(): HasMany
    {
        return $this->hasMany(ProviderDocument::class);
    }

    /**
     * Get the branches that this user belongs to.
     *
     * This defines a many-to-many relationship where a user can belong
     * to multiple branches, and a branch can have multiple users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Branch>
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class)->withPivot('is_primary')->withTimestamps();
    }

    /**
     * Get the primary branch for this user.
     */
    public function primaryBranch(): ?Branch
    {
        return $this->branches()->wherePivot('is_primary', true)->first();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }
}
