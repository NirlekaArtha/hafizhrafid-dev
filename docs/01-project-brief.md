# Project Brief: Website Personal Branding

| | |
|---|---|
| **Versi** | 0.2 |
| **Tanggal** | 23 November 2026 |
| **Penulis** | M. Hafizh Rafid Hidayat (nama brand: Hafizh Rafid) |
| **Status** | Disetujui untuk implementasi MVP (Fase 0 → Fase 1) |

> Dokumen ini adalah acuan tertinggi untuk ruang lingkup. Jika ada fitur yang tidak tercantum di sini, anggap **belum** dikerjakan sampai dokumen ini diperbarui.
> **Hierarki Sumber Kebenaran (SSOT):** `01-project-brief.md` (ruang lingkup) → `02-technical-design.md` (arsitektur & skema) → `03-sitemap-wireframe-design.md` (UI & routing) → `04-roadmap-backlog.md` (backlog) → `06-progress-log.md` (status & keputusan terkini).

---

## 1. Ringkasan

Website pribadi yang berfungsi sebagai **etalase profesional** seorang mahasiswa Teknik Informatika yang berfokus pada pengembangan perangkat lunak, web, dan AI. Website berisi halaman beranda, portofolio (profil, proyek, CV), dan blog teknis dua bahasa (Indonesia dan Inggris) yang memuat hasil eksperimen.

Target jangka pendek: dilirik perusahaan lokal (software house, startup, korporasi) untuk magang dan posisi junior. Target jangka panjang: menjadi hub tempat proyek SaaS pribadi dipamerkan.

## 2. Latar belakang dan masalah

- Recruiter membutuhkan bukti kemampuan yang bisa diperiksa dengan cepat, bukan sekadar daftar skill di CV.
- Profil di LinkedIn dan GitHub tersebar dan tidak punya narasi tunggal.
- Tulisan dan eksperimen yang sudah dikerjakan belum punya tempat yang mudah ditemukan lewat mesin pencari.

## 3. Tujuan dan metrik keberhasilan

Angka berikut adalah **target awal** dan boleh direvisi setelah ada data nyata.

| # | Tujuan | Metrik | Target |
|---|---|---|---|
| G1 | Kesan pertama yang cepat dan profesional | Lighthouse mobile (Performance, Accessibility, Best Practices, SEO) pada beranda dan halaman artikel | ≥ 95 masing-masing |
| G2 | Mudah ditemukan | Halaman publik terindeks Google | Semua halaman utama terindeks dalam 4 minggu setelah rilis |
| G3 | Membuktikan kompetensi | Jumlah studi kasus proyek di portofolio | ≥ 3 pada rilis MVP |
| G4 | Membangun kredibilitas lewat tulisan | Jumlah artikel terbit | ≥ 6 dalam 3 bulan pertama (sekitar 1 artikel per 2 minggu) |
| G5 | Memudahkan dihubungi | Jumlah klik unduh CV dan pesan masuk | Dipantau sejak rilis, tanpa target angka |
| G6 | Repo layak dipamerkan | README lengkap, CI hijau, badge, dokumentasi | Terpenuhi pada rilis MVP |

## 4. Target pengguna

| Persona | Kebutuhan utama | Waktu yang dimiliki |
|---|---|---|
| **Recruiter/HR** | Tahu siapa kamu, skill apa, dan cara menghubungi, serta mengunduh CV | 30 detik sampai 2 menit |
| **Hiring manager/engineer** | Menilai kedalaman teknis lewat studi kasus, kode, dan tulisan | 5 sampai 15 menit |
| **Pembaca blog (sesama developer)** | Artikel teknis yang jelas, mudah dicari, dan bisa disimpan | Sesuai panjang artikel |
| **Calon kolaborator/klien** | Melihat jejak proyek dan cara menghubungi | 2 sampai 5 menit |

Prinsip utama: **tidak ada hambatan akses.** Semua halaman terbuka tanpa login.

## 5. Ruang lingkup

### 5.1 MVP (Fase 1)

