<?php

namespace Smony\FilamentUserSessions\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDeviceLogin extends Notification
{
    use Queueable;

    public function __construct(
        public Authenticatable $user,
        public string $device,
        public ?string $ipAddress,
    ) {}

    /**
     * @return array<string>
     */
    public function via(mixed $notifiable): array
    {
        return config('filament-user-sessions.new_device_notification_channels', ['mail']);
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('New device sign-in detected')
            ->line("{$this->userLabel()} just signed in from a new device: {$this->device}.");

        if (filled($this->ipAddress)) {
            $message->line("IP address: {$this->ipAddress}");
        }

        return $message->line('Review this session in the admin panel if it looks suspicious.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'user_id' => $this->user->getAuthIdentifier(),
            'user' => $this->userLabel(),
            'device' => $this->device,
            'ip_address' => $this->ipAddress,
        ];
    }

    private function userLabel(): string
    {
        return $this->user->email ?? $this->user->name ?? (string) $this->user->getAuthIdentifier();
    }
}
