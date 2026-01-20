<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Generate JWT Token
 */
if (!function_exists('generateJWT')) {
    function generateJWT($userId, $email)
    {
        $key = getenv('JWT_SECRET') ?: 'your_secret_key_change_this';
        $issuedAt = time();
        $expirationTime = $issuedAt + 86400; // 24 hours

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'user_id' => $userId,
            'email' => $email
        ];

        return JWT::encode($payload, $key, 'HS256');
    }
}

/**
 * Decode JWT Token
 */
if (!function_exists('decodeJWT')) {
    function decodeJWT($token)
    {
        try {
            $key = getenv('JWT_SECRET') ?: 'your_secret_key_change_this';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            log_message('error', 'JWT decode error: ' . $e->getMessage());
            return null;
        }
    }
}

/**
 * Verify JWT Token
 */
if (!function_exists('verifyJWT')) {
    function verifyJWT($token)
    {
        try {
            $decoded = decodeJWT($token);
            
            if (!$decoded) {
                return false;
            }

            // Check if token has expired
            if (isset($decoded->exp) && $decoded->exp < time()) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}