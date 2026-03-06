<?php

namespace App\Application\Actions\Auth;

use App\Application\UseCases\Auth\LoginUseCase;
use App\Domain\DTOs\LoginDTO;

class LoginAction
{
    public function __construct(
        private LoginUseCase $loginUseCase
    ) {
    }

    public function execute(LoginDTO $dto): array
    {
        return $this->loginUseCase->execute($dto);
    }
}





