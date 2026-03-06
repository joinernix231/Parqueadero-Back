<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Auth\LoginAction;
use App\Application\Actions\Auth\LogoutAction;
use App\Domain\DTOs\LoginDTO;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private LoginAction $loginAction,
        private LogoutAction $logoutAction,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function login(LoginRequest $request): JsonResponse|AuthResource
    {
        try {
            $dto = LoginDTO::fromArray($request->validated());
            $result = $this->loginAction->execute($dto);
            return new AuthResource($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $this->userRepository->findById($request->user()->id);
            $this->logoutAction->execute($user);
            return response()->json([
                'message' => 'Sesión cerrada exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function me(Request $request): JsonResponse|\App\Http\Resources\UserResource
    {
        try {
            $userEntity = $this->userRepository->findById($request->user()->id);
            if (!$userEntity) {
                return response()->json([
                    'message' => 'Usuario no encontrado',
                ], 404);
            }
            return new \App\Http\Resources\UserResource($userEntity);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        }
    }
}




