<?php

namespace App\Application\UseCases\Auth;

use App\Domain\Entities\User;

class LogoutUseCase
{
    public function execute(User $user): void
    {
        // Revocar todos los tokens del usuario
        $eloquentUser = \App\Models\User::find($user->getId());
        $eloquentUser->tokens()->delete();
    }
}





