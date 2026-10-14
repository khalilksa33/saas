<?php
/**
 * API: Smart Short Links & UTM Campaign Generator
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

if ($action === 'create') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        json_response(['success' => false, 'message' => 'Invalid JSON'], 400);
    }

    $targetUrl = filter_var(trim($input['target_url'] ?? ''), FILTER_SANITIZE_URL);
    if (!filter_var($targetUrl, FILTER_VALIDATE_URL)) {
        json_response(['success' => false, 'message' => 'Please provide a valid destination URL'], 422);
    }

    $title = htmlspecialchars(trim($input['title'] ?? 'Smart Link'), ENT_QUOTES, 'UTF-8');
    $code = trim($input['code'] ?? '');
    
    // Generate slug if empty
    if (empty($code)) {
        $code = substr(bin2hex(random_bytes(4)), 0, 6);
    } else {
        $code = slugify($code);
    }

    // Check unique code
    $check = $pdo->prepare("SELECT id FROM short_links WHERE code = ?");
    $check->execute([$code]);
    if ($check->fetch()) {
        json_response(['success' => false, 'message' => 'This custom alias/code is already in use. Please try another.'], 422);
    }

    $utmSource = htmlspecialchars(trim($input['utm_source'] ?? ''), ENT_QUOTES, 'UTF-8');
    $utmMedium = htmlspecialchars(trim($input['utm_medium'] ?? ''), ENT_QUOTES, 'UTF-8');
    $utmCampaign = htmlspecialchars(trim($input['utm_campaign'] ?? ''), ENT_QUOTES, 'UTF-8');

    try {
        $stmt = $pdo->prepare("INSERT INTO short_links (user_id, code, target_url, title, utm_source, utm_medium, utm_campaign)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $code, $targetUrl, $title, $utmSource, $utmMedium, $utmCampaign]);

        $shortUrl = url('s/' . $code);
        json_response([
            'success' => true,
            'message' => 'Short link created successfully!',
            'data' => [
                'code' => $code,
                'short_url' => $shortUrl,
                'target_url' => $targetUrl,
                'title' => $title
            ]
        ]);
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }

} elseif ($action === 'delete') {
    $id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM short_links WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    json_response(['success' => true, 'message' => 'Link removed']);
}
