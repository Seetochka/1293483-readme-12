<section class="profile__likes tabs__content tabs__content--active">
    <h2 class="visually-hidden">Лайки</h2>
    <ul class="profile__likes-list">
        <?php foreach ($posts as $post): ?>
            <li class="post-mini post-mini--<?= $post['class_name']; ?> post user">
                <div class="post-mini__user-info user__info">
                    <div class="post-mini__avatar user__avatar">
                        <a class="user__avatar-link" href="/profile.php?id=<?= $post['user_id']; ?>">
                            <img class="post-mini__picture user__picture"
                                 src="<?= $post['avatar'] ?? 'img/icon-input-user.svg'; ?>" alt="Аватар пользователя"
                                 width="60" height="60">
                        </a>
                    </div>
                    <div class="post-mini__name-wrapper user__name-wrapper">
                        <a class="post-mini__name user__name" href="/profile.php?id=<?= $post['user_id']; ?>">
                            <span><?= htmlspecialchars($post['login']); ?></span>
                        </a>
                        <div class="post-mini__action">
                            <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                            <time class="post-mini__time user__additional" datetime="<?= $post['dt_add']; ?>"
                                  title="<?= date_format(date_create($post['dt_add']),
                                      'd.m.Y H:i'); ?>"><?= format_time($post['dt_add']); ?> назад
                            </time>
                        </div>
                    </div>
                </div>
                <div class="post-mini__preview">
                    <a class="post-mini__link" href="/post.php?id=<?= $post['id'] ?>" title="Перейти на публикацию">
                        <?php switch ($post['class_name']):
                            case 'quote': ?>
                                <span class="visually-hidden">Цитата</span>
                                <svg class="post-mini__preview-icon" width="21" height="20">
                                    <use xlink:href="#icon-filter-quote"></use>
                                </svg>
                                <?php break; ?>
                            <?php case 'link': ?>
                                <span class="visually-hidden">Ссылка</span>
                                <svg class="post-mini__preview-icon" width="21" height="18">
                                    <use xlink:href="#icon-filter-link"></use>
                                </svg>
                                <?php break; ?>
                            <?php case 'photo': ?>
                                <div class="post-mini__image-wrapper">
                                    <img class="post-mini__image" src="<?= htmlspecialchars($post['photo']); ?>"
                                         width="109" height="109" alt="Превью публикации">
                                </div>
                                <span class="visually-hidden">Фото</span>
                                <?php break; ?>
                            <?php case 'video': ?>
                                <div class="post-mini__image-wrapper">
                                    <?= embed_youtube_cover(htmlspecialchars($post['video'])); ?>
                                    <span class="post-mini__play-big">
                                        <svg class="post-mini__play-big-icon" width="12" height="13">
                                            <use xlink:href="#icon-video-play-big"></use>
                                        </svg>
                                    </span>
                                </div>
                                <span class="visually-hidden">Видео</span>
                                <?php break; ?>
                            <?php case 'text': ?>
                                <span class="visually-hidden">Текст</span>
                                <svg class="post-mini__preview-icon" width="20" height="21">
                                    <use xlink:href="#icon-filter-text"></use>
                                </svg>
                                <?php break; ?>
                        <?php endswitch; ?>
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
