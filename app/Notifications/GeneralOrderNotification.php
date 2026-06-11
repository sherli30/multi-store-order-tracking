<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->data['order_id'] ?? null,
            'title'    => $this->data['title'] ?? 'Notifikasi Baru',
            'message'  => $this->data['message'] ?? '',
            'type'     => $this->data['type'] ?? 'info', // new_order, payment, status_update, cancel
        ];
    }
}
