<?php

namespace App\Models;

class User
{
    public ?int    $id        = null;
    public string  $name      = '';
    public string  $email     = '';
    public string  $password  = '';
    public string  $role      = 'customer';
    public ?string $createdAt = null;

    public function __construct(array $data = [])
    {
        $this->id        = isset($data['id'])         ? (int)$data['id'] : null;
        $this->name      = $data['name']              ?? '';
        $this->email     = $data['email']             ?? '';
        $this->password  = $data['password']          ?? '';
        $this->role      = $data['role']              ?? 'customer';
        $this->createdAt = $data['created_at']        ?? null;
    }

    public function toPublicArray(): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'role'      => $this->role,
            'createdAt' => $this->createdAt,
        ];
    }
}
