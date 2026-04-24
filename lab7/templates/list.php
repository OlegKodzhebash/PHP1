<div class="stats">
    <div class="stat-card">
        <div class="number"><?= $total ?></div>
        <div class="label">Всего привычек</div>
    </div>

    <div class="stat-card">
        <div class="number"><?= $cats_count ?></div>
        <div class="label">Категорий</div>
    </div>

    <div class="stat-card">
        <div class="number"><?= $avg_goal ?></div>
        <div class="label">Средняя цель</div>
    </div>
</div>

<div class="card">
    <?php if (empty($records)): ?>
        <div class="empty">
            <p>Привычек пока нет.</p>
            <p><a href="index.php">Добавить первую привычку</a></p>
        </div>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>
                    <a href="<?= sortUrl('name', $sort, $order) ?>">
                        Название <?= sortArrow('name', $sort, $order) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= sortUrl('category', $sort, $order) ?>">
                        Категория <?= sortArrow('category', $sort, $order) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= sortUrl('difficulty', $sort, $order) ?>">
                        Сложность <?= sortArrow('difficulty', $sort, $order) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= sortUrl('start_date', $sort, $order) ?>">
                        Дата начала <?= sortArrow('start_date', $sort, $order) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= sortUrl('goal_days', $sort, $order) ?>">
                        Цель <?= sortArrow('goal_days', $sort, $order) ?>
                    </a>
                </th>
                <th>Дни</th>
                <th>Описание</th>
                <th>
                    <a href="<?= sortUrl('created_at', $sort, $order) ?>">
                        Добавлено <?= sortArrow('created_at', $sort, $order) ?>
                    </a>
                </th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name'] ?? '') ?></td>

                    <td>
                        <span class="badge badge-cat">
                            <?= htmlspecialchars($category_labels[$r['category'] ?? ''] ?? ($r['category'] ?? '')) ?>
                        </span>
                    </td>

                    <td>
                        <?php $diff = $r['difficulty'] ?? ''; ?>
                        <span class="badge" style="background: <?= htmlspecialchars($difficulty_colors[$diff] ?? '#eee') ?>">
                            <?= htmlspecialchars($difficulty_labels[$diff] ?? $diff) ?>
                        </span>
                    </td>

                    <td><?= htmlspecialchars($r['start_date'] ?? '') ?></td>

                    <td><?= (int)($r['goal_days'] ?? 0) ?> дней</td>

                    <td>
                        <?php foreach (($r['days'] ?? []) as $d): ?>
                            <span class="badge badge-day">
                                <?= htmlspecialchars($day_labels[$d] ?? $d) ?>
                            </span>
                        <?php endforeach; ?>
                    </td>

                    <td>
                        <?= nl2br(htmlspecialchars(safeTrimText($r['description'] ?? '', 80))) ?>
                    </td>

                    <td><?= htmlspecialchars($r['created_at'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>