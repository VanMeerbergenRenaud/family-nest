<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Log;
use MailerSend\Exceptions\MailerSendAssertException;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\LaravelDriver\MailerSendTrait;

class WelcomeMessage extends Mailable
{
    use MailerSendTrait, Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $to = Arr::get($this->to, '0.address');

        // Additional options for MailerSend API features
        try {
            $this->mailersend(
                template_id: null,
                tags: ['tag'],
                personalization: [
                    new Personalization($to, [
                        'var' => 'variable',
                        'number' => 123,
                        'object' => [
                            'key' => 'object-value',
                        ],
                        'objectCollection' => [
                            [
                                'name' => 'John',
                            ],
                            [
                                'name' => 'Patrick',
                            ],
                        ],
                    ]),
                ],
                precedenceBulkHeader: true,
                sendAt: new Carbon(now()->addMinutes(5)),
            );
        } catch (MailerSendAssertException $e) {
            Log::error('MailerSendAssertException: '.$e->getMessage());
        }

        return new Content(
            view: 'emails.welcome',
            text: 'emails.welcome'
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('public', 'img/img_placeholder.jpg'),
        ];
    }
}
