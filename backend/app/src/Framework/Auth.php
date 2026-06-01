<?php

namespace App\Framework;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
    private static string $algorithm = 'HS256';

    private static function secret(): string
    {
        $secret = $_ENV['JWT_SECRET'] ?? 'changeme-use-env-in-production';
        if (strlen($secret) < 32) {
            throw new \RuntimeException('JWT_SECRET must be at least 32 characters');
        }
        return $secret;
    }

    public static function generateToken(int $userId, string $email, string $role): string
    {
        $now     = time();
        $payload = [
            'iat'   => $now,
            'exp'   => $now + 3600,
            'sub'   => $userId,
            'email' => $email,
            'role'  => $role,
        ];
        return JWT::encode($payload, self::secret(), self::$algorithm);
    }

    public static function validateToken(string $token): object
    {
        return JWT::decode($token, new Key(self::secret(), self::$algorithm));
    }

    public static function getTokenFromRequest(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public static function requireAuth(): object
    {
        $token = self::getTokenFromRequest();
        if (!$token) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Authorization token required']);
            exit;
        }
        try {
            return self::validateToken($token);
        } catch (\Exception $e) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid or expired token']);
            exit;
        }
    }

    public static function requireRole(string $role): object
    {
        $payload = self::requireAuth();
        if ($payload->role !== $role) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Forbidden: insufficient permissions']);
            exit;
        }
        return $payload;
    }
}
