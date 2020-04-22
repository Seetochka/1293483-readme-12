<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <div class="popular__filters-wrapper">
        <div class="popular__sorting sorting">
            <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
            <ul class="popular__sorting-list sorting__list">
                <li class="sorting__item sorting__item--popular">
                    <a class="sorting__link sorting__link--active" href="#">
                        <span>Популярность</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
                <li class="sorting__item">
                    <a class="sorting__link" href="#">
                        <span>Лайки</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
                <li class="sorting__item">
                    <a class="sorting__link" href="#">
                        <span>Дата</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
        <div class="popular__filters filters">
            <b class="popular__filters-caption filters__caption">Тип контента:</b>
            <ul class="popular__filters-list filters__list">
                <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                    <a class="filters__button filters__button--ellipse filters__button--all filters__button--active" href="#">
                        <span>Все</span>
                    </a>
                </li>
                <?php foreach ($content_types as $content_type):
                    $type = $content_type['class_name']; ?>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--<?= $content_type['class_name']; ?> button" href="#">
                            <span class="visually-hidden"><?= $content_type['title']; ?></span>
                            <svg class="filters__icon" width="<?= $content_type_size[$type]['width']; ?>" height="<?= $content_type_size[$type]['height']; ?>">
                                <use xlink:href="#icon-filter-<?= $content_type['class_name']; ?>"></use>
                            </svg>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="popular__posts">
        <?php foreach ($posts as $post): ?>
            <article class="popular__post post post-<?= $post['class_name']; ?>">
                <header class="post__header">
                    <h2><?= htmlspecialchars($post['title']); ?></h2>
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
                                <a class="post-link__external" href="http://<?= htmlspecialchars($post['link']); ?>" title="Перейти по ссылке">
                                    <div class="post-link__info-wrapper">
                                        <div class="post-link__icon-wrapper">
                                            <img src="https://www.google.com/s2/favicons?domain=vitadental.ru" alt="Иконка">
                                        </div>
                                        <div class="post-link__info">
                                            <h3><?= htmlspecialchars($post['title']); ?></h3>
                                        </div>
                                    </div>
                                    <span><?= htmlspecialchars($post['link']); ?></span>
                                </a>
                            </div>
                            <?php break; ?>

                        <?php case 'photo': ?>
                            <div class="post-photo__image-wrapper">
                                <img src="<?= htmlspecialchars($post['photo']); ?>" alt="<?= htmlspecialchars($post['title']); ?>" width="360" height="240">
                            </div>
                            <?php break; ?>

                        <?php case 'video': ?>
                            <div class="post-video__block">
                                <div class="post-video__preview">
                                    <?=embed_youtube_cover(htmlspecialchars($post['video'])); ?>
                                    <img src="img/coast-medium.jpg" alt="Превью к видео" width="360" height="188">
                                </div>
                                <a href="post-details.html" class="post-video__play-big button">
                                    <svg class="post-video__play-big-icon" width="14" height="14">
                                        <use xlink:href="#icon-video-play-big"></use>
                                    </svg>
                                    <span class="visually-hidden">Запустить проигрыватель</span>
                                </a>
                            </div>
                            <?php break; ?>

                        <?php case 'text':
                            $post_content = cut_text(htmlspecialchars($post['content']));
                            ?>
                            <p><?= $post_content; ?></p>
                            <?php if (htmlspecialchars($post['content']) !== $post_content): ?>
                            <div class="post-text__more-link-wrapper">
                                <a class="post-text__more-link" href="#">Читать далее</a>
                            </div>
                        <?php endif; ?>
                            <?php break; ?>
                        <?php endswitch; ?>
                </div>
                <footer class="post__footer">
                    <div class="post__author">
                        <a class="post__author-link" href="#" title="Автор">
                            <div class="post__avatar-wrapper">
                                <!--укажите путь к файлу аватара-->
                                <img class="post__author-avatar" src="<?= $post['avatar']; ?>" alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name"><?= htmlspecialchars($post['login']); ?></b>
                                <time class="post__time" datetime="<?= $post['dt_add']; ?>" title="<?= date_format(date_create($post['dt_add']), 'd.m.Y H:i'); ?>"><?= format_time($post['dt_add']); ?></time>
                            </div>
                        </a>
                    </div>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span>0</span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="#" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span>0</span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                        </div>
                    </div>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
</div>
