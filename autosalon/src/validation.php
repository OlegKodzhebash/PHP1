<?php

function validateCar(array $data): array
{
    $errors = [];

    if (trim($data['brand'] ?? '') === '') {
        $errors[] = 'Введите марку автомобиля.';
    }

    if (trim($data['model'] ?? '') === '') {
        $errors[] = 'Введите модель автомобиля.';
    }

    if (!isset($data['year']) || !filter_var($data['year'], FILTER_VALIDATE_INT)) {
        $errors[] = 'Год выпуска должен быть числом.';
    } elseif ($data['year'] < 1980 || $data['year'] > date('Y')) {
        $errors[] = 'Год выпуска указан некорректно.';
    }

    if (!isset($data['price']) || !filter_var($data['price'], FILTER_VALIDATE_FLOAT)) {
        $errors[] = 'Цена должна быть числом.';
    } elseif ($data['price'] <= 0) {
        $errors[] = 'Цена должна быть больше нуля.';
    }

    if (empty($data['fuel_type'])) {
        $errors[] = 'Выберите тип топлива.';
    }

    if (empty($data['transmission'])) {
        $errors[] = 'Выберите коробку передач.';
    }

    if (trim($data['description'] ?? '') === '') {
        $errors[] = 'Введите описание автомобиля.';
    }

    return $errors;
}