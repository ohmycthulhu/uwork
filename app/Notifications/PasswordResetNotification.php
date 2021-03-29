<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Nutnet\LaravelSms\Notifications\NutnetSmsChannel;
use Nutnet\LaravelSms\Notifications\NutnetSmsMessage;

class PasswordResetNotification extends Notification
{
  use Queueable;

  protected $hasEmail;
  protected $hasPhone;
  protected $uuid;

  /**
   * Create a new notification instance.
   *
   * @param bool $hasEmail
   * @param bool $hasPhone
   * @param string $uuid
   *
   * @return void
   */
  public function __construct(bool $hasEmail, bool $hasPhone, string $uuid)
  {
    $this->hasEmail = $hasEmail;
    $this->hasPhone = $hasPhone;
    $this->uuid = $uuid;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param mixed $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    $result = [];
    if ($this->hasPhone) {
      array_push($result, NutnetSmsChannel::class);
    }
    if ($this->hasEmail) {
      array_push($result, 'email');
    }
    return $result;
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param mixed $notifiable
   * @return MailMessage
   */
  public function toMail($notifiable)
  {
    return (new MailMessage)
      ->line('To reset the password, use following code:')
      ->line($this->uuid)
      ->line('Thank you for using our application!');
  }

  /**
   * Get the array representation of the notification.
   *
   * @param mixed $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      'uuid' => $this->uuid,
    ];
  }

  /**
   * To Nutnet SMS
   *
   * @param mixed $notifable
   * @return NutnetSmsMessage
  */
  public function toNutnetSms($notifable): array {
    return new NutnetSmsMessage("Use following code to reset password: {$this->uuid}");
  }
}
