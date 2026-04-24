<div class="card form-card">
    <?php if (!empty($errors)): ?>
        <p class="error-title">❌ Ошибки валидации</p>

        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>

        <div class="actions">
            <a href="javascript:history.back()" class="btn btn-primary">← Вернуться к форме</a>
        </div>
    <?php else: ?>
        <p class="success-title">✅ Привычка сохранена!</p>

        <table class="record-table">
            <tr>
                <td>Название</td>
                <td><?= htmlspecialchars($record['name']) ?></td>
            </tr>

            <tr>
                <td>Описание</td>
                <td><?= nl2br(htmlspecialchars($record['description'] ?: '—')) ?></td>
            </tr>

            <tr>
                <td>Категория</td>
                <td><?= htmlspecialchars($category_labels[$record['category']] ?? $record['category']) ?></td>
            </tr>

            <tr>
                <td>Сложность</td>
                <td><?= htmlspecialchars($difficulty_labels[$record['difficulty']] ?? $record['difficulty']) ?></td>
            </tr>

            <tr>
                <td>Дата начала</td>
                <td><?= htmlspecialchars($record['start_date']) ?></td>
            </tr>

            <tr>
                <td>Цель</td>
                <td><?= (int)$record['goal_days'] ?> дней</td>
            </tr>

            <tr>
                <td>Дни выполнения</td>
                <td>
                    <?php foreach ($record['days'] as $day): ?>
                        <span class="badge badge-day">
                            <?= htmlspecialchars($day_labels[$day] ?? $day) ?>
                        </span>
                    <?php endforeach; ?>
                </td>
            </tr>

            <tr>
                <td>Дата добавления</td>
                <td><?= htmlspecialchars($record['created_at']) ?></td>
            </tr>
        </table>

        <div class="actions">
            <a href="index.php" class="btn btn-outline">➕ Добавить ещё</a>
            <a href="index.php?page=list" class="btn btn-primary">📊 Все привычки</a>
        </div>
    <?php endif; ?>
</div>