<?php
$is_auth = 1;
$user_name = 'Светлана';

$content_type_size = [
    'quote' => ['width' => '21', 'height' => '20'],
    'link' => ['width' => '21', 'height' => '18'],
    'photo' => ['width' => '22', 'height' => '18'],
    'video' => ['width' => '24', 'height' => '16'],
    'text' => ['width' => '20', 'height' => '21']
];

define('MAX_COMMENT_COUNT', 6);
define('POST_QUOTE_MAX_LENGTH', 70);
define('POST_PHOTO_MAX_FILE_SIZE', 10485760);
define('DATABASE_VARCHAR_MAX_SIZE', 128);
define('PAGE_ITEMS_POOR_DB', 9);
define('PAGE_ITEMS_RICH_DB', 6);
define('COMMENT_MIN_SIZE', 4);
