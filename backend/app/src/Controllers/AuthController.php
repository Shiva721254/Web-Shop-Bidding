<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\Interfaces\IAuthService;
use App\Framework\Controller;

class AuthController extends Controller
{
    private IAuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register()
    {
        try {
            $body     = $this->getJsonBody();
            $name     = trim($body['name']     ?? '');
            $email    = trim($body['email']    ?? '');
            $password = $body['password']      ?? '';

            if (empty($name)) {
                return $this->sendErrorResponse('Name is required', 422);
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->sendErrorResponse('A valid email is required', 422);
            }
            if (strlen($password) < 8) {
                return $this->sendErrorResponse('Password must be at least 8 characters', 422);
            }

            $result = $this->authService->register($name, $email, $password);
            return $this->sendSuccessResponse($result, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), $e->getCode() ?: 422);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function login()
    {
        try {
            $body     = $this->getJsonBody();
            $email    = trim($body['email']    ?? '');
            $password = $body['password']      ?? '';

            if (empty($email)) {
                return $this->sendErrorResponse('Email is required', 422);
            }
            if (empty($password)) {
                return $this->sendErrorResponse('Password is required', 422);
            }

            $result = $this->authService->login($email, $password);
            return $this->sendSuccessResponse($result);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), $e->getCode() ?: 401);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }
}
