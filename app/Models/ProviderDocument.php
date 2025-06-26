<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * ProviderDocument Pivot Model
 *
 * This model represents the pivot table between users and document types with
 * additional attributes for managing the document lifecycle. Unlike a standard
 * pivot table, this model includes extra columns for tracking file uploads,
 * status changes, expiration dates, and rejection reasons.
 *
 * This pivot model enables complex document management workflows including:
 * - Document status tracking through our dynamic status system
 * - File upload management with storage paths
 * - Expiration date calculations based on document type validity
 * - Rejection reason tracking for compliance and feedback
 *
 * @property int $id Primary key (auto-incrementing)
 * @property int $user_id Foreign key to users table
 * @property int $document_type_id Foreign key to document_types table
 * @property int $document_status_id Foreign key to document_statuses table
 * @property string|null $file_path Path to uploaded document file
 * @property \Illuminate\Support\Carbon|null $uploaded_at When document was uploaded
 * @property \Illuminate\Support\Carbon|null $expires_at When document expires
 * @property string|null $rejection_reason Reason for rejection if applicable
 * @property \Illuminate\Support\Carbon $created_at Creation timestamp
 * @property \Illuminate\Support\Carbon $updated_at Last update timestamp
 * @property-read \App\Models\User $user The user who owns this document
 * @property-read \App\Models\DocumentType $documentType The type of document required
 * @property-read \App\Models\DocumentStatus $documentStatus The current status of the document
 */
class ProviderDocument extends Pivot
{
    /**
     * The table associated with the model.
     *
     * Explicitly defined since this is a custom pivot model with extra functionality.
     *
     * @var string
     */
    protected $table = 'provider_documents';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * Set to true because our pivot table has its own primary key column,
     * unlike standard pivot tables that use composite keys.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures proper type conversion for date fields when retrieving
     * from database or working with the model attributes.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uploaded_at' => 'datetime',
        'expires_at' => 'date',
    ];

    /**
     * Get the user that owns this document requirement.
     *
     * This relationship links back to the user who needs to provide
     * this document. Essential for identifying document ownership.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the document type for this requirement.
     *
     * This relationship provides access to the document type definition
     * including allowed file types, validity period, and other constraints.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<DocumentType>
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Get the current status of this document.
     *
     * This relationship connects to our dynamic status system, providing
     * access to status information including color, icon, and workflow rules.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<DocumentStatus>
     */
    public function documentStatus(): BelongsTo
    {
        return $this->belongsTo(DocumentStatus::class);
    }

    /**
     * Check if the document has been uploaded.
     *
     * Convenience method to determine if the provider has submitted
     * a file for this document requirement.
     *
     * @return bool True if document file has been uploaded, false otherwise
     */
    public function hasFile(): bool
    {
        return ! empty($this->file_path);
    }

    /**
     * Check if the document is expired.
     *
     * Determines if the document has passed its expiration date.
     * Returns false for documents that don't have expiration dates.
     *
     * @return bool True if document is expired, false otherwise
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the document is expiring soon.
     *
     * Determines if the document will expire within the specified number of days.
     * Useful for sending renewal reminders to providers.
     *
     * @param  int  $days  Number of days to check ahead (default: 30)
     * @return bool True if document expires within the specified days, false otherwise
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expires_at &&
               $this->expires_at->isFuture() &&
               $this->expires_at->diffInDays(now()) <= $days;
    }

    /**
     * Get the days until expiration.
     *
     * Returns the number of days until the document expires.
     * Returns null for documents without expiration dates.
     *
     * @return int|null Number of days until expiration, null if no expiration
     */
    public function getDaysUntilExpiration(): ?int
    {
        if (! $this->expires_at) {
            return null;
        }

        return max(0, now()->diffInDays($this->expires_at, false));
    }
}
