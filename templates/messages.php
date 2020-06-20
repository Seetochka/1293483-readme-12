<main class="page__main page__main--messages">
    <h1 class="visually-hidden">Личные сообщения</h1>
    <section class="messages tabs">
        <h2 class="visually-hidden">Сообщения</h2>
        <div class="messages__contacts">
            <ul class="messages__contacts-list tabs__list">
                <?php foreach ($interlocutors as $interlocutor): ?>
                    <li class="messages__contacts-item">
                        <a class="messages__contacts-tab <?= $interlocutor['id'] === $interlocutor_id ? 'messages__contacts-tab--active' : ''; ?> tabs__item <?= $interlocutor['id'] === $interlocutor_id ? 'tabs__item--active' : ''; ?>"
                           href="<?= get_query_href(['id' => $interlocutor['id']], '/messages.php'); ?>">
                            <div class="messages__avatar-wrapper">
                                <img class="messages__avatar"
                                     src="<?= $interlocutor['avatar'] ?? 'img/icon-input-user.svg'; ?>"
                                     alt="Аватар пользователя" width="60" height="60">
                                <?php if (!empty($interlocutor['unread_messages_count']) && $interlocutor['unread_messages_count'] > 0): ?>
                                    <i class="messages__indicator"><?= $interlocutor['unread_messages_count']; ?></i>
                                <?php endif; ?>
                            </div>
                            <div class="messages__info">
                                <span class="messages__contact-name">
                                    <?= htmlspecialchars($interlocutor['login']); ?>
                                </span>
                                <div class="messages__preview">
                                    <p class="messages__preview-text">
                                        <?= !empty($interlocutor['content']) ? htmlspecialchars($interlocutor['content']) : ''; ?>
                                    </p>
                                    <time class="messages__preview-time"
                                          datetime="<?= !empty($interlocutor['dt_add']) ? $interlocutor['dt_add'] : ''; ?>"
                                          title="<?= !empty($interlocutor['dt_add']) ? date_format(date_create($interlocutor['dt_add']),
                                              'd.m.Y H:i') : ''; ?>">
                                        <?= !empty($interlocutor['date']) ? $interlocutor['date'] : ''; ?>
                                    </time>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="messages__chat">
            <?php if (!empty($interlocutor_id)): ?>
                <div class="messages__chat-wrapper">
                    <?php if (!empty($messages) && count($messages) > 0): ?>
                        <ul class="messages__list tabs__content tabs__content--active">
                            <?php foreach ($messages as $message): ?>
                                <li class="messages__item <?= $message['sender_id'] === $user_data['id'] ? 'messages__item--my' : ''; ?>">
                                    <div class="messages__info-wrapper">
                                        <div class="messages__item-avatar">
                                            <a class="messages__author-link"
                                               href="/profile.php?id=<?= $message['sender_id']; ?>">
                                                <img class="messages__avatar"
                                                     src="<?= $message['avatar'] ?? 'img/icon-input-user.svg'; ?>"
                                                     alt="Аватар пользователя" width="40" height="40">
                                            </a>
                                        </div>
                                        <div class="messages__item-info">
                                            <a class="messages__author"
                                               href="/profile.php?id=<?= $message['sender_id']; ?>">
                                                <?= htmlspecialchars($message['login']); ?>
                                            </a>
                                            <time class="messages__time" datetime="<?= $message['dt_add']; ?>"
                                                  title="<?= date_format(date_create($message['dt_add']),
                                                      'd.m.Y H:i'); ?>">
                                                <?= format_time($message['dt_add']); ?> назад
                                            </time>
                                        </div>
                                    </div>
                                    <p class="messages__text">
                                        <?= htmlspecialchars($message['content']); ?>
                                    </p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="comments">
                    <form class="comments__form form" method="post">
                        <div class="comments__my-avatar">
                            <img class="comments__picture"
                                 src="<?= $user_data['avatar'] ?? 'img/icon-input-user.svg'; ?>"
                                 alt="Аватар пользователя" width="40" height="40">
                        </div>
                        <div class="form__input-section <?= !empty($errors) ? 'form__input-section--error' : ''; ?>">
                            <textarea class="comments__textarea form__textarea form__input" placeholder="Ваше сообщение"
                                      name="content"><?= !empty($new_message['content']) ? htmlspecialchars($new_message['content']) : ''; ?></textarea>
                            <label class="visually-hidden">Ваше сообщение</label>
                            <button class="form__error-button button" type="button">!</button>
                            <?php if (!empty($errors)): ?>
                                <div class="form__error-text">
                                    <h3 class="form__error-title">Сообщение</h3>
                                    <p class="form__error-desc"><?= $errors['content']; ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button class="comments__submit button button--green" type="submit">Отправить</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
