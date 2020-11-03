<?php

namespace Foundry\Core\Auth\EmailVerifier\Traits;

use Exception;
use Illuminate\Support\Facades\Date;

/**
 * Model has a verifiable email using a code
 *
 * @method mixed getAttribute(string $key)
 * @method mixed setAttribute(string $key, mixed $value)
 */
trait HasVerifiableEmail
{
    /**
     * Determines if email verified.
     *
     * @return  boolean  True if email verified, False otherwise.
     */
    public function isEmailVerified(): bool
    {
        return ($this->getEmailVerifiedAt() !== null);
    }

    /**
     * Gets the email verification date.
     *
     * @return  null|string  The email verification date.
     */
    public function getEmailVerifiedAt(): ?string
    {
        $column = defined('static::EMAIL_VERIFIER_DATE_COLUMN') ? static::EMAIL_VERIFIER_DATE_COLUMN : 'email_verified_at';

        return $this->getAttribute($column);
    }

    /**
     * Gets the email to verify
     *
     * @return  string  The email to verify
     */
    public function getVerifiableEmail(): string
    {
        $column = defined('static::EMAIL_VERIFIER_EMAIL_COLUMN') ? static::EMAIL_VERIFIER_EMAIL_COLUMN : 'email';

        return $this->getAttribute($column);
    }

    /**
     * Gets the verification code.
     *
     * @return  null|string  The verification code.
     */
    public function getVerificationCode(): ?string
    {
        $column = defined('static::EMAIL_VERIFIER_VERIFICATION_CODE_COLUMN') ? static::EMAIL_VERIFIER_VERIFICATION_CODE_COLUMN : 'email_verification_code';

        return $this->getAttribute($column);
    }

    /**
     * Sets the email as verified.
     */
    public function setEmailAsVerified()
    {
        $this->setAttribute($this->emailVerifiableCode, "");
        $this->setAttribute($this->emailVerifiableDate, Date::now());
        $this->save();
    }

    /**
     * Sets the verification code.
     *
     * @param  string  $code  The code
     */
    public function setVerificationCode($code)
    {
        $this->setAttribute($this->emailVerifiableCode, $code);
        $this->save();
    }
}
