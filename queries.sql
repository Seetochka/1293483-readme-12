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
INSERT INTO likes SET user_id = 1, post_id = 4;

/*Добавляет подписку на пользователя*/
INSERT INTO subscriptions SET follower_id = 1, author_id = 3;
