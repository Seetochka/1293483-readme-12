<main class="page__main page__main--adding-post">
    <div class="page__main-section">
        <div class="container">
            <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
            <div class="adding-post__tabs-wrapper tabs">
                <div class="adding-post__tabs filters">
                    <ul class="adding-post__tabs-list filters__list tabs__list">
                        <?php foreach ($content_types as $content_type) :
                            $type = $content_type['class_name']; ?>
                            <li class="adding-post__tabs-item filters__item">
                                <a class="
                                adding-post__tabs-link
                                filters__button
                                filters__button--<?= $content_type['class_name']; ?>
                                <?= (int) $content_type['id'] === (int) $active_content_type ?
                                    'filters__button--active' : ''; ?>
                                tabs__item
                                <?= (int) $content_type['id'] === (int) $active_content_type ?
                                    'tabs__item--active' : ''; ?>
                                button"
                                   href="<?= get_query_href(['content-type' => $content_type['id']], 'add.php'); ?>">
                                    <svg class="filters__icon" width="<?= CONTENT_TYPE_SIZE[$type]['width']; ?>"
                                         height="<?= CONTENT_TYPE_SIZE[$type]['height']; ?>">
                                        <use xlink:href="#icon-filter-<?= $content_type['class_name']; ?>"></use>
                                    </svg>
                                    <span><?= $content_type['title']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="adding-post__tab-content">
                    <section class="
                            adding-post__<?= $content_types[$index]['class_name']; ?>
                            tabs__content
                            tabs__content--active">
                        <h2 class="visually-hidden">Форма добавления <?= $form_title[$index]; ?></h2>
                        <form class="adding-post__form form" action="/add.php" method="post"
                              enctype="multipart/form-data">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label"
                                               for="<?= $content_types[$index]['class_name']; ?>-heading">Заголовок
                                            <span class="form__input-required">*</span>
                                        </label>
                                        <div class="form__input-section <?= !empty($errors['title']) ?
                                            'form__input-section--error' : ''; ?>">
                                            <input class="adding-post__input form__input"
                                                   id="<?= $content_types[$index]['class_name']; ?>-heading" type="text"
                                                   name="title" placeholder="Введите заголовок"
                                                   value="<?= !empty($post['title']) ?
                                                       htmlspecialchars($post['title']) : ''; ?>">
                                            <button class="form__error-button button" type="button">!
                                                <span class="visually-hidden">Информация об ошибке</span>
                                            </button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title"><?= $title_errors['title']; ?></h3>
                                                <p class="form__error-desc"><?= !empty($errors['title']) ?
                                                        $errors['title'] : ''; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="content_type_id"
                                           value="<?= $content_types[$index]['id']; ?>">

                                    <?= $add_post_content; ?>

                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="video-tags">Теги</label>
                                        <div class="form__input-section <?= !empty($errors['hashtag_name']) ?
                                            'form__input-section--error' : ''; ?>">
                                            <input class="adding-post__input form__input" id="video-tags" type="text"
                                                   name="hashtag_name" placeholder="Введите теги"
                                                   value="<?= !empty($post['hashtag_name']) ?
                                                       htmlspecialchars($post['hashtag_name']) : ''; ?>">
                                            <button class="form__error-button button" type="button">!
                                                <span class="visually-hidden">Информация об ошибке</span>
                                            </button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title"><?= $title_errors['hashtag_name']; ?></h3>
                                                <p class="form__error-desc"><?= !empty($errors['hashtag_name']) ?
                                                        $errors['hashtag_name'] : ''; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (count($errors) > 0) : ?>
                                    <div class="form__invalid-block">
                                        <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                        <ul class="form__invalid-list">
                                            <?php foreach ($errors as $error => $message) : ?>
                                                <li class="form__invalid-item">
                                                    <?= "{$title_errors[$error]}. $message"; ?>.
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($content_types[$index]['class_name'] === 'photo') : ?>
                                <div class="
                                adding-post__input-file-container
                                form__input-container
                                form__input-container--file">
                                    <div class="adding-post__input-file-wrapper form__input-file-wrapper">
                                        <input class="adding-post__input-file form__input-file visually-hidden"
                                               id="userpic-file-photo" type="file" name="photo" title=" ">
                                        <label class="
                                        adding-post__input-file-button
                                        form__input-file-button
                                        form__input-file-button--photo
                                        button"
                                               for="userpic-file-photo">
                                            <span>Выбрать фото</span>
                                            <svg class="adding-post__attach-icon form__attach-icon" width="10"
                                                 height="20">
                                                <use xlink:href="#icon-attach"></use>
                                            </svg>
                                        </label>
                                    </div>
                                    <div class="
                                    adding-post__file
                                    adding-post__file--photo
                                    form__file
                                    dropzone-previews">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="adding-post__buttons">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать
                                </button>
                                <a class="adding-post__close" href="/">Закрыть</a>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</main>
