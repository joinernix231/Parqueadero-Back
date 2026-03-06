<?php

namespace App\Application\UseCases\Auth;

use App\Domain\DTOs\LoginDTO;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class LoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(LoginDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->getPassword())) {
            throw new \Exception('Credenciales inválidas');
        }

        // Crear token Sanctum
        $token = $this->createToken($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    private function createToken(User $user): string
    {
        // El token se creará usando el modelo Eloquent
        $eloquentUser = \App\Models\User::find($user->getId());
        return $eloquentUser->createToken('auth-token')->plainTextToken;
    }
}





