<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification
{
    use Queueable;

    public function __construct(
        public array $data
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->data['type'] ?? 'general',
            'title' => $this->data['title'] ?? 'Pemberitahuan',
            'message' => $this->data['message'] ?? '-',
            'action_url' => $this->data['action_url'] ?? null,
            'extra_data' => $this->data['extra_data'] ?? [],
        ];
    }
}
