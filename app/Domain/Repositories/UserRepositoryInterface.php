<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function findById(int $id): ?User;

    public function create(array $data): User;

    public function update(int $id, array $data): bool;

    public function createApiToken(int $userId, string $name): string;

    public function revokeAllTokens(int $userId): void;
}
