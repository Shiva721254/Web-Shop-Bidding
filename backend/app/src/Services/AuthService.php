<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\IUserRepository;
use App\Repositories\UserRepository;
use App\Services\Interfaces\IAuthService;
use App\Framework\Auth;

class AuthService implements IAuthService
{
    private IUserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function register(string $name, string $email, string $password): array
    {
        if ($this->userRepository->emailExists($email)) {
            throw new \InvalidArgumentException('Email is already registered', 409);
        }

        $user           = new User();
        $user->name     = $name;
        $user->email    = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->role     = 'customer';

        $user  = $this->userRepository->create($user);
        $token = Auth::generateToken($user->id, $user->email, $user->role);

        return [
            'token' => $token,
            'user'  => $user->toPublicArray(),
        ];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !password_verify($password, $user->password)) {
            throw new \InvalidArgumentException('Invalid email or password', 401);
        }

        $token = Auth::generateToken($user->id, $user->email, $user->role);

        return [
            'token' => $token,
            'user'  => $user->toPublicArray(),
        ];
    }

    public function getUser(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }
}
