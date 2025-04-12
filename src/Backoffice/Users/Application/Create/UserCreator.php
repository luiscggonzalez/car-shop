<?php

declare(strict_types=1);

namespace CarShop\Backoffice\Users\Application\Create;

use CarShop\Backoffice\Users\Domain\UserRepository;
use CarShop\Backoffice\Users\Domain\User;
use CarShop\Shared\Domain\Bus\Event\EventBus;

class UserCreator
{
    public function __construct(
      private readonly UserRepository $userRepository,
      private readonly EventBus $eventBus
    ) {
    }

    public function __invoke(UserCreatorRequest $request): int
    {
        $id = $this->userRepository->nextId();

        $user = User::create(
            $id,
            $request->name(),
            $request->email(),
            $request->password()
        );

        $userId = $this->userRepository->save($user);

        $this->eventBus->publish(...$user->pullDomainEvents());

        return $userId;
    }}
