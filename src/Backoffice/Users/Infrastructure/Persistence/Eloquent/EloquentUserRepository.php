<?php

declare(strict_types=1);

namespace CarShop\Backoffice\Users\Infrastructure\Persistence\Eloquent;

use CarShop\Backoffice\Users\Domain\UserRepository;
use App\Models\User as UserModel;
use CarShop\Backoffice\Users\Domain\User;
use RuntimeException;

final class EloquentUserRepository implements UserRepository
{
    public function save(User $user): int
    {
        $model = UserModel::where([
            "email" => $user->email()
        ])->first();

        if ($model) {
            $model->name = $user->name();
            $model->save();

            return $model->id;
        }

        try {
            UserModel::create([
                'id' => $user->id(),
                'name' => $user->name(),
                'email' => $user->email(),
                'password' => $user->password(),
            ]);
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to save user: " . $e->getMessage(), 0, $e);
        }

        return $user->id();
    }

    public function nextId(): int
    {
        /** @var int|null $maxId */
        $maxId = UserModel::max('id');
        return ($maxId ?? 0) + 1;
    }
}
