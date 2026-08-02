<?php

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPiketReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(public int $notificationLogId) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $log = NotificationLog::find($this->notificationLogId);
        if (! $log || $log->status === 'sent') {
            return;
        }

        try {
            $messageId = $whatsApp->send($log->phone, $log->message);
            $log->update(['status' => 'sent', 'provider_message_id' => $messageId ?: null, 'sent_at' => now(), 'failed_at' => null, 'error_message' => null]);
        } catch (\Throwable $exception) {
            $log->update(['status' => 'failed', 'failed_at' => now(), 'error_message' => $exception->getMessage()]);
            Log::warning('Gagal mengirim pengingat WhatsApp.', ['notification_log_id' => $log->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}
