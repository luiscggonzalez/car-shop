<?php

declare(strict_types=1);

namespace CarShop\Shared\Domain;

/**
 * Base interface for domain exceptions
 * This allows the system to identify domain-specific errors
 */
interface DomainError
{
    /**
     * Get the error code
     */
    public function errorCode(): string;

    /**
     * Get a human-readable error message
     */
    public function errorMessage(): string;
}
