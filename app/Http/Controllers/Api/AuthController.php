<?php

namespace App\Http\Controllers\Api;

use App\Application\UseCases\Auth\LoginUseCase;
use App\Application\UseCases\Auth\LogoutUseCase;
use App\Domain\DTOs\LoginDTO;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AuthController extends Controller
{
    public function __construct(
        private LoginUseCase $loginUseCase,
        private LogoutUseCase $logoutUseCase,
        private UserRepositoryInterface $userRepository
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = LoginDTO::fromArray($request->validated());
            $result = $this->loginUseCase->execute($dto);

            return $this->sendResponse(
                new AuthResource($result),
                'Login successful'
            );
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->findById($request->user()->id);
            $this->logoutUseCase->execute($user);

            return $this->sendResponse(null, 'Logged out successfully');
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->findById($request->user()->id);
            if (! $user) {
                return $this->sendError('User not found', 404);
            }

            return $this->sendResponse(
                new \App\Http\Resources\UserResource($user),
                'Authenticated user retrieved successfully'
            );
        } catch (Throwable $e) {
            return $this->sendError($e->getMessage(), 401);
        }
    }
}
