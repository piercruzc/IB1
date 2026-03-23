SET NAMES utf8mb4;

-- Blog Admin System - Database Init Script

CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(280) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT,
    image_path VARCHAR(500),
    category_id INT,
    author VARCHAR(100) DEFAULT 'IBM FinTech',
    is_featured TINYINT(1) DEFAULT 0,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed categories
INSERT INTO blog_categories (name, slug) VALUES
    ('Educación Financiera', 'educacion-financiera'),
    ('Tendencias', 'tendencias'),
    ('Marco Legal', 'marco-legal'),
    ('Estrategias', 'estrategias'),
    ('Tecnología', 'tecnologia'),
    ('Análisis de Mercado', 'analisis-de-mercado');

-- Seed admin user (password: Admin@2025!)
INSERT INTO admin_users (username, password_hash) VALUES
    ('admin', '$2y$12$4n9U.6.Tljz7Gii/YRHlVuwcHjkuRML7r89eYJbOUIhVyUunto.rq');

-- Blog settings (key-value)
CREATE TABLE IF NOT EXISTS blog_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO blog_settings (setting_key, setting_value) VALUES
    ('author_name', 'IB1 Fintech'),
    ('author_bio', 'Especialistas en inversiones digitales y asesoría financiera. Ayudamos a personas y empresas a hacer crecer su patrimonio de manera segura y transparente.'),
    ('author_avatar', ''),
    ('author_facebook', 'https://www.facebook.com/share/1LfqRFXCco/'),
    ('author_instagram', 'https://www.instagram.com/ibm.fintech?igsh=Nmc1ajdibjNwc3g5'),
    ('author_linkedin', 'https://www.linkedin.com/company/ibm-fintech');
