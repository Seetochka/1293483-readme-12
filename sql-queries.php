<?php
require_once 'helpers.php';

function get_sql_content_types($connection) {
    $sql_content_types = 'SELECT id, title, class_name FROM content_types';
    return fetch_all($connection, $sql_content_types);
} //запрос на получение типов только существующих постов SELECT c.title, c.class_name FROM content_types c INNER JOIN posts p ON c.id = p.content_type_id GROUP BY c.title

function get_sql_posts($connection, $sort_field, $sorting_order) {
    $sorting_order = mb_strtolower($sorting_order) === 'asc' ? 'ASC' : 'DESC';
    $sql_posts = "SELECT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, u.login, u.avatar, ct.class_name, 
                COUNT(DISTINCT l.user_id) AS likes_count, COUNT(DISTINCT c.user_id) AS comments_count FROM posts p 
                INNER JOIN users u ON p.user_id = u.id
                INNER JOIN content_types ct ON p.content_type_id = ct.id
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN comments c ON p.id = c.post_id
                GROUP BY p.id
                ORDER BY ? $sorting_order LIMIT 6";
    $stmt = db_get_prepare_stmt($connection, $sql_posts, [$sort_field]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

function get_sql_posts_filters($connection, $active_content_type, $sort_field, $sorting_order) {
    $sql_posts = "SELECT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, u.login, u.avatar, ct.class_name, 
                COUNT(DISTINCT l.user_id) AS likes_count, COUNT(DISTINCT c.user_id) AS comments_count FROM posts p 
                INNER JOIN users u ON p.user_id = u.id
                INNER JOIN content_types ct ON p.content_type_id = ct.id
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN comments c ON p.id = c.post_id
                WHERE content_type_id = $active_content_type
                GROUP BY p.id
                ORDER BY $sort_field $sorting_order LIMIT 6";

    return fetch_all($connection, $sql_posts);
}

function get_sql_post($connection, $post_id) {
    $sql_post = "SELECT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, p.show_count, ct.class_name, 
                COUNT(DISTINCT l.user_id) AS likes_count, COUNT(DISTINCT c.user_id) AS comments_count FROM posts p 
                INNER JOIN users u ON p.user_id = u.id 
                INNER JOIN content_types ct ON p.content_type_id = ct.id 
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN comments c ON p.id = c.post_id
                WHERE p.id = $post_id";

    return fetch_assoc($connection, $sql_post);
}

function get_sql_comments($connection, $post_id) {
    $sql_comments = "SELECT c.id, c.dt_add, c.content, u.login, u.avatar FROM comments c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.post_id = $post_id 
                ORDER BY c.dt_add DESC";
    return fetch_all($connection, $sql_comments);
}

function get_sql_user($connection, $post_id) {
    $sql_user = "SELECT p.id, u.dt_add, u.login, u.avatar, p.user_id, 
                COUNT(DISTINCT s.follower_id) AS follower_count, COUNT(DISTINCT posts_by_user_id.id) AS posts_count FROM posts p 
                INNER JOIN users u ON p.user_id = u.id 
                LEFT JOIN subscriptions s ON p.user_id = s.author_id
                LEFT JOIN posts posts_by_user_id ON p.user_id = posts_by_user_id.user_id
                WHERE p.id = $post_id";
    return fetch_assoc($connection, $sql_user);
}
