-- Schema definition for SQLite & MySQL compatibility

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    plan VARCHAR(20) DEFAULT 'free',
    role VARCHAR(20) DEFAULT 'user',
    avatar_url VARCHAR(255) DEFAULT '',
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS bio_profiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL UNIQUE,
    theme VARCHAR(50) DEFAULT 'midnight',
    display_name VARCHAR(100) DEFAULT '',
    bio_text TEXT,
    avatar_url VARCHAR(255) DEFAULT '',
    social_links TEXT DEFAULT '{}',
    custom_css TEXT,
    is_published INTEGER DEFAULT 1,
    views_count INTEGER DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bio_links (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    title VARCHAR(150) NOT NULL,
    url VARCHAR(500) NOT NULL,
    icon VARCHAR(50) DEFAULT 'link',
    badge VARCHAR(50) DEFAULT '',
    animation VARCHAR(30) DEFAULT 'none',
    clicks_count INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(profile_id) REFERENCES bio_profiles(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS short_links (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    target_url VARCHAR(1000) NOT NULL,
    title VARCHAR(150) DEFAULT '',
    clicks_count INTEGER DEFAULT 0,
    utm_source VARCHAR(100) DEFAULT '',
    utm_medium VARCHAR(100) DEFAULT '',
    utm_campaign VARCHAR(100) DEFAULT '',
    qr_options TEXT DEFAULT '{}',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS link_clicks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    link_id INTEGER NOT NULL,
    link_type VARCHAR(20) NOT NULL, -- 'bio_link' or 'short_link'
    ip_hash VARCHAR(64) DEFAULT '',
    user_agent VARCHAR(255) DEFAULT '',
    referer VARCHAR(255) DEFAULT '',
    device VARCHAR(50) DEFAULT 'Desktop',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS creator_invoices (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    client_name VARCHAR(150) NOT NULL,
    client_email VARCHAR(150) DEFAULT '',
    items_json TEXT NOT NULL,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    currency VARCHAR(10) DEFAULT 'USD',
    status VARCHAR(30) DEFAULT 'Draft',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS saved_assets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    asset_type VARCHAR(50) NOT NULL, -- 'thumbnail', 'copy', 'preset'
    title VARCHAR(200) NOT NULL,
    data_json TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS blog_posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content TEXT NOT NULL,
    cover_image VARCHAR(500) DEFAULT '',
    author_name VARCHAR(100) DEFAULT 'iLoveDigital Team',
    category VARCHAR(50) DEFAULT 'Growth',
    tags VARCHAR(255) DEFAULT '',
    read_time VARCHAR(20) DEFAULT '5 min read',
    lang VARCHAR(10) DEFAULT 'en', -- 'en' or 'ar'
    status VARCHAR(20) DEFAULT 'published', -- 'draft' or 'published'
    views_count INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

