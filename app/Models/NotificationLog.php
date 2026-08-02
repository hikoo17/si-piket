<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'schedule_id', 'date', 'channel', 'phone', 'message', 'status', 'provider_message_id', 'sent_at', 'failed_at', 'error_message'])]
class NotificationLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['date' => 'date', 'sent_at' => 'datetime', 'failed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(PiketSchedule::class, 'schedule_id');
    }
}