| ID | Fitur | Prioritas |
|---|---|---|
| FR-01 | Beranda: hero singkat, CTA ke Portofolio dan Blog, cuplikan proyek dan artikel terbaru | Must |
| FR-02 | Portofolio: perkenalan, skill, pengalaman/pendidikan, daftar proyek, tombol unduh CV (versi ID dan EN) | Must |
| FR-03 | Halaman detail proyek berformat studi kasus (masalah, solusi, peran, keputusan teknis & tantangan, stack, hasil, artikel terkait, tautan demo dan repo) | Must |
| FR-04 | Blog: daftar artikel sesuai locale aktif, halaman detail, filter per kategori, pagination | Must |
| FR-05 | Banyak kategori artikel (AI, Web, IoT, DevOps, dst.), satu artikel bisa punya lebih dari satu kategori | Must |
| FR-06 | Dua bahasa (ID/EN) pada UI, portofolio, dan artikel, dengan URL per bahasa dan pemilih bahasa manual | Must |
| FR-07 | Deteksi bahasa otomatis (Accept-Language, cookie, IP sebagai cadangan), dapat diubah pengguna | Must |
| FR-08 | Panel admin (Filament) untuk mengelola artikel, kategori, proyek, profil, dan pesan masuk | Must |
| FR-09 | Editor artikel CKEditor 5 dengan upload gambar dan validasi wajib atribut `alt` saat publikasi | Must |
| FR-10 | Terjemahan artikel: tombol "buat terjemahan" yang menghasilkan draf, ditinjau manual sebelum terbit | Must |
| FR-11 | Formulir kontak dan tautan GitHub, LinkedIn, dan email | Must |
| FR-12 | SEO dasar & structured data: meta, Open Graph, sitemap, hreflang, canonical, robots, serta JSON-LD (`Person`, `BlogPosting`, `BreadcrumbList`) | Must |
| FR-13 | Mode gelap dan terang, responsif, halaman 404 rapi | Should |
| FR-14 | Halaman Kebijakan Privasi (menjelaskan cookie dan data yang dipakai) | Must |
| FR-15 | Pengalaman baca artikel: daftar isi (TOC) otomatis, estimasi waktu baca, dan artikel terkait (berdasarkan kategori) | Should |

### 5.2 Fase 2

| ID | Fitur | Prioritas |
|---|---|---|
| FR-20 | View counter anonim (satu view per pengunjung per 24 jam, filter bot) | Must |
| FR-21 | Like anonim (cookie first-party + rate limit) | Must |
| FR-22 | RSS feed per bahasa dan OG image otomatis per artikel | Should |
| FR-23 | Peningkatan artikel terkait (berdasarkan kategori dan kemiripan konten) | Should |
| FR-24 | Analytics ringan tanpa cookie (Umami atau Plausible) | Should |
| FR-25 | Cache halaman penuh untuk artikel | Should |

### 5.3 Fase 3

| ID | Fitur | Prioritas |
|---|---|---|
| FR-30 | Login sosial (Google, GitHub), opsional dan tidak pernah wajib | Must |
| FR-31 | Bookmark/simpan artikel (hanya untuk pengguna login) | Must |
| FR-32 | Ajakan login tidak mengganggu: banner inline di akhir artikel, maksimal satu kali per sesi | Must |
| FR-33 | Penggabungan like anonim ke akun saat pengguna login | Should |
| FR-34 | Pencarian artikel | Should |
| FR-35 | Newsletter email | Could |
| FR-36 | Fitur AI di web (pencarian semantik atau "tanya tentang saya" berbasis RAG) | Could |

## 6. Di luar ruang lingkup (sengaja tidak dikerjakan)

- Komentar bawaan (jika diperlukan nanti, pakai layanan eksternal seperti Giscus).
- Multi-tenancy, pembayaran, atau fitur SaaS apa pun. Proyek SaaS dibangun di repo terpisah.
- Banyak penulis atau sistem peran kompleks. Admin hanya satu orang.
- Aplikasi mobile.
- Terjemahan ke bahasa selain Indonesia dan Inggris.
- Browser fingerprinting (diganti metode hash harian + cookie UUID, lihat Technical Design).
- Pencampuran artikel lintas bahasa dalam satu halaman daftar blog (setiap prefix `/{locale}/blog` hanya menampilkan artikel berbahasa tersebut).

## 7. Kebutuhan non-fungsional

| ID | Kategori | Kebutuhan |
|---|---|---|
| NFR-01 | Performa | LCP ≤ 2,5 detik, CLS ≤ 0,1 pada koneksi mobile standar. Halaman publik tidak memuat JS editor |
| NFR-02 | SEO | HTML dirender di server, hreflang untuk pasangan bahasa, sitemap, canonical, JSON-LD |
| NFR-03 | Aksesibilitas | Kontras memadai, navigasi keyboard, `alt` pada gambar, target WCAG 2.2 AA |
| NFR-04 | Keamanan | HTML dari editor disanitasi, rate limit di endpoint publik, secret tidak masuk repo, header keamanan, panel admin hanya untuk peran admin |
| NFR-05 | Privasi | Tidak ada pelacakan lintas situs, IP tidak disimpan mentah, kebijakan privasi tersedia |
| NFR-06 | Keandalan | Backup database otomatis harian, pemulihan pernah diuji |
| NFR-07 | Keterawatan | Test otomatis untuk logika inti, CI wajib hijau sebelum deploy |
| NFR-08 | Biaya | Biaya hosting terjangkau (VPS kecil + Cloudflare gratis), tanpa lisensi berbayar |

## 8. Keputusan yang sudah ditetapkan

