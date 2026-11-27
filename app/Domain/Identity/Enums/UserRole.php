<?php

declare(strict_types=1);

namespace App\Domain\Identity\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Member = 'member';
}
