<?php

declare(strict_types=1);

namespace CarShop\Backoffice\Users\Domain;

final class UserEmail
{
    private string $email;

    public function __construct(string $email)
    {
        $this->ensureIsValidEmail($email);
        $this->email = $email;
    }

    public function value(): string
    {
        return $this->email;
    }

    private function ensureIsValidEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('<%s> is not a valid email address', $email));
        }
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
