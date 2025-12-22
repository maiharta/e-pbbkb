<?php

namespace App\Mail;

use App\Models\Pelaporan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class PelaporanRevisionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $pelaporan;
    public $catatanRevisi;

    /**
     * Create a new message instance.
     */
    public function __construct(Pelaporan $pelaporan, string $catatanRevisi)
    {
        $this->pelaporan = $pelaporan;
        $this->catatanRevisi = $catatanRevisi;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Revisi Pelaporan',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.pelaporan-revision',
            with: [
                'pelaporan' => $this->pelaporan,
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
