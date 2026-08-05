<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
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

        try {
            $response = Http::connectTimeout(10)
                ->timeout(config('services.fonnte.timeout', 30))
                ->retry(2, 1000, throw: false)
                ->withHeaders(['Authorization' => $token])
                ->acceptJson()
                ->post($url, [
                    'target' => $phone,
                    'message' => $message,
                ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Fonnte tidak dapat dihubungi. Periksa koneksi internet atau coba kembali beberapa saat lagi.', previous: $exception);
        }

        $response->throw();

        if ($response->json('status') === false) {
            $error = $response->json('reason') ?? $response->json('message') ?? 'Fonnte menolak pengiriman pesan.';
            throw new RuntimeException(is_array($error) ? implode(', ', $error) : (string) $error);
        }

        $messageId = $response->json('message_id') ?? $response->json('id') ?? '';

        return is_array($messageId) ? (string) ($messageId[0] ?? '') : (string) $messageId;
    }
}