| Topik | Keputusan |
|---|---|
| Arsitektur | Modular monolith |
| Stack | Laravel 13, Livewire 4, Alpine.js, Tailwind CSS, Filament 5, PostgreSQL |
| Editor | CKEditor 5 (GPL, self-hosted) lewat plugin komunitas `kahusoftware/filament-ckeditor-field` (terkonfirmasi pada spike P0-06) |
| Repo | Publik di GitHub (`NirlekaArtha/hafizhrafid-dev`) |
| Akses | Semua halaman terbuka tanpa login |
| Konten dua bahasa | Satu artikel per bahasa dihubungkan oleh grup terjemahan, terjemahan dibantu mesin lalu ditinjau manual; setiap prefix `/{locale}/blog` terisolasi per bahasa |
| Data profil | Keahlian dan timeline pendidikan/pengalaman disimpan sebagai JSON terstruktur di tabel `settings` |
| View dan like anonim | Hash harian (IP + User-Agent + salt) untuk view, UUID cookie first-party untuk like, tanpa fingerprinting (aktif di Fase 2) |
| Login | Hanya login sosial, tidak wajib, ajakan berupa banner inline (Fase 3) |
| Hosting | VPS + Docker + Cloudflare (domain `hafizhrafid.dev` sudah dibeli; pengadaan VPS dan setup Cloudflare ditunda hingga pra-deployment sebelum `F1-55`; development berjalan lokal) |

## 9. Asumsi

- Penulis tunggal, dikerjakan paruh waktu di sela kuliah (sekitar 8 sampai 12 jam per minggu).
- Plugin `kahusoftware/filament-ckeditor-field` telah diverifikasi kompatibel dengan Filament 5 pada spike P0-06; penegakan wajib `alt` gambar ditangani di sisi server saat publikasi.
- Layanan terjemahan (LLM API atau DeepL) memiliki biaya yang kecil untuk volume artikel pribadi.
- Domain (`hafizhrafid.dev`) sudah dibeli; VPS dan konfigurasi Cloudflare disiapkan menjelang deployment produksi (`F1-55`), tidak menghalangi development lokal Fase 1.

## 10. Risiko dan mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Scope creep karena ingin semua fitur ada | MVP tidak pernah rilis | Patuhi pembagian fase, rilis MVP dulu |
| Konten kosong atau sedikit | Web tidak meyakinkan | Siapkan 3 studi kasus dan 3 artikel **sebelum** rilis publik |
| Perubahan breaking pada plugin CKEditor di versi Filament mendatang | Terhambat saat upgrade panel admin | Kunci versi minor di `composer.json` dan pertahankan opsi custom field sebagai cadangan |
| Terjemahan mesin berkualitas buruk | Mengurangi kredibilitas | Wajib ditinjau manual, label "dibantu AI", jangan terjemahkan code block |
| Lisensi GPL CKEditor terbawa ke proyek SaaS | Masalah hukum | Gunakan hanya di web ini, evaluasi editor lain atau lisensi komersial untuk SaaS |
| Penyalahgunaan like dan view | Angka tidak dipercaya | Rate limit, filter bot, Turnstile bila perlu |
| Kebocoran API key di repo publik | Kerugian biaya dan keamanan | `.env` tidak di-commit, secret scanning GitHub, rotasi kunci bila perlu |
| Kesibukan kuliah | Proyek terhenti | Pecah jadi tugas kecil, target mingguan realistis |

## 11. Pertanyaan terbuka

1. ~~Nama brand~~ **Diputuskan:** Hafizh Rafid (domain `hafizhrafid.dev`, GitHub `NirlekaArtha`, repo `hafizhrafid-dev`). Domain telah dibeli, sedangkan VPS masih dalam tahap pengambilan keputusan.
2. Penyedia layanan terjemahan: DeepL atau LLM API? *(Sedang dievaluasi di spike `P0-07`)*
3. VPS di penyedia lokal (latensi ke Indonesia) atau luar negeri? *(Diputuskan saat `P0-09`, sebelum `F1-55`)*
4. ~~Apakah pop-up ajakan login ditunda sampai Fase 3?~~ **Diputuskan:** Tidak memakai pop-up; menggunakan banner inline tidak mengganggu di akhir artikel pada Fase 3 (lihat `FR-32` dan TDD §7).

## 12. Definition of Done rilis MVP

- [ ] Seluruh fitur `FR-01` sampai `FR-15` berfungsi di produksi.
- [ ] Minimal 3 studi kasus proyek dan 3 artikel (masing-masing tersedia dalam dua bahasa, atau diberi label bahasa dengan jelas).
- [ ] Lighthouse mobile ≥ 95 untuk beranda, daftar blog, dan satu artikel.
- [ ] CI hijau, test inti lolos, backup otomatis berjalan.
- [ ] README publik lengkap (deskripsi, tangkapan layar, cara menjalankan, keputusan arsitektur, badge CI).
- [ ] Sitemap dikirim ke Google Search Console.
- [ ] Kebijakan privasi terbit.
