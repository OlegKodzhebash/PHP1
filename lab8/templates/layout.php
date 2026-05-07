<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Трекер привычек') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            padding: 30px;
            color: #333;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        nav {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        nav a,
        .btn {
            padding: 9px 16px;
            border-radius: 6px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-weight: bold;
            display: inline-block;
        }

        .btn-danger {
            background: #e74c3c;
        }

        .btn-warning {
            background: #f39c12;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccd6dd;
            border-radius: 6px;
        }

        textarea {
            min-height: 90px;
        }

        .checkbox-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .checkbox-label {
            background: #f0f4f8;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .checkbox-label input {
            width: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 11px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f0f4f8;
        }

        .errors {
            background: #ffe5e5;
            border: 1px solid #e74c3c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .search {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search input {
            flex: 1;
        }

        .inline-form {
            display: inline;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📋 Трекер привычек</h1>

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