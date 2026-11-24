<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Blog\Services\PhikiCodeHighlighter;
use Illuminate\Console\Command;

class SpikeHighlightCommand extends Command
{
    protected $signature = 'spike:highlight';

    protected $description = 'Spike P0-08: benchmark and inspect server-side syntax highlighting with Phiki (pure PHP).';

    public function handle(PhikiCodeHighlighter $highlighter): int
    {
        $samples = [
            'php' => "<?php\n\ndeclare(strict_types=1);\n\nfinal class Example\n{\n    public function greet(string \$name): string\n    {\n        return \"Hello, {\$name}\";\n    }\n}",
            'ts' => "interface Article {\n  id: number;\n  slug: string;\n}\n\nexport const formatTitle = (a: Article): string => a.slug.toUpperCase();",
            'bash' => "#!/usr/bin/env bash\nset -euo pipefail\nphp artisan test --compact",
            'python' => "def fib(n: int) -> int:\n    return n if n < 2 else fib(n - 1) + fib(n - 2)",
            'sql' => "SELECT id, slug, published_at\nFROM articles\nWHERE locale = 'id' AND status = 'published'\nORDER BY published_at DESC\nLIMIT 10;",
            'yaml' => "services:\n  app:\n    build: ./docker/php\n    ports:\n      - '8000:80'",
            'dockerfile' => "FROM php:8.5-fpm-alpine\nWORKDIR /var/www/html\nCMD [\"php-fpm\"]",
        ];

        $this->info('=== Spike P0-08: Phiki (Pure PHP TextMate Highlighter) ===');
        $totalMs = 0.0;

        foreach ($samples as $language => $code) {
            $start = microtime(true);
            $html = $highlighter->highlight($code, $language);
            $elapsedMs = (microtime(true) - $start) * 1000;
            $totalMs += $elapsedMs;

            $this->line(sprintf(
                '[%s] %.2f ms | output=%d bytes | dual-theme=%s',
                $language,
                $elapsedMs,
                strlen($html),
                str_contains($html, '--phiki-dark-color') ? 'yes' : 'no'
            ));
        }

        $this->newLine();
        $this->info(sprintf('Total across %d blocks: %.2f ms', count($samples), $totalMs));

        return self::SUCCESS;
    }
}
