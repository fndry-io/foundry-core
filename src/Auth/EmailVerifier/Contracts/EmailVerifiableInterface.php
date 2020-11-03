<?php

namespace Foundry\Core\Auth\EmailVerifier\Contracts;

/**
 * Contract for classes with verifiable emails using a code
 */
interface EmailVerifiableInterface
{
    /**
     * Determines if email verified.
     *
     * @return  boolean  True if email verified, False otherwise.
     */
    public function isEmailVerified(): bool;

    /**
     * Gets the email verification date.
     *
     * @return  null|string  The email verification date.
     */
    public function getEmailVerifiedAt(): ?string;

    /**
     * Gets the email to verify
     *
     * @return  string  The email to verify
     */
    public function getVerifiableEmail(): string;

    /**
     * Gets the verification code.
     *
     * @return  null|string  The verification code.
     */
    public function getVerificationCode(): ?string;

    /**
     * Sets the email as verified.
     */
    public function setEmailAsVerified();

    /**
     * Sets the verification code.
     *
     * @param  string  $code  The code
     */
    public function setVerificationCode($code);
}
