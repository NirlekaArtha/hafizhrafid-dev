<?php

declare(strict_types=1);

use App\Domain\Localization\Services\CrawlerDetector;

it('detects known search and social crawlers', function (string $userAgent): void {
    $detector = new CrawlerDetector;

    expect($detector->isCrawler($userAgent))->toBeTrue();
})->with([
    'Googlebot' => ['Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'],
    'Googlebot Mobile' => ['Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'],
    'Bingbot' => ['Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'],
    'DuckDuckBot' => ['DuckDuckBot/1.1; (+http://duckduckgo.com/duckduckbot.html)'],
    'YandexBot' => ['Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)'],
    'Applebot' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1.1 Safari/605.1.15 (Applebot/0.1; +http://www.apple.com/go/applebot)'],
    'Facebook' => ['facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)'],
    'Twitter' => ['Twitterbot/1.0'],
    'LinkedIn' => ['LinkedInBot/1.0 (compatible; Mozilla/5.0; Apache-HttpClient +http://www.linkedin.com)'],
    'WhatsApp' => ['WhatsApp/2.23.20.0'],
    'Telegram' => ['TelegramBot (like TwitterBot)'],
    'Discord' => ['Mozilla/5.0 (compatible; Discordbot/2.0; +https://discordapp.com)'],
]);

it('does not flag regular browsers or empty user agents as crawlers', function (?string $userAgent): void {
    $detector = new CrawlerDetector;

    expect($detector->isCrawler($userAgent))->toBeFalse();
})->with([
    'Chrome desktop' => ['Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'],
    'Firefox desktop' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:133.0) Gecko/20100101 Firefox/133.0'],
    'Safari iOS' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 18_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.1 Mobile/15E148 Safari/604.1'],
    'empty string' => [''],
    'null' => [null],
]);
