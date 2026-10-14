<?php
/**
 * Authentication & Session Management
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

class Auth {
    public static function check(): bool {
        return !empty($_SESSION['user_id']);
    }

    public static function id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function user(): ?array {
        if (!self::check()) {
            return null;
        }

        static $cachedUser = null;
        if ($cachedUser !== null) {
            return $cachedUser;
        }

        $pdo = DB::getConnection();
        $stmt = $pdo->prepare("SELECT id, name, username, email, plan, avatar_url, bio, created_at FROM users WHERE id = ?");
        $stmt->execute([self::id()]);
        $cachedUser = $stmt->fetch() ?: null;

        if (!$cachedUser) {
            self::logout();
        }

        return $cachedUser;
    }

    public static function login(array $user): void {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_plan'] = $user['plan'];
    }

    public static function logout(): void {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_username'], $_SESSION['user_plan']);
        session_regenerate_id(true);
    }

    public static function requireAuth(): void {
        if (!self::check()) {
            set_flash('error', 'Please log in to access this tool.');
            redirect('login');
        }
    }
}
