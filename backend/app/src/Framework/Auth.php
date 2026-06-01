<?php

namespace App\Framework;

class Auth
{
    private static function secret(): string
    {
        $secret = $_ENV['JWT_SECRET'] ?? 'changeme-32-chars-minimum-secret!';
        if (strlen($secret) < 32) {
            throw new \RuntimeException('JWT_SECRET must be at least 32 characters');
        }
        return $secret;
    }

    public static function generateToken(int $userId, string $email, string $role): string
    {
        $now = time();

        $header  = self::base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = self::base64UrlEncode(json_encode([
            'iat'   => $now,
            'exp'   => $now + 3600,
            'sub'   => $userId,
            'email' => $email,
            'role'  => $role,
        ]));

        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", self::secret(), true)
        );

        return "$header.$payload.$signature";
    }

    public static function validateToken(string $token): object
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Invalid token format');
        }

        [$header, $payload, $signature] = $parts;

        $expectedSig = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", self::secret(), true)
        );

        if (!hash_equals($expectedSig, $signature)) {
            throw new \InvalidArgumentException('Invalid token signature');
        }

        $data = json_decode(self::base64UrlDecode($payload));

        if (!$data || !isset($data->exp) || $data->exp < time()) {
            throw new \InvalidArgumentException('Token has expired');
        }

        return $data;
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
        } catch (\Exception) {
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

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
