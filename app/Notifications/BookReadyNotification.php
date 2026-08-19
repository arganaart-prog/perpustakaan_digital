<?php

namespace App\Notifications;

use App\Models\BookQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookReadyNotification extends Notification
{
    use Queueable;

    public function __construct(
        public BookQueue $bookQueue
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Secara default simpan ke database.
        // Email sudah ditangani oleh manual Mailable di Service, 
        // tapi kita bisa aktifkan di sini jika ingin migrasi full ke Notification.
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'book_ready',
            'title' => 'Buku Siap Diambil!',
            'message' => "Buku '{$this->bookQueue->book->title}' sudah tersedia dan siap untuk Anda ambil.",
            'book_id' => $this->bookQueue->book_id,
            'queue_id' => $this->bookQueue->id,
            'deadline' => $this->bookQueue->deadline ? $this->bookQueue->deadline->format('Y-m-d H:i:s') : null,
            'action_url' => route('member.books.index'),
        ];
    }
}
