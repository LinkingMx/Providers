<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DocumentType Model
 *
 * Represents the different types of documents that can be managed in the system.
 * This model defines the characteristics, validation rules, and behavior for
 * each document type including file restrictions and validity periods.
 *
 * @property int $id Primary key
 * @property string $name Unique name of the document type
 * @property string|null $description Optional detailed description of the document type
 * @property array $allowed_file_types Array of allowed file extensions or MIME types
 * @property int $validity_days Number of days the document remains valid (0 for no expiration)
 * @property bool $is_active Whether this document type is currently active and available
 * @property \Illuminate\Support\Carbon $created_at Creation timestamp
 * @property \Illuminate\Support\Carbon $updated_at Last update timestamp
 */
class DocumentType extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentTypeFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * Includes all columns from the migration that should be fillable
     * for creating and updating document type records.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'allowed_file_types',
        'validity_days',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures proper type conversion when retrieving data from database:
     * - is_active: Converts to boolean for easier conditional logic
     * - allowed_file_types: Converts JSON to array for easier manipulation
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'allowed_file_types' => 'array',
    ];

    /**
     * Check if the document type has an expiration period.
     *
     * Documents with validity_days = 0 are considered to never expire.
     *
     * @return bool True if the document type expires, false otherwise
     */
    public function hasExpiration(): bool
    {
        return $this->validity_days > 0;
    }

    /**
     * Check if a given file extension is allowed for this document type.
     *
     * @param  string  $extension  The file extension to check (with or without dot)
     * @return bool True if the extension is allowed, false otherwise
     */
    public function isFileTypeAllowed(string $extension): bool
    {
        // Remove dot from extension if present
        $extension = ltrim(strtolower($extension), '.');

        // Check if extension exists in allowed file types array
        return in_array($extension, array_map('strtolower', $this->allowed_file_types ?? []));
    }

    /**
     * Get the allowed file types as a formatted string for display.
     *
     * @return string Comma-separated list of allowed file types
     */
    public function getAllowedFileTypesString(): string
    {
        return implode(', ', $this->allowed_file_types ?? []);
    }
}
