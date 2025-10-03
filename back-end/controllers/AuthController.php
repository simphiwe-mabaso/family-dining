<?php
namespace Controllers;

use Core\Request;
use Core\Response;
use Core\JWT;
use Services\AuthService;
use Services\EmailService;
use Models\User;

class AuthController {
    private $authService;
    private $emailService;
    
    public function __construct() {
        $this->authService = new AuthService();
        $this->emailService = new EmailService();
    }
    
    public function register(Request $request, Response $response) {
        try {
            $data = $request->getBody();
            
            // Validate input
            $validation = $this->validateRegistration($data);
            if (!$validation['valid']) {
                return $response->json([
                    'error' => true,
                    'message' => 'Validation failed',
                    'errors' => $validation['errors']
                ], 400);
            }
            
            // Check if user exists
            if ($this->authService->userExists($data['email'])) {
                return $response->json([
                    'error' => true,
                    'message' => 'User already exists'
                ], 409);
            }
            
            // Create user
            $user = $this->authService->createUser($data);
            
            // Generate tokens
            $tokens = $this->authService->generateTokens($user);
            
            // Send welcome email
            $this->emailService->sendWelcomeEmail($user);
            
            return $response->json([
                'success' => true,
                'message' => 'Registration successful',
                'user' => $this->sanitizeUser($user),
                'token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token']
            ], 201);
            
        } catch (\Exception $e) {
            return $response->json([
                'error' => true,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function login(Request $request, Response $response) {
        try {
            $data = $request->getBody();
            
            // Validate credentials
            $user = $this->authService->validateCredentials(
                $data['email'], 
                $data['password']
            );
            
            if (!$user) {
                return $response->json([
                    'error' => true,
                    'message' => 'Invalid credentials'
                ], 401);
            }
            
            // Check if user is active
            if (!$user['is_active']) {
                return $response->json([
                    'error' => true,
                    'message' => 'Account is deactivated'
                ], 403);
            }
            
            // Generate tokens
            $tokens = $this->authService->generateTokens($user);
            
            // Log activity
            $this->authService->logActivity($user['id'], 'login', [
                'ip' => $request->getIp(),
                'user_agent' => $request->getUserAgent()
            ]);
            
            return $response->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $this->sanitizeUser($user),
                'token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token']
            ]);
            
        } catch (\Exception $e) {
            return $response->json([
                'error' => true,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function refresh(Request $request, Response $response) {
        try {
            $refreshToken = $request->getBody()['refresh_token'];
            
            // Validate refresh token
            $tokens = $this->authService->refreshTokens($refreshToken);
            
            if (!$tokens) {
                return $response->json([
                    'error' => true,
                    'message' => 'Invalid refresh token'
                ], 401);
            }
            
            return $response->json([
                'success' => true,
                'token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token']
            ]);
            
        } catch (\Exception $e) {
            return $response->json([
                'error' => true,
                'message' => 'Token refresh failed'
            ], 500);
        }
    }
    
    public function logout(Request $request, Response $response) {
        try {
            $user = $request->getAttribute('user');
            
            // Invalidate tokens
            $this->authService->logout($user['id']);
            
            return $response->json([
                'success' => true,
                'message' => 'Logout successful'
            ]);
            
        } catch (\Exception $e) {
            return $response->json([
                'error' => true,
                'message' => 'Logout failed'
            ], 500);
        }
    }
    
    public function forgotPassword(Request $request, Response $response) {
        try {
            $email = $request->getBody()['email'];
            
            // Find user
            $user = User::findByEmail($email);
            
            if (!$user) {
                // Don't reveal if user exists
                return $response->json([
                    'success' => true,
                    'message' => 'If the email exists, a reset link has been sent'
                ]);
            }
            
            // Generate reset token
            $resetToken = $this->authService->generatePasswordResetToken($user);
            
            // Send reset email
            $this->emailService->sendPasswordResetEmail($user, $resetToken);
            
            return $response->json([
                'success' => true,
                'message' => 'If the email exists, a reset link has been sent'
            ]);
            
        } catch (\Exception $e) {
            return $response->json([
                'error' => true,
                'message' => 'Password reset failed'
            ], 500);
        }
    }
    
    private function validateRegistration($data) {
        $errors = [];
        
        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        // Password validation
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        
        // Name validation
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required';
        }
        
        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    private function sanitizeUser($user) {
        unset($user['password']);
        unset($user['refresh_token']);
        return $user;
    }
}