<?php

namespace App\Http\Controllers\Backoffice\Users;

use CarShop\Backoffice\Users\Application\Create\UserCreator;
use CarShop\Backoffice\Users\Application\Create\UserCreatorRequest;
use App\Http\Controllers\ApiController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use CarShop\Backoffice\Users\Infrastructure\Authorization\UsersPermissions;

/**
 * @OA\Post(
 *     path="/api/backoffice/users",
 *     tags={"Backoffice - Users"},
 *     summary="Create a new user",
 *     description="Creates a new user with the provided information",
 *     operationId="createUser",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"name", "email", "password"},
 *             @OA\Property(property="name", type="string", example="John Doe", description="User's full name"),
 *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com", description="User's email address"),
 *             @OA\Property(property="password", type="string", format="password", example="SecurePassword123", description="User's password")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="User created successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="title", type="string", example="Validation Error"),
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="detail", type="string", example="The given data was invalid."),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "name": {"The name field is required."},
 *                     "email": {"The email must be a valid email address."},
 *                     "password": {"The password must be at least 8 characters."}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", example="Unauthorized"),
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="detail", type="string", example="You do not have permission to create users.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="title", type="string", example="Internal Server Error"),
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="detail", type="string", example="An unexpected error occurred while processing your request.")
 *         )
 *     ),
 *     security={
 *         {"bearerAuth":{}}
 *     }
 * )
 */
final class UserPostController extends ApiController
{
    public function __construct(
       private readonly UserCreator $userCreator
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
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
        } catch (ValidationException $e) {
            return new JsonResponse([
                'title' => 'Validation Error',
                'status' => Response::HTTP_BAD_REQUEST,
                'detail' => 'The given data was invalid.',
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (UnauthorizedException) {
            return response()->json([
                'title' => 'Unauthorized',
                'detail' => 'You do not have permission to view this resource.',
                'status' => 403,
            ], 403);
        } catch (Exception $e) {
            return new JsonResponse([
                'title' => 'Internal Server Error',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'detail' => 'An unexpected error occurred'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
