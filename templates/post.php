<main class="page__main page__main--publication">
    <div class="container">
        <h1 class="page__title page__title--publication"><?= htmlspecialchars($post['title']); ?></h1>
        <section class="post-details">
            <h2 class="visually-hidden">Публикация</h2>
            <div class="post-details__wrapper post-photo">
                <div class="post-details__main-block post post--details">

                    <?= $post_content; ?>

                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button"
                               href="/actions/toggle-like.php?id=<?= $post['id']; ?>" title="Лайк">
                                <svg class="post__indicator-icon <?= !empty($post['is_liked'] )? 'post__indicator-icon--like-active' : ''; ?>"
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
                            <a class="post__indicator post__indicator--comments button" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?= $post['comments_count']; ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                            <a class="post__indicator post__indicator--repost button"
                               <?php if ($post['user_id'] !== $user_data['id']): ?>href="/actions/repost.php?id=<?= $post['id']; ?>"<?php endif; ?>
                               title="Репост">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-repost"></use>
                                </svg>
                                <span><?= $post['repost_count']; ?></span>
                                <span class="visually-hidden">количество репостов</span>
                            </a>
                        </div>
                        <span class="post__view"><?= $post['show_count'] . get_noun_plural_form($post['show_count'],
                                ' просмотр', ' просмотра', ' просмотров'); ?></span>
                    </div>
                    <div class="comments">
                        <form class="comments__form form" method="post">
                            <div class="comments__my-avatar">
                                <img class="comments__picture"
                                     src="../<?= $user_data['avatar'] ?? 'img/icon-input-user.svg'; ?>"
                                     alt="Аватар пользователя" width="40" height="40">
                            </div>
                            <div class="form__input-section <?= !empty($errors) ? 'form__input-section--error' : ''; ?>">
                                <textarea class="comments__textarea form__textarea form__input"
                                          placeholder="Ваш комментарий"
                                          name="content"><?= !empty($new_comment['content']) ? htmlspecialchars($new_comment['content']) : ''; ?></textarea>
                                <label class="visually-hidden">Ваш комментарий</label>
                                <button class="form__error-button button" type="button">!</button>
                                <?php if (!empty($errors)): ?>
                                    <div class="form__error-text">
                                        <h3 class="form__error-title">Комментарий</h3>
                                        <p class="form__error-desc"><?= $errors['content']; ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button class="comments__submit button button--green" type="submit">Отправить</button>
                        </form>
                        <div class="comments__list-wrapper">
                            <ul class="comments__list">
                                <?php for ($i = 0, $comment_num = min(count($comments),
                                    $comments_count); $i < $comment_num; $i++): ?>
                                    <li class="comments__item user">
                                        <div class="comments__avatar">
                                            <a class="user__avatar-link"
                                               href="/profile.php?id=<?= $comments[$i]['user_id']; ?>">
                                                <img class="comments__picture" src="<?= $comments[$i]['avatar']; ?>"
                                                     alt="Аватар пользователя" width="40" height="40">
                                            </a>
                                        </div>
                                        <div class="comments__info">
                                            <div class="comments__name-wrapper">
                                                <a class="comments__user-name"
                                                   href="/profile.php?id=<?= $comments[$i]['user_id']; ?>">
                                                    <span><?= htmlspecialchars($comments[$i]['login']); ?></span>
                                                </a>
                                                <time class="comments__time" datetime="<?= $comments[$i]['dt_add']; ?>"
                                                      title="<?= date_format(date_create($comments[$i]['dt_add']),
                                                          'd.m.Y H:i'); ?>"><?= format_time($comments[$i]['dt_add']); ?>
                                                    назад
                                                </time>
                                            </div>
                                            <p class="comments__text">
                                                <?= htmlspecialchars($comments[$i]['content']); ?>
                                            </p>
                                        </div>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                            <?php if ($post['comments_count'] > MAX_COMMENT_COUNT && $post['comments_count'] !== $comments_count): ?>
                                <a class="comments__more-link"
                                   href="<?= get_query_href(['comments' => 'all'], '/post.php') ?>">
                                    <span>Показать все комментарии</span>
                                    <sup class="comments__amount"><?= $post['comments_count']; ?></sup>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="post-details__user user">
                    <div class="post-details__user-info user__info">
                        <div class="post-details__avatar user__avatar">
                            <a class="post-details__avatar-link user__avatar-link"
                               href="/profile.php?id=<?= $author['id']; ?>">
                                <img class="post-details__picture user__picture"
                                     src="<?= $author['avatar'] ?? 'img/icon-input-user.svg'; ?>"
                                     alt="Аватар пользователя" width="60" height="60">
                            </a>
                        </div>
                        <div class="post-details__name-wrapper user__name-wrapper">
                            <a class="post-details__name user__name" href="/profile.php?id=<?= $author['id']; ?>">
                                <span><?= htmlspecialchars($author['login']); ?></span>
                            </a>
                            <time class="post-details__time user__time" datetime="<?= $author['dt_add']; ?>"
                                  title="<?= date_format(date_create($author['dt_add']),
                                      'd.m.Y H:i'); ?>"><?= format_time($author['dt_add']); ?>на сайте
                            </time>
                        </div>
                    </div>
                    <div class="post-details__rating user__rating">
                        <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                            <span class="post-details__rating-amount user__rating-amount"><?= $author['follower_count']; ?></span>
                            <span class="post-details__rating-text user__rating-text"><?= get_noun_plural_form($author['follower_count'],
                                    'подписчик', 'подписчика', 'подписчиков'); ?></span>
                        </p>
                        <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                            <span class="post-details__rating-amount user__rating-amount"><?= $author['posts_count']; ?></span>
                            <span class="post-details__rating-text user__rating-text"><?= get_noun_plural_form($author['posts_count'],
                                    'публикация', 'публикации', 'публикаций'); ?></span>
                        </p>
                    </div>
                    <div class="post-details__user-buttons user__buttons">
                        <?php if ($author['id'] !== $user_data['id']): ?>
                            <a class="user__button user__button--subscription button button--<?= !$author['is_follower'] ? 'main' : 'quartz'; ?>"
                               href="/actions/toggle-subscription.php?author_id=<?= $author['id'] ?>"><?= !$author['is_follower'] ? 'Подписаться' : 'Отписаться'; ?></a>
                            <?php if ($author['is_follower']): ?>
                                <a class="user__button user__button--writing button button--green"
                                   href="/messages.php?id=<?= $author['id']; ?>">Сообщение</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
