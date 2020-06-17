<main class="page__main page__main--profile">
    <h1 class="visually-hidden">Профиль</h1>
    <div class="profile profile--default">
        <div class="profile__user-wrapper">
            <div class="profile__user user container">
                <div class="profile__user-info user__info">
                    <div class="profile__avatar user__avatar">
                        <img class="profile__picture user__picture" src="<?= $profile_data['avatar'] ?? 'img/icon-input-user.svg'; ?>" alt="Аватар пользователя" width="100" height="100">
                    </div>
                    <div class="profile__name-wrapper user__name-wrapper">
                        <span class="profile__name user__name"><?= htmlspecialchars($profile_data['login']); ?></span>
                        <time class="profile__user-time user__time" datetime="<?= $profile_data['dt_add']; ?>"><?= format_time($profile_data['dt_add']); ?> на сайте</time>
                    </div>
                </div>
                <div class="profile__rating user__rating">
                    <p class="profile__rating-item user__rating-item user__rating-item--publications">
                        <span class="user__rating-amount"><?= $profile_data['posts_count']; ?></span>
                        <span class="profile__rating-text user__rating-text"><?= get_noun_plural_form($profile_data['posts_count'] , 'публикация', 'публикации', 'побликаций'); ?></span>
                    </p>
                    <p class="profile__rating-item user__rating-item user__rating-item--subscribers">
                        <span class="user__rating-amount"><?= $profile_data['follower_count']; ?></span>
                        <span class="profile__rating-text user__rating-text"><?= get_noun_plural_form($profile_data['follower_count'], 'подписчик', 'подписчика', 'подписчиков'); ?></span>
                    </p>
                </div>
                <div class="profile__user-buttons user__buttons">
                    <?php if ($profile_data['id'] !== $user_data['id']): ?>
                        <a class="profile__user-button user__button user__button--subscription button button--<?= !$profile_data['is_follower'] ? 'main' : 'quartz'; ?>" href="/actions/toggle-subscription.php?author_id=<?= $profile_data['id'] ?>"><?= !$profile_data['is_follower'] ? 'Подписаться' : 'Отписаться'; ?></a>
                        <?php if ($profile_data['is_follower']): ?>
                            <a class="profile__user-button user__button user__button--writing button button--green" href="/messages.php?id=<?= $profile_data['id']; ?>">Сообщение</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="profile__tabs-wrapper tabs">
            <div class="container">
                <div class="profile__tabs filters">
                    <b class="profile__tabs-caption filters__caption">Показать:</b>
                    <ul class="profile__tabs-list filters__list tabs__list">
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item button <?= $query_parameter === 'posts' ? 'filters__button--active tabs__item--active' : ''; ?>"
                               href="<?= get_query_href(['query-parameter' => null, 'comments' => null, 'comments-all' => null], '/profile.php'); ?>">Посты</a>
                        </li>
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item button <?= $query_parameter === 'likes' ? 'filters__button--active tabs__item--active' : ''; ?>"
                               href="<?= get_query_href(['query-parameter' => 'likes', 'comments' => null, 'comments-all' => null], '/profile.php'); ?>">Лайки</a>
                        </li>
                        <li class="profile__tabs-item filters__item">
                            <a class="profile__tabs-link filters__button tabs__item button <?= $query_parameter === 'subscriptions' ? 'filters__button--active tabs__item--active' : ''; ?>"
                               href="<?= get_query_href(['query-parameter' => 'subscriptions', 'comments' => null, 'comments-all' => null], '/profile.php'); ?>">Подписки</a>
                        </li>
                    </ul>
                </div>
                <div class="profile__tab-content">
                    <?= $profile_content; ?>
                </div>
            </div>
        </div>
    </div>
</main>
