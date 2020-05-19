<?php
/**
 * Функция обрезания текста при превышении максимально допустимой длины
 * @param string $text Проверяемая строка
 * @param int $max_length Максимально допустимая длина текста
 * @return string Первоначальную строку, если ее длина была меньше максимально допустимой, либо обрезанный текст
 */
function cut_text(string $text, int $max_length = 300): string
{
    $text_length = mb_strlen($text);

    if ($text_length < $max_length) {
        return $text;
    }

    $words = explode(' ', $text);
    $cutted_text = '';
    $index = 0;

    while (mb_strlen($cutted_text . $words[$index]) <= $max_length) {
        $cutted_text .= "{$words[$index]} ";

        ++$index;
    }

    $cutted_text .= '...';

    return $cutted_text;
}

/**
 * Возвращает дату в относительном формате, удобном для пользователя
 * @param string $date Дата в виде строки
 * @return string Дату в относительном формате
 */
function format_time(string $date): string {
    define('WEEK', 7);

    $dt_current = date_create('now');
    $dt_date = date_create($date);
    $dt_diff = date_diff($dt_current, $dt_date);
    $date_diff_unix = strtotime(date_interval_format($dt_diff, '%Y-%M-%D %H:%I'));
    $date_int = null;
    $noun_plural_form = null;

    switch (true) {
        case ($date_diff_unix < strtotime('00-00-00 01:00')):
            $date_int = idate('i', $date_diff_unix);
            $noun_plural_form = get_noun_plural_form($date_int, 'минуту', 'минуты', 'минут');

            break;

        case ($date_diff_unix < strtotime('00-00-01 00:00')):
            $date_int = idate('H', $date_diff_unix);
            $noun_plural_form = get_noun_plural_form($date_int, 'час', 'часа', 'часов');

            break;

        case ($date_diff_unix < strtotime('00-00-07 00:00')):
            $date_int = idate('d', $date_diff_unix);
            $noun_plural_form = get_noun_plural_form($date_int, 'день', 'дня', 'дней');

            break;

        case ($date_diff_unix < strtotime('00-01-00 00:00')):
            $date_int = floor(idate('d', $date_diff_unix) / WEEK);
            $noun_plural_form = get_noun_plural_form($date_int, 'неделю', 'недели', 'недель');

            break;

        default:
            $date_int = idate('m', $date_diff_unix);
            $noun_plural_form = get_noun_plural_form($date_int, 'месяц', 'месяца', 'месяцев');

            break;
    }

    return "$date_int $noun_plural_form ";
}

/**
 * Выполняет SQL запрос к базе данных
 * @param mysqli $connection Ресурс соединения
 * @param string $sql_request SQL запрос
 * @return  //Объект в случае удачного выполнения SQL запроса или сообщение об ошибке в случае неудачи
 */
function fetch($connection, string $sql_request) {
    $query_result = mysqli_query($connection, $sql_request);

    if (mysqli_errno($connection) > 0) {
        $error_msg = 'Не удалось выполнить запрос: ' . mysqli_error($connection);
        die($error_msg);
    }

    return $query_result;
}

/**
 * Выполняет SQL запрос к базе данных на основе подготовленного выражения
 * @param mysqli $connection Ресурс соединения
 * @param string $sql_request SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 * @return //Результат из подготовленного SQL запроса или false в случае ошибки
 */
function get_prepare_stmt($connection, string $sql_request, array  $data = []) {
    $stmt = db_get_prepare_stmt($connection, $sql_request, $data);
    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}

/**
 * Возвращает все записи результата SQL запроса
 * @param mysqli $connection Ресурс соединения
 * @param string $sql_request SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 * @return array | null Массив в случае удачного выполнения SQL запроса или false в случае ошибки
 */
