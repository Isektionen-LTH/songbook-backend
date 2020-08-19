CREATE DATABASE IF NOT EXISTS songbook DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci;

USE songbook;

CREATE TABLE IF NOT EXISTS songs (
    uuid CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    melody VARCHAR(255) NULL,
    comment TEXT NULL,
    categories VARCHAR(500) NOT NULL,
    lyrics TEXT NOT NULL,
    media VARCHAR(500) NOT NULL,
    PRIMARY KEY (uuid)
);

CREATE TABLE IF NOT EXISTS categories (
    uuid CHAR(36) NOT NULL,
    name VARCHAR(250) NOT NULL,
    slug VARCHAR(250) NOT NULL,
    PRIMARY KEY (uuid),
    UNIQUE INDEX unique_slug (slug(250))
);

CREATE TABLE IF NOT EXISTS media (
    hash CHAR(64) NOT NULL,
    mime VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    PRIMARY KEY (hash)
);
