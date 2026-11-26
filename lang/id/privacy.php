<?php

declare(strict_types=1);

return [
    'title' => 'Kebijakan Privasi',
    'meta_description' => 'Kebijakan privasi hafizhrafid.dev: penjelasan singkat mengenai cookie, penyimpanan lokal, dan data yang digunakan.',
    'draft_notice' => 'Draf awal (tahap MVP). Halaman ini diperbarui sebelum fitur baru yang memakai data pengunjung diaktifkan.',
    'last_updated_label' => 'Terakhir diperbarui',
    'last_updated_date' => '26 November 2026',
    'intro' => 'Situs hafizhrafid.dev adalah portofolio dan blog teknis pribadi milik Hafizh Rafid. Halaman ini menjelaskan data apa saja yang disimpan saat kamu berkunjung dan bagaimana data tersebut dipakai.',
    'sections' => [
        'principles' => [
            'heading' => 'Prinsip dasar',
            'body' => 'Situs ini tidak memasang pelacak lintas situs, tidak menampilkan iklan pihak ketiga, tidak melakukan browser fingerprinting, dan tidak menyimpan alamat IP mentah pengunjung di database.',
        ],
        'storage' => [
            'heading' => 'Cookie dan penyimpanan lokal',
            'intro' => 'Pada tahap saat ini, situs hanya menggunakan penyimpanan minimal agar pilihan bahasa, tema tampilan, dan keamanan formulir berfungsi:',
            'items' => [
                'locale' => [
                    'key' => 'locale',
                    'type' => 'Cookie first-party (HttpOnly, SameSite=Lax, 1 tahun)',
                    'description' => 'Menyimpan pilihan bahasa antarmuka (id atau en) saat kamu mengganti bahasa secara manual.',
                ],
                'theme' => [
                    'key' => 'theme',
                    'type' => 'localStorage browser (tidak dikirim ke server)',
                    'description' => 'Menyimpan pilihan tema tampilan (terang, gelap, atau ikuti sistem) di perangkat kamu.',
                ],
                'session' => [
                    'key' => 'Cookie sesi dan XSRF-TOKEN',
                    'type' => 'Cookie first-party bawaan framework',
                    'description' => 'Dipakai untuk perlindungan keamanan pengiriman formulir (CSRF) dan sesi administrasi situs.',
                ],
            ],
        ],
        'contact_form' => [
            'heading' => 'Formulir kontak',
            'body' => 'Jika kamu mengirim pesan melalui formulir kontak (saat tersedia), nama, alamat email, dan isi pesan disimpan agar saya dapat membalasnya. Untuk mencegah spam, server hanya menyimpan nilai hash satu arah dan bukan alamat IP mentah.',
        ],
        'future' => [
            'heading' => 'Fitur mendatang (belum aktif)',
            'body' => 'Tahap pengembangan berikutnya merencanakan cookie identitas pengunjung anonim (vid) untuk fitur suka artikel, penghitung kunjungan harian berbasis hash satu arah dengan salt harian, analitik mandiri tanpa cookie, serta login sosial opsional untuk menyimpan artikel. Seluruh fitur tersebut belum aktif saat ini dan kebijakan ini akan diperbarui sebelum dirilis.',
        ],
        'contact' => [
            'heading' => 'Pertanyaan dan penghapusan data',
            'body' => 'Jika kamu memiliki pertanyaan mengenai privasi atau ingin meminta penghapusan pesan kontak yang pernah dikirim, silakan kirim email ke:',
        ],
    ],
];
