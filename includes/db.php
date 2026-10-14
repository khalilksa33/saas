<?php
/**
 * Database Handler & Auto-Migration
 */
require_once __DIR__ . '/../config/config.php';

class DB {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                if (DB_CONNECTION === 'sqlite') {
                    $dbPath = DB_DATABASE;
                    $dbDir = dirname($dbPath);
                    if (!is_dir($dbDir)) {
                        @mkdir($dbDir, 0755, true);
                    }
                    $isNew = !file_exists($dbPath) || filesize($dbPath) === 0;

                    self::$instance = new PDO("sqlite:" . $dbPath);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    self::$instance->exec("PRAGMA foreign_keys = ON;");

                    if ($isNew) {
                        self::initializeSchema(self::$instance);
                    }
                } else {
                    // MySQL / Hostinger MariaDB
                    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_DATABASE . ";charset=utf8mb4";
                    self::$instance = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);

                    // Check if tables exist, if not initialize
                    $stmt = self::$instance->query("SHOW TABLES LIKE 'users'");
                    if ($stmt->rowCount() === 0) {
                        self::initializeSchema(self::$instance);
                    }
                }
            } catch (PDOException $e) {
                die("Database Connection Error: " . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$instance;
    }

    private static function initializeSchema(PDO $pdo): void {
        $schemaFile = __DIR__ . '/../config/schema.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            
            // If MySQL, adjust AUTOINCREMENT to AUTO_INCREMENT
            if (DB_CONNECTION === 'mysql') {
                $sql = str_replace('AUTOINCREMENT', 'AUTO_INCREMENT', $sql);
            }

            // Split and run statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    try {
                        $pdo->exec($statement);
                    } catch (PDOException $e) {
                        // ignore if table exists
                    }
                }
            }

            // Seed Demo User & Sample Content
            self::seedDemoData($pdo);
        }
    }

    private static function seedDemoData(PDO $pdo): void {
        try {
            $check = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            if ($check == 0) {
                $demoPassword = password_hash('demo123', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password_hash, plan, avatar_url, bio) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    'Alex Rivera',
                    'alexcreator',
                    'demo@creatorpulse.com',
                    $demoPassword,
                    'pro',
                    'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80',
                    'Digital Creator & Visual Storyteller. Sharing tech insights, creator workflows, and creative assets.'
                ]);
                $userId = $pdo->lastInsertId();

                // Seed Bio Profile
                $socials = json_encode([
                    'youtube' => 'https://youtube.com',
                    'instagram' => 'https://instagram.com',
                    'tiktok' => 'https://tiktok.com',
                    'twitter' => 'https://x.com',
                    'spotify' => 'https://spotify.com'
                ]);

                $profileStmt = $pdo->prepare("INSERT INTO bio_profiles (user_id, theme, display_name, bio_text, avatar_url, social_links, is_published, views_count)
                                              VALUES (?, ?, ?, ?, ?, ?, 1, 1420)");
                $profileStmt->execute([
                    $userId,
                    'midnight',
                    'Alex Rivera ⚡',
                    'Building in public • Free Lightroom Presets & Creator Assets below 👇',
                    'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80',
                    $socials
                ]);
                $profileId = $pdo->lastInsertId();

                // Seed Bio Links
                $links = [
                    ['🎥 Latest YouTube: How I Grew to 100k in 6 Months', 'https://youtube.com', 'video', '🔥 HOT', 'pulse', 684, 1],
                    ['🎨 Free Cinematic Lightroom & LUTs Pack', 'https://gumroad.com', 'gift', 'FREE', 'none', 945, 2],
                    ['🎙️ Creative Pulse Podcast (Episode #42 Out Now)', 'https://spotify.com', 'headphones', 'NEW', 'shake', 312, 3],
                    ['💻 My 2026 Camera & Studio Gear Checklist', 'https://kit.co', 'camera', '', 'none', 520, 4],
                    ['📬 Join The 10k Weekly Creator Newsletter', 'https://substack.com', 'mail', '', 'none', 418, 5]
                ];

                $linkStmt = $pdo->prepare("INSERT INTO bio_links (profile_id, title, url, icon, badge, animation, clicks_count, sort_order)
                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($links as $link) {
                    $linkStmt->execute([
                        $profileId, $link[0], $link[1], $link[2], $link[3], $link[4], $link[5], $link[6]
                    ]);
                }

                // Seed Smart Link
                $shortStmt = $pdo->prepare("INSERT INTO short_links (user_id, code, target_url, title, clicks_count, utm_source, utm_medium, utm_campaign)
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $shortStmt->execute([
                    $userId,
                    'lutpack26',
                    'https://gumroad.com/l/cinematic-pack',
                    'Cinematic LUTs Pack 2026',
                    389,
                    'youtube',
                    'video_description',
                    'spring_launch'
                ]);
            }
        } catch (Exception $e) {
            // Keep going if seed encounters issue
        }
    }
}