function fetch_all($connection, string $sql_request, array  $data = []): ?array {
    $result = get_prepare_stmt($connection, $sql_request, $data);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Возвращает ряд результата SQL запроса в качестве ассоциативного массива
 * @param mysqli $connection Ресурс соединения
 * @param string $sql_request SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 * @return array | null Ассоциативный массив в случае удачного выполнения SQL запроса или false в случае ошибки
 */
function fetch_assoc($connection, string $sql_request, array  $data = []): ?array {
    $result = get_prepare_stmt($connection, $sql_request, $data);

    return mysqli_fetch_assoc($result);
}

/**
 * Выдает столбец, по которому будет производится сортировка, исходя из типа сортировки
 * @param string | null $active_sorting_type Тип сортировки
 * @return string Столбец для сортировки
 */
function get_sorting_field(?string $active_sorting_type): string {
    switch ($active_sorting_type) {
        case 'date':
            return 'dt_add';
        case 'likes':
            return 'likes_count';
        default:
            return 'show_count';
    }
}

/**
 * Формирует URL исходя из переданного пути и параметров запроса
 * @param array $params Массив с параметрами запрса
 * @param string $path Адрес страницы
 * @return string Сформираванный URL
 */
function get_query_href(array $params, string $path): string {
    $current_params = $_GET;
    $merged_params = array_merge($current_params, $params);
    $query = http_build_query($merged_params);

    return $path . ($query ? "?$query" : '');
}

/**
 * Находит индекс в двумерном массиве по значению поля
 * @param array $array Массив по которому будет проходить поиск
 * @param string $field Ключ по которому будет проходить поиск
 * @param string $compared Искомое значение
 * @return int Индекс элемента массива
 */
function find_index(array $array, string $field, string $compared): int {
    foreach ($array as $key => $value) {
        if ($value[$field] == $compared) return $key;
    }
}

/**
 * Удаляет пробелы в начале и конце строк массива
 * @param array $array Массив
 * @return array Массив, в котором из каждой строки удалили пробелы в начале и конце
 */
function remove_space(array $array): array {
    return array_map(function($value) {return trim($value); }, $array);
}

/**
 * Проверяет является ли строка корректным email
 * @param string $value Email
 * @return bool true при правильном email, иначе false
 */
function is_email(string $value): bool {
    return boolval(filter_var($value, FILTER_VALIDATE_EMAIL));
}

/**
 * Проверяет пуста ли строка
 * @param string $value Проверяемая строка
 * @return bool true если строка не пустая, иначе false
 */
function is_filled(string $value): bool {
    return !empty($value);
}

/**
 * Проверяет корректная ли длина строки
 * @param string $value Проверяемая строка
 * @param int $min Минимально допустимая длина
 * @param int $max Максимально допустимая длина
 * @return bool true при допустимой длине строки, иначе false
 */
function is_correct_length(string $value, int $min = 0, int $max = INF): bool {
    $length = iconv_strlen($value);

    return $length >= $min && $length <= $max;
}

/**
 * Проверяет является ли строка корректной ссылкой
 * @param string $value Ссылка
 * @return bool true при корректной ссылке, иначе false
 */
function is_correct_link(string $value): bool {
    return boolval(filter_var(check_protocol($value), FILTER_VALIDATE_URL)) && @get_headers(check_protocol($value)) !== false;
}

/**
 * Проверяет является ли строка ссылкой на видео YouTube
 * @param string $value Ссылка
 * @return bool true при корректной ссылке на видео YouTube, иначе false
 */
function is_link_youtube(string $value): bool {
    $youtube_hosts = ['www.youtube.com', 'youtube.com', 'youtu.be'];
    $value = check_protocol($value);

    if (in_array(parse_url($value, PHP_URL_HOST), $youtube_hosts)) {
        return boolval(extract_youtube_id($value));
    }

    return false;
}

/**
 * Проверяет возможно ли загрузить фото по ссылке
 * @param string $value Ссылка на фото
 * @return bool true если возможно загрузить файл по ссылке, иначе false
 */
function is_available_resource(string $value): bool {
    $headers = get_headers(check_protocol($value), 1);

    return boolval(strpos($headers[0], '200'));
}

/**
 * Проверяет является ли строка корректным тегом
 * @param string $value Тег
 * @return bool true при корректном теге, иначе false
 */
function is_tag(string $value): bool {
    return boolval(preg_match('/^[a-zA-Zа-яёА-ЯЁ0-9_]+$/u', $value));
}

/**
 * Проверяет корректный ли MIME-тип загружаемого файла
 * @param string $file_type MIME-тип файла
 * @param array | string $mime_type Допустимые MIME-типы
 * @return bool true при корректном MIME-типе, иначе false
 */
function is_correct_mime_types(string $file_type, $mime_type): bool {
    if (is_array($mime_type)) {
        return in_array($file_type, $mime_type);
    } elseif (!is_string($mime_type)) {
        return false;
    }

    return $file_type === $mime_type;
}

/**
 * Проверяет размер файла
 * @param int $value Размер файла
 * @param int $max_file_size Максимально допустимый размер файла
 * @return bool true при допустимом размере файла, иначе false
 */
function is_correct_file_size(int $value, int $max_file_size): bool {
    return $value <= $max_file_size;
}

/**
 * Проверяет, чтобы в составе пароля были минимум одна цифра и по одной букве верхнего и нижнего регистров
 * @param string $value Пароль
 * @return bool true соответствии пароля условию, иначе false
 */
function is_strong_password(string $value): bool {
    return boolval(preg_match('/^\S*(?=\S{6,128})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/', $value));
}

/**
 * Валидация поля Заголовок
 * @param string $value Содержимое поля Заголовок
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_title(string $value): ?string {
    $error_message = null;

    switch (false) {
        case (is_filled($value)):
            $error_message = 'Это поле должно быть заполнено';
            break;
        case (is_correct_length($value, 0, DATABASE_VARCHAR_MAX_SIZE)):
            $error_message = 'Введите значение до ' . DATABASE_VARCHAR_MAX_SIZE . ' символов';
            break;
    }

    return $error_message;
}

/**
 * Валидация поля Ссылка
 * @param string $value Содержимое поля Ссылка
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_link(string $value): ?string {
    $error_message = null;

    switch (false) {
        case (is_filled($value)):
            $error_message = 'Это поле должно быть заполнено';
            break;
        case (is_correct_link($value)):
            $error_message = 'Введите корректную ссылку';
            break;
    }

    return $error_message;
}

/**
 * Валидация поля Теги
 * @param string $value Содержимое поля Теги
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_tags(string $value): ?string {
    if (!is_filled($value)) {
        return 'Это поле должно быть заполнено';
    }

    $tag_array = array_filter(explode(' ', $value));

    foreach ($tag_array as $tag) {
        if (!is_tag($tag)) {
            return 'Введите корректный тег';
        }
    }

    return null;
}

/**
 * Валидация поля Текст поста
 * @param string $value Содержимое поля Текст поста
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_content_text(string $value): ?string {
    if (!is_filled($value)) {
        return'Это поле должно быть заполнено';
    }

    return null;
}

/**
 * Валидация поля Текст цитаты
 * @param string $value Содержимое поля Текст цитаты
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_content_quote(string $value): ?string {
    $error_message = null;

    switch (false) {
        case (is_filled($value)):
            $error_message = 'Это поле должно быть заполнено';
            break;
        case (is_correct_length($value, 0, POST_QUOTE_MAX_LENGTH)):
            $error_message = 'Введите значение до ' . POST_QUOTE_MAX_LENGTH . ' символов';
            break;
    }

    return $error_message;
}

/**
 * Валидация поля Автор
 * @param string $value Содержимое поля Автор
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_quote_author(string $value): ?string {
    $error_message = null;

    switch (false) {
        case (is_filled($value)):
            $error_message = 'Это поле должно быть заполнено';
            break;
        case (is_correct_length($value, 0, DATABASE_VARCHAR_MAX_SIZE)):
            $error_message = 'Введите значение до ' . DATABASE_VARCHAR_MAX_SIZE . ' символов';
            break;
    }

    return $error_message;
}

/**
 * Валидация загружаемого файла для поста фото
 * @param array $value Массив с данными файла
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_photo(array $value): ?string {
    $error_message = null;
    $image_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $value['tmp_name']);

    switch (false) {
        case (is_correct_mime_types($file_type, $image_mime_types)):
            $error_message = 'Загрузите изображение в формате JPEG, PNG или GIF';
            break;
        case (is_correct_file_size($value['size'], POST_PHOTO_MAX_FILE_SIZE)):
            $error_message = 'Размер файла не должен превышать ' . POST_PHOTO_MAX_FILE_SIZE / 1048576 . 'МБ';
            break;
    }

    return $error_message;
}

/**
 * Валидация поля Ссылка из интернета
 * @param string $value Содержимое поля Ссылка из интернета
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_link_photo(string $value): ?string {
    $error_message = null;
    $image_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
    $headers = [];

    if (is_correct_link($value)) {
        $headers = get_headers(check_protocol($value), 1);
    }

    switch (false) {
        case (is_filled($value)):
            $error_message = 'Загрузите фото или укажите ссылку на него';
            break;
        case (is_correct_link($value)):
            $error_message = 'Введите корректную ссылку';
            break;
        case (is_available_resource($value)):
            $error_message = 'Невозможно загрузить изображение по указанной ссылке';
            break;
        case (is_correct_mime_types($headers['Content-Type'], $image_mime_types)):
            $error_message = 'Укажите ссылку на изображение в формате JPEG, PNG или GIF';
            break;
    }

    return $error_message;
}

/**
 * Валидация поля Ссылка YouTube
 * @param string $value Содержимое поля Ссылка YouTube
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_link_youtube(string $value): ?string {
    $error_message = null;

    switch (false) {
        case (is_filled($value)):
            $error_message = 'Это поле должно быть заполнено';
            break;
        case (is_correct_link($value)):
            $error_message = 'Введите корректную ссылку';
            break;
        case (is_link_youtube($value)):
            $error_message = 'Введите ссылку на видео с YouTube';
            break;
    }

    return $error_message;
}

/**
 * Валидация поля Электронная почта
 * @param string $value Содержимое поля Электронная почта
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_email(string $value): ?string {
    $error_message = null;

    switch (false) {
        case (is_filled($value)):
            $error_message = 'Это поле должно быть заполнено';
            break;
        case (is_email($value)):
            $error_message = 'Введите корректный Email';
            break;
    }

    return $error_message;
}

/**
 * Валидация поля Логин
 * @param string $value Содержимое поля Логин
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_login(string $value): ?string {
    $error_message = null;

    switch (false) {
        case (is_filled($value)):
            $error_message = 'Это поле должно быть заполнено';
            break;
        case (is_correct_length($value, 0, DATABASE_VARCHAR_MAX_SIZE)):
            $error_message = 'Введите значение до ' . DATABASE_VARCHAR_MAX_SIZE . ' символов';
            break;
    }

    return $error_message;
}

/**
 * Валидация поля Пароль
 * @param string $value Содержимое поля Пароль
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_password(string $value): ?string {
    $error_message = null;

    switch (false) {
        case (is_filled($value)):
            $error_message = 'Это поле должно быть заполнено';
            break;
        case (is_strong_password($value)):
            $error_message = 'Пароль должен содержать не менее 6 символов, в нем должны быть цифры и латинские буквы верхнего и нижнего регистров';
            break;
    }

    return $error_message;
}

/**
 * Валидация поля Повтор пароля
 * @param string $value Содержимое поля Повтор пароля
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_password_repeat(string $value): ?string {
    $error_message = null;

    switch (false) {
        case (is_filled($value)):
            $error_message = 'Это поле должно быть заполнено';
            break;
        case ($value === $_POST['password']):
            $error_message = 'Пароли не совпадают';
            break;
    }

    return $error_message;
}

/**
 * Валидация загружаемого файла для аватара
 * @param array $value Массив с данными файла
 * @return string | null Текст ошибки или null, если валидация пройдена
 */
function validate_avatar(array $value): ?string {
    $error_message = null;
    $image_mime_types = ['image/jpeg', 'image/png'];
    $file_type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $value['tmp_name']);

    switch (false) {
        case (is_correct_mime_types($file_type, $image_mime_types)):
            $error_message = 'Загрузите изображение в формате JPEG или PNG';
            break;
        case (is_correct_file_size($value['size'], POST_PHOTO_MAX_FILE_SIZE)):
            $error_message = 'Размер файла не должен превышать ' . POST_PHOTO_MAX_FILE_SIZE / 1048576 . 'МБ';
            break;
    }

    return $error_message;
}

