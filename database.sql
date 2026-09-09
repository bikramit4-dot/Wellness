-- ==========================================================================
-- Chitrawan Nature Cure Hospital — Database Schema
-- Apply with:  mysql -u root < database.sql
-- ==========================================================================

CREATE DATABASE IF NOT EXISTS wellness
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE wellness;

-- ---------------------------------------------------------------
-- Appointment bookings (submitted through the Contact page form)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS appointments (
    id          CHAR(16)     NOT NULL,
    name        VARCHAR(120) NOT NULL,
    email       VARCHAR(190) NOT NULL,
    phone       VARCHAR(40)  NOT NULL DEFAULT '',
    treatment   VARCHAR(120) NOT NULL DEFAULT '',
    date        VARCHAR(20)  NOT NULL DEFAULT '',
    time        VARCHAR(20)  NOT NULL DEFAULT '',
    message     TEXT         NULL,
    status      ENUM('new', 'confirmed', 'rejected', 'completed') NOT NULL DEFAULT 'new',
    created_at  DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_created_at (created_at),
    KEY idx_status (status)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Advance payments via QR code (submitted through the Tariff page)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS qr_payments (
    id             CHAR(16)     NOT NULL,
    name           VARCHAR(120) NOT NULL,
    email          VARCHAR(190) NOT NULL,
    phone          VARCHAR(40)  NOT NULL DEFAULT '',
    address        VARCHAR(300) NOT NULL DEFAULT '',
    package        VARCHAR(120) NOT NULL DEFAULT '',
    amount         VARCHAR(40)  NOT NULL DEFAULT '',
    transaction_id VARCHAR(120) NOT NULL DEFAULT '',
    message        TEXT         NULL,
    screenshot     VARCHAR(300) NOT NULL DEFAULT '',
    status         ENUM('new', 'verified', 'rejected') NOT NULL DEFAULT 'new',
    created_at     DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_created_at (created_at),
    KEY idx_status (status)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Therapy pages (one row per therapy, content JSON-encoded)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS therapies (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,  -- numeric id for the admin content manager
    slug        VARCHAR(80)  NOT NULL,
    category    VARCHAR(40)  NOT NULL,
    title       VARCHAR(120) NOT NULL,
    icon        VARCHAR(40)  NOT NULL DEFAULT 'icon-leaf',
    image       VARCHAR(300) NOT NULL DEFAULT '',
    intro       TEXT         NULL,
    about       TEXT         NULL,   -- JSON array of paragraphs
    methods     TEXT         NULL,   -- JSON array
    benefits    TEXT         NULL,   -- JSON array
    PRIMARY KEY (slug),
    KEY idx_category (category)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Blog posts (one row per article, content JSON-encoded)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS posts (
    slug        VARCHAR(80)  NOT NULL,
    title       VARCHAR(160) NOT NULL,
    category    VARCHAR(40)  NOT NULL DEFAULT 'Wellness',
    date        VARCHAR(30)  NOT NULL DEFAULT '',
    image       VARCHAR(300) NOT NULL DEFAULT '',
    excerpt     TEXT         NULL,
    content     TEXT         NULL,   -- JSON array of {h, p} sections
    key_points  TEXT         NULL,   -- JSON array
    PRIMARY KEY (slug)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Gallery media items (/gallery page)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS gallery (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(160) NOT NULL,
    description TEXT         NULL,
    image       VARCHAR(300) NOT NULL DEFAULT '',
    alt         VARCHAR(200) NOT NULL DEFAULT '',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Patient testimonials (home page)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS testimonials (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    role        VARCHAR(120) NOT NULL DEFAULT '',
    initials    VARCHAR(8)   NOT NULL DEFAULT '',
    avatar      VARCHAR(8)   NOT NULL DEFAULT 'a1',
    rating      TINYINT      NOT NULL DEFAULT 5,
    quote       TEXT         NULL,
    status      ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Team members (About page, "Our Group" section)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS team (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    role        VARCHAR(160) NOT NULL DEFAULT '',
    initials    VARCHAR(8)   NOT NULL DEFAULT '',
    avatar      VARCHAR(8)   NOT NULL DEFAULT 'a1',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Tariff: membership-style packages (/tariff page)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tariff_plans (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    description TEXT         NULL,
    price       VARCHAR(20)  NOT NULL DEFAULT '',
    period      VARCHAR(30)  NOT NULL DEFAULT '',
    features    TEXT         NULL,   -- JSON array of strings
    featured    TINYINT(1)   NOT NULL DEFAULT 0,
    badge       VARCHAR(60)  NOT NULL DEFAULT '',
    cta_label   VARCHAR(60)  NOT NULL DEFAULT 'Book a Session',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Tariff: individual service rate card (/tariff page)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tariff_services (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service     VARCHAR(160) NOT NULL,
    duration    VARCHAR(40)  NOT NULL DEFAULT '',
    price       VARCHAR(20)  NOT NULL DEFAULT '',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- "Why Choose Us" feature cards (home page photo carousel)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS features (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(160) NOT NULL,
    description TEXT         NULL,
    image       VARCHAR(300) NOT NULL DEFAULT '',
    icon        VARCHAR(40)  NOT NULL DEFAULT 'icon-leaf',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- "What We Offer" service cards (home page photo carousel)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS offers (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(160) NOT NULL,
    description TEXT         NULL,
    image       VARCHAR(300) NOT NULL DEFAULT '',
    icon        VARCHAR(40)  NOT NULL DEFAULT 'icon-leaf',
    link        VARCHAR(200) NOT NULL DEFAULT '/treatments',
    sort_order  INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Admin panel users (usernames + passwords, managed from the admin panel)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email         VARCHAR(190) NOT NULL DEFAULT '',
    username      VARCHAR(60)  NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    display_name  VARCHAR(120) NOT NULL DEFAULT '',
    role          ENUM('admin', 'staff') NOT NULL DEFAULT 'admin',
    created_at    DATETIME     NOT NULL,
    updated_at    DATETIME     NOT NULL,
    UNIQUE KEY uq_admin_username (username)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Editable page sections (per-page, per-section content)
-- Each row = one section of one page (e.g. home/hero, about/founder).
-- Field names are fixed; `extras` stores a JSON array of lines used for
-- checklists, stats, buttons, doctor cards, etc.
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS page_sections (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_key     VARCHAR(60)  NOT NULL,
    section_key  VARCHAR(80)  NOT NULL,
    kicker       VARCHAR(200) NOT NULL DEFAULT '',
    heading      VARCHAR(300) NOT NULL DEFAULT '',
    content      TEXT         NULL,
    sub_content  TEXT         NULL,
    image        VARCHAR(300) NOT NULL DEFAULT '',
    media        VARCHAR(300) NOT NULL DEFAULT '',
    link         VARCHAR(300) NOT NULL DEFAULT '',
    link_label   VARCHAR(160) NOT NULL DEFAULT '',
    extras       TEXT         NULL,
    UNIQUE KEY uq_page_section (page_key, section_key)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
