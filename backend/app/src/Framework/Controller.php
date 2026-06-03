<?php

namespace App\Framework;

class Controller
{
    public function __construct()
    {
    }

    protected function sendSuccessResponse(mixed $data = [], int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    protected function sendErrorResponse(string $message, int $code = 500): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode(['error' => $message], JSON_PRETTY_PRINT);
    }

    protected function mapPostDataToClass(string $className): object
    {
        $input = file_get_contents('php://input');
        $data  = json_decode($input, true);

        if (!is_array($data)) {
            throw new \InvalidArgumentException('Request body must contain valid JSON');
        }

        $instance = new $className();
        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }

        return $instance;
    }

    protected function getJsonBody(): array
    {
        $input = file_get_contents('php://input');
        $data  = json_decode($input, true);

        if (!is_array($data)) {
            throw new \InvalidArgumentException('Request body must contain valid JSON');
        }

        return $data;
    }
}
