<?php

namespace App\Application\UseCases\Auth;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;

class LogoutUseCase
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function execute(User $user): void
    {
        $this->userRepository->revokeAllTokens($user->getId());
    }
}
