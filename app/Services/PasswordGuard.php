<?php

namespace App\Services;

/**
 * Class PasswordGuard
 *
 * This class manages password authentication attempts and lockout logic to prevent brute-force attacks.
 *
 * @package App\Services
 */
class PasswordGuard
{
    /**
     * The maximum number of allowed attempts before lockout.
     *
     * @var int
     */
    protected $maxAttempts;

    /**
     * The duration of the lockout time in seconds.
     *
     * @var int
     */
    protected $lockoutTime;

    /**
     * Reference to the session array where attempts and timestamps are stored.
     *
     * @var array
     */
    protected $session;

    /**
     * PasswordGuard constructor.
     *
     * Initializes the PasswordGuard instance with the maximum number of attempts and lockout time.
     *
     * @param int $maxAttempts The maximum number of allowed attempts before lockout. Default is 2.
     * @param int $lockoutTime The duration of the lockout time in seconds. Default is 900 seconds (15 minutes).
     */
    public function __construct($maxAttempts = 2, $lockoutTime = 900)
    {
        $this->maxAttempts = $maxAttempts;
        $this->lockoutTime = $lockoutTime;
        $this->session = &$_SESSION;

        // Initialize session variables if not set
        if (!isset($this->session['attempts'])) {
            $this->session['attempts'] = 0;
        }
        if (!isset($this->session['last_attempt_time'])) {
            $this->session['last_attempt_time'] = time();
        }
    }

    /**
     * Checks if the user is currently locked out due to too many failed attempts.
     *
     * @return bool Returns true if the user is locked out, false otherwise.
     */
    public function isLockedOut(): bool
    {
        if ($this->session['attempts'] >= $this->maxAttempts) {
            $timeSinceLastAttempt = time() - $this->session['last_attempt_time'];
            if ($timeSinceLastAttempt < $this->lockoutTime) {
                return true;
            } else {
                // Reset attempts after lockout time has passed
                $this->resetAttempts();
            }
        }
        return false;
    }

    /**
     * Increments the number of failed password attempts.
     *
     * This method also updates the timestamp of the last attempt.
     */
    public function incrementAttempts(): void
    {
        $this->session['attempts']++;
        $this->session['last_attempt_time'] = time();
    }

    /**
     * Resets the number of failed password attempts.
     *
     * This method is typically called after a successful login or when the lockout period expires.
     */
    public function resetAttempts(): void
    {
        $this->session['attempts'] = 0;
    }

    /**
     * Verifies if the provided password matches the correct password.
     *
     * @param string $inputPassword The password provided by the user.
     * @param string $correctPassword The correct password to compare against.
     * @return bool Returns true if the passwords match, false otherwise.
     */
    public function verifyPassword($inputPassword, $correctPassword): bool
    {
        return $inputPassword === $correctPassword;
    }
}
