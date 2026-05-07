<?php

require_once __DIR__ . '/db.php';

$pdo = getDb();

$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'user',
    created_at TEXT NOT NULL
);
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS cars (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    brand TEXT NOT NULL,
    model TEXT NOT NULL,
    year INTEGER NOT NULL,
    price REAL NOT NULL,
    fuel_type TEXT NOT NULL,
    transmission TEXT NOT NULL,
    description TEXT NOT NULL,
    created_at TEXT NOT NULL
);
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    car_id INTEGER NOT NULL,
    message TEXT NOT NULL,
    created_at TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (car_id) REFERENCES cars(id)
);
");

$adminEmail = 'admin@example.com';
$adminPassword = 'admin123';

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$adminEmail]);

if (!$stmt->fetch()) {
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password_hash, role, created_at)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        'Admin',
        $adminEmail,
        password_hash($adminPassword, PASSWORD_DEFAULT),
        'admin',
        date('Y-m-d H:i:s')
    ]);
}

$stmt = $pdo->query("SELECT COUNT(*) FROM cars");
$count = $stmt->fetchColumn();

if ($count == 0) {
    $cars = [
        ['Mercedes-Benz', 'E-Class W213', 2019, 28500, 'Diesel', 'Automatic', 'Комфортный седан бизнес-класса с экономичным дизельным двигателем.'],
        ['Ford', 'Focus 4', 2019, 10900, 'Diesel', 'Manual', 'Практичный универсал, хорошо подходит для дальних поездок.'],
        ['BMW', '320d', 2018, 21900, 'Diesel', 'Automatic', 'Динамичный автомобиль с хорошей управляемостью.']
    ];

    foreach ($cars as $car) {
        $stmt = $pdo->prepare("
            INSERT INTO cars (brand, model, year, price, fuel_type, transmission, description, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $car[0],
            $car[1],
            $car[2],
            $car[3],
            $car[4],
            $car[5],
            $car[6],
            date('Y-m-d H:i:s')
        ]);
    }
}