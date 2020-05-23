<?php
/**
 * Получает массив с типами контента из базы данных
 * @param mysqli $connection Ресурс соединения
 * @return array Массив с типами контента
 */
function get_sql_content_types($connection): array {
    $sql_content_types = 'SELECT id, title, class_name FROM content_types';
    $query_result = fetch($connection, $sql_content_types);

    return mysqli_fetch_all($query_result, MYSQLI_ASSOC);
}

/**
 * Получает отсортированный массив с постами и, при необходимости, отфильтрованный
 * @param mysqli $connection Ресурс соединения
 * @param array $params Массив c параметрами фильтрации
 * @param string $sort_field Тип сортиовки
 * @param string $sorting_order Порядок сортировки
 * @param int $limit Необходимое количество записей
 * @return array Массив с постами
 */
function get_sql_posts_filters($connection, array $params, string $sort_field = 'show_count', string $sorting_order = 'desc', int $limit = 6): array {
    $sorting_order = mb_strtolower($sorting_order) === 'asc' ? 'ASC' : 'DESC';
    $sort_field = in_array($sort_field, ['show_count', 'dt_add', 'likes_count']) ? $sort_field : 'show_count';

    $sql_posts = "SELECT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, u.login, u.avatar, ct.class_name,
                (SELECT COUNT(*) FROM likes WHERE p.id = likes.post_id) AS likes_count,
                (SELECT COUNT(*) FROM comments WHERE p.id = comments.post_id) AS comments_count
                FROM posts p
                INNER JOIN users u ON p.user_id = u.id
                INNER JOIN content_types ct ON p.content_type_id = ct.id";

    if (array_key_exists('follower_id = ?', $params)) {
        $sql_posts .= " INNER JOIN subscriptions s ON p.user_id = s.author_id";
    }

    if (count($params) > 0) {
        $sql_posts .= " WHERE " . implode(' AND ', array_keys($params));
    }

    $sql_posts .= " ORDER BY $sort_field $sorting_order LIMIT $limit";

    return fetch_all($connection, $sql_posts, $params);
}

/**
 * Получает массив с данными поста по его id
 * @param mysqli $connection Ресурс соединения
 * @param int $post_id id поста
 * @return array | null Массив с данными поста или null, если такого поста нет
 */
function get_sql_post($connection, int $post_id): ?array {
    $sql_post = "SELECT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, p.show_count, ct.class_name, p.user_id,
                (SELECT COUNT(*) FROM likes WHERE p.id = likes.post_id) AS likes_count,
                (SELECT COUNT(*) FROM comments WHERE p.id = comments.post_id) AS comments_count
                FROM posts p
                INNER JOIN content_types ct ON p.content_type_id = ct.id 
                WHERE p.id = ?";

    return fetch_assoc($connection, $sql_post, [$post_id]);
}

/**
 * Получает массив с комментариями поста по его id
 * @param mysqli $connection Ресурс соединения
 * @param int $post_id id поста
 * @param int $limit Необходимое количество записей
 * @return array Массив с комментариями
 */
function get_sql_comments($connection, int $post_id, int $limit = 100): array {
    $sql_comments = "SELECT c.id, c.dt_add, c.content, u.login, u.avatar FROM comments c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ? 
                ORDER BY c.dt_add DESC LIMIT $limit";

    return fetch_all($connection, $sql_comments, [$post_id]);
}

/**
 * Получает число комментариев поста по его id
 * @param mysqli $connection Ресурс соединения
 * @param int $post_id id поста
 * @return int Число комментариев
 */
function get_sql_comments_count($connection, int $post_id): int {
    $sql_comments = "SELECT COUNT(c.id) AS count FROM comments c
                WHERE c.post_id = ?";

    return fetch_assoc($connection, $sql_comments, [$post_id])['count'];
}

/**
 * Получает массив с данными пользователя по его id
 * @param mysqli $connection Ресурс соединения
 * @param int $user_id id пользователя
 * @return array Массив с данными пользователя
 */
function get_sql_user($connection, int $user_id): array {
    $sql_user = "SELECT u.id, u.dt_add, u.login, u.avatar,
                (SELECT COUNT(*) FROM subscriptions WHERE u.id = subscriptions.author_id) AS follower_count,
                (SELECT COUNT(*) FROM posts WHERE u.id = posts.user_id) AS posts_count
                FROM users u
                WHERE u.id = ?";

    return fetch_assoc($connection, $sql_user, [$user_id]);
}

/**
 * Создает новый пост
 * @param mysqli $connection Ресурс соединения
 * @param string $content_type Тип контента
 * @param array $post Массив с данными поста
 * @return int | null id поста, если удалось сохранить все данные в базу данных, иначе null
 */
