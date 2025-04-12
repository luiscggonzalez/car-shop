<?php

namespace App\Http\Controllers\Backoffice\Users;

use CarShop\Backoffice\Users\Application\Create\UserCreator;
use CarShop\Backoffice\Users\Application\Create\UserCreatorRequest;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use CarShop\Backoffice\Users\Infrastructure\Authorization\UsersPermissions;

final class UserPostController extends ApiController
{
    public function __construct(
       private readonly UserCreator $userCreator
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->can(UsersPermissions::CREATE)) {
            throw new UnauthorizedException(Response::HTTP_UNAUTHORIZED);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $creatorRequest = new UserCreatorRequest(
            $validatedData['name'],
            $validatedData['email'],
            $validatedData['password'],
        );

        DB::transaction(function () use ($creatorRequest) {
            ($this->userCreator)($creatorRequest);
        });

        return new JsonResponse(null, Response::HTTP_CREATED);

    }
}
