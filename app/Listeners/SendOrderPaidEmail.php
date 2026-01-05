<?php

namespace App\Listeners;

use App\Events\OrderPaidEvent;
use App\Mail\OrderPaid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderPaidEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Jumlah percobaan ulang jika job gagal
     */
    public int $tries = 3;

    /**
     * Waktu delay (detik) sebelum retry (opsional)
     */
    public int $backoff = 10;

    /**
     * Handle the event.
     */
    public function handle(OrderPaidEvent $event): void
    {
        // Pastikan user ada
        if (!$event->order->user || !$event->order->user->email) {
            return;
        }

        // Kirim email ke user
        Mail::to($event->order->user->email)
            ->send(new OrderPaid($event->order));

        // Opsional: kirim ke admin
        // Mail::to(config('mail.admin_email'))
        //     ->send(new OrderPaid($event->order));
    }
}