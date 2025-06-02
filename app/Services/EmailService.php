<?php

namespace App\Services;

use App\Mail\BaseMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Envoie un email via la queue
     */
    public function send(string|array $to, BaseMail $mail): bool
    {
        try {
            Mail::to($to)->queue($mail);

            Log::info('Email queued successfully', [
                'to' => $to,
                'mail_class' => get_class($mail),
                'subject' => $mail->envelope()->subject,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to queue email', [
                'to' => $to,
                'mail_class' => get_class($mail),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Envoie un email immédiatement (sans queue)
     * À utiliser uniquement pour des cas urgents
     */
    public function sendNow(string|array $to, BaseMail $mail): bool
    {
        try {
            Mail::to($to)->send($mail);

            Log::info('Email sent immediately', [
                'to' => $to,
                'mail_class' => get_class($mail),
                'subject' => $mail->envelope()->subject,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email immediately', [
                'to' => $to,
                'mail_class' => get_class($mail),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
