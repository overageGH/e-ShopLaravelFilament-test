<?php

namespace App\Notifications;

use App\Models\VerificationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationRequestStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(protected VerificationRequest $request) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $subject = $this->request->status === VerificationRequest::STATUS_APPROVED ? 'Ваша компания подтверждена' : 'Заявка на верификацию отклонена';

        $mail = (new MailMessage)->subject($subject)->line('Статус вашей заявки: '.$this->request->status);

        if ($this->request->status === VerificationRequest::STATUS_REJECTED && $this->request->rejection_reason) {
            $mail->line('Причина отказа:')->line($this->request->rejection_reason);
        }

        $mail->action('Посмотреть заявку', route('verification_requests.show', $this->request->id));

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'verification_request.status_changed',
            'request_id' => $this->request->id,
            'status' => $this->request->status,
            'rejection_reason' => $this->request->rejection_reason,
        ];
    }
}
