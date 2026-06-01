<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface IUserRepository
{
    public function findByEmail(string $email): ?User;
    public function findById(int $id): ?User;
    public function create(User $user): User;
    public function emailExists(string $email): bool;
}
