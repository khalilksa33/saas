<?php
/**
 * API: Bio Profile & Links Management
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

if (!Auth::check()) {
    json_response(['success' => false, 'message' => 'Unauthorized'], 401);
}

$action = $_GET['action'] ?? '';
$userId = Auth::id();
$pdo = DB::getConnection();

if ($action === 'save_profile') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        json_response(['success' => false, 'message' => 'Invalid JSON input'], 400);
    }

    $theme = sanitizeInput($input['theme'] ?? 'midnight');
    $displayName = sanitizeInput($input['display_name'] ?? '');
    $bioText = sanitizeInput($input['bio_text'] ?? '');
    $avatarUrl = filter_var($input['avatar_url'] ?? '', FILTER_SANITIZE_URL);
    $socials = json_encode($input['socials'] ?? []);
    $links = $input['links'] ?? [];

    try {
        $pdo->beginTransaction();

        // Check if profile exists
        $stmt = $pdo->prepare("SELECT id FROM bio_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();

        if ($profile) {
            $profileId = $profile['id'];
            $update = $pdo->prepare("UPDATE bio_profiles SET theme = ?, display_name = ?, bio_text = ?, avatar_url = ?, social_links = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $update->execute([$theme, $displayName, $bioText, $avatarUrl, $socials, $profileId]);
        } else {
            $insert = $pdo->prepare("INSERT INTO bio_profiles (user_id, theme, display_name, bio_text, avatar_url, social_links) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$userId, $theme, $displayName, $bioText, $avatarUrl, $socials]);
            $profileId = $pdo->lastInsertId();
        }

        // Replace links
        $del = $pdo->prepare("DELETE FROM bio_links WHERE profile_id = ?");
        $del->execute([$profileId]);

        $insLink = $pdo->prepare("INSERT INTO bio_links (profile_id, title, url, icon, badge, animation, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $order = 0;
        foreach ($links as $link) {
            $title = trim($link['title'] ?? '');
            $url = trim($link['url'] ?? '');
            if (!empty($title) && !empty($url)) {
                $insLink->execute([
                    $profileId,
                    $title,
                    $url,
                    $link['icon'] ?? '🔗',
                    $link['badge'] ?? '',
                    $link['animation'] ?? 'none',
                    $order++
                ]);
            }
        }

        $pdo->commit();
        json_response(['success' => true, 'message' => 'Bio profile updated successfully!']);

    } catch (Exception $e) {
        $pdo->rollBack();
        json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

function sanitizeInput(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
