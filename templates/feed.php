<main class="page__main page__main--feed">
    <div class="container">
        <h1 class="page__title page__title--feed">Моя лента</h1>
    </div>
    <div class="page__main-wrapper container">
        <section class="feed">
            <h2 class="visually-hidden">Лента</h2>
            <div class="feed__main-wrapper">
                <div class="feed__wrapper">
                    <?php foreach ($posts as $post): ?>
                        <article class="feed__post post post-<?= $post['class_name']; ?>">
                            <header class="post__header post__author">
                                <a class="post__author-link"
                                   href="/profile.php?id=<?= empty($post['author_id']) ? $post['user_id'] : $post['author_id']; ?>"
                                   title="Автор">
                                    <div class="post__avatar-wrapper <?= empty($post['author_id']) ? '' : 'post__avatar-wrapper--repost'; ?>">
                                        <img class="post__author-avatar"
                                             src="<?= empty($post['author_id']) ? $post['avatar'] ?? 'img/icon-input-user.svg' : $post['author_avatar'] ?? 'img/icon-input-user.svg'; ?>"
                                             alt="Аватар пользователя" width="60" height="60">
                                    </div>
                                    <div class="post__info">
                                        <b class="post__author-name"><?= empty($post['author_id']) ? htmlspecialchars($post['login']) : 'Репост: ' . htmlspecialchars($post['author_login']); ?></b>
                                        <time class="post__time" datetime="<?= $post['dt_add']; ?>"
                                              title="<?= date_format(date_create($post['dt_add']),
                                                  'd.m.Y H:i'); ?>"><?= format_time($post['dt_add']); ?> назад
                                        </time>
                                    </div>
                                </a>
                            </header>
                            <div class="post__main">
                                <?php switch ($post['class_name']):
                                    case 'quote': ?>
                                        <blockquote>
                                            <p>
                                                <?= htmlspecialchars($post['content']); ?>
                                            </p>
                                            <cite><?= htmlspecialchars($post['quote_author']); ?></cite>
                                        </blockquote>
                                        <?php break; ?>
                                    <?php case 'link': ?>
                                        <div class="post-link__wrapper">
                                            <a class="post-link__external"
                                               href="<?= append_protocol(htmlspecialchars($post['link'])); ?>"
                                               title="Перейти по ссылке">
                                                <div class="post-link__icon-wrapper">
                                                    <img src="https://www.google.com/s2/favicons?domain=<?= htmlspecialchars($post['link']); ?>"
                                                         alt="Иконка" width="120" height="120">
                                                </div>
                                                <div class="post-link__info">
                                                    <h3><?= htmlspecialchars($post['title']); ?></h3>
                                                    <p><?= htmlspecialchars($post['title']); ?></p>
                                                    <span><?= htmlspecialchars($post['link']); ?></span>
                                                </div>
                                                <svg class="post-link__arrow" width="11" height="16">
                                                    <use xlink:href="#icon-arrow-right-ad"></use>
                                                </svg>
                                            </a>
                                        </div>
                                        <?php break; ?>
                                    <?php case 'photo': ?>
                                        <h2>
                                            <a href="post.php?id=<?= $post['id']; ?>"><?= htmlspecialchars($post['title']); ?></a>
                                        </h2>
                                        <div class="post-photo__image-wrapper">
                                            <img src="<?= htmlspecialchars($post['photo']); ?>"
                                                 alt="<?= htmlspecialchars($post['title']); ?>" width="760"
                                                 height="396">
                                        </div>
                                        <?php break; ?>
                                    <?php case 'video': ?>
                                        <div class="post-video__block">
                                            <div class="post-video__preview">
                                                <?= embed_youtube_video(htmlspecialchars($post['video'])); ?>
                                            </div>
                                        </div>
                                        <?php break; ?>
                                    <?php case 'text':
                                        $post_content = cut_text(htmlspecialchars($post['content'])); ?>
                                        <h2>
                                            <a href="post.php?id=<?= $post['id']; ?>"><?= htmlspecialchars($post['title']); ?></a>
                                        </h2>
                                        <p>
                                            <?= $post_content; ?>
                                        </p>
                                        <?php if (htmlspecialchars($post['content']) !== $post_content): ?>
                                            <div class="post-text__more-link-wrapper">
                                                <a class="post-text__more-link"
                                                   href="/post.php?id=<? $post['id']; ?>">Читать далее</a>
                                            </div>
                                        <?php endif; ?>
                                        <?php break; ?>
                                <?php endswitch; ?>
                            </div>
                            <footer class="post__footer post__indicators">
                                <div class="post__buttons">
                                    <a class="post__indicator post__indicator--likes button"
                                       href="/actions/toggle-like.php?id=<?= $post['id']; ?>" title="Лайк">
                                        <svg class="post__indicator-icon <?= !empty($post['is_liked']) ? 'post__indicator-icon--like-active' : ''; ?>"
                                             width="20" height="17">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                        <svg class="post__indicator-icon <?= empty($post['is_liked']) ? 'post__indicator-icon--like-active' : ''; ?>"
                                             width="20" height="17">
                                            <use xlink:href="#icon-heart-active"></use>
                                        </svg>
                                        <span><?= $post['likes_count']; ?></span>
                                        <span class="visually-hidden">количество лайков</span>
                                    </a>
                                    <a class="post__indicator post__indicator--comments button"
                                       href="/post.php?id=<?= $post['id']; ?>" title="Комментарии">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-comment"></use>
                                        </svg>
                                        <span><?= $post['comments_count']; ?></span>
                                        <span class="visually-hidden">количество комментариев</span>
                                    </a>
                                    <a class="post__indicator post__indicator--repost button"
                                       href="/actions/repost.php?id=<?= $post['id']; ?>" title="Репост">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-repost"></use>
                                        </svg>
                                        <span><?= $post['repost_count']; ?></span>
                                        <span class="visually-hidden">количество репостов</span>
                                    </a>
                                </div>
                                <ul class="post__tags">
                                    <?php foreach ($post['hashtags'] as $hashtag): ?>
                                        <li><a href="/search.php?q=%23<?= $hashtag; ?>">#<?= $hashtag; ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <ul class="feed__filters filters">
                <li class="feed__filters-item filters__item">
                    <a class="filters__button <?= empty($active_content_type) ? 'filters__button--active' : ''; ?>"
                       href="<?= get_query_href([
                           'content-type' => null,
                           'sorting-type' => null,
                           'sorting-order' => null
                       ], '/feed.php'); ?>">
                        <span>Все</span>
                    </a>
                </li>
                <?php foreach ($content_types as $content_type):
                    $type = $content_type['class_name']; ?>
                    <li class="feed__filters-item filters__item">
                        <a class="filters__button filters__button--<?= $content_type['class_name']; ?> button <?= (int) $content_type['id'] === $active_content_type ? 'filters__button--active' : ''; ?>"
                           href="<?= get_query_href([
                               'content-type' => $content_type['id'],
                               'sorting-type' => null,
                               'sorting-order' => null
                           ], '/feed.php'); ?>">
                            <span class="visually-hidden"><?= $content_type['title']; ?></span>
                            <svg class="filters__icon" width="<?= $content_type_size[$type]['width']; ?>"
                                 height="<?= $content_type_size[$type]['height']; ?>">
                                <use xlink:href="#icon-filter-<?= $content_type['class_name']; ?>"></use>
                            </svg>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <aside class="promo">
            <article class="promo__block promo__block--barbershop">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Все еще сидишь на окладе в офисе? Открой свой барбершоп по нашей франшизе!
                </p>
                <a class="promo__link" href="#">
                    Подробнее
                </a>
            </article>
            <article class="promo__block promo__block--technomart">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Товары будущего уже сегодня в онлайн-сторе Техномарт!
                </p>
                <a class="promo__link" href="#">
                    Перейти в магазин
                </a>
            </article>
            <article class="promo__block">
                <h2 class="visually-hidden">Рекламный блок</h2>
                <p class="promo__text">
                    Здесь<br> могла быть<br> ваша реклама
                </p>
                <a class="promo__link" href="#">
                    Разместить
                </a>
            </article>
        </aside>
    </div>
</main>
