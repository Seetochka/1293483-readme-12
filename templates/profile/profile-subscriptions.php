<section class="profile__subscriptions tabs__content tabs__content--active">
    <h2 class="visually-hidden">Подписки</h2>
    <ul class="profile__subscriptions-list">
        <?php foreach ($authors as $author): ?>
            <li class="post-mini post-mini--photo post user">
                <div class="post-mini__user-info user__info">
                    <div class="post-mini__avatar user__avatar">
                        <a class="user__avatar-link" href="/profile.php?id=<?= $author['id'] ?>">
                            <img class="post-mini__picture user__picture"
                                 src="<?= $author['avatar'] ?? 'img/icon-input-user.svg'; ?>" alt="Аватар пользователя"
                                 width="60" height="60">
                        </a>
                    </div>
                    <div class="post-mini__name-wrapper user__name-wrapper">
                        <a class="post-mini__name user__name" href="/profile.php?id=<?= $author['id'] ?>">
                            <span><?= htmlspecialchars($author['login']); ?></span>
                        </a>
                        <time class="post-mini__time user__additional" datetime="<?= $author['dt_add']; ?>"
                              title="<?= date_format(date_create($author['dt_add']),
                                  'd.m.Y H:i'); ?>"><?= format_time($author['dt_add']); ?> на сайте
                        </time>
                    </div>
                </div>
                <div class="post-mini__rating user__rating">
                    <p class="post-mini__rating-item user__rating-item user__rating-item--publications">
                        <span class="post-mini__rating-amount user__rating-amount"><?= $author['posts_count']; ?></span>
                        <span class="post-mini__rating-text user__rating-text"><?= get_noun_plural_form($author['posts_count'],
                                'публикация', 'публикации', 'побликаций'); ?></span>
                    </p>
                    <p class="post-mini__rating-item user__rating-item user__rating-item--subscribers">
                        <span class="post-mini__rating-amount user__rating-amount"><?= $author['follower_count']; ?></span>
                        <span class="post-mini__rating-text user__rating-text"><?= get_noun_plural_form($author['follower_count'],
                                'подписчик', 'подписчика', 'подписчиков'); ?></span>
                    </p>
                </div>
                <div class="post-mini__user-buttons user__buttons" style="min-width: 200px;">
                    <?php if ($author['id'] !== $user_data['id']): ?>
                        <a class="post-mini__user-button user__button user__button--subscription button button--<?= !$author['is_follower'] ? 'main' : 'quartz'; ?>"
                           href="/actions/toggle-subscription.php?author_id=<?= $author['id'] ?>"><?= !$author['is_follower'] ? 'Подписаться' : 'Отписаться'; ?></a>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
