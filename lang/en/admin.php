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
];
