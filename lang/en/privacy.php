<?php

declare(strict_types=1);

return [
    'title' => 'Privacy Policy',
    'meta_description' => 'Privacy policy for hafizhrafid.dev explaining what cookies, local storage, and data this site uses.',
    'draft_notice' => 'Initial draft (MVP stage). This page will be updated before any new feature that uses visitor data is enabled.',
    'last_updated_label' => 'Last updated',
    'last_updated_date' => 'November 26, 2026',
    'intro' => 'hafizhrafid.dev is the personal portfolio and engineering blog of Hafizh Rafid. This page explains what data is stored when you visit and how it is used.',
    'sections' => [
        'principles' => [
            'heading' => 'Core principles',
            'body' => 'This site uses no cross-site trackers, shows no third-party ads, performs no browser fingerprinting, and does not store raw visitor IP addresses in the database.',
        ],
        'storage' => [
            'heading' => 'Cookies and local storage',
            'intro' => 'At the current stage, this site uses minimal storage so language preference, theme choice, and form security work:',
            'items' => [
                'locale' => [
                    'key' => 'locale',
                    'type' => 'First-party cookie (HttpOnly, SameSite=Lax, 1 year)',
                    'description' => 'Stores your chosen interface language (id or en) when you switch languages manually.',
                ],
                'theme' => [
                    'key' => 'theme',
                    'type' => 'Browser localStorage (never sent to the server)',
                    'description' => 'Stores your display theme preference (light, dark, or system) on your device.',
                ],
                'session' => [
                    'key' => 'Session cookie and XSRF-TOKEN',
                    'type' => 'Framework first-party cookies',
                    'description' => 'Used for form submission security (CSRF) and site administration sessions.',
                ],
            ],
        ],
        'contact_form' => [
            'heading' => 'Contact form',
            'body' => 'When you send a message through the contact form (once available), your name, email address, and message are stored so I can reply. To prevent spam, the server stores only a one-way hash rather than your raw IP address.',
        ],
        'future' => [
            'heading' => 'Upcoming features (not yet active)',
            'body' => 'Future phases plan an anonymous visitor cookie (vid) for article likes, a daily view counter using a one-way hash with a daily rotating salt, self-hosted cookieless analytics, and optional social login for bookmarking articles. None of these features are active yet, and this policy will be updated before they launch.',
        ],
        'contact' => [
            'heading' => 'Questions and data removal',
            'body' => 'If you have questions about privacy or want to request deletion of a contact message you sent, please email:',
        ],
    ],
];
