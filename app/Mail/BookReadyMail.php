<?php

namespace App\Mail;

use App\Models\BookQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BookQueue $bookQueue
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Buku Siap Diambil: ' . $this->bookQueue->book->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.book-ready',
        );
    }
}
