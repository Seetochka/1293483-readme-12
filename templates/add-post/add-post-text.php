<div class="adding-post__textarea-wrapper form__textarea-wrapper">
    <label class="adding-post__label form__label" for="post-text">Текст поста
        <span class="form__input-required">*</span>
    </label>
    <div class="form__input-section <?= !empty($errors['content'] )? 'form__input-section--error' : ''; ?>">
        <textarea class="adding-post__textarea form__textarea form__input" id="post-text"
                  placeholder="Введите текст публикации"
                  name="content"><?= !empty($post['content']) ? htmlspecialchars($post['content']) : ''; ?></textarea>
        <button class="form__error-button button" type="button">!
            <span class="visually-hidden">Информация об ошибке</span>
        </button>
        <div class="form__error-text">
            <h3 class="form__error-title"><?= $title_errors['content']; ?></h3>
            <p class="form__error-desc"><?= $errors['content']; ?></p>
        </div>
    </div>
</div>
