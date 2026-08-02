<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['piket_log_id', 'photo_path', 'latitude', 'longitude', 'accuracy_meters', 'distance_meters', 'status', 'rejection_reason', 'submitted_at'])]
class PiketLogAttempt extends Model
{
    protected function casts(): array
    {
        return ['latitude' => 'decimal:8', 'longitude' => 'decimal:8', 'accuracy_meters' => 'decimal:2', 'submitted_at' => 'datetime'];
    }

    public function log(): BelongsTo
    {
        return $this->belongsTo(PiketLog::class, 'piket_log_id');
    }
}
