<?php

declare(strict_types=1);

namespace CarShop\Backoffice\Users\Domain;

use CarShop\Shared\Domain\Aggregate\AggregateRoot;

final class User extends AggregateRoot
{
    private UserEmail $email;

    public function __construct(
        private readonly int $id,
        private string $name,
        string $email,
        private readonly string $password
    ) {
        $this->email = new UserEmail($email);
    }

    public static function create(
        int $id,
        string $name,
        string $email,
        string $password
    ): self
    {
        $user = new self($id, $name, $email, $password);

        $user->record(new UserCreatedDomainEvent(
            (string)$user->id(),
            $user->name(),
            $user->email()
        ));

        return $user;
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self(
            $primitives['id'],
            $primitives['name'],
            $primitives['email'],
            $primitives['password'] ?? ''
        );
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id(),
            'name' => $this->name(),
            'email' => $this->email(),
            'password' => $this->password()
        ];
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email->value();
    }

    public function password(): string
    {
        return $this->password;
    }
}
