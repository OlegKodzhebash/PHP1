<?php
declare(strict_types=1);


$transactions = [
    ["id"=>1,"date"=>"2024-01-10","amount"=>1200.50,"description"=>"Парфюм","merchant"=>"Ovico"],
    ["id"=>2,"date"=>"2024-02-15","amount"=>75.00,"description"=>"Покушал","merchant"=>"Restaurant"],
    ["id"=>3,"date"=>"2023-12-01","amount"=>2500.00,"description"=>"Наушники","merchant"=>"BOMBA"],
];



function calculateTotalAmount(array $transactions): float {
    return array_reduce($transactions, fn($sum,$t) => $sum + $t['amount'], 0);
}

function daysSinceTransaction(string $date): int {
    $d1 = new DateTime($date);
    $d2 = new DateTime();
    return (int)$d2->diff($d1)->format('%a');
}

function addTransaction(int $id,string $date,float $amount,string $description,string $merchant): void {
    global $transactions;
    $transactions[] = compact("id","date","amount","description","merchant");
}

function deleteTransaction(int $id): void {
    global $transactions;
    $transactions = array_filter($transactions, fn($t) => $t['id'] !== $id);
}

function findTransactionByDescription(array $transactions,string $text): array {
    return array_filter($transactions,
        fn($t) => stripos($t['description'],$text) !== false
    );
}

function sortByDateDesc(array &$transactions): void {
    usort($transactions, fn($a,$b) =>
        strtotime($b['date']) <=> strtotime($a['date'])
    );
}

function sortByAmountDesc(array &$transactions): void {
    usort($transactions, fn($a,$b) =>
        $b['amount'] <=> $a['amount']
    );
}



if (isset($_GET['add'])) {
    addTransaction(
        (int)$_GET['id'],
        $_GET['date'],
        (float)$_GET['amount'],
        $_GET['description'],
        $_GET['merchant']
    );
}

if (isset($_GET['delete'])) {
    deleteTransaction((int)$_GET['delete']);
}

if (!empty($_GET['search'])) {
    $transactions = findTransactionByDescription($transactions, $_GET['search']);
}

if (isset($_GET['sort'])) {
    if ($_GET['sort']=='date') sortByDateDesc($transactions);
    if ($_GET['sort']=='amount') sortByAmountDesc($transactions);
}

if (!isset($_GET['sort'])) {
    sortByDateDesc($transactions);
}

$total = calculateTotalAmount($transactions);
?>

<!DOCTYPE html>
<html>
<head>
<title>Transaction Manager</title>
<style>
body{
    font-family: Arial;
    background:#f4f6f9;
    margin:20px;
}

/* Контейнер */
.container{
    width:95%;
    margin:auto;
}

/* Заголовки */
h2{
    text-align:center;
    margin-top:30px;
}

/* Панели */
.panel{
    background:white;
    padding:15px;
    margin-bottom:15px;
    border-radius:8px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
    text-align:center;
}

input{
    padding:6px;
    margin:4px;
}

button{
    padding:6px 12px;
    cursor:pointer;
}

/* Таблица */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:8px;
    overflow:hidden;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

th{
    background:#34495e;
    color:white;
    padding:10px;
}

td{
    padding:8px;
    text-align:center;
}

tr:nth-child(even){
    background:#f2f2f2;
}

tr:hover{
    background:#e8f0ff;
}

.total{
    text-align:right;
    font-size:18px;
    margin-top:10px;
    font-weight:bold;
}

/* Галерея */
.gallery{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(150px,1fr));
    gap:10px;
    margin-top:20px;
}

.gallery img{
    width:100%;
    height:120px;
    object-fit:cover;
    border-radius:6px;
    box-shadow:0 2px 4px rgba(0,0,0,0.2);
    transition:0.3s;
}

.gallery img:hover{
    transform:scale(1.05);
}
</style>
</head>
<body>

<div class="container">

<h2>Управление транзакциями</h2>

<div class="panel">
<form>
<button name="sort" value="date">По дате</button>
<button name="sort" value="amount">По сумме</button>
</form>
</div>

<div class="panel">
<form>
<input name="search" placeholder="Поиск по описанию">
<button>Найти</button>
</form>
</div>

<div class="panel">
<form>
<input name="id" placeholder="ID" required>
<input name="date" type="date" required>
<input name="amount" placeholder="Сумма" required>
<input name="description" placeholder="Описание" required>
<input name="merchant" placeholder="Получатель" required>
<button name="add" value="1">Добавить</button>
</form>
</div>

<table>
<tr>
<th>ID</th>
<th>Дата</th>
<th>Сумма</th>
<th>Описание</th>
<th>Получатель</th>
<th>Дней назад</th>
<th>Удалить</th>
</tr>

<?php foreach($transactions as $t): ?>
<tr>
<td><?= $t['id'] ?></td>
<td><?= $t['date'] ?></td>
<td><?= number_format($t['amount'],2) ?></td>
<td><?= $t['description'] ?></td>
<td><?= $t['merchant'] ?></td>
<td><?= daysSinceTransaction($t['date']) ?></td>
<td>
<a href="?delete=<?= $t['id'] ?>" onclick="return confirm('Удалить?')">❌</a>
</td>
</tr>
<?php endforeach; ?>
</table>

<div class="total">
Общая сумма: <?= number_format($total,2) ?>
</div>

<h2>Галерея изображений</h2>

<div class="gallery">
<?php
$dir = 'image/';
$files = scandir($dir);

if ($files !== false) {
    foreach ($files as $file) {
        if ($file != "." && $file != ".." && strtolower(pathinfo($file, PATHINFO_EXTENSION)) == 'jpg') {
            echo "<img src='image/$file'>";
        }
    }
}
?>
</div>

</div>

</body>
</html>