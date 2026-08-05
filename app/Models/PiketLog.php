<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'schedule_id',
    'user_id',
    'date',
    'photo_path',
    'description',
    'latitude',
    'longitude',
    'accuracy_meters',
    'distance_meters',
    'photo_captured_at',
    'status',
    'verified_by',
    'verified_at',
    'rejection_reason',
])]
class PiketLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'distance_meters' => 'integer',
            'verified_at' => 'datetime',
            'accuracy_meters' => 'decimal:2',
            'photo_captured_at' => 'datetime',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(PiketSchedule::class, 'schedule_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
