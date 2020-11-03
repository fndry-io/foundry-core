<?php

namespace Foundry\Core\Auth\EmailVerifier;

use Foundry\Core\Auth\EmailVerifier\EmailNotification;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class Notifier
{
    /** @var Dispatcher object for handling notifications */
    protected $dispatcher;

    protected $messageBuilder;

    public function __construct(Dispatcher $dispatcher, callable $messageBuilder = null)
    {
        $this->dispatcher = $dispatcher;
        $this->messageBuilder = $messageBuilder;
    }

    public function withMessage(callable $messageBuilder)
    {
        return new static($this->dispatcher, $messageBuilder);
    }

    protected function getMessageBuilder()
    {
        if ($this->messageBuilder !== null) {
            return $this->messageBuilder;
        }

        $builder = function ($notifiable) {
            return (new MailMessage())
                ->subject(Lang::get('Verify Email Address'))
                ->line(Lang::get('Please enter the OTP code provided below'))
                ->line(Lang::get($notifiable->getVerificationCode()))
                ->line(Lang::get('If you did not create an account, no further action is required.'));
        };

        return $builder;
    }

    public function notify(EmailVerifiableInterface $verifiable)
    {
        $notification = new EmailNotification($this->getMessageBuilder());

        return $this->dispatcher->send($verifiable, $notification);
    }
}
