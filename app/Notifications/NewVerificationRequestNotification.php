<?php

namespace App\Notifications;

use App\Models\VerificationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVerificationRequestNotification extends Notification
{
    use Queueable;

    public function __construct(protected VerificationRequest $request) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Новая заявка на верификацию')
            ->line('Поступила новая заявка на верификацию: '.$this->request->company_name)
            ->action('Открыть', route('admin.verification_requests.show', $this->request->id));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'verification_request.new',
            'request_id' => $this->request->id,
            'company_name' => $this->request->company_name,
        ];
    }
}
