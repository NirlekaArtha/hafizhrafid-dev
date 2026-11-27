<?php

declare(strict_types=1);

namespace App\Domain\Blog\Enums;

enum ArticleStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Published = 'published';
}
