<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRevisionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $catatanRevisi;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $catatanRevisi)
    {
        $this->user = $user;
        $this->catatanRevisi = $catatanRevisi;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Revisi Data Akun',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.user-revision',
            with: [
                'user' => $this->user,
                'catatanRevisi' => $this->catatanRevisi,
            ],
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
