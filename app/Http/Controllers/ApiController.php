<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use CarShop\Shared\Domain\DomainError;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends Controller
{
    /**
     * @param callable(): JsonResponse $callable
     */
    protected function execute(callable $callable): JsonResponse
    {
        try {
            return $callable();
        } catch (Exception $error) {
            return $this->handleException($error);
        }
    }

    protected function handleException(Exception $error): JsonResponse
    {
        $statusCode = $this->exceptionStatusCode($error);

        if ($statusCode >= 500) {
            Log::error($error->getMessage(), [
                'exception' => get_class($error),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'trace' => $error->getTraceAsString(),
            ]);
        }

        return new JsonResponse(
            [
                'title' => $this->exceptionTitle($error),
                'status' => $statusCode,
                'detail' => $this->exceptionDetail($error),
                'errors' => $error instanceof ValidationException ? $error->errors() : null,
            ],
            $statusCode
        );
    }

    protected function exceptionStatusCode(Exception $error): int
    {
        $statusCode = $this->exceptions()[get_class($error)] ?? null;

        if (null !== $statusCode) {
            return $statusCode;
        }

        if ($error instanceof DomainError) {
            return Response::HTTP_BAD_REQUEST;
        }

        if ($error instanceof UnauthorizedException) {
            return Response::HTTP_FORBIDDEN;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    protected function exceptionTitle(Exception $error): string
    {
        return match (true) {
            $error instanceof UnauthorizedException => 'Unauthorized',
            $this->exceptionStatusCode($error) === Response::HTTP_NOT_FOUND => 'Not Found',
            $this->exceptionStatusCode($error) === Response::HTTP_BAD_REQUEST => 'Bad Request',
            $this->exceptionStatusCode($error) === Response::HTTP_FORBIDDEN => 'Forbidden',
            default => 'Internal Server Error',
        };
    }

    protected function exceptionDetail(Exception $error): string
    {
        if ($error instanceof DomainError) {
            return $error->getMessage();
        }

        if ($error instanceof UnauthorizedException) {
            return 'You do not have permission to access this resource.';
        }

        return $this->exceptionStatusCode($error) >= 500
            ? 'An Unexpected Error Occurred'
            : $error->getMessage();
    }

    /**
     * @return array<class-string, int>
     */
    protected function exceptions(): array
    {
        return [];
    }
}
