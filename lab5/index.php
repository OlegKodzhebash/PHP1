<?php

declare(strict_types=1);

interface TransactionStorageInterface
{
    public function addTransaction(Transaction $transaction): void;
    public function removeTransactionById(int $id): void;
    public function getAllTransactions(): array;
    public function findById(int $id): ?Transaction;
}

class Transaction
{
    public function __construct(
        private int $id,
        private string $date,
        private float $amount,
        private string $description,
        private string $merchant
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getMerchant(): string
    {
        return $this->merchant;
    }

    public function getDaysSinceTransaction(): int
    {
        $transactionDate = new DateTime($this->date);
        $currentDate = new DateTime();

        return (int)$transactionDate->diff($currentDate)->days;
    }
}

class TransactionRepository implements TransactionStorageInterface
{
    private array $transactions = [];

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    public function removeTransactionById(int $id): void
    {
        foreach ($this->transactions as $key => $transaction) {
            if ($transaction->getId() === $id) {
                unset($this->transactions[$key]);
                break;
            }
        }

        $this->transactions = array_values($this->transactions);
    }

    public function getAllTransactions(): array
    {
        return $this->transactions;
    }

    public function findById(int $id): ?Transaction
    {
        foreach ($this->transactions as $transaction) {
            if ($transaction->getId() === $id) {
                return $transaction;
            }
        }

        return null;
    }
}

class TransactionManager
{
    public function __construct(
        private TransactionStorageInterface $repository
    ) {
    }

    public function calculateTotalAmount(): float
    {
        $total = 0.0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $total += $transaction->getAmount();
        }

        return $total;
    }

    public function calculateTotalAmountByDateRange(string $startDate, string $endDate): float
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $total = 0.0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            $transactionDate = new DateTime($transaction->getDate());

            if ($transactionDate >= $start && $transactionDate <= $end) {
                $total += $transaction->getAmount();
            }
        }

        return $total;
    }

    public function countTransactionsByMerchant(string $merchant): int
    {
        $count = 0;

        foreach ($this->repository->getAllTransactions() as $transaction) {
            if (strtolower($transaction->getMerchant()) === strtolower($merchant)) {
                $count++;
            }
        }

        return $count;
    }

    public function sortTransactionsByDate(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort(
            $transactions,
            fn(Transaction $a, Transaction $b): int => strtotime($a->getDate()) <=> strtotime($b->getDate())
        );

        return $transactions;
    }

    public function sortTransactionsByAmountDesc(): array
    {
        $transactions = $this->repository->getAllTransactions();

        usort(
            $transactions,
            fn(Transaction $a, Transaction $b): int => $b->getAmount() <=> $a->getAmount()
        );

        return $transactions;
    }
}

final class TransactionTableRenderer
{
    public function render(array $transactions): string
    {
        $html = '<div class="table-wrapper">';
        $html .= '<table class="transactions-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>ID</th>';
        $html .= '<th>Дата</th>';
        $html .= '<th>Сумма</th>';
        $html .= '<th>Описание</th>';
        $html .= '<th>Получатель</th>';
        $html .= '<th>Категория</th>';
        $html .= '<th>Дней прошло</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($transactions as $transaction) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars((string)$transaction->getId()) . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getDate()) . '</td>';
            $html .= '<td class="amount">' . htmlspecialchars(number_format($transaction->getAmount(), 2, '.', '')) . ' MDL</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getDescription()) . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction->getMerchant()) . '</td>';
            $html .= '<td>' . htmlspecialchars($this->getMerchantCategory($transaction->getMerchant())) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)$transaction->getDaysSinceTransaction()) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }

    private function getMerchantCategory(string $merchant): string
    {
        $categories = [
            'Supermarket Nr1' => 'Продукты',
            'Fuel Station Rompetrol' => 'Топливо',
            'Orange Moldova' => 'Связь',
            'Farmacia Felicia' => 'Аптека',
            'Amazon' => 'Покупки',
            'Moldcell' => 'Связь',
            'McDonalds' => 'Еда',
            'Linella' => 'Продукты',
            'Endava' => 'Услуги',
            'Air Moldova' => 'Путешествия',
        ];

        return $categories[$merchant] ?? 'Прочее';
    }
}

$repository = new TransactionRepository();

$transactions = [
    new Transaction(1, '2026-03-10', 250.50, 'Покупка продуктов', 'Supermarket Nr1'),
    new Transaction(2, '2026-03-12', 800.00, 'Заправка автомобиля', 'Fuel Station Rompetrol'),
    new Transaction(3, '2026-03-14', 120.75, 'Оплата интернета', 'Orange Moldova'),
    new Transaction(4, '2026-03-16', 340.20, 'Покупка лекарств', 'Farmacia Felicia'),
    new Transaction(5, '2026-03-18', 1500.00, 'Покупка техники', 'Amazon'),
    new Transaction(6, '2026-03-20', 95.99, 'Оплата мобильной связи', 'Moldcell'),
    new Transaction(7, '2026-03-22', 430.00, 'Ужин в ресторане', 'McDonalds'),
    new Transaction(8, '2026-03-24', 275.40, 'Покупка бытовых товаров', 'Linella'),
    new Transaction(9, '2026-03-26', 2200.00, 'Оплата обучения', 'Endava'),
    new Transaction(10, '2026-03-28', 3100.00, 'Покупка авиабилетов', 'Air Moldova'),
];

foreach ($transactions as $transaction) {
    $repository->addTransaction($transaction);
}

$manager = new TransactionManager($repository);
$renderer = new TransactionTableRenderer();

$totalAmount = $manager->calculateTotalAmount();
$rangeAmount = $manager->calculateTotalAmountByDateRange('2026-03-12', '2026-03-24');
$amazonCount = $manager->countTransactionsByMerchant('Amazon');
$sortedByDate = $manager->sortTransactionsByDate();
$sortedByAmount = $manager->sortTransactionsByAmountDesc();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Transactions</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .page-title {
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            color: #1e3a8a;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .card {
            background: #ffffff;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 16px;
            color: #6b7280;
        }

        .card p {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }

        .section {
            margin-bottom: 35px;
        }

        .section-title {
            margin-bottom: 15px;
            font-size: 22px;
            color: #1d4ed8;
        }

        .table-wrapper {
            overflow-x: auto;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        .transactions-table thead {
            background: #2563eb;
            color: #ffffff;
        }

        .transactions-table th,
        .transactions-table td {
            padding: 14px 16px;
            text-align: left;
        }

        .transactions-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .transactions-table tbody tr:hover {
            background: #eaf2ff;
        }

        .amount {
            font-weight: bold;
            color: #047857;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="page-title">Управление банковскими транзакциями</h1>

        <div class="stats">
            <div class="card">
                <h3>Общая сумма</h3>
                <p><?= htmlspecialchars(number_format($totalAmount, 2, '.', '')) ?> MDL</p>
            </div>

            <div class="card">
                <h3>Сумма за период</h3>
                <p><?= htmlspecialchars(number_format($rangeAmount, 2, '.', '')) ?> MDL</p>
            </div>

            <div class="card">
                <h3>Транзакций для Amazon</h3>
                <p><?= htmlspecialchars((string)$amazonCount) ?></p>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Все транзакции</h2>
            <?= $renderer->render($repository->getAllTransactions()) ?>
        </div>

        <div class="section">
            <h2 class="section-title">Сортировка по дате</h2>
            <?= $renderer->render($sortedByDate) ?>
        </div>

        <div class="section">
            <h2 class="section-title">Сортировка по сумме по убыванию</h2>
            <?= $renderer->render($sortedByAmount) ?>
        </div>
    </div>
</body>
</html>