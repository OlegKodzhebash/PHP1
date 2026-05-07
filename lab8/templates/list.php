<div class="card">
    <form class="search" method="get">
        <input type="hidden" name="page" value="list">

        <input
            type="text"
            name="q"
            placeholder="Поиск по названию, описанию или категории"
            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
        >

        <button class="btn" type="submit">Искать</button>
    </form>

    <?php if (empty($records)): ?>
        <p>Записей пока нет.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Категория</th>
                <th>Сложность</th>
                <th>Дата начала</th>
                <th>Цель</th>
                <th>Дни</th>
                <th>Действия</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($records as $record): ?>
                <tr>
                    <td><?= (int)$record['id'] ?></td>
                    <td><?= htmlspecialchars($record['name']) ?></td>
                    <td><?= htmlspecialchars($record['category_name']) ?></td>
                    <td><?= htmlspecialchars($difficulties[$record['difficulty']] ?? $record['difficulty']) ?></td>
                    <td><?= htmlspecialchars($record['start_date']) ?></td>
                    <td><?= (int)$record['goal_days'] ?> дней</td>
                    <td>
                        <?php foreach ($record['days'] as $day): ?>
                            <?= htmlspecialchars($dayLabels[$day] ?? $day) ?>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <div class="actions">
                            <a class="btn btn-warning" href="edit.php?id=<?= (int)$record['id'] ?>">
                                Редактировать
                            </a>

                            <form class="inline-form" method="post" action="delete.php" onsubmit="return confirm('Удалить запись?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                <input type="hidden" name="id" value="<?= (int)$record['id'] ?>">
                                <input type="hidden" name="view" value="native">

                                <button class="btn btn-danger" type="submit">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>