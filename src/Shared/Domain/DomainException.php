<?php

declare(strict_types=1);

namespace CarShop\Shared\Domain;

use Exception;

/**
 * Base class for domain exceptions
 */
abstract class DomainException extends Exception implements DomainError
{
    public function __construct()
    {
        parent::__construct($this->errorMessage());
    }

    /**
     * Get the error code
     */
    abstract public function errorCode(): string;

    /**
     * Get a human-readable error message
     */
    abstract public function errorMessage(): string;
}
