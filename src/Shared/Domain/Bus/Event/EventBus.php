<?php

declare(strict_types=1);

namespace CarShop\Shared\Domain\Bus\Event;

interface EventBus
{
    /**
     * @param DomainEvent ...$events
     */
    public function publish(DomainEvent ...$events): void;
}
