<section class="profile__posts tabs__content tabs__content--active">
    <h2 class="visually-hidden">Публикации</h2>
    <?php foreach ($posts as $post) : ?>
        <article class="profile__post post post-<?= $post['class_name']; ?>">
            <header class="post__header">
                <?php if (!empty($post['author_id'])) : ?>
                    <div class="post__author">
                        <a class="post__author-link" href="/profile.php?id=<?= $post['author_id']; ?>" title="Автор">
                            <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                                <img class="post__author-avatar"
                                     src="<?= $post['author_avatar'] ?? 'img/icon-input-user.svg'; ?>"
                                     alt="Аватар пользователя" width="60" height="60">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name">Репост: <?= htmlspecialchars($post['author_login']); ?></b>
                                <time class="post__time" datetime="<?= $post['original_post_dt_add']; ?>"
                                      title="
                                      <?= date_format(date_create($post['original_post_dt_add']), 'd.m.Y H:i'); ?>">
                                    <?= format_time($post['original_post_dt_add']); ?> назад
                                </time>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
                <h2 style="padding: 29px 40px;">
                    <a href="../post.php?id=<?= $post['id']; ?>"><?= htmlspecialchars($post['title']); ?></a>
                </h2>
            </header>
            <div class="post__main">
                <?php switch ($post['class_name']) :
                    case 'quote':
                        ?>
                        <blockquote>
                            <p>
                                <?= htmlspecialchars($post['content']); ?>
                            </p>
                            <cite><?= htmlspecialchars($post['quote_author']); ?></cite>
                        </blockquote>
                        <?php
                              break; ?>
                    <?php case 'link':
                        ?>
                        <div class="post-link__wrapper">
                            <a class="post-link__external"
                               href="<?= append_protocol(htmlspecialchars($post['link'])); ?>"
                               title="Перейти по ссылке">
                                <div class="post-link__info-wrapper">
                                    <div class="post-link__icon-wrapper">
                                        <img src="https://www.google.com/s2/favicons?domain=<?=
                                        htmlspecialchars($post['link']);
                                        ?>"
                                             alt="Иконка" width="120" height="120">
                                    </div>
                                    <div class="post-link__info">
                                        <h3><?= htmlspecialchars($post['title']); ?></h3>
                                        <span><?= htmlspecialchars($post['link']); ?></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php
                              break; ?>
                    <?php case 'photo':
                        ?>
                        <div class="post-photo__image-wrapper">
                            <img src="<?= htmlspecialchars($post['photo']); ?>"
                                 alt="<?= htmlspecialchars($post['title']); ?>" width="760" height="396">
                        </div>
                        <?php
                              break; ?>
                    <?php case 'video':
                        ?>
                        <div class="post-video__block">
                            <div class="post-video__preview">
                                <?= embed_youtube_video(htmlspecialchars($post['video'])); ?>
                            </div>
                        </div>
                        <?php
                              break; ?>
                    <?php case 'text':
                        $post_content = cut_text(htmlspecialchars($post['content'])); ?>
                        <p><?= $post_content; ?></p>
                        <?php if (htmlspecialchars($post['content']) !== $post_content) : ?>
                            <div class="post-text__more-link-wrapper">
                                <a class="post-text__more-link" href="/post.php?id=<?= $post['id']; ?>">Читать далее</a>
                            </div>
                        <?php endif; ?>
                        <?php
                              break; ?>
                <?php endswitch; ?>
            </div>
            <footer class="post__footer">
                <div class="post__indicators">
                    <div class="post__buttons">
                        <a class="post__indicator post__indicator--likes button"
                           href="/actions/toggle-like.php?id=<?= $post['id']; ?>" title="Лайк">
                            <svg class="post__indicator-icon <?= !empty($post['is_liked']) ?
                                'post__indicator-icon--like-active' : ''; ?>"
                                 width="20" height="17">
                                <use xlink:href="#icon-heart"></use>
                            </svg>
                            <svg class="post__indicator-icon <?= empty($post['is_liked']) ?
                                'post__indicator-icon--like-active' : ''; ?>"
                                 width="20" height="17">
                                <use xlink:href="#icon-heart-active"></use>
                            </svg>
                            <span><?= $post['likes_count']; ?></span>
                            <span class="visually-hidden">количество лайков</span>
                        </a>
                        <a class="post__indicator post__indicator--repost button"
                           <?php if ($post['user_id'] !== $user_data['id']) : ?>
                               href="/actions/repost.php?id=<?= $post['id']; ?>"
                           <?php endif; ?>
                           title="Репост">
                            <svg class="post__indicator-icon" width="19" height="17">
                                <use xlink:href="#icon-repost"></use>
                            </svg>
                            <span><?= $post['repost_count']; ?></span>
                            <span class="visually-hidden">количество репостов</span>
                        </a>
                    </div>
                    <time class="post__time" datetime="<?= $post['dt_add']; ?>"
                          title="<?= date_format(date_create($post['dt_add']), 'd.m.Y H:i'); ?>">
                        <?= format_time($post['dt_add']); ?>назад
                    </time>
                </div>
                <ul class="post__tags">
                    <?php if (!empty($post['hashtags'])) : ?>
                        <?php foreach ($post['hashtags'] as $hashtag) : ?>
                            <li><a href="/search.php?q=%23<?= $hashtag; ?>">#<?= $hashtag; ?></a></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </footer>
            <?php if ($post['comments_count'] > 0) : ?>
                <?php if (!in_array($post['id'], $showed_comments_post_ids)) : ?>
                    <div class="comments">
                        <a class="comments__button button" href="<?= get_query_href([
                            'comments' => !$showed_comments_post_ids ? $post['id'] : filter_input(
                                INPUT_GET,
                                'comments'
                            ) . "+{$post['id']}"
                        ], '/profile.php'); ?>">Показать комментарии</a>
                    </div>
                <?php else : ?>
                    <div class="comments__list-wrapper">
                        <ul class="comments__list">
                            <?php for ($i = 0, $comment_num = min(
                                count($post['comments']),
                                $post['comments_count_in_page']
                            ); $i < $comment_num; $i++) : ?>
                                <li class="comments__item user">
                                    <div class="comments__avatar">
                                        <a class="user__avatar-link"
                                           href="../profile.php?id=<?= $post['comments'][$i]['user_id']; ?>">
                                            <img class="comments__picture"
                                                 src="../<?= $post['comments'][$i]['avatar'] ??
                                                    'img/icon-input-user.svg'; ?>"
                                                 alt="Аватар пользователя" width="40" height="40">
                                        </a>
                                    </div>
                                    <div class="comments__info">
                                        <div class="comments__name-wrapper">
                                            <a class="comments__user-name"
                                               href="../profile.php?id=<?= $post['comments'][$i]['user_id']; ?>">
                                                <span><?= htmlspecialchars($post['comments'][$i]['login']); ?></span>
                                            </a>
                                            <time class="comments__time"
                                                  datetime="<?= $post['comments'][$i]['dt_add']; ?>"
                                                  title="
                                                  <?= date_format(
                                                      date_create($post['original_post_dt_add']),
                                                      'd.m.Y H:i'
                                                  ); ?>">
                                                <?= format_time($post['comments'][$i]['dt_add']); ?> назад
                                            </time>
                                        </div>
                                        <p class="comments__text">
                                            <?= htmlspecialchars($post['comments'][$i]['content']); ?>
                                        </p>
                                    </div>
                                </li>
                            <?php endfor; ?>
                        </ul>
                        <?php if ($post['comments_count'] > MAX_COMMENT_COUNT &&
                            $post['comments_count'] !== $post['comments_count_in_page']) : ?>
                            <a class="comments__more-link"
                               href="<?= get_query_href(['comments-all' => !$showed_comments_all_post_ids ?
                                   $post['id'] :
                                   filter_input(INPUT_GET, 'comments-all') . "+{$post['id']}"], '/profile.php'); ?>">
                                <span>Показать все комментарии</span>
                                <sup class="comments__amount"><?= $post['comments_count']; ?></sup>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($post['comments_count'] === 0 || in_array($post['id'], $showed_comments_post_ids)) : ?>
                <form class="comments__form form" method="post" style="margin-bottom: 0">
                    <div class="comments__my-avatar">
                        <img class="comments__picture"
                             src="../<?= $user_data['avatar'] ?? 'img/icon-input-user.svg'; ?>"
                             alt="Аватар пользователя" width="40" height="40">
                    </div>
                    <div class="form__input-section <?= !empty($errors) && !empty($new_comment['post_id']) &&
                    (int)$new_comment['post_id'] === $post['id'] ? 'form__input-section--error' : ''; ?>">
                        <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
                        <textarea class="comments__textarea form__textarea" placeholder="Ваш комментарий"
                                  name="content"><?= !empty($new_comment['post_id']) &&
                                    (int)$new_comment['post_id'] === $post['id'] ?
                                    htmlspecialchars($new_comment['content']) : ''; ?></textarea>
                        <label class="visually-hidden">Ваш комментарий</label>
                        <button class="form__error-button button" type="button">!</button>
                        <?php if (!empty($errors)) : ?>
                            <div class="form__error-text">
                                <h3 class="form__error-title">Комментарий</h3>
                                <p class="form__error-desc"><?= !empty($errors['content']) ?
                                        $errors['content'] : ''; ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button class="comments__submit button button--green" type="submit">Отправить</button>
                </form>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</section>
