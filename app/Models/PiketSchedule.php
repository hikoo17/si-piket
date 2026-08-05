<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'day_of_week', 'shift'])]
class PiketSchedule extends Model
{
    use HasFactory;

    public function getShiftLabelAttribute(): string
    {
        return $this->shift === 'afternoon' ? 'Piket Pulang' : 'Piket Pagi';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PiketLog::class, 'schedule_id');
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class, 'schedule_id');
    }
}
