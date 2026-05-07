<div class="card">
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <strong>Ошибки:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

        <div class="form-group">
            <label>Название привычки</label>
            <input
                type="text"
                name="name"
                value="<?= htmlspecialchars($record['name'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label>Описание</label>
            <textarea name="description"><?= htmlspecialchars($record['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Категория</label>
            <select name="category_id">
                <option value="">Выберите категорию</option>

                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= (int)$category['id'] ?>"
                        <?= (int)($record['category_id'] ?? 0) === (int)$category['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Сложность</label>
            <select name="difficulty">
                <option value="">Выберите сложность</option>

                <?php foreach ($difficulties as $key => $label): ?>
                    <option
                        value="<?= htmlspecialchars($key) ?>"
                        <?= ($record['difficulty'] ?? '') === $key ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Дата начала</label>
            <input
                type="date"
                name="start_date"
                value="<?= htmlspecialchars($record['start_date'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label>Цель в днях</label>
            <input
                type="number"
                name="goal_days"
                min="1"
                max="365"
                value="<?= htmlspecialchars((string)($record['goal_days'] ?? '')) ?>"
            >
        </div>

        <div class="form-group">
            <label>Дни выполнения</label>

            <div class="checkbox-group">
                <?php foreach ($dayLabels as $key => $label): ?>
                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            name="days[]"
                            value="<?= htmlspecialchars($key) ?>"
                            <?= in_array($key, $record['days'] ?? [], true) ? 'checked' : '' ?>
                        >
                        <?= htmlspecialchars($label) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button class="btn" type="submit">
            <?= $record ? 'Сохранить изменения' : 'Добавить привычку' ?>
        </button>
    </form>
</div>