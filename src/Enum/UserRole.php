<?php

namespace App\Enum;

enum UserRole: string
{
    case CREATOR = 'content creator';
    case MANAGER = 'manager';
    case EDITOR = 'editor';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATOR => 'Content Creator',
            self::MANAGER => 'Manager',
            self::EDITOR => 'Editor',
        };
    }
}
