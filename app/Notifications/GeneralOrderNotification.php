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
        return ['database', \App\Channels\FcmChannel::class];
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
            'invoice_id' => $this->data['invoice_id'] ?? null,
            'target_type' => $this->data['target_type'] ?? 'order',
            'title'    => $this->data['title'] ?? 'Notifikasi Baru',
            'message'  => $this->data['message'] ?? '',
            'type'     => $this->data['type'] ?? 'info', // new_order, payment, status_update, cancel
        ];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm(object $notifiable): array
    {
        return [
            'title' => $this->data['title'] ?? 'Notifikasi Baru',
            'body'  => $this->data['message'] ?? '',
            'data'  => [
                'order_id' => (string) ($this->data['order_id'] ?? ''),
                'invoice_id' => (string) ($this->data['invoice_id'] ?? ''),
                'target_type' => (string) ($this->data['target_type'] ?? 'order'),
                'status'   => (string) ($this->data['status'] ?? ''),
                'type'     => (string) ($this->data['type'] ?? 'info'),
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        ];
    }
}
