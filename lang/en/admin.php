<?php

declare(strict_types=1);

return [
    'locales' => [
        'id' => 'Indonesian (ID)',
        'en' => 'English (EN)',
    ],
    'categories' => [
        'navigation_label' => 'Categories',
        'model_label' => 'Category',
        'plural_model_label' => 'Categories',
        'tabs' => [
            'translations' => 'Translations',
        ],
        'sections' => [
            'settings' => 'Settings',
        ],
        'fields' => [
            'name' => 'Name (:locale)',
            'slug' => 'Slug (:locale)',
            'slug_helper' => 'Lowercase letters, numbers, and hyphens (e.g. web-engineering).',
            'description' => 'Description (:locale)',
            'sort_order' => 'Order',
            'sort_order_helper' => 'Smaller numbers appear first.',
        ],
        'validation' => [
            'slug_unique' => 'Slug (:locale) is already used by another category.',
        ],
        'columns' => [
            'name_id' => 'Name (ID)',
            'name_en' => 'Name (EN)',
            'articles_count' => 'Articles',
            'projects_count' => 'Projects',
            'sort_order' => 'Order',
            'updated_at' => 'Updated',
        ],
    ],
    'articles' => [
        'navigation_label' => 'Articles',
        'model_label' => 'Article',
        'plural_model_label' => 'Articles',
        'sections' => [
            'content' => 'Content',
            'publishing' => 'Publishing',
            'taxonomy' => 'Categories',
            'cover' => 'Cover Image',
            'seo' => 'SEO',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'scheduled' => 'Scheduled',
            'published' => 'Published',
        ],
        'fields' => [
            'locale' => 'Language',
            'title' => 'Title',
            'slug' => 'Slug',
            'slug_helper' => 'Lowercase letters, numbers, and hyphens. Unique per language.',
            'excerpt' => 'Excerpt',
            'body_html' => 'Article Body',
            'body_html_helper' => 'Use H2–H4 headings, language-tagged code blocks, and make sure every image has alternative text (alt) before publishing.',
            'body_html_placeholder' => 'Write the article content here...',
            'status' => 'Status',
            'published_at' => 'Publish Date',
            'published_at_helper' => 'Required with a future time for Scheduled status. Defaults to now when Published if left blank.',
            'categories' => 'Categories',
            'cover' => 'Cover Image (16:9)',
            'cover_helper' => 'JPG, PNG, or WebP format (max 5 MB). Recommended 16:9 ratio (1200×675).',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
        ],
        'validation' => [
            'slug_unique' => 'Slug is already used by another article in the same language.',
            'locale_group_unique' => 'This translation group already has an article for the selected language.',
            'published_at_future' => 'Publish date must be in the future for scheduled articles.',
        ],
        'columns' => [
            'cover' => 'Cover',
            'title' => 'Title',
            'locale' => 'Language',
            'status' => 'Status',
            'categories' => 'Categories',
            'published_at' => 'Published',
            'updated_at' => 'Updated',
        ],
        'filters' => [
            'locale' => 'Language',
            'status' => 'Status',
            'categories' => 'Categories',
        ],
    ],
];
