<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhatsAppService
{
    public function send(string $phone, string $message): string
    {
        $url = config('services.fonnte.url');
        $token = config('services.fonnte.token');

        if (blank($url) || blank($token)) {
            throw new RuntimeException('WhatsApp API belum dikonfigurasi.');
        }

        $response = Http::timeout(config('services.fonnte.timeout', 15))
            ->withHeaders(['Authorization' => $token])
            ->acceptJson()
            ->post($url, [
                'target' => $phone,
                'message' => $message,
            ]);

        $response->throw();

        return (string) ($response->json('message_id') ?? $response->json('id') ?? '');
    }
}
