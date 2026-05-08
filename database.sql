CREATE DATABASE chashibondhu;

*** ----- Contact Form ----- ***

CREATE TABLE contact_messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    phone       VARCHAR(20) DEFAULT NULL,
    role        ENUM('consumer', 'farmer', 'other') DEFAULT NULL,
    subject     VARCHAR(255) NOT NULL,
    message     TEXT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* Farmer Registration */

CREATE TABLE farmers (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)  NOT NULL,
    email           VARCHAR(150)  NOT NULL UNIQUE,
    phone           VARCHAR(20)   NOT NULL,
    district        VARCHAR(50)   NOT NULL,
    land_size       VARCHAR(30)   NOT NULL,
    crops           VARCHAR(255)  NOT NULL,
    nid             VARCHAR(30)   NOT NULL UNIQUE,
    password        VARCHAR(255)  NOT NULL,
    is_verified     TINYINT(1)    DEFAULT 0,
    status          ENUM('pending','active','suspended') DEFAULT 'pending',
    created_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


