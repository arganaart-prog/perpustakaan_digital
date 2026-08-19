<?php

namespace App\Services;

use App\Models\BookQueue;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookQueueNotificationService
{
    public function notifyCalled(BookQueue $queue, string $source = 'auto'): void
    {
        if ($queue->notified_at) {
            return;
        }

        $queue->loadMissing(['user', 'book']);

        $user = $queue->user;
        $book = $queue->book;

        if (!$user || !$book) {
            return;
        }

        $deadlineText = optional($queue->deadline)->format('d-m-Y H:i') ?? '-';
        $message = "Halo {$user->name}, buku '{$book->title}' ({$book->code}) yang kamu booking sudah dikembalikan dan siap dipinjam di meja perpustakaan! Batas waktu pengambilan adalah 2 hari (hingga {$deadlineText}). Silakan segera ambil sebelum berpindah ke antrean berikutnya 📚";

        // 1. In-App Notification (Database Notification)
        $user->notify(new \App\Notifications\BookReadyNotification($queue));

        // 2. Automated Chat Message from Librarian / Perpustakaan System
        $this->sendAutomatedChatMessage($user, $message);

        // 3. Optional initial email
        if (!empty($user->email)) {
            try {
                Mail::to($user->email)->send(new \App\Mail\BookReadyMail($queue));
            } catch (\Throwable $e) {
                Log::warning("Gagal mengirim email BookReadyMail: " . $e->getMessage());
            }
        }

        Log::info('Book queue notification & chat sent', [
            'queue_id' => $queue->id,
            'user_id' => $user->id,
            'source' => $source,
        ]);

        $queue->update([
            'notified_at' => now(),
        ]);
    }

    /**
     * Send automated chat message from Librarian to Student.
     */
    protected function sendAutomatedChatMessage(User $student, string $messageText): void
    {
        $librarian = User::role('petugas')->first() ?? User::role('admin')->first() ?? User::first();

        if (!$librarian || $librarian->id === $student->id) {
            return;
        }

        // Find or create conversation between librarian and student
        $conversation = ChatConversation::where(function ($q) use ($librarian, $student) {
            $q->where('user_one_id', $librarian->id)->where('user_two_id', $student->id);
        })->orWhere(function ($q) use ($librarian, $student) {
            $q->where('user_one_id', $student->id)->where('user_two_id', $librarian->id);
        })->first();

        if (!$conversation) {
            $conversation = ChatConversation::create([
                'user_one_id' => $librarian->id,
                'user_two_id' => $student->id,
                'starter_id' => $librarian->id,
                'is_accepted' => true,
                'last_message_at' => now(),
            ]);
        } else {
            $conversation->update([
                'is_accepted' => true,
                'last_message_at' => now(),
            ]);
        }

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $librarian->id,
            'receiver_id' => $student->id,
            'message' => $messageText,
            'is_read' => false,
        ]);
    }
}
