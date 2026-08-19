<?php

namespace App\Mail;

use App\Models\BookQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnclaimedBookReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BookQueue $bookQueue
    ) {
        $this->bookQueue->loadMissing(['user', 'book']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Pengingat 24 Jam: Buku Booking "' . $this->bookQueue->book->title . '" Menunggumu di Perpustakaan',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.unclaimed-book-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
