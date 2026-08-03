<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'address', 'google_place_id', 'latitude', 'longitude', 'radius_meters', 'upload_start_time', 'upload_deadline'])]
class School extends Model
{
    use HasFactory;

    public static function primary(): self
    {
        return self::query()->firstOrCreate(
            ['name' => 'SMAN 1 Tasikmalaya'],
            [
                'address' => 'Jl. Rumah Sakit Umum No. 28, Empangsari, Kec. Tawang, Kota Tasikmalaya, Jawa Barat',
                'latitude' => -7.327096,
                'longitude' => 108.220349,
                'radius_meters' => 100,
                'upload_start_time' => '05:00',
                'upload_deadline' => '17:00',
            ],
        );
    }

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'radius_meters' => 'integer',
            'upload_start_time' => 'string',
            'upload_deadline' => 'string',
        ];
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }
}
