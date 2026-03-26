<?php

namespace App\Application\UseCases\Auth;

use App\Domain\DTOs\LoginDTO;
use App\Domain\Repositories\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;

class LoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(LoginDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (! $user || ! Hash::check($dto->password, $user->getPassword())) {
            throw new Exception('Invalid credentials');
        }

        return [
            'user' => $user,
            'token' => $this->userRepository->createApiToken($user->getId(), 'auth-token'),
        ];
    }
}
