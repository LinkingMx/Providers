<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_type',
        'recipient_email',
        'status',
        'sent_at',
        'error_message',
        'mail_data',
        'events_log',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'mail_data' => 'array',
        'events_log' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Helper methods
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'sent';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function addEvent(string $event, array $data = []): void
    {
        $events = $this->events_log ?? [];
        $events[] = [
            'event' => $event,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ];
        
        $this->update(['events_log' => $events]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
        $this->addEvent('email_sent');
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
        ]);
        $this->addEvent('email_failed', ['error' => $error]);
    }
}
