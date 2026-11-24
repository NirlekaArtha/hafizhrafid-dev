<?php

declare(strict_types=1);

namespace App\Domain\Blog\Services;

use App\Domain\Blog\Contracts\CodeHighlighterInterface;
use Phiki\Phiki;
use Phiki\Theme\Theme;

class PhikiCodeHighlighter implements CodeHighlighterInterface
{
    private readonly Phiki $phiki;

    public function __construct(?Phiki $phiki = null)
    {
        $this->phiki = $phiki ?? new Phiki;
    }

    public function highlight(string $code, ?string $language = null): string
    {
        $normalized = $language !== null ? strtolower(trim($language)) : '';

        if ($normalized === '' || ! $this->phiki->environment->grammars->has($normalized)) {
            return '<pre class="phiki"><code>'.htmlspecialchars($code, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</code></pre>';
        }

        return $this->phiki->codeToHtml($code, $normalized, [
            'light' => Theme::GithubLight,
            'dark' => Theme::GithubDark,
        ])->toString();
    }
}
