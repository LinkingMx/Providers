<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Branch Model
 *
 * Represents the different branches/offices in the system.
 * Branches serve as organizational units to which users and providers can be assigned.
 *
 * @property int $id Primary key
 * @property string $name Branch name
 * @property string|null $description Optional detailed description of the branch
 * @property string|null $address Physical address of the branch
 * @property string|null $phone Contact phone number
 * @property string|null $email Contact email address
 * @property bool $is_active Whether this branch is currently active
 * @property \Illuminate\Support\Carbon $created_at Creation timestamp
 * @property \Illuminate\Support\Carbon $updated_at Last update timestamp
 */
class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'address',
        'phone',
        'email',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the users that belong to this branch.
     *
     * This defines a many-to-many relationship where a branch can have
     * multiple users, and a user can belong to multiple branches.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('is_primary')->withTimestamps();
    }

    /**
     * Check if the branch is currently active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
