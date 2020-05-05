<?php
function get_sql_content_types($connection): array {
    $sql_content_types = 'SELECT id, title, class_name FROM content_types';
    return fetch_all($connection, $sql_content_types);
} //запрос на получение типов только существующих постов SELECT c.id, c.title, c.class_name FROM content_types c INNER JOIN posts p ON c.id = p.content_type_id GROUP BY c.id

function get_sql_posts($connection, string $sort_field, string $sorting_order, int $limit = 6): array {
    $sorting_order = mb_strtolower($sorting_order) === 'asc' ? 'ASC' : 'DESC';
    $sort_field = in_array($sort_field, ['show_count', 'dt_add', 'likes_count']) ? $sort_field : 'show_count';

    $sql_posts = "SELECT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, u.login, u.avatar, ct.class_name, 
                (SELECT COUNT(*) FROM likes WHERE p.id = likes.post_id) AS likes_count,
                (SELECT COUNT(*) FROM comments WHERE p.id = comments.post_id) AS comments_count 
                FROM posts p 
                INNER JOIN users u ON p.user_id = u.id
                INNER JOIN content_types ct ON p.content_type_id = ct.id
                GROUP BY p.id
                ORDER BY $sort_field $sorting_order LIMIT $limit";

    return fetch_all($connection, $sql_posts);
}

function get_sql_posts_filters($connection, int $active_content_type, string $sort_field, string $sorting_order, int $limit = 6): array {
    $sorting_order = mb_strtolower($sorting_order) === 'asc' ? 'ASC' : 'DESC';
    $sort_field = in_array($sort_field, ['show_count', 'dt_add', 'likes_count']) ? $sort_field : 'show_count';

    $sql_posts = "SELECT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, u.login, u.avatar, ct.class_name, 
                (SELECT COUNT(*) FROM likes WHERE p.id = likes.post_id) AS likes_count,
                (SELECT COUNT(*) FROM comments WHERE p.id = comments.post_id) AS comments_count 
                FROM posts p 
                INNER JOIN users u ON p.user_id = u.id
                INNER JOIN content_types ct ON p.content_type_id = ct.id
                WHERE content_type_id = $active_content_type
                GROUP BY p.id
                ORDER BY $sort_field $sorting_order LIMIT $limit";

    return fetch_all($connection, $sql_posts);
}

function get_sql_post($connection, int $post_id): ?array {
    $sql_post = "SELECT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, p.show_count, ct.class_name, p.user_id,
                (SELECT COUNT(*) FROM likes WHERE p.id = likes.post_id) AS likes_count,
                (SELECT COUNT(*) FROM comments WHERE p.id = comments.post_id) AS comments_count
                FROM posts p
                INNER JOIN content_types ct ON p.content_type_id = ct.id 
                WHERE p.id = ?";

    $stmt = db_get_prepare_stmt($connection, $sql_post, [$post_id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($res);
}

function get_sql_comments($connection, int $post_id, int $limit = 100): array {
    $sql_comments = "SELECT c.id, c.dt_add, c.content, u.login, u.avatar FROM comments c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.post_id = $post_id 
                ORDER BY c.dt_add DESC LIMIT $limit";

    return fetch_all($connection, $sql_comments);
}

function get_sql_comments_count($connection, int $post_id): int {
    $sql_comments = "SELECT COUNT(c.id) AS count FROM comments c
                WHERE c.post_id = $post_id";

    return fetch_assoc($connection, $sql_comments)['count'];
}

function get_sql_user($connection, int $user_id): array {
    $sql_user = "SELECT u.id, u.dt_add, u.login, u.avatar,
                (SELECT COUNT(*) FROM subscriptions WHERE u.id = subscriptions.author_id) AS follower_count,
                (SELECT COUNT(*) FROM posts WHERE u.id = posts.user_id) AS posts_count
                FROM users u
                WHERE u.id = $user_id";

    return fetch_assoc($connection, $sql_user);
}
