<?php

namespace Foundry\Core\Auth\EmailVerifier;

use Exception;
use Foundry\Core\Auth\EmailVerifier\Notifier;

/**
 * Service for notifying and validating EmailVerifiable objects
 */
class EmailVerifier
{
    /** @var Notifier object for handling notifications */
    protected $notifier = null;

    /**
     * Constructs a new instance.
     *
     * @param  Notifier  $notifier  The notifier
     */
    public function __construct(Notifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * Verify email with code provided
     *
     * @param  EmailVerifiableInterface  $verifiable  The verifiable
     * @param  string                    $code        The code
     *
     * @return  bool
     */
    public function verify(EmailVerifiableInterface $verifiable, string $code): bool
    {
        if ($verifiable->getVerificationCode() === $code) {
            $verifiable->setEmailAsVerified();
        }

        return $verifiable->isEmailVerified();
    }

    /**
     * Sends an email verification.
     *
     * @param  EmailVerifiableInterface  $verifiable  The verifiable
     */
    public function notify(EmailVerifiableInterface $verifiable)
    {
        $code = $this->generateVerificationCode();
        $verifiable->setVerificationCode($code);

        $this->getNotifier()->notify($verifiable);
    }

    /**
     * Sets the notifier.
     *
     * @param  Notifier  $notifier  The dispatcher
     *
     * @return static
     */
    public function setNotifier(Notifier $notifier)
    {
        $this->notifier = $notifier;

        return $this;
    }

    /**
     * Gets the notifier.
     *
     * @throws  Exception  if not notifier is set
     *
     * @return  Notifier  The notifier.
     */
    public function getNotifier(): Notifier
    {
        return $this->notifier;
    }

    /**
     * Accept a callable that will provide a MailMessage object to send as notification
     *
     * @param  callable  $callable  The callable
     *
     * @return  self
     */
    public function setMailBuilder(callable $callable)
    {
        $this->notifier = $this->getNotifier()->withMessage($callable);

        return $this;
    }

    /**
     * Simple utility function for generating a random code
     *
     * @return  string
     * @todo create a customizable implementation of a code generator
     */
    protected function generateVerificationCode(): string
    {
        return mt_rand(10000, 99999);
    }
}
