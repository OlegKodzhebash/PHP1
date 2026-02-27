<?php

// ЧАСТЬ 1: Условные конструкции

$employees = [
    1 => ["name" => "John Styles", "schedule" => ""],
    2 => ["name" => "Jane Doe",    "schedule" => ""],
];

$day = (int)date("N"); // 1=Пн, 2=Вт, 3=Ср, 4=Чт, 5=Пт, 6=Сб, 7=Вс

// John Styles: Пн(1), Ср(3), Пт(5) → 8:00-12:00
if (in_array($day, [1, 3, 5])) {
    $employees[1]["schedule"] = "8:00-12:00";
} else {
    $employees[1]["schedule"] = "Нерабочий день";
}

// Jane Doe: Вт(2), Чт(4), Сб(6) → 12:00-16:00
if (in_array($day, [2, 4, 6])) {
    $employees[2]["schedule"] = "12:00-16:00";
} else {
    $employees[2]["schedule"] = "Нерабочий день";
}

$dayNames = [1=>"Понедельник",2=>"Вторник",3=>"Среда",4=>"Четверг",5=>"Пятница",6=>"Суббота",7=>"Воскресенье"];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лабораторная №3</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 60%; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; }
        th { background: #f0f0f0; }
        h2 { margin-top: 30px; }
        pre { background: #f8f8f8; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>

<h1>Лабораторная работа №3. Управляющие конструкции</h1>
<p><strong>Сегодня:</strong> <?= $dayNames[$day] ?></p>

<h2>Условные конструкции — Расписание</h2>
<table>
    <tr>
        <th>#</th>
        <th>Фамилия Имя</th>
        <th>График работы</th>
    </tr>
    <?php foreach ($employees as $id => $emp): ?>
    <tr>
        <td><?= $id ?></td>
        <td><?= $emp["name"] ?></td>
        <td><?= $emp["schedule"] ?></td>
    </tr>
    <?php endforeach; ?>
</table>


<!-- // ЧАСТЬ 2: Циклы -->                              


<h2>Циклы — for</h2>
<?php
$a = 0;
$b = 0;

echo "<pre>";
for ($i = 0; $i < 5; $i++) {
    $a += 10;
    $b += 5;
    echo "Шаг $i: a = $a, b = $b\n";
}
echo "End of the loop: a = $a, b = $b";
echo "</pre>";
?>

<h2>Циклы — while</h2>
<?php
$a = 0;
$b = 0;
$i = 0;

echo "<pre>";
while ($i < 5) {
    $a += 10;
    $b += 5;
    echo "Шаг $i: a = $a, b = $b\n";
    $i++;
}
echo "End of the loop: a = $a, b = $b";
echo "</pre>";
?>

<h2>Циклы — do-while</h2>
<?php
$a = 0;
$b = 0;
$i = 0;

echo "<pre>";
do {
    $a += 10;
    $b += 5;
    echo "Шаг $i: a = $a, b = $b\n";
    $i++;
} while ($i < 5);
echo "End of the loop: a = $a, b = $b";
echo "</pre>";
?>

</body>
</html>