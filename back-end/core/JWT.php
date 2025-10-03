<?php
namespace Core;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT {
    private static $secret;
    private static $algorithm = 'HS256';
    
    public static function init() {
        self::$secret = $_ENV['JWT_SECRET'];
    }
    
    public static function encode($payload) {
        $issuedAt = time();
        $expire = $issuedAt + (int)$_ENV['JWT_EXPIRATION'];
        
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expire;
        $payload['iss'] = $_ENV['APP_URL'];
        
        return FirebaseJWT::encode($payload, self::$secret, self::$algorithm);
    }
    
    public static function decode($token) {
        try {
            return FirebaseJWT::decode($token, new Key(self::$secret, self::$algorithm));
        } catch (\Exception $e) {
            throw new \Exception('Invalid token: ' . $e->getMessage());
        }
    }
    
    public static function generateRefreshToken() {
        return bin2hex(random_bytes(32));
    }
}