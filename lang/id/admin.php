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
    'articles' => [
        'navigation_label' => 'Artikel',
        'model_label' => 'Artikel',
        'plural_model_label' => 'Artikel',
        'sections' => [
            'content' => 'Konten',
            'publishing' => 'Publikasi',
            'taxonomy' => 'Kategori',
            'cover' => 'Gambar Sampul',
            'seo' => 'SEO',
        ],
        'statuses' => [
            'draft' => 'Draf',
            'scheduled' => 'Terjadwal',
            'published' => 'Terbit',
        ],
        'fields' => [
            'locale' => 'Bahasa',
            'title' => 'Judul',
            'slug' => 'Slug',
            'slug_helper' => 'Huruf kecil, angka, dan tanda hubung. Unik per bahasa.',
            'excerpt' => 'Ringkasan',
            'body_html' => 'Isi Artikel (HTML)',
            'body_html_helper' => 'Editor sementara sebelum integrasi CKEditor 5.',
            'status' => 'Status',
            'published_at' => 'Tanggal Terbit',
            'published_at_helper' => 'Wajib diisi dengan waktu mendatang untuk status Terjadwal. Jika kosong saat Terbit, diisi waktu saat ini.',
            'categories' => 'Kategori',
            'cover' => 'Gambar Sampul (16:9)',
            'cover_helper' => 'Format JPG, PNG, atau WebP (maks. 5 MB). Disarankan rasio 16:9 (1200×675).',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
        ],
        'validation' => [
            'slug_unique' => 'Slug sudah digunakan oleh artikel lain pada bahasa yang sama.',
            'locale_group_unique' => 'Grup terjemahan ini sudah memiliki artikel untuk bahasa tersebut.',
            'published_at_future' => 'Tanggal terbit harus berada di masa mendatang untuk artikel terjadwal.',
        ],
        'columns' => [
            'cover' => 'Sampul',
            'title' => 'Judul',
            'locale' => 'Bahasa',
            'status' => 'Status',
            'categories' => 'Kategori',
            'published_at' => 'Terbit',
            'updated_at' => 'Diperbarui',
        ],
        'filters' => [
            'locale' => 'Bahasa',
            'status' => 'Status',
            'categories' => 'Kategori',
        ],
    ],
];
