<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Localization\Services\DeepLTranslator;
use App\Domain\Localization\Services\GeminiTranslator;
use App\Domain\Localization\Services\PlaceholderProtector;
use Illuminate\Console\Command;
use Throwable;

class SpikeTranslateCommand extends Command
{
    protected $signature = 'spike:translate {--source=id} {--target=en} {--provider=gemini : Provider to run (gemini, deepl, all)}';

    protected $description = 'Spike P0-07: compare DeepL vs Gemini API on a sample technical article with code blocks.';

    public function handle(PlaceholderProtector $protector): int
    {
        $source = (string) $this->option('source');
        $target = (string) $this->option('target');

        $sampleHtml = $this->sampleArticle();
        $protected = $protector->protect($sampleHtml);

        $this->info('=== Spike P0-07: DeepL vs Gemini ===');
        $this->line(sprintf(
            'Original chars: %d | Protected chars: %d | Placeholders: %d',
            mb_strlen($sampleHtml),
            mb_strlen($protected['html']),
            count($protected['placeholders'])
        ));
        $this->newLine();

        $provider = strtolower((string) $this->option('provider'));

        if (in_array($provider, ['deepl', 'all'], true)) {
            $deeplKey = (string) config('services.deepl.key', '');

            $this->runProvider(
                'DeepL',
                fn (): string => (new DeepLTranslator($deeplKey))->translateHtml($protected['html'], $source, $target),
                $protector,
                $protected['placeholders']
            );
        }

        if (in_array($provider, ['gemini', 'all'], true)) {
            $geminiKey = (string) config('services.gemini.key', '');
            $geminiModel = (string) config('services.gemini.model', 'gemini-3.5-flash-lite');

            $gemini = new GeminiTranslator($geminiKey, $geminiModel);
            $this->runProvider(
                'Gemini ('.$geminiModel.')',
                fn (): string => $gemini->translateHtml($protected['html'], $source, $target),
                $protector,
                $protected['placeholders'],
                fn () => $gemini->lastUsage
            );
        }

        return self::SUCCESS;
    }

    /**
     * @param  callable(): string  $callback
     * @param  array<string, array{type: 'html'|'url', value: string}>  $placeholders
     * @param  (callable(): ?array{prompt_tokens: int, candidate_tokens: int, total_tokens: int})|null  $usageCallback
     */
    private function runProvider(
        string $label,
        callable $callback,
        PlaceholderProtector $protector,
        array $placeholders,
        ?callable $usageCallback = null,
    ): void {
        $this->info('--- '.$label.' ---');
        $start = microtime(true);

        try {
            $rawTranslated = $callback();
            $elapsedMs = (int) round((microtime(true) - $start) * 1000);
            $restored = $protector->restore($rawTranslated, $placeholders);

            $this->line("Status: OK | Latency: {$elapsedMs} ms | Placeholder check: PASS");

            if ($usageCallback !== null) {
                $usage = $usageCallback();
                if ($usage !== null) {
                    $this->line(sprintf(
                        'Tokens: prompt=%d, output=%d, total=%d',
                        $usage['prompt_tokens'],
                        $usage['candidate_tokens'],
                        $usage['total_tokens']
                    ));
                }
            }

            $this->newLine();
            $this->line($restored);
            $this->newLine();
        } catch (Throwable $e) {
            $elapsedMs = (int) round((microtime(true) - $start) * 1000);
            $this->error("Status: FAILED ({$elapsedMs} ms): ".$e->getMessage());
            $this->newLine();
        }
    }

    private function sampleArticle(): string
    {
        return <<<'HTML'
<h2>Menjaga Idempotensi pada Queue Worker Laravel</h2>
<p>Ketika menjalankan <code>php artisan queue:work</code> di lingkungan produksi, kegagalan jaringan atau <em>timeout</em> bisa memicu <strong>job</strong> dijalankan lebih dari satu kali. Tanpa pengamanan yang tepat, kondisi <em>race condition</em> dapat menyebabkan pengiriman email ganda atau mutasi saldo berulang.</p>
<p>Untuk mengatasinya, kita bisa memanfaatkan <code>Cache::lock()</code> atau <code>ShouldBeUnique</code> seperti dokumentasi resmi di <a href="https://laravel.com/docs/13.x/queues#unique-jobs">Laravel Unique Jobs</a>.</p>
<pre><code class="language-php">&lt;?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Queue\Queueable;

class TranslateArticleJob implements ShouldBeUnique
{
    use Queueable;

    public int $uniqueFor = 3600;

    public function uniqueId(): string
    {
        return "article-{$this-&gt;articleId}-{$this-&gt;targetLocale}";
    }
}</code></pre>
<p>Selain itu, pastikan kolom <code>reviewed_at</code> tetap bernilai <code>null</code> sampai penulis meninjau hasil terjemahan di panel admin.</p>
<figure><img src="https://hafizhrafid.dev/storage/diagrams/queue-flow.png" alt="Diagram alur antrean terjemahan artikel"><figcaption>Alur antrean terjemahan sebelum ditinjau penulis.</figcaption></figure>
HTML;
    }
}
