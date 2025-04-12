<?php

declare(strict_types=1);

namespace Tests\Backoffice\Users\Application\Create;

use CarShop\Backoffice\Users\Application\Create\UserCreator;
use CarShop\Backoffice\Users\Application\Create\UserCreatorRequest;
use CarShop\Backoffice\Users\Domain\UserCreatedDomainEvent;
use CarShop\Backoffice\Users\Domain\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use Tests\Backoffice\Users\Domain\UserMother;
use Tests\Shared\Infrastructure\PhpUnit\UnitTestCase;

final class UserCreatorTest extends UnitTestCase
{
    #[Test]
    public function it_should_create_a_user(): void
    {
        $userId = 1;
        $user = UserMother::create(
            id: $userId
        );

        $userRepository = $this->mock(UserRepository::class);
        $userRepository->shouldReceive('nextId')
            ->once()
            ->andReturn($userId);

        $userRepository->shouldReceive('save')
            ->once()
            ->with($this->similarTo($user))
            ->andReturn($userId);

        $request = new UserCreatorRequest(
            $user->name(),
            $user->email(),
            $user->password(),
        );

        $eventBus = $this->eventBus();
        $event = new UserCreatedDomainEvent(
            (string)$userId,
            $user->name(),
            $user->email(),
        );
        $this->shouldPublishDomainEvent($event);

        $creator = new UserCreator(
            $userRepository,
            $eventBus
        );
        ($creator)($request);
    }
}
