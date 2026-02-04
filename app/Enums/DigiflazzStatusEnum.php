<?php

namespace App\Enums;

enum DigiflazzStatusEnum: int
{
    case PENDING = 0;
    case ON_PROGRESS = 1;
    case SUCCESS = 2;
    case FAILED = 3;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ON_PROGRESS => 'On Progress',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'badge bg-warning text-white',
            self::ON_PROGRESS => 'badge bg-info text-white',
            self::SUCCESS => 'badge bg-success text-white',
            self::FAILED => 'badge bg-danger text-white',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
