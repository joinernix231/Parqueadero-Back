<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Models\User as UserModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        $model = UserModel::where('email', $email)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findById(int $id): ?User
    {
        $model = UserModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function create(array $data): User
    {
        $model = UserModel::create($data);
        return $this->toEntity($model);
    }

    public function update(int $id, array $data): bool
    {
        return UserModel::where('id', $id)->update($data) > 0;
    }

    private function toEntity(UserModel $model): User
    {
        // Get password directly from attributes since it's in hidden array
        $password = $model->getAttributes()['password'] ?? $model->password;
        
        return new User(
            id: $model->id,
            name: $model->name,
            email: $model->email,
            password: $password,
            role: $model->role,
            createdAt: $model->created_at?->toDateTimeString(),
            updatedAt: $model->updated_at?->toDateTimeString()
        );
    }
}

