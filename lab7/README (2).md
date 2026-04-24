# Лабораторная работа №7. Шаблонизация

## Цель работы

Освоить принципы шаблонизации в PHP: реализовать нативные PHP-шаблоны и подключить готовый шаблонизатор Twig. Улучшить структуру проекта, разделив логику обработки данных и представление.

---

## Описание проекта

Проект — **Трекер привычек** — позволяет добавлять личные привычки (название, описание, категория, сложность, дата начала, цель в днях, дни выполнения) и просматривать их список с сортировкой по различным полям. Данные хранятся в файле `data.json`.

---

## Структура проекта

```
lab7/
├── templates/               # Нативные PHP-шаблоны
│   ├── layout.php           # Общий макет (HTML, стили, навигация)
│   ├── form.php             # Форма добавления привычки
│   ├── list.php             # Таблица со списком привычек
│   └── result.php           # Страница результата сохранения
├── twig_templates/          # Twig-шаблоны
│   ├── layout.twig          # Базовый макет с блоками
│   ├── form.twig            # Форма (наследует layout.twig)
│   ├── list.twig            # Список (наследует layout.twig)
│   └── result.twig          # Результат (наследует layout.twig)
├── src/
│   ├── functions.php        # Функции для работы с данными
│   ├── handler.php          # Обработчик POST-запроса
│   └── TwigExtensions.php   # Кастомный фильтр Twig
├── vendor/                  # Зависимости Composer
├── composer.json
├── data.json                # Хранилище данных
└── index.php                # Точка входа, маршрутизация
```

---

## Шаг 1. Нативные PHP-шаблоны

### Принцип разделения

Логика и представление разделены следующим образом:

- **`src/functions.php`** — чистые функции: загрузка/сохранение записей, валидация, сортировка, получение справочных данных (категории, сложность, дни недели).
- **`src/handler.php`** — обработчик POST-запроса: вызывает валидацию, сохраняет запись, подготавливает переменные и передаёт управление шаблону.
- **`index.php`** — точка входа и маршрутизатор: определяет страницу (`form` или `list`), вызывает нужные функции, присваивает переменные и подключает `layout.php`.
- **`templates/`** — файлы шаблонов, содержат только HTML и вывод переменных через `<?= ?>`. Никакой бизнес-логики внутри нет.

### Механизм работы layout

`layout.php` является общим макетом. Конкретный контент передаётся через переменную `$content`, содержащую путь к файлу шаблона, который подключается через `require`:

```php
// index.php
$content = __DIR__ . '/templates/form.php';
require __DIR__ . '/templates/layout.php';

// templates/layout.php
<?php require $content; ?>
```

### Пример нативного шаблона (фрагмент `form.php`)

```php
<select id="category" name="category">
    <option value="">Выберите категорию</option>
    <?php foreach ($category_labels as $key => $label): ?>
        <option value="<?= htmlspecialchars($key) ?>">
            <?= htmlspecialchars($label) ?>
        </option>
    <?php endforeach; ?>
</select>
```

---

## Шаг 2. Шаблонизатор Twig

### Установка

Twig установлен через Composer:

```json
{
  "require": {
    "twig/twig": "^3.0"
  },
  "autoload": {
    "psr-4": {
      "App\\": "src/"
    }
  }
}
```

```bash
composer install
```

### Инициализация в index.php

```php
require_once __DIR__ . '/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/twig_templates');
$twig = new \Twig\Environment($loader);
$twig->addExtension(new \App\TwigExtensions());

echo $twig->render('list.twig', [
    'records'           => $records,
    'sort'              => $sort,
    'order'             => $order,
    'total'             => count($records),
    'cats_count'        => count(array_unique(array_column($records, 'category'))),
    'avg_goal'          => getAverageGoal($records),
    'category_labels'   => getCategoryLabels(),
    'difficulty_labels' => getDifficultyLabels(),
    'day_labels'        => getDayLabels(),
]);
```

### Наследование шаблонов

В Twig используется механизм наследования через `{% extends %}` и `{% block %}`. Базовый шаблон `layout.twig` определяет три блока:

```twig
{# layout.twig #}
<title>{% block title %}Трекер привычек{% endblock %}</title>
...
<p class="subtitle">{% block subtitle %}{% endblock %}</p>
...
{% block content %}{% endblock %}
```

Дочерние шаблоны переопределяют эти блоки:

