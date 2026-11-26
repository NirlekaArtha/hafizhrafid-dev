<?php

declare(strict_types=1);

namespace App\Domain\Localization\Enums;

enum Locale: string
{
    case Id = 'id';
    case En = 'en';

    public static function default(): self
    {
        return self::En;
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $locale): string => $locale->value,
            self::cases(),
        );
    }

    public static function tryFromInput(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom(strtolower(trim($value)));
    }
}
