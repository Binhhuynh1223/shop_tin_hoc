<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user']);
    }

    public static function isAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }

    public static function requireAuth()
    {
        if (!self::isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireAdmin()
    {
        if (!self::isAdmin()) {
            header('Location: /error');
            exit;
        }
    }
}
