<?php


namespace App\Enum;

enum TaskStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
            self::ARCHIVED => 'Archived',
        };
    }
}

