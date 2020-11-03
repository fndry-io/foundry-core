<?php

namespace Foundry\Core\Auth\EmailVerifier;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification class for emails with a customizable message
 */
class EmailNotification extends Notification
{
    /** @var callable $builder callable for creating a MailMessage object */
    protected $builder;

    /**
     * Constructs a new instance.
     *
     * @param  callable  $builder  The builder
     */
    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return call_user_func_array($this->builder, [$notifiable]);
    }
}