function create_sql_post($connection, string $content_type, array $post): ?int {
    $hashtags = array_filter(explode(' ', $post['hashtag_name']));
    unset($post['hashtag_name']);
    $post['user_id'] = $_SESSION['user']['id'];

    $sql_post = 'INSERT INTO posts (dt_add, title, content_type_id';

    switch ($content_type) {
        case 'photo':
            $sql_post .= ', photo';
            break;
        case 'video':
            $sql_post .= ', video';
            break;
        case 'text':
            $sql_post .= ', content';
            break;
        case 'quote':
            $sql_post .= ', content, quote_author, user_id) VALUES (NOW(), ?, ?, ?, ?, ?)';
            break;
        case 'link':
            $sql_post .= ', link';
            break;
    }

    if ($content_type !== 'quote') {
        $sql_post .= ', user_id) VALUES (NOW(), ?, ?, ?, ?)';
    }

    $stmt_post = db_get_prepare_stmt($connection, $sql_post, $post);
    $result_post = mysqli_stmt_execute($stmt_post);

    if ($result_post) {
        $post_id = mysqli_insert_id($connection);
        $result_hashtag = create_sql_post_hashtag($connection, $post_id, $hashtags);

        if ($result_hashtag) {
            return $post_id;
        }
    }

    return null;
}

/**
 * Ищет тег в базе данных, если не нашел, создает новый
 * @param mysqli $connection Ресурс соединения
 * @param string $hashtag Тег
 * @return int | null id тега, если нашел его или создал, иначе null
 */
function get_sql_hashtag_id($connection, string $hashtag): ?int {
    $sql_hashtag_db = 'SELECT h.id FROM hashtags h WHERE h.hashtag_name = ?';
    $result_hashtag_db = fetch_assoc($connection, $sql_hashtag_db, [$hashtag]);

    if ($result_hashtag_db) {
        $hashtag_id = $result_hashtag_db['id'];
    } else {
        $sql_hashtag = 'INSERT INTO hashtags (hashtag_name) VALUES (?)';
        $stmt_hashtag = db_get_prepare_stmt($connection, $sql_hashtag, [$hashtag]);
        $hashtag_id = mysqli_stmt_execute($stmt_hashtag) ? mysqli_insert_id($connection) : null;
    }

    return $hashtag_id;
}

/**
 * Создает связи тегов с постом
 * @param mysqli $connection Ресурс соединения
 * @param int $post_id id поста
 * @param array $hashtags Массив с тегами
 * @return bool true если все связи удалось создать, иначе false
 */
function create_sql_post_hashtag($connection, int $post_id, array $hashtags): bool {
    foreach ($hashtags as $hashtag) {
        $hashtag_id = get_sql_hashtag_id($connection, $hashtag);

        if (!$hashtag_id) {
            return false;
        }

        $sql_post_hashtag = 'INSERT INTO post_hashtag (post_id, hashtag_id) VALUES (?, ?)';
        $stmt_post_hashtag = db_get_prepare_stmt($connection, $sql_post_hashtag, [$post_id, $hashtag_id]);
        $result = mysqli_stmt_execute($stmt_post_hashtag);

        if (!$result) {
            return false;
        }
    }

    return true;
}

/**
 * Ищет в базе данных пользователя по электронной почте
 * @param mysqli $connection Ресурс соединения
 * @param string $user_email Электронная почта
 * @return array | null Массив с id пользователя, если его электронная почта есть в базе данных, иначе null
 */
function search_sql_email($connection, string $user_email): ?array {
    $sql_user = 'SELECT u.id, u.dt_add, u.email, u.login, u.password, u.avatar FROM users u WHERE u.email = ?';

    return fetch_assoc($connection, $sql_user, [$user_email]);
}

/**
 * Ищет в базе данных пользователя по логину
 * @param mysqli $connection Ресурс соединения
 * @param string $user_login Логин
 * @return array | null Массив с id пользователя, если его логин есть в базе данных, иначе null
 */
function search_sql_login($connection, string $user_login): ?array {
    $sql_login = 'SELECT u.id FROM users u WHERE u.login = ?';

    return fetch_assoc($connection, $sql_login, [$user_login]);
}

/**
 * Создает нового пользователя
 * @param mysqli $connection Ресурс соединения
 * @param array $user Массив с данными пользователя
 * @return bool true при удачном сохранении данных, false в случае возникновения ошибки
 */
function create_sql_user($connection, array $user): bool {
    $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
    unset($user['password-repeat']);

    if (empty($_FILES['avatar']['name'])) {
        $sql_user = 'INSERT INTO users (dt_add, email, login, password) VALUES (NOW(), ?, ?, ?)';
    } else {
        $user['avatar'] = upload_file($_FILES['avatar']);
        $sql_user = 'INSERT INTO users (dt_add, email, login, password, avatar) VALUES (NOW(), ?, ?, ?, ?)';
    }

    $stmt = db_get_prepare_stmt($connection, $sql_user, $user);

    return mysqli_stmt_execute($stmt);
}