/**
 * Валидация массива данных по массиву правил
 * @param array $data_array Массив данных для валидации
 * @param array $rules_array Массив правил валидации
 * @return array Массив с ошибками
 */
function validate(array $data_array, array $rules_array): array  {
    $errors =[];

    foreach ($data_array as $key => $value) {
        if (isset($rules_array[$key])) {
            $rule = $rules_array[$key];
            $errors[$key] = $rule($value);
        }
    }

    return $errors;
}

/**
 * Загружает файл в папку uploads
 * @param string | array $file Файл, который нужно записать
 * @return string Имя записанного файла
 */
function upload_file($file): string {
    $file_extension = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif'
    ];

    if (is_string($file)) {
        $file = check_protocol($file);
        $headers = get_headers($file, 1);
        $path = $file_extension[$headers['Content-Type']];
        $filename = 'uploads/' . uniqid() . '.' . $path;

        file_put_contents($filename, file_get_contents($file));
    } else {
        $tmp_name = $file['tmp_name'];
        $path = $file['name'];
        $filename = 'uploads/' . uniqid() . '.' . pathinfo($path, PATHINFO_EXTENSION);

        move_uploaded_file($tmp_name, $filename);
    }

    return $filename;
}

/**
 * Преобразует массив с данными если тип контента - Фото
 * @param array Первоначальный массив с данными
 * @param string $content_type Тип контента
 * @return array Массив с данными
 */
