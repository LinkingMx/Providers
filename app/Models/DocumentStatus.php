<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * DocumentStatus Model
 *
 * Represents the various statuses that a document can have throughout its lifecycle.
 * This model manages status information including visual properties (color, icon)
 * and behavioral properties (default status, completion status).
 *
 * @property int $id Primary key
 * @property string $name Unique name of the document status
 * @property string $color Color code for UI display (hex, named color, or CSS class)
 * @property string|null $icon Optional icon identifier for visual representation
 * @property bool $is_default Indicates if this is the default status for new documents
 * @property bool $is_complete Indicates if this status represents successful completion
 * @property \Illuminate\Support\Carbon $created_at Creation timestamp
 * @property \Illuminate\Support\Carbon $updated_at Last update timestamp
 */
class DocumentStatus extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentStatusFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * Includes all columns from the migration that should be fillable
     * for creating and updating document status records.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'color',
        'icon',
        'is_default',
        'is_complete',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * Ensures boolean fields are properly cast when retrieved from database.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_complete' => 'boolean',
    ];

    /**
     * Define the next possible statuses relationship.
     *
     * This creates a many-to-many self-referencing relationship that defines
     * which statuses can transition to which other statuses. This allows for
     * flexible workflow configuration where each status can have multiple
     * possible next statuses.
     *
     * The relationship uses a 'status_transitions' pivot table with:
     * - 'from_status_id': The current status ID
     * - 'to_status_id': The target status ID
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<DocumentStatus>
     */
    public function next_statuses(): BelongsToMany
    {
        return $this->belongsToMany(
            DocumentStatus::class,
            'status_transitions',
            'from_status_id',
            'to_status_id'
        );
    }
}