```twig
{# form.twig #}
{% extends "layout.twig" %}

{% block title %}Добавить привычку — Twig{% endblock %}

{% block subtitle %}
    Форма добавления через Twig
{% endblock %}

{% block content %}
    <div class="card form-card">
        ...
    </div>
{% endblock %}
```

### Пример цикла в Twig (фрагмент `form.twig`)

```twig
<select id="category" name="category">
    <option value="">Выберите категорию</option>
    {% for key, label in category_labels %}
        <option value="{{ key }}">{{ label }}</option>
    {% endfor %}
</select>
```

---

## Шаг 3. Собственный фильтр Twig

Для отображения цели в днях в человекочитаемом формате создан кастомный фильтр `goal_duration`, реализованный в классе `App\TwigExtensions`.

### Реализация (`src/TwigExtensions.php`)

```php
namespace App;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtensions extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('goal_duration', [$this, 'formatGoalDuration']),
        ];
    }

    public function formatGoalDuration(int $days): string
    {
        if ($days === 1) {
            return '1 день';
        }
        if ($days > 1 && $days < 5) {
            return $days . ' дня';
        }
        return $days . ' дней';
    }
}
```

### Регистрация

```php
$twig->addExtension(new \App\TwigExtensions());
```

### Использование в шаблоне (`list.twig`)

```twig
{{ r.goal_days | goal_duration }}
```

**Примеры вывода:**

| Значение | Результат |
|----------|-----------|
| `1`      | `1 день`  |
| `3`      | `3 дня`   |
| `30`     | `30 дней` |

---

## Переключение между режимами

Оба режима (нативный и Twig) доступны одновременно через GET-параметр `view`:

| Ссылка | Описание |
|--------|----------|
| `index.php` | Форма — нативный PHP |
| `index.php?page=list` | Список — нативный PHP |
| `index.php?view=twig` | Форма — Twig |
| `index.php?page=list&view=twig` | Список — Twig |

Маршрутизация реализована в `index.php`:

```php
$view = $_GET['view'] ?? 'native';
$page = $_GET['page'] ?? 'form';

if ($view === 'twig') {
    // ... инициализация Twig и рендеринг
    exit;
}
// ... нативный рендеринг
```

---

## Сравнение подходов

| Критерий | Нативные PHP-шаблоны | Twig |
|---|---|---|
| **Синтаксис** | PHP (`<?php ?>`, `<?= ?>`) | Собственный (`{{ }}`, `{% %}`) |
| **Наследование** | Ручное через `require` | Встроенное (`{% extends %}`, `{% block %}`) |
| **Безопасность** | Требует ручного `htmlspecialchars()` | Автоэкранирование по умолчанию |
| **Расширяемость** | Любые PHP-функции | Фильтры, функции, расширения |
| **Производительность** | Без накладных расходов | Компилирует шаблоны в PHP-кэш |
| **Порог входа** | Минимальный (знание PHP) | Нужно освоить синтаксис |
| **Читаемость** | Снижается при сложной логике | Чище за счёт ограниченного синтаксиса |

---

## Ответы на контрольные вопросы

**1. В чём отличие нативных PHP-шаблонов от Twig?**

Нативные PHP-шаблоны — это обычные `.php`-файлы, в которых HTML смешан с PHP-кодом. Их преимущество — нулевые зависимости и полный доступ к возможностям языка. Недостаток — отсутствие механизмов защиты: разработчик должен вручную экранировать вывод через `htmlspecialchars()`, иначе возможны XSS-уязвимости. Twig предоставляет собственный синтаксис с автоэкранированием по умолчанию, встроенным наследованием шаблонов и изоляцией логики: шаблон не может выполнять произвольный PHP-код, что делает его безопаснее и чище.

**2. Зачем разделять логику и представление?**

Смешивание логики и представления в одном файле приводит к дублированию кода, затрудняет тестирование и поддержку. При изменении бизнес-правил приходится искать нужный код среди HTML-разметки. Разделение позволяет изменять шаблон (дизайн) независимо от логики и наоборот, а также переиспользовать функции в разных представлениях.

**3. Что такое наследование шаблонов в Twig?**

Наследование позволяет определить базовый шаблон с общей структурой (HTML-скелет, навигация, стили) и выделить в нём именованные блоки через `{% block имя %}{% endblock %}`. Дочерние шаблоны подключаются директивой `{% extends "layout.twig" %}` и переопределяют только нужные блоки, не дублируя остальное. В данном проекте `layout.twig` содержит три блока: `title`, `subtitle` и `content`. Шаблоны `form.twig`, `list.twig` и `result.twig` наследуют его и заполняют эти блоки своим содержимым.
