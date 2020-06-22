<main class="page__main page__main--search-results">
    <h1 class="visually-hidden">Страница результатов поиска</h1>
    <section class="search">
        <h2 class="visually-hidden">Результаты поиска</h2>
        <div class="search__query-wrapper">
            <div class="search__query container">
                <span>Вы искали:</span>
                <span class="search__query-text"><?= htmlspecialchars($search_query); ?></span>
            </div>
        </div>
        <div class="search__results-wrapper">
            <?php if (!empty($posts)) : ?>
                <div class="container">
                    <div class="search__content">
                        <?php foreach ($posts as $post) : ?>
                            <article class="search__post post post-<?= $post['class_name']; ?>">
                                <header class="post__header post__author">
                                    <a class="post__author-link" href="/profile.php?id=<?= $post['user_id']; ?>"
                                       title="Автор">
                                        <div class="post__avatar-wrapper">
                                            <img class="post__author-avatar"
                                                 src="<?= $post['avatar'] ?? 'img/icon-input-user.svg'; ?>"
                                                 alt="Аватар пользователя" width="60" height="60">
                                        </div>
                                        <div class="post__info">
                                            <b class="post__author-name"><?= htmlspecialchars($post['login']); ?></b>
                                            <span class="post__time"><?= format_time($post['dt_add']); ?>назад</span>
                                        </div>
                                    </a>
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
                                                    <div class="post-link__icon-wrapper">
                                                        <img src="https://www.google.com/s2/favicons?domain=<?=
                                                        htmlspecialchars($post['link']);
                                                        ?>" alt="Иконка" width="120" height="120">
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
                                            <?php
                                                  break; ?>
                                        <?php case 'photo':
                                            ?>
                                            <h2>
                                                <a href="post.php?id=<?= $post['id']; ?>">
                                                    <?= htmlspecialchars($post['title']); ?>
                                                </a>
                                            </h2>
                                            <div class="post-photo__image-wrapper">
                                                <img src="<?= htmlspecialchars($post['photo']); ?>"
                                                     alt="<?= htmlspecialchars($post['title']); ?>" width="760"
                                                     height="396">
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
                                            <h2>
                                                <a href="post.php?id=<?= $post['id']; ?>">
                                                    <?= htmlspecialchars($post['title']); ?>
                                                </a>
                                            </h2>
                                            <p>
                                                <?= $post_content; ?>
                                            </p>
                                            <?php if (htmlspecialchars($post['content']) !== $post_content) : ?>
                                                <div class="post-text__more-link-wrapper">
                                                    <a class="post-text__more-link"
                                                       href="/post.php?id=<?= $post['id']; ?>">Читать далее</a>
                                                </div>
                                            <?php endif; ?>
                                            <?php
                                                  break; ?>
                                    <?php endswitch; ?>
                                </div>
                                <footer class="post__footer post__indicators">
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
                                        <a class="post__indicator post__indicator--comments button"
                                           href="/post.php?id=<?= $post['id']; ?>" title="Комментарии">
                                            <svg class="post__indicator-icon" width="19" height="17">
                                                <use xlink:href="#icon-comment"></use>
                                            </svg>
                                            <span><?= $post['comments_count']; ?></span>
                                            <span class="visually-hidden">количество комментариев</span>
                                        </a>
                                    </div>
                                    <ul class="post__tags">
                                        <?php if (!empty($post['hashtags'])) : ?>
                                            <?php foreach ($post['hashtags'] as $hashtag) : ?>
                                                <li>
                                                    <a href="/search.php?q=%23<?= $hashtag; ?>">
                                                        #<?= $hashtag; ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </footer>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="search__no-results container">
                    <p class="search__no-results-info">К сожалению, ничего не найдено.</p>
                    <p class="search__no-results-desc">
                        Попробуйте изменить поисковый запрос или просто зайти в раздел &laquo;Популярное&raquo;, там
                        живет самый крутой контент.
                    </p>
                    <div class="search__links">
                        <a class="search__popular-link button button--main" href="/popular.php">Популярное</a>
                        <a class="search__back-link" href="<?= $_SERVER['HTTP_REFERER'] ?? '/'; ?>">Вернуться назад</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
