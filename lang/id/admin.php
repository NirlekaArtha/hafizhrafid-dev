<?php

declare(strict_types=1);

return [
    'locales' => [
        'id' => 'Indonesia (ID)',
        'en' => 'English (EN)',
    ],
    'categories' => [
        'navigation_label' => 'Kategori',
        'model_label' => 'Kategori',
        'plural_model_label' => 'Kategori',
        'tabs' => [
            'translations' => 'Terjemahan',
        ],
        'sections' => [
            'settings' => 'Pengaturan',
        ],
        'fields' => [
            'name' => 'Nama (:locale)',
            'slug' => 'Slug (:locale)',
            'slug_helper' => 'Huruf kecil, angka, dan tanda hubung (mis. rekayasa-web).',
            'description' => 'Deskripsi (:locale)',
            'sort_order' => 'Urutan',
            'sort_order_helper' => 'Angka lebih kecil tampil lebih awal.',
        ],
        'validation' => [
            'slug_unique' => 'Slug (:locale) sudah digunakan oleh kategori lain.',
        ],
        'columns' => [
            'name_id' => 'Nama (ID)',
            'name_en' => 'Nama (EN)',
            'articles_count' => 'Artikel',
            'projects_count' => 'Proyek',
            'sort_order' => 'Urutan',
            'updated_at' => 'Diperbarui',
        ],
    ],
];