function prepare_post_data(array $post, string $content_type): array {
    if ($content_type === 'photo') {
        if (!empty($_FILES['photo']['name'])) {
            $post['photo'] = upload_file($_FILES['photo']);
        } elseif (!empty($post['link'])) {
            $post['photo'] = upload_file($post['link']);
        }

        unset($post['link']);
    }

    return $post;
}

/**
 * Подготавливает массив с правилами валидации в зависимости от типа контента
 * @param string $content_type Тип контента
 * @return array Массив с правилами валидации
 */
function prepare_post_rules(string $content_type): array {
    $rules = [
        'title' => function($value) {return validate_title($value); },
        'hashtag_name' => function($value) {return validate_tags($value); },
    ];

    switch ($content_type) {
        case 'photo':
            if (!empty($_FILES['photo']['name'])) {
                $rules['photo'] = function($value) {return validate_photo($value); };
            } else {
                $rules['link'] = function($value) {return validate_link_photo($value); };
            }
            break;
        case 'video':
            $rules['video'] = function($value) {return validate_link_youtube($value); };
            break;
        case 'text':
            $rules['content'] = function($value) {return validate_content_text($value); };
            break;
        case 'quote':
            $rules['content'] = function($value) {return validate_content_quote($value); };
            $rules['quote_author'] = function($value) {return validate_quote_author($value); };
            break;
        case 'link':
            $rules['link'] = function($value) {return validate_link($value); };
            break;
    }

    return $rules;
}

/**
 * Проверяет наличие протокола в ссылке, если его нет - добавляет
 * @param string $value Ссылка
 * @return string Ссылка с протоколом
 */
function check_protocol(string $value): string {
    return parse_url($value, PHP_URL_SCHEME) ? $value : "http://{$value}";
}
