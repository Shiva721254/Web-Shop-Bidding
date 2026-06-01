<?php

namespace App\Services\Interfaces;

use App\Models\User;

interface IAuthService
{
    public function register(string $name, string $email, string $password): array;
    public function login(string $email, string $password): array;
    public function getUser(int $id): ?User;
}
