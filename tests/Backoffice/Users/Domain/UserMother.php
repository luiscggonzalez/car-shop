<?php

declare(strict_types=1);

namespace Tests\Backoffice\Users\Domain;

use CarShop\Backoffice\Users\Domain\User;
use Faker\Factory;

final class UserMother
{
    public static function create(
        ?int $id = null,
        ?string $name = null,
        ?string $email = null,
        ?string $password = null,
    ): User {
        $faker = Factory::create();

        return User::fromPrimitives([
            "id" => $id ?? $faker->randomNumber(),
            "name" => $name ?? $faker->name(),
            "email" => $email ?? $faker->email(),
            "password" => $password ?? $faker->password(),
        ]);
    }
}
