<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Трекер привычек') ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f4f8;
            color: #333;
            padding: 30px 15px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 8px;
            font-size: 2rem;
            color: #2c3e50;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        nav {
            display: flex;
            gap: 12px;
            margin-bottom: 28px;
            justify-content: center;
            flex-wrap: wrap;
        }

        nav a {
            padding: 9px 22px;
            background: #fff;
            border: 2px solid #3498db;
            color: #3498db;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }

        nav a:hover,
        nav a.active {
            background: #3498db;
            color: #fff;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        .form-card {
            max-width: 700px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: 700;
            margin-bottom: 7px;
            color: #2c3e50;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #ccd6dd;
            border-radius: 7px;
            font-size: 0.95rem;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .checkbox-label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f0f4f8;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 600;
        }

        .checkbox-label input {
            width: auto;
        }

        .btn {
            display: inline-block;
            padding: 11px 20px;
            border-radius: 7px;
            text-align: center;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #3498db;
            color: #fff;
        }

        .btn-outline {
            background: #fff;
            color: #3498db;
            border: 2px solid #3498db;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .actions .btn {
            flex: 1;
        }

        .stats {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 16px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            flex: 1;
            min-width: 140px;
            text-align: center;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: 800;
            color: #3498db;
        }

        .stat-card .label {
            font-size: 0.82rem;
            color: #888;
            margin-top: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            min-width: 700px;
        }

        thead tr {
            background: #f0f4f8;
        }

        th {
            padding: 12px 14px;
            text-align: left;
            font-weight: 700;
            color: #555;
            white-space: nowrap;
            border-bottom: 2px solid #dde3ea;
        }

        th a {
            color: inherit;
            text-decoration: none;
        }

        td {
            padding: 11px 14px;
            border-bottom: 1px solid #eef0f2;
            vertical-align: top;
        }

        .badge {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 12px;
            font-size: 0.78rem;
            font-weight: 600;
            margin: 2px 2px 2px 0;
        }

        .badge-day {
            background: #eaf0fb;
            color: #2471a3;
        }

        .badge-cat {
            background: #eafaf1;
            color: #1e8449;
        }

        .empty {
            text-align: center;
            padding: 48px 0;
            color: #aaa;
            font-size: 1.05rem;
        }

        .success-title {
            text-align: center;
            font-size: 1.4rem;
            font-weight: 700;
            color: #27ae60;
            margin-bottom: 20px;
        }

        .error-title {
            text-align: center;
            font-size: 1.4rem;
            font-weight: 700;
            color: #e74c3c;
            margin-bottom: 16px;
        }

        .error-list {
            background: #fdf0ef;
            border: 1.5px solid #e74c3c;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }

        .record-table td:first-child {
            font-weight: 700;
            background: #f8f9fa;
            width: 40%;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📋 Трекер привычек</h1>
    <p class="subtitle"><?= htmlspecialchars($subtitle ?? '') ?></p>

    <nav>
        <a href="index.php">➕ Добавить Native</a>
        <a href="index.php?page=list">📊 Список Native</a>
        <a href="index.php?view=twig">➕ Добавить Twig</a>
        <a href="index.php?page=list&view=twig">📊 Список Twig</a>
    </nav>

    <?php require $content; ?>
</div>
</body>
</html>