<?php

declare(strict_types=1);

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