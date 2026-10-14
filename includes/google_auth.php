<?php
/**
 * Google OAuth 2.0 Authentication Service
 * Pure Native PHP 8.1+ Zero-Dependency Implementation
 * iLoveDigital SaaS Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

class GoogleAuth {

    /**
     * Check if Google OAuth credentials are configured
     */
    public static function isConfigured(): bool {
        return !empty(GOOGLE_CLIENT_ID) && !empty(GOOGLE_CLIENT_SECRET);
    }

    /**
     * Build Google OAuth 2.0 Authorization URL
     */
    public static function getAuthUrl(string $intent = 'login'): string {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        $_SESSION['oauth_intent'] = $intent;

        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * Handle the OAuth callback code exchange & profile fetch
     */
    public static function handleCallback(string $code, string $state): array {
        if (empty($_SESSION['oauth_state']) || !hash_equals($_SESSION['oauth_state'], $state)) {
            throw new Exception('Invalid OAuth state parameter. Cross-site request forgery protection failed.');
        }
        unset($_SESSION['oauth_state']);

        // 1. Exchange authorization code for tokens via Google Token endpoint
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'grant_type' => 'authorization_code',
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            $errData = json_decode($response, true);
            $errMsg = $errData['error_description'] ?? $errData['error'] ?? $curlError ?: "HTTP $httpCode";
            throw new Exception('Google token exchange failed: ' . $errMsg);
        }

        $tokenData = json_decode($response, true);
        $accessToken = $tokenData['access_token'] ?? null;
        if (!$accessToken) {
            throw new Exception('Google OAuth response did not contain an access token.');
        }

        // 2. Fetch user profile from Google UserInfo endpoint
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo';
        $chUser = curl_init($userInfoUrl);
        curl_setopt($chUser, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chUser, CURLOPT_TIMEOUT, 15);
        curl_setopt($chUser, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($chUser, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json'
        ]);

        $userResponse = curl_exec($chUser);
        $userHttpCode = curl_getinfo($chUser, CURLINFO_HTTP_CODE);
        $userCurlError = curl_error($chUser);
        curl_close($chUser);

        if ($userCurlError || $userHttpCode !== 200) {
            throw new Exception('Failed to fetch Google user profile: ' . ($userCurlError ?: "HTTP $userHttpCode"));
        }

        $profile = json_decode($userResponse, true);
        if (empty($profile['email'])) {
            throw new Exception('Google profile did not contain a verified email address.');
        }

        return self::loginOrCreateUser([
            'google_id' => $profile['sub'] ?? '',
            'email' => strtolower(trim($profile['email'])),
            'name' => trim($profile['name'] ?? explode('@', $profile['email'])[0]),
            'avatar_url' => $profile['picture'] ?? '',
        ]);
    }

    /**
     * Find existing user or auto-register a new creator via Google data
     */
    public static function loginOrCreateUser(array $googleData): array {
        $pdo = DB::getConnection();
        $email = strtolower(trim($googleData['email']));
        $googleId = !empty($googleData['google_id']) ? $googleData['google_id'] : null;
        $name = !empty($googleData['name']) ? trim($googleData['name']) : 'Digital Creator';
        $avatar = !empty($googleData['avatar_url']) ? $googleData['avatar_url'] : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80';

        // 1. Check if user already exists with this google_id OR email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (google_id IS NOT NULL AND google_id = ?) OR email = ?");
        $stmt->execute([$googleId, $email]);
        $user = $stmt->fetch();

        if ($user) {
            $updates = [];
            $params = [];

            if (empty($user['google_id']) && !empty($googleId)) {
                $updates[] = "google_id = ?";
                $params[] = $googleId;
            }
            if (empty($user['avatar_url']) && !empty($avatar)) {
                $updates[] = "avatar_url = ?";
                $params[] = $avatar;
            }
            // Auto-grant superadmin to platform owner khalilksa33@gmail.com
            if ($email === 'khalilksa33@gmail.com' && ($user['role'] ?? '') !== 'superadmin') {
                $updates[] = "role = 'superadmin'";
            }

            if (!empty($updates)) {
                $params[] = $user['id'];
                $updSql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
                $pdo->prepare($updSql)->execute($params);

                // Reload fresh user data
                $rStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $rStmt->execute([$user['id']]);
                $user = $rStmt->fetch();
            }

            // Ensure bio profile exists
            $bCheck = $pdo->prepare("SELECT id FROM bio_profiles WHERE user_id = ?");
            $bCheck->execute([$user['id']]);
            if (!$bCheck->fetch()) {
                $insBio = $pdo->prepare("INSERT INTO bio_profiles (user_id, theme, display_name, bio_text, avatar_url, social_links, is_published)
                                         VALUES (?, ?, ?, ?, ?, ?, 1)");
                $insBio->execute([$user['id'], 'midnight', $user['name'], $user['bio'] ?: 'Digital Creator', $user['avatar_url'], '{}']);
            }

            Auth::login($user);
            return ['user' => $user, 'is_new' => false];
        }

        // 2. User does not exist, auto-register new account
        $pdo->beginTransaction();
        try {
            // Generate clean unique username
            $prefix = explode('@', $email)[0];
            $baseUsername = strtolower(slugify($prefix));
            if (empty($baseUsername)) {
                $baseUsername = strtolower(slugify($name)) ?: 'creator';
            }

            $username = $baseUsername;
            $counter = 1;
            while (true) {
                $uCheck = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $uCheck->execute([$username]);
                if (!$uCheck->fetch()) {
                    break;
                }
                $username = $baseUsername . $counter;
                $counter++;
            }

            // Determine role and plan
            $isSuper = ($email === 'khalilksa33@gmail.com');
            $role = $isSuper ? 'superadmin' : 'user';
            $plan = $isSuper ? 'pro' : 'free';
            $randomPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            $bio = 'Digital Creator & Content Producer. Powered by iLoveDigital.';

            $insStmt = $pdo->prepare("INSERT INTO users (name, username, email, password_hash, google_id, plan, role, avatar_url, bio)
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insStmt->execute([$name, $username, $email, $randomPassword, $googleId, $plan, $role, $avatar, $bio]);
            $newUserId = $pdo->lastInsertId();

            // Seed bio profile
            $insBio = $pdo->prepare("INSERT INTO bio_profiles (user_id, theme, display_name, bio_text, avatar_url, social_links, is_published)
                                     VALUES (?, ?, ?, ?, ?, ?, 1)");
            $socials = json_encode(['youtube' => '', 'instagram' => '', 'tiktok' => '', 'twitter' => '']);
            $insBio->execute([$newUserId, 'midnight', $name, $bio, $avatar, $socials]);

            $pdo->commit();

            // Fetch created user
            $getNew = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $getNew->execute([$newUserId]);
            $newUser = $getNew->fetch();

            Auth::login($newUser);
            return ['user' => $newUser, 'is_new' => true];
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Instant Demo / Simulation mode for fast testing without Google API credentials
     */
    public static function simulateLogin(string $email, ?string $name = null): array {
        $email = strtolower(trim($email));
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = 'khalilksa33@gmail.com';
        }

        $parts = explode('@', $email);
        $displayName = $name ?: ucwords(str_replace(['.', '_', '-'], ' ', $parts[0]));
        $simGoogleId = 'google_sim_' . md5($email);
        $simAvatar = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80';

        return self::loginOrCreateUser([
            'google_id' => $simGoogleId,
            'email' => $email,
            'name' => $displayName,
            'avatar_url' => $simAvatar
        ]);
    }
}
