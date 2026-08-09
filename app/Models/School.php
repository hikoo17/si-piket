<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'address', 'latitude', 'longitude', 'radius_meters', 'upload_start_time', 'upload_deadline', 'return_upload_start_time', 'return_upload_deadline', 'whatsapp_enabled', 'whatsapp_send_time', 'whatsapp_send_time_return', 'whatsapp_message_template'])]
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
                'return_upload_start_time' => '14:00',
                'return_upload_deadline' => '17:00',
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
            'return_upload_start_time' => 'string',
            'return_upload_deadline' => 'string',
            'whatsapp_enabled' => 'boolean',
            'whatsapp_send_time' => 'string',
            'whatsapp_send_time_return' => 'string',
        ];
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }
}
