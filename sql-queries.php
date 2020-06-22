<?php
/**
 * Получает массив с типами контента из базы данных
 * @param mysqli $connection Ресурс соединения
 *
 * @return array Массив с типами контента
 */
function get_sql_content_types($connection): array
{
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
 * @param int $offset Смещение выборки
 *
 * @return array Массив с постами
 */
function get_sql_posts_filters(
    $connection,
    array $params,
    string $sort_field = 'show_count',
    string $sorting_order = 'desc',
    int $limit = 6,
    int $offset = 0
): array {
    $sorting_order = mb_strtolower($sorting_order) === 'asc' ? 'ASC' : 'DESC';
    $sort_field = in_array($sort_field, ['show_count', 'dt_add', 'likes_count']) ? $sort_field : 'show_count';

    if (array_key_exists('user_data_id', $params)) {
        $user_data_id = $params['user_data_id'];
        unset($params['user_data_id']);
    } else {
        $user_data_id = $params['follower_id = ?'];//для страницы feed user_data_id передается как follower_id
    }

    $sql_posts = "SELECT DISTINCT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, 
                u.login, u.avatar, ct.class_name, p.user_id, p.author_id, p. original_id,
                (SELECT COUNT(post_id) FROM likes WHERE p.id = likes.post_id) AS likes_count,
                (SELECT COUNT(id) FROM comments WHERE p.id = comments.post_id) AS comments_count,
                (SELECT user_id FROM likes WHERE p.id = likes.post_id AND likes.user_id = $user_data_id) AS is_liked
                FROM posts p
                INNER JOIN users u ON p.user_id = u.id
                INNER JOIN content_types ct ON p.content_type_id = ct.id";

    if (array_key_exists('follower_id = ?', $params)) {
        $sql_posts .= " INNER JOIN subscriptions s ON p.user_id = s.author_id";
    }

    if (count($params) > 0 && !array_key_exists('q', $params)) {
        $sql_posts .= " WHERE " . implode(' AND ', array_keys($params));
    }

    if (array_key_exists('q', $params)) {
        if (substr($params['q'], 0, 1) !== '#') {
            $sql_posts .= ' WHERE MATCH(p.title, p.content) AGAINST(?)';

            return fetch_all($connection, $sql_posts, $params);
        }

        //разбивает поисковый запрос на отдельные слова и делает из них массив
        $params = array_filter(explode(' ', $params['q']));
        $params = array_map(function ($value) {
            if (substr($value, 0, 1) === '#') {
                return substr($value, 1);
            } else {
                return $value;
            }
        }, $params);//удаляет из начала строки каждого элемента массива #, если он есть
        $sql_posts .= ' INNER JOIN post_hashtag ph ON p.id = ph.post_id
            INNER JOIN hashtags h ON ph.hashtag_id = h.id
            WHERE BINARY hashtag_name = ?';

        for ($i = 1; $i < count($params); $i++) {
            $sql_posts .= ' OR hashtag_name = ?';
        }
    }

    $sql_posts .= " ORDER BY $sort_field $sorting_order LIMIT $limit OFFSET $offset";

    return fetch_all($connection, $sql_posts, $params);
}

/**
 * Получает массив с данными поста по его id
 * @param mysqli $connection Ресурс соединения
 * @param int $post_id id поста
 * @param int $user_data_id id пользователя
 *
 * @return array | null Массив с данными поста или null, если такого поста нет
 */
function get_sql_post($connection, int $post_id, int $user_data_id): ?array
{
    $sql_post = "SELECT p.id, p.dt_add, p.title, p.content, p.quote_author, p.photo, p.video, p.link, p.show_count, 
                ct.class_name, p.content_type_id, p.user_id, p.author_id, p. original_id,
                (SELECT COUNT(post_id) FROM likes WHERE p.id = likes.post_id) AS likes_count,
                (SELECT COUNT(id) FROM comments WHERE p.id = comments.post_id) AS comments_count,
                (SELECT user_id FROM likes WHERE p.id = likes.post_id AND likes.user_id = $user_data_id) AS is_liked
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
 *
 * @return array Массив с комментариями
 */
function get_sql_comments($connection, int $post_id, int $limit = 100): array
{
    $sql_comments = "SELECT c.id, c.dt_add, c.content, u.login, u.avatar, c.user_id FROM comments c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ? 
                ORDER BY c.dt_add DESC LIMIT $limit";

    return fetch_all($connection, $sql_comments, [$post_id]);
}

/**
 * Получает массив с данными пользователя по его id
 * @param mysqli $connection Ресурс соединения
 * @param int $user_id id пользователя
 *
 * @return array | null Массив с данными пользователя или null если такого пользователя нет в БД
 */
function get_sql_user($connection, int $user_id): ?array
{
    $sql_user = "SELECT u.id, u.dt_add, u.login, u.avatar, u.email,
                (SELECT COUNT(follower_id) FROM subscriptions WHERE u.id = subscriptions.author_id) AS follower_count,
                (SELECT COUNT(id) FROM posts WHERE u.id = posts.user_id) AS posts_count
                FROM users u
                WHERE u.id = ?";

    return fetch_assoc($connection, $sql_user, [$user_id]);
}

/**
 * Создает новый пост
 * @param mysqli $connection Ресурс соединения
 * @param string $content_type Тип контента
 * @param array $post Массив с данными поста
 *
 * @return int | null id поста, если удалось сохранить все данные в базу данных, иначе null
 */
function create_sql_post($connection, string $content_type, array $post): ?int
{
    if (!empty($post['hashtag_name'])) {
        $hashtags = array_filter(explode(' ', $post['hashtag_name']));
    }

    unset($post['hashtag_name']);

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
            $sql_post .= array_key_exists('author_id', $post) ?
                ', content, quote_author, user_id, author_id, original_id) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?)' :
                ', content, quote_author, user_id) VALUES (NOW(), ?, ?, ?, ?, ?)';
            break;
        case 'link':
            $sql_post .= ', link';
            break;
    }

    if ($content_type !== 'quote') {
        $sql_post .= array_key_exists('author_id', $post) ?
            ', user_id, author_id, original_id) VALUES (NOW(), ?, ?, ?, ?, ?, ?)' :
            ', user_id) VALUES (NOW(), ?, ?, ?, ?)';
    }

    mysqli_query($connection, "START TRANSACTION");

    $stmt_post = db_get_prepare_stmt($connection, $sql_post, $post);
    $result_post = mysqli_stmt_execute($stmt_post);
    $post_id = mysqli_insert_id($connection);

    if (!empty($hashtags)) {
        $result_hashtag = create_sql_post_hashtag($connection, $post_id, $hashtags);
    }

    if (!empty($hashtags) && !empty($result_hashtag) ? $result_post && $result_hashtag : $result_post) {
        mysqli_query($connection, "COMMIT");

        return $post_id;
    }

    mysqli_query($connection, "ROLLBACK");

    return null;
}

/**
 * Ищет тег в базе данных, если не нашел, создает новый
 * @param mysqli $connection Ресурс соединения
 * @param string $hashtag Тег
 *
 * @return int | null id тега, если нашел его или создал, иначе null
 */
function get_sql_hashtag_id($connection, string $hashtag): ?int
{
    $sql_hashtag_db = 'SELECT h.id FROM hashtags h WHERE BINARY h.hashtag_name = ?';
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
 *
 * @return bool true если все связи удалось создать, иначе false
 */
function create_sql_post_hashtag($connection, int $post_id, array $hashtags): bool
{
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
 *
 * @return array | null Массив с данными пользователя, если его электронная почта есть в базе данных, иначе null
 */
function search_sql_email($connection, string $user_email): ?array
{
    $sql_user = 'SELECT u.id, u.dt_add, u.email, u.login, u.password, u.avatar FROM users u WHERE u.email = ?';

    return fetch_assoc($connection, $sql_user, [$user_email]);
}

/**
 * Ищет в базе данных пользователя по логину
 * @param mysqli $connection Ресурс соединения
 * @param string $user_login Логин
 *
 * @return array | null Массив с id пользователя, если его логин есть в базе данных, иначе null
 */
function search_sql_login($connection, string $user_login): ?array
{
    $sql_login = 'SELECT u.id FROM users u WHERE u.login = ?';

    return fetch_assoc($connection, $sql_login, [$user_login]);
}

/**
 * Создает нового пользователя
 * @param mysqli $connection Ресурс соединения
 * @param array $user Массив с данными пользователя
 *
 * @return bool true при удачном сохранении данных, false в случае возникновения ошибки
 */
function create_sql_user($connection, array $user): bool
{
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

/**
 * Получает количество постов, находящихся в базе данных
 * @param mysqli $connection Ресурс соединения
 * @param array $params Массив с параметрами фильтрации
 *
 * @return int Колличество постов
 */
function get_sql_posts_count($connection, array $params = []): int
{
    $sql_posts_count = 'SELECT COUNT(p.id) AS count FROM posts p';

    if (count($params) > 0) {
        $sql_posts_count .= ' WHERE ' . implode(' AND ', array_keys($params));
        $items_count = fetch_assoc($connection, $sql_posts_count, $params)['count'];
    } else {
        $query_result = fetch($connection, $sql_posts_count);
        $items_count = mysqli_fetch_assoc($query_result)['count'];
    }

    return $items_count;
}

/**
 * Получает теги, по id поста
 * @param mysqli $connection Ресурс соединения
 * @param int $post_id id поста
 *
 * @return array Массив с тегами
 */
function get_sql_hashtags($connection, int $post_id): array
{
    $sql_hashtags = 'SELECT h.hashtag_name FROM hashtags h 
                    INNER JOIN post_hashtag ph ON h.id = ph.hashtag_id
                    WHERE ph.post_id = ?';
    $hashtags = fetch_all($connection, $sql_hashtags, [$post_id]);

    foreach ($hashtags as $key => $value) {
        $hashtags[$key] = $value['hashtag_name'];
    }

    return $hashtags;
}

/**
 * Получает посты по id пользователя с лайками и данными пользователей, которые лайкнули пост
 * @param mysqli $connection Ресурс соединения
 * @param int $user_id id пользователя
 *
 * @return array Массив с постами
 */
function get_sql_posts_likes($connection, int $user_id): array
{
    $sql_posts_likes = 'SELECT p.id, l.dt_add, p.photo, p.video, u.login, u.avatar, ct.class_name, l.user_id
                    FROM posts p
                    INNER JOIN likes l ON p.id = l.post_id
                    INNER JOIN users u ON l.user_id = u.id
                    INNER JOIN content_types ct ON p.content_type_id = ct.id
                    WHERE (SELECT COUNT(post_id) AS likes_count FROM likes) > 0 AND p.user_id = ?
                    ORDER BY dt_add DESC';

    return fetch_all($connection, $sql_posts_likes, [$user_id]);
}

/**
 * Получает подписки пользователя по его id
 * @param mysqli $connection Ресурс соединения
 * @param int $user_id id пользователя
 *
 * @return array Массив с авторами, на которых подписан пользователь
 */
function get_sql_authors($connection, int $user_id): array
{
    $sql_authors = 'SELECT u.id, u.dt_add, u.login, u.avatar,
                    (SELECT COUNT(follower_id) FROM subscriptions WHERE u.id = subscriptions.author_id) 
                        AS follower_count,
                    (SELECT COUNT(id) FROM posts WHERE u.id = posts.user_id) AS posts_count 
                    FROM users u
                    INNER JOIN subscriptions s ON u.id = s.author_id
                    WHERE s.follower_id = ?';

    return fetch_all($connection, $sql_authors, [$user_id]);
}

/**
 * Создает лайк (связь поста с пользователем) или убирает его
 * @param mysqli $connection Ресурс соединения
 * @param int $post_id id поста
 * @param int $user_id id пользователя
 *
 * @return bool true при успешном создании или удалении лайка, false в случае неудачи
 */
function create_like($connection, int $post_id, int $user_id): bool
{
    if (!get_sql_post($connection, $post_id, $user_id)) {
        return false;
    }

    if (!is_liked_post($connection, $post_id, $user_id)) {
        $sql_like = 'INSERT INTO likes (dt_add, user_id, post_id) VALUES (NOW(), ?, ?)';
    } else {
        $sql_like = 'DELETE FROM likes WHERE user_id = ? AND post_id = ?';
    }

    $stmt = db_get_prepare_stmt($connection, $sql_like, [$user_id, $post_id]);

    return mysqli_stmt_execute($stmt);
}

/**
 * Проверяет есть ли лайк (связь поста с пользователем)
 * @param mysqli $connection Ресурс соединения
 * @param int $post_id id поста
 * @param int $user_id id пользователя
 *
 * @return bool true - если лайк есть, false - если лайка нет
 */
function is_liked_post($connection, int $post_id, int $user_id): bool
{
    $sql_like_post = 'SELECT dt_add, user_id, post_id FROM likes WHERE user_id = ? AND post_id = ?';

    if (fetch_all($connection, $sql_like_post, [$user_id, $post_id])) {
        return true;
    }

    return false;
}

/**
 * Создает подписку (связь пользователя с пользователем) или убирает ее
 * @param mysqli $connection Ресурс соединения
 * @param int $author_id id автора
 * @param int $follower_id id подписчика
 *
 * @return bool true при успешном создании или удалении подписки, false в случае неудачи
 */
function create_subscription($connection, int $author_id, int $follower_id): bool
{
    if (!get_sql_user($connection, $author_id)) {
        return false;
    }

    if ($author_id === $follower_id) {
        return false;
    }

    if (!is_follower($connection, $author_id, $follower_id)) {
        $sql_subscription = 'INSERT INTO subscriptions (follower_id, author_id) VALUES (?, ?)';
    } else {
        $sql_subscription = 'DELETE FROM subscriptions WHERE follower_id = ? AND author_id = ?';
    }

    $stmt = db_get_prepare_stmt($connection, $sql_subscription, [$follower_id, $author_id]);

    return mysqli_stmt_execute($stmt);
}

/**
 * Проверяет является ли пользователь подписчиком автора
 * @param mysqli $connection Ресурс соединения
 * @param int $author_id id автора
 * @param int $follower_id id подписчика
 *
 * @return bool true - если подписан, false - если нет
 */
function is_follower($connection, int $author_id, int $follower_id): bool
{
    $sql_subscription = 'SELECT follower_id, author_id FROM subscriptions WHERE follower_id = ? AND author_id = ?';

    if (fetch_all($connection, $sql_subscription, [$follower_id, $author_id])) {
        return true;
    }

    return false;
}

/**
 * Получает число репостов поста по его id
 * @param mysqli $connection Ресурс соединения
 * @param int $post_id id поста
 *
 * @return int Число репостов
 */
function get_sql_repost_count($connection, int $post_id): int
{
    $sql_repost_count = 'SELECT COUNT(p.id) AS count FROM posts p WHERE p.original_id = ?';

    return fetch_assoc($connection, $sql_repost_count, [$post_id])['count'];
}

/**
 * Проверяет, делал ли пользователь репост поста
 * @param mysqli $connection Ресурс соединения
 * @param int $post_id id поста
 * @param int $user_id id пользователя
 *
 * @return bool true - если делал репост, false - если нет
 */
function is_reposted($connection, int $post_id, int $user_id): bool
{
    $sql = 'SELECT COUNT(p.id) AS count FROM posts p WHERE p.original_id = ? AND p.user_id = ?';

    if (fetch_assoc($connection, $sql, [$post_id, $user_id])['count'] > 0) {
        return true;
    }

    return false;
}

/**
 * Создает комментарий к посту
 * @param mysqli $connection Ресурс соединения
 * @param string $comment Текст комментария
 * @param int $post_id id поста
 * @param int $user_id id пользователя
 *
 * @return bool true если комментарий создан, иначе false
 */
function create_sql_comment($connection, string $comment, int $post_id, int $user_id): bool
{
    $sql_comment = 'INSERT INTO comments (dt_add, content, user_id, post_id) VALUES (NOW(), ?, ?, ?)';

    $stmt = db_get_prepare_stmt($connection, $sql_comment, [$comment, $user_id, $post_id]);

    return mysqli_stmt_execute($stmt);
}

/**
 * Получает массив собеседников с датой и текстом последнего сообщения
 * @param mysqli $connection Ресурс соединения
 * @param int $user_data_id id пользователя
 *
 * @return array Массив с собеседниками
 */
function get_sql_interlocutors($connection, int $user_data_id): array
{
    $sql_messages_sent = "SELECT DISTINCT m.sender_id AS id, m.content, m.dt_add FROM messages m
                        WHERE m.receiver_id = ? AND m.dt_add = (SELECT MAX(m2.dt_add) FROM messages m2 
                        WHERE m2.receiver_id = CASE m2.receiver_id
                            WHEN m.sender_id THEN m.sender_id
                            ELSE m.receiver_id END 
                        AND m2.sender_id = CASE m2.sender_id
                            WHEN m.sender_id THEN m.sender_id
                            ELSE m.receiver_id END)
                        UNION
                        SELECT DISTINCT m.receiver_id AS id, m.content, m.dt_add FROM messages m
                        WHERE m.sender_id = ? AND m.dt_add = (SELECT MAX(m2.dt_add) FROM messages m2 
                        WHERE m2.receiver_id = CASE m2.receiver_id
                            WHEN m.receiver_id THEN m.receiver_id
                            ELSE m.sender_id END 
                        AND m2.sender_id = CASE m2.sender_id
                            WHEN m.receiver_id THEN m.receiver_id
                            ELSE m.sender_id END)
                        ORDER BY dt_add";

    return fetch_all($connection, $sql_messages_sent, [$user_data_id, $user_data_id]);
}


/**
 * Получает массив с перепиской пользователя и собеседника
 * @param mysqli $connection Ресурс соединения
 * @param int $user_data_id id пользователя
 * @param int $interlocutor_id id собеседника
 *
 * @return array Массив с сообщениями
 */
function get_sql_messages($connection, int $user_data_id, int $interlocutor_id): array
{
    $sql_messages_sent = "SELECT DISTINCT m.sender_id, m.content, m.dt_add FROM messages m
                        WHERE m.receiver_id = ? AND m.sender_id = ?
                        UNION
                        SELECT DISTINCT m.sender_id, m.content, m.dt_add FROM messages m
                        WHERE m.sender_id = ? AND m.receiver_id = ?
                        ORDER BY dt_add";

    return fetch_all(
        $connection,
        $sql_messages_sent,
        [$user_data_id, $interlocutor_id, $user_data_id, $interlocutor_id]
    );
}

/**
 * Создает сообщение
 * @param mysqli $connection Ресурс соединения
 * @param string $message Текст сообщения
 * @param int $sender_id id отправителя
 * @param int $receiver_id id получателя
 *
 * @return bool true если сообщение создано, иначе false
 */
function create_sql_message($connection, string $message, int $sender_id, int $receiver_id): bool
{
    $sql_message = 'INSERT INTO messages (dt_add, content, sender_id, receiver_id) VALUES (NOW(), ?, ?, ?)';

    $stmt = db_get_prepare_stmt($connection, $sql_message, [$message, $sender_id, $receiver_id]);

    return mysqli_stmt_execute($stmt);
}


/**
 * Помечает сообщение как прочитанное
 * @param mysqli $connection Ресурс соединения
 * @param int $sender_id id отправителя
 * @param int $receiver_id id получателя
 *
 * @return bool true если сообщение отмечено как прочитанное, иначе false
 */
function read_sql_message($connection, int $sender_id, int $receiver_id)
{
    $read_message = 'UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0';

    $stmt = db_get_prepare_stmt($connection, $read_message, [$sender_id, $receiver_id]);

    return mysqli_stmt_execute($stmt);
}

/**
 * Получает общее количество непрочитанных сообщений, либо от конкретного пользователя
 * @param mysqli $connection Ресурс соединения
 * @param int $receiver_id id получателя
 * @param int $sender_id id отправителя, если нужно получить число непрочитанных сообщений от этого пользователя
 *
 * @return int Число непрочитанных сообщений
 */
function get_sql_unread_messages_count($connection, int $receiver_id, int $sender_id = null): int
{
    $sql_unread_messages_count = 'SELECT COUNT(m.id) AS count FROM messages m WHERE receiver_id = ? AND is_read = 0';

    if ($sender_id) {
        $sql_unread_messages_count .= ' AND sender_id = ?';

        return fetch_assoc($connection, $sql_unread_messages_count, [$receiver_id, $sender_id])['count'];
    }

    return fetch_assoc($connection, $sql_unread_messages_count, [$receiver_id])['count'];
}

/**
 * Увеличивает число просмотров поста на 1
 * @param mysqli $connection Ресурс соединения
 * @param int $post_id id поста
 *
 * @return bool true если обновление числа просмотров прошло успешно, иначе false
 */
function increase_sql_show_count($connection, int $post_id): bool
{
    $sql_post_show_count = 'UPDATE posts SET show_count = show_count + 1 WHERE id = ?';

    $stmt = db_get_prepare_stmt($connection, $sql_post_show_count, [$post_id]);

    return mysqli_stmt_execute($stmt);
}

/**
 * Получает подписчиков пользователя по его id
 * @param mysqli $connection Ресурс соединения
 * @param int $user_id id пользователя
 *
 * @return array Массив с подписчиками, которые подписаны на пользователя
 */
function get_sql_followers($connection, int $user_id): array
{
    $sql_authors = 'SELECT u.id, u.email, u.login
                    FROM users u
                    INNER JOIN subscriptions s ON u.id = s.follower_id
                    WHERE s.author_id = ?';

    return fetch_all($connection, $sql_authors, [$user_id]);
}
