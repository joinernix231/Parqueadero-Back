<?php

namespace App\Application\Actions\Auth;

use App\Application\UseCases\Auth\LogoutUseCase;
use App\Domain\Entities\User;

class LogoutAction
{
    public function __construct(
        private LogoutUseCase $logoutUseCase
    ) {
    }

    public function execute(User $user): void
    {
        $this->logoutUseCase->execute($user);
    }
}




