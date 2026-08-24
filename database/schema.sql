CREATE DATABASE teatrosvicente
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE teatrosvicente;


-- =========================
-- ADMIN
-- =========================

CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================
-- ESPETÁCULOS
-- =========================

CREATE TABLE productions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    short_description TEXT,
    description TEXT,
    year SMALLINT UNSIGNED,
    poster_path VARCHAR(500),
    status ENUM('upcoming', 'current', 'archive') NOT NULL DEFAULT 'upcoming',
    is_featured BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);


-- =========================
-- APRESENTAÇÕES
-- =========================

CREATE TABLE performances (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    production_id INT UNSIGNED NOT NULL,
    performance_date DATE NOT NULL,
    performance_time TIME,
    venue VARCHAR(200) NOT NULL,
    city VARCHAR(150),
    address VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_performance_production
        FOREIGN KEY (production_id)
        REFERENCES productions(id)
        ON DELETE CASCADE,

    INDEX idx_performances_date (performance_date)
);


-- =========================
-- PESSOAS
-- =========================

CREATE TABLE people (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    bio TEXT,
    photo_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);


-- =========================
-- FICHA ARTÍSTICA / ELENCO
-- =========================

CREATE TABLE production_credits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    production_id INT UNSIGNED NOT NULL,
    person_id INT UNSIGNED NOT NULL,
    role VARCHAR(150) NOT NULL,
    credit_type ENUM('cast', 'creative') NOT NULL,
    display_order INT UNSIGNED NOT NULL DEFAULT 0,

    CONSTRAINT fk_credit_production
        FOREIGN KEY (production_id)
        REFERENCES productions(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_credit_person
        FOREIGN KEY (person_id)
        REFERENCES people(id)
        ON DELETE CASCADE,

    UNIQUE (production_id, person_id, role),

    INDEX idx_production_credits_order (production_id, credit_type, display_order)
);


-- =========================
-- GALERIAS
-- =========================

CREATE TABLE galleries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    production_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_gallery_production
        FOREIGN KEY (production_id)
        REFERENCES productions(id)
        ON DELETE CASCADE
);


-- =========================
-- FOTOGRAFIAS
-- =========================

CREATE TABLE photos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gallery_id INT UNSIGNED NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    caption VARCHAR(500),
    alt_text VARCHAR(255),
    photographer VARCHAR(150),
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_cover BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_photo_gallery
        FOREIGN KEY (gallery_id)
        REFERENCES galleries(id)
        ON DELETE CASCADE,

    INDEX idx_photos_gallery_order (gallery_id, sort_order)
);


-- =========================
-- NOTÍCIAS
-- =========================

CREATE TABLE news (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    summary TEXT,
    content MEDIUMTEXT NOT NULL,
    cover_image_path VARCHAR(500),
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    is_featured BOOLEAN NOT NULL DEFAULT FALSE,
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_news_status_published (status, published_at)
);


-- =========================
-- PARCEIROS
-- =========================

CREATE TABLE partners (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    logo_path VARCHAR(500),
    website VARCHAR(500),
    category VARCHAR(100),
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);
