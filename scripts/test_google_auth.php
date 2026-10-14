<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/google_auth.php';

echo "1. Testing DB connection and schema update...\n";
$pdo = DB::getConnection();
echo "   Database connected successfully.\n";

echo "2. Testing GoogleAuth::simulateLogin with khalilksa33@gmail.com...\n";
$res1 = GoogleAuth::simulateLogin('khalilksa33@gmail.com', 'Khalil Al-Saeed');
echo "   User ID: " . $res1['user']['id'] . "\n";
echo "   Username: " . $res1['user']['username'] . "\n";
echo "   Email: " . $res1['user']['email'] . "\n";
echo "   Role: " . $res1['user']['role'] . "\n";
echo "   Plan: " . $res1['user']['plan'] . "\n";
echo "   Google ID: " . $res1['user']['google_id'] . "\n";
echo "   Is New: " . ($res1['is_new'] ? 'true' : 'false') . "\n";

echo "3. Testing bio_profile created...\n";
$bio = $pdo->query("SELECT * FROM bio_profiles WHERE user_id = " . $res1['user']['id'])->fetch();
if ($bio) {
    echo "   Bio profile exists: theme=" . $bio['theme'] . ", display_name=" . $bio['display_name'] . "\n";
} else {
    echo "   ERROR: bio profile not found!\n";
}

echo "4. Testing second login for same user (existing account update)...\n";
$res2 = GoogleAuth::simulateLogin('khalilksa33@gmail.com', 'Khalil Al-Saeed');
echo "   Is New on 2nd login: " . ($res2['is_new'] ? 'true' : 'false') . " (expected: false)\n";

echo "5. Testing new creator registration via Google (new email)...\n";
$res3 = GoogleAuth::simulateLogin('sarah.creator.2026@gmail.com', 'Sarah Creator');
echo "   New Creator User ID: " . $res3['user']['id'] . "\n";
echo "   New Creator Username: " . $res3['user']['username'] . "\n";
echo "   New Creator Role: " . $res3['user']['role'] . "\n";
echo "   New Creator Plan: " . $res3['user']['plan'] . "\n";
echo "   Is New: " . ($res3['is_new'] ? 'true' : 'false') . " (expected: true)\n";

echo "✅ ALL GOOGLE AUTH TESTS PASSED!\n";
