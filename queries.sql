USE readme;

/*Добавляет типы контента для поста*/
INSERT INTO content_types
    (title, class_name)
VALUES ('Картинка', 'photo'),
       ('Видео', 'video'),
       ('Текст', 'text'),
       ('Цитата', 'quote'),
       ('Ссылка', 'link');

/*Добавляет пользователей*/
INSERT INTO users
    (dt_add, email, login, password, avatar)
VALUES ('2019-05-26 18:49', 'larisa@mail.ru', 'Лариса', '12345', 'img/userpic-larisa-small.jpg'),
       ('2019-10-07 22:11', 'vladik@gmail.com', 'Владик', 'qwerty', 'img/userpic.jpg'),
       ('2020-02-11 11:22', 'victor@gmail.com', 'Виктор', 'abc321', 'img/userpic-mark.jpg');

/*Добавляет посты*/
INSERT INTO posts
(dt_add, title, link, show_count, user_id, content_type_id)
VALUES ('2020-01-07 12:47', 'Лучшие курсы', 'www.htmlacademy.ru', '1024', 2, 5);

INSERT INTO posts
(dt_add, title, photo, show_count, user_id, content_type_id)
VALUES ('2020-03-25 16:18', 'Моя мечта', 'img/coast-medium.jpg', '549', 1, 1),
       ('2020-04-17 14:23', 'Наконец, обработал фотки!', 'img/rock-medium.jpg', '711', 3, 1);

INSERT INTO posts
(dt_add, title, content, show_count, user_id, content_type_id)
VALUES ('2020-04-21 09:18', 'Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', '924', 2, 3);

INSERT INTO posts
(dt_add, title, content, quote_author, show_count, user_id, content_type_id)
VALUES ('2020-04-21 17:08', 'Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Лариса', '506', 1, 4);

/*Добавляет комментарии к постам*/
INSERT INTO comments
    (dt_add, content, user_id, post_id)
VALUES ('2020-03-31 18:03', 'Тоже хочу на море!', 2, 2),
       ('2020-04-17 21:14', 'Неплохо вышло', 1, 3),
       ('2020-04-20 11:01', 'Ничего ты не знаешь, Джон Сноу...', 1, 4),
       ('2020-04-21 14:22', 'Говорят, последнюю серию переснимать будут', 3, 4);

/*Получение списка постов с сортировкой по популярности и вместе с именами авторов и типом контента*/
SELECT p.title, p.show_count, u.login, c.title as content_type FROM posts p
INNER JOIN users u ON p.user_id = u.id
INNER JOIN content_types c ON p.content_type_id = c.id
ORDER BY show_count DESC;

/*Получение списка постов для конкретного пользователя*/
SELECT dt_add, title, content, quote_author, photo, video, link, show_count FROM posts WHERE user_id = 2;

/*Получение списка комментариев для одного поста с логином пользователя*/
SELECT p.title, c.content, u.login FROM comments c
INNER JOIN users u ON c.user_id = u.id
INNER JOIN posts p ON c.post_id = p.id
WHERE post_id = 4;

/*Добавление лайка к посту*/
INSERT INTO likes SET dt_add = '2020-05-27 19:52', user_id = 1, post_id = 2;

/*Добавляет подписку на пользователя*/
INSERT INTO subscriptions SET follower_id = 1, author_id = 3;

/*Добавляет больше данных в БД*/
INSERT INTO users SET dt_add = '2020-05-27 15:51', email = 'svetlana@gmail.com', login = 'Светлана', password = '$2y$10$P45dZuztDb/rUfuhNUE8suFh2j4eLWf1MOsVio/l85giaZgsWOtNO', avatar = 'img/userpic-elvira.jpg';

INSERT INTO posts SET dt_add = '2020-05-27 17:52', title = 'Всегда выручает', link = 'www.php.net/manual/ru/langref.php', show_count = '0', user_id = 4, content_type_id = 5;
INSERT INTO posts SET dt_add = '2020-05-27 17:58', title = 'Обан: звездные гонки', photo = 'img/rock-medium.jpg', show_count = '0', user_id = 4, content_type_id = 1;
INSERT INTO posts SET dt_add = '2020-05-27 18:01', title = 'Вот и у меня то же самое!', content = 'Я просто выгляжу как лось, а в душе я бабочка.', quote_author = 'Лосяш', show_count = '0', user_id = 4, content_type_id = 4;
INSERT INTO posts SET dt_add = '2020-05-27 18:10', title = 'Неземной перламутр', video = 'https://www.youtube.com/watch?v=CMBDm3bjZ6E', show_count = '0', user_id = 4, content_type_id = 2;

INSERT INTO likes (dt_add, user_id, post_id)
VALUES ('2020-05-27 20:55', 1, 6),
       ('2020-05-28 17:59', 2, 7),
       ('2020-05-28 18:05', 2, 9),
       ('2020-05-29 17:59', 3, 7);

INSERT INTO subscriptions (follower_id, author_id) VALUES (4, 1), (4, 2), (2, 1);

INSERT INTO hashtags (hashtag_name) VALUES ('красота'), ('учеба'), ('курсы'), ('когдаскучно'), ('всеподряд');

INSERT INTO post_hashtag (post_id, hashtag_id)VALUES (1, 2), (1, 3), (2, 1), (3, 1), (4, 4), (5, 5), (6, 2), (6, 4), (7, 4), (8, 5), (9, 1), (9, 5);
