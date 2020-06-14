CREATE DATABASE readme
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dt_add DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(128) NOT NULL UNIQUE,
    login VARCHAR(128) NOT NULL UNIQUE,
    password VARCHAR(64) NOT NULL,
    avatar VARCHAR(128)
);

CREATE TABLE content_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(128) NOT NULL UNIQUE,
    class_name VARCHAR(128) NOT NULL UNIQUE
);

CREATE TABLE posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dt_add DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    title VARCHAR(128),
    content TEXT,
    quote_author VARCHAR(128),
    photo VARCHAR(128),
    video VARCHAR(128),
    link VARCHAR(128),
    show_count INT UNSIGNED DEFAULT 0,

    user_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    content_type_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (content_type_id) REFERENCES content_types(id),
    author_id INT UNSIGNED,
    FOREIGN KEY (author_id) REFERENCES users(id),
    original_id int,

    FULLTEXT (title, content)
);

CREATE TABLE comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dt_add DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    content TEXT NOT NULL,

    user_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    post_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id)
);

CREATE TABLE likes (
    dt_add DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id INT UNSIGNED NOT NULL,
    post_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (post_id) REFERENCES posts(id)
);

CREATE TABLE subscriptions (
    follower_id INT UNSIGNED NOT NULL,
    author_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (follower_id, author_id),
    FOREIGN KEY (follower_id) REFERENCES users(id),
    FOREIGN KEY (author_id) REFERENCES users(id)
);

CREATE TABLE messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dt_add DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    content TEXT NOT NULL,

    sender_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    receiver_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

CREATE TABLE hashtags (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hashtag_name VARCHAR(128) UNIQUE
);

CREATE TABLE post_hashtag (
    post_id INT UNSIGNED NOT NULL,
    hashtag_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (post_id, hashtag_id),
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (hashtag_id) REFERENCES hashtags(id)
);
