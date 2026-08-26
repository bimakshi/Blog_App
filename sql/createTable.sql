-- =========================================================
-- BLOGNEST DATABASE
-- =========================================================

-- Use the existing database
USE blog_app;


-- =========================================================
-- USERS TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS users (

    id INT AUTO_INCREMENT PRIMARY KEY,

    username VARCHAR(50) NOT NULL UNIQUE,

    email VARCHAR(150) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB;


-- =========================================================
-- CATEGORIES TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS categories (

    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL UNIQUE

) ENGINE=InnoDB;


-- =========================================================
-- DEFAULT CATEGORIES
-- =========================================================

INSERT IGNORE INTO categories (name) VALUES
('Technology'),
('Education'),
('Travel'),
('Lifestyle'),
('Personal'),
('Programming');


-- =========================================================
-- BLOGS TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS blogs (

    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    category_id INT NULL,

    title VARCHAR(255) NOT NULL,

    content TEXT NOT NULL,

    image VARCHAR(255) DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NULL DEFAULT NULL,


    -- User relationship
    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,


    -- Category relationship
    FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE SET NULL

) ENGINE=InnoDB;


-- =========================================================
-- LIKES TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS likes (

    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    blog_id INT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,


    -- One user can like a story only once
    UNIQUE KEY unique_user_blog_like (
        user_id,
        blog_id
    ),


    -- User relationship
    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,


    -- Blog relationship
    FOREIGN KEY (blog_id)
        REFERENCES blogs(id)
        ON DELETE CASCADE

) ENGINE=InnoDB;


-- =========================================================
-- COMMENTS TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS comments (

    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    blog_id INT NOT NULL,

    parent_id INT NULL,

    comment TEXT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,


    -- User relationship
    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,


    -- Blog relationship
    FOREIGN KEY (blog_id)
        REFERENCES blogs(id)
        ON DELETE CASCADE,


    -- Parent comment relationship
    -- NULL = normal comment
    -- ID = reply to another comment
    FOREIGN KEY (parent_id)
        REFERENCES comments(id)
        ON DELETE CASCADE

) ENGINE=InnoDB;


-- =========================================================
-- COMMENT LIKES TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS comment_likes (

    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    comment_id INT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,


    -- One user can like a comment only once
    UNIQUE KEY unique_user_comment_like (
        user_id,
        comment_id
    ),


    -- User relationship
    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,


    -- Comment relationship
    FOREIGN KEY (comment_id)
        REFERENCES comments(id)
        ON DELETE CASCADE

) ENGINE=InnoDB;