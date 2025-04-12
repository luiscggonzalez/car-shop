<?php

namespace CarShop\Backoffice\Users\Domain;

use CarShop\Shared\Domain\Bus\Event\DomainEvent;

class UserCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly string $name,
        private readonly string $email,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        parent::__construct($aggregateId, $eventId, $occurredOn);
    }

    /**
     * @param array{name: string, email: string} $body
     */
    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): DomainEvent {
        return new self(
            $aggregateId,
            $body['name'],
            $body['email'],
            $eventId,
            $occurredOn
        );
    }

    public static function eventName(): string
    {
        return "backoffice.user.create";
    }

    public function toPrimitives(): array
    {
        return [
            'name' => $this->name(),
            'email' => $this->email()
        ];
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }
}
