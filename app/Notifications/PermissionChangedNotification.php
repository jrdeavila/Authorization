<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PermissionChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $type,
        private string $subject,
        private string $performedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $action = $this->type === 'assigned' ? 'asignó' : 'revocó';

        return (new MailMessage)
            ->subject('Tu acceso ha sido actualizado')
            ->line("Se {$action} el acceso: {$this->subject}.")
            ->line("Realizado por: {$this->performedBy}")
            ->line('Si no reconoces esta acción, contacta al administrador.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'subject' => $this->subject,
            'performed_by' => $this->performedBy,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
