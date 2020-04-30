<?php
function get_sql_content_types($connection): array {
    $sql_content_types = 'SELECT id, title, class_name FROM content_types';
    return fetch_all($connection, $sql_content_types);
} //запрос на получение типов только существующих постов SELECT c.id, c.title, c.class_name FROM content_types c INNER JOIN posts p ON c.id = p.content_type_id GROUP BY c.id

function get_sql_posts($connection, string $sort_field, string $sorting_order): array {
    $sorting_order = mb_strtolower($sorting_order) === 'asc' ? 'ASC' : 'DESC';
    $sort_field = in_array($sort_field, ['show_count', 'dt_add', 'likes_count']) ? $sort_field : 'show_count';

    $sql_posts = "SELECT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, u.login, u.avatar, ct.class_name, 
                COUNT(DISTINCT l.user_id) AS likes_count, COUNT(DISTINCT c.user_id) AS comments_count FROM posts p 
                INNER JOIN users u ON p.user_id = u.id
                INNER JOIN content_types ct ON p.content_type_id = ct.id
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN comments c ON p.id = c.post_id
                GROUP BY p.id
                ORDER BY $sort_field $sorting_order LIMIT 6";

    return fetch_all($connection, $sql_posts);
}

function get_sql_posts_filters($connection, int $active_content_type, string $sort_field, string $sorting_order): array {
    $sorting_order = mb_strtolower($sorting_order) === 'asc' ? 'ASC' : 'DESC';
    $sort_field = in_array($sort_field, ['show_count', 'dt_add', 'likes_count']) ? $sort_field : 'show_count';

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

function get_sql_post($connection, int $post_id): array {
    $sql_post = "SELECT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, p.show_count, ct.class_name, 
                COUNT(DISTINCT l.user_id) AS likes_count, COUNT(DISTINCT c.user_id) AS comments_count FROM posts p 
                INNER JOIN users u ON p.user_id = u.id 
                INNER JOIN content_types ct ON p.content_type_id = ct.id 
                LEFT JOIN likes l ON p.id = l.post_id
                LEFT JOIN comments c ON p.id = c.post_id
                WHERE p.id = $post_id";

    return fetch_assoc($connection, $sql_post);
}

function get_sql_comments($connection, int $post_id): array {
    $sql_comments = "SELECT c.id, c.dt_add, c.content, u.login, u.avatar FROM comments c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.post_id = $post_id 
                ORDER BY c.dt_add DESC";

    return fetch_all($connection, $sql_comments);
}

function get_sql_user($connection, int $post_id): array {
    $sql_user = "SELECT p.id, u.dt_add, u.login, u.avatar, p.user_id, 
                COUNT(DISTINCT s.follower_id) AS follower_count, COUNT(DISTINCT posts_by_user_id.id) AS posts_count FROM posts p 
                INNER JOIN users u ON p.user_id = u.id 
                LEFT JOIN subscriptions s ON p.user_id = s.author_id
                LEFT JOIN posts posts_by_user_id ON p.user_id = posts_by_user_id.user_id
                WHERE p.id = $post_id";

    return fetch_assoc($connection, $sql_user);
}
