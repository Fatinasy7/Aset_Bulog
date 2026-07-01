<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DamageReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asset;
    public $oldCondition;
    public $newCondition;

    /**
     * Create a new message instance.
     */
    public function __construct($asset, $oldCondition, $newCondition)
    {
        $this->asset = $asset;
        $this->oldCondition = $oldCondition;
        $this->newCondition = $newCondition;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Damage Report Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.damage-report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
