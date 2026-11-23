# Roadmap dan Backlog

| | |
|---|---|
| **Versi** | 0.2 |
| **Tanggal** | 23 November 2026 |
| **Mengacu ke** | `01-project-brief.md` (v0.2), `02-technical-design.md` (v0.2), `03-sitemap-wireframe-design.md` (v0.2) |

> Dokumen ini dirancang untuk dipindahkan ke **GitHub Projects/Issues**. Setiap baris tugas bisa menjadi satu issue. Estimasi dihitung untuk pengerjaan paruh waktu (8 sampai 12 jam per minggu) dan hanya perkiraan kasar.

---

## 1. Ringkasan roadmap

| Fase | Fokus | Perkiraan | Hasil |
|---|---|---|---|
| **Fase 0** | Persiapan dan spike teknis | 3 sampai 5 hari | Repo, lingkungan lokal, keputusan teknis terkonfirmasi |
| **Fase 1** | MVP | 3 sampai 4 minggu | Web publik dengan portofolio, blog dua bahasa, admin |
| **Konten** | Penulisan konten (paralel dengan Fase 1) | Berjalan | 3 studi kasus, 3 artikel, CV dua bahasa |
| **Fase 2** | Interaksi, SEO lanjutan, performa | 2 sampai 3 minggu | View, like, RSS, OG image, analytics, cache |
| **Fase 3** | Akun dan fitur lanjutan | 3 sampai 4 minggu | Login sosial, bookmark, pencarian, newsletter, fitur AI |

Prinsip pengerjaan:
1. **Rilis MVP dulu.** Jangan mulai Fase 2 sebelum Definition of Done MVP terpenuhi.
2. **Konten sama pentingnya dengan kode.** Web tanpa isi tidak membantu personal branding.
3. Satu tugas sebaiknya selesai dalam **maksimal 1 sampai 2 hari kerja**. Jika lebih, pecah.

---

## 2. Cara memakai di GitHub

### 2.1 Kolom papan (GitHub Projects)

`Backlog` → `Siap dikerjakan` → `Sedang dikerjakan` → `Review/Uji` → `Selesai`

### 2.2 Label

| Kelompok | Label |
|---|---|
| Fase | `fase-0`, `fase-1`, `fase-2`, `fase-3` |
| Tipe | `feat`, `bug`, `chore`, `docs`, `content`, `spike` |
| Area | `blog`, `portfolio`, `i18n`, `admin`, `seo`, `perf`, `security`, `infra`, `a11y` |
| Prioritas | `must`, `should`, `could` |

### 2.3 Template issue

```markdown
## Tujuan
Satu kalimat: apa yang ingin dicapai dan kenapa.

## Kriteria selesai
- [ ] ...
- [ ] ...

## Catatan teknis
Tautan ke bagian dokumen terkait (mis. TDD §6.2).
```

### 2.4 Konvensi

- Branch: `feat/<singkat>`, `fix/<singkat>`, `chore/<singkat>`.
- Commit: Conventional Commits (`feat: ...`, `fix: ...`).
- Satu PR menutup satu atau beberapa issue kecil. CI harus hijau sebelum merge.
- Rilis ditandai dengan tag `v0.1.0` (MVP), `v0.2.0` (Fase 2), dan seterusnya.

---

## 3. Backlog

Kolom **Ref** merujuk ke ID kebutuhan pada Project Brief atau bagian pada Technical Design (TDD). Ukuran: **S** ≤ 0,5 hari, **M** 1 hari, **L** 2 hari.

### Fase 0: Persiapan dan spike teknis

| ID | Tugas | Ukuran | Ref |
|---|---|---|---|
| P0-01 | Buat repo GitHub (publik), lisensi, `.gitignore`, `.env.example`, folder `docs/` berisi keenam dokumen (`01` sampai `06`) | S | G6 |
| P0-02 | Inisialisasi Laravel, Livewire, Tailwind, Vite. Verifikasi versi terbaru dan kompatibilitasnya | M | TDD §2 |
| P0-03 | Docker Compose lokal (app, web, postgres, redis, worker) | M | TDD §11 |
| P0-04 | Pasang Pint, Larastan, Pest dan konfigurasi dasar | S | TDD §12 |
| P0-05 | CI dasar GitHub Actions (lint, analisis statis, test) | M | TDD §11.2 |
| P0-06 | **Spike (Selesai):** pasang Filament dan uji plugin CKEditor komunitas. Terpilih `kahusoftware/filament-ckeditor-field` | M | TDD §8 |
| P0-07 | **Spike:** coba layanan terjemahan (DeepL vs LLM API) pada 1 artikel contoh berisi code block. Putuskan penyedia dan batas biaya | M | TDD §5.3 |
| P0-08 | **Spike:** pilih highlighter sisi server (PHP murni vs Shiki) | S | TDD §8.3 |
| P0-09 | Pesan domain, pilih VPS, siapkan Cloudflare **[Ditunda — dikerjakan menjelang deployment sebelum `F1-55`]** | S | Brief §11 |
| P0-10 | Aktifkan Dependabot, secret scanning, dan proteksi branch `main` | S | TDD §10 |

**Selesai Fase 0 jika:** repo berjalan lokal, CI hijau, tiga spike teknis (`P0-06`, `P0-07`, `P0-08`) sudah punya keputusan tertulis di TDD §14 dan `06-progress-log.md`, serta `P0-10` aktif. **`P0-09` tidak menghalangi masuk ke Fase 1** dan dikerjakan sebelum `F1-55`.

### Fase 1: MVP

#### 1A. Fondasi dan i18n

| ID | Tugas | Ukuran | Ref |
|---|---|---|---|
| F1-01 | Layout dasar (header, footer tanpa tautan RSS, tema terang/gelap), token warna dan font | M | Desain §3 |
| F1-02 | Middleware `SetLocale`, prefix `/id` dan `/en`, redirect root untuk non-crawler, isolasi route non-locale | M | FR-06, FR-07, TDD §5.1 |
| F1-03 | File terjemahan UI (`lang/id`, `lang/en`) dan pemilih bahasa | M | FR-06 |
| F1-04 | Komponen hreflang dan meta SEO dasar (title, description, canonical, OG) | M | FR-12 |
| F1-05 | Halaman 404 dan kebijakan privasi (draf) | S | FR-14 |

#### 1B. Model data dan admin

| ID | Tugas | Ukuran | Ref |
|---|---|---|---|
| F1-10 | Migrasi dan model Fase 1: `users`, `categories`, `articles` (kolom `view_count`/`like_count` default `0`), `article_category`, `article_revisions`, `projects` (termasuk `technical_decisions`), `category_project`, `article_project`, `contact_messages`, `settings` | L | TDD §4.1–§4.2 |
| F1-11 | Seeder data contoh (kategori, 2 proyek + relasi, 3 artikel, settings profil/skills/timeline) | S | TDD §4.3 |
| F1-12 | Filament: akses hanya admin, middleware `NoIndexAdmin` | S | TDD §7 |
| F1-13 | Filament Resource: Kategori | S | FR-08 |
| F1-14 | Filament Resource: Artikel (field dasar, status, jadwal terbit, kategori, media `cover`) | L | FR-08, TDD §4.4 |
| F1-15 | Integrasi CKEditor (`kahusoftware/filament-ckeditor-field`) di form artikel sesuai konfigurasi toolbar TDD §8.2 | L | FR-09, TDD §8.1–§8.2 |
| F1-16 | Upload gambar dari editor ke Media Library (`content_images`) + validasi sisi server wajib atribut `alt` pada Action `PublishArticle` | M | FR-09, TDD §8.1 |
| F1-17 | Filament Resource: Proyek (field studi kasus termasuk `technical_decisions`, teknologi, relasi kategori & artikel terkait, media `thumbnail` & `gallery`, tautan) | M | FR-08, TDD §4.4 |
| F1-18 | Halaman pengaturan profil di admin (`profile.about`, `profile.hero_subtitle`, `profile.skills`, `profile.timeline` via Repeater, `social.links`, CV ID/EN, `profile.open_to_work`) | M | FR-02, TDD §4.3 |

#### 1C. Pipeline konten

| ID | Tugas | Ukuran | Ref |
|---|---|---|---|
| F1-20 | Sanitasi HTML (allow-list) saat simpan, disertai test XSS | M | TDD §8.3, §10 |
| F1-21 | `HtmlProcessor`: anchor heading, daftar isi (TOC), atribut gambar, `rel` link | L | FR-15, TDD §8.3 |
| F1-22 | Highlight code block sisi server (sesuai hasil spike `P0-08`) dan tombol salin | M | TDD §8.3 |
| F1-23 | Estimasi waktu baca (`reading_time_min`) dan penyimpanan `body_rendered` + `toc` | S | FR-15 |
| F1-24 | Revisi artikel otomatis (batas 20 terakhir) | S | TDD §8.3 |

#### 1D. Terjemahan artikel

| ID | Tugas | Ukuran | Ref |
|---|---|---|---|
| F1-30 | `TranslatorInterface` dan implementasi penyedia terpilih (hasil spike `P0-07`) | M | TDD §5.3 |
| F1-31 | Perlindungan `<pre>`, `<code>`, URL dengan placeholder, beserta unit test | M | TDD §5.3 |
| F1-32 | `TranslateArticleJob` dan tombol "Buat terjemahan" di Filament, hasil berupa draf | L | FR-10 |
| F1-33 | Aturan publikasi: terjemahan perlu ditinjau (`reviewed_at`), penanda "terjemahan usang" | M | TDD §5.3 |
| F1-34 | Label "diterjemahkan dengan bantuan AI" pada halaman artikel | S | Desain §3.9 |

#### 1E. Halaman publik

| ID | Tugas | Ukuran | Ref |
|---|---|---|---|
| F1-40 | Beranda: hero, CTA, proyek pilihan, artikel terbaru | L | FR-01 |
| F1-41 | Portofolio: profil, keahlian (`profile.skills`), timeline (`profile.timeline`), daftar proyek + filter kategori, unduh CV, formulir kontak | L | FR-02, FR-11 |
| F1-42 | Detail proyek (studi kasus termasuk `technical_decisions`, galeri, dan artikel terkait dari `article_project`) | M | FR-03, Desain §2.3 |
| F1-43 | Daftar blog (hanya artikel sesuai `{locale}` aktif, filter kategori, pagination; tanpa angka view/like di MVP) | M | FR-04, FR-05, ADR-10 |
| F1-44 | Detail artikel: tipografi `prose`, daftar isi (TOC), waktu baca, breadcrumb, artikel terkait per kategori, tombol bagikan, pemilih bahasa pasangan *(tanpa view/like/bookmark di MVP)* | L | FR-04, FR-15 |
| F1-45 | Endpoint unduh CV `/cv/{locale}` | S | FR-02 |
| F1-46 | Formulir kontak: validasi, honeypot, rate limit, simpan ke `contact_messages`, notifikasi email | M | FR-11 |
| F1-47 | Responsif dan pemeriksaan aksesibilitas (keyboard, kontras, `alt`) | M | NFR-03 |

#### 1F. SEO, kualitas, dan rilis

| ID | Tugas | Ukuran | Ref |
|---|---|---|---|
| F1-50 | Sitemap dua bahasa dan `robots.txt` | S | FR-12 |
| F1-51 | JSON-LD `Person`, `BlogPosting`, dan `BreadcrumbList` | M | FR-12, TDD §9 |
| F1-52 | Pisahkan bundle `admin.js` (CKEditor) agar tidak dimuat di halaman publik | S | NFR-01 |
| F1-53 | Test fitur inti: locale, hreflang, isolasi blog per locale, publikasi artikel + validasi `alt`, akses admin, sanitasi | L | TDD §12 |
| F1-54 | Lighthouse CI dengan ambang skor minimum | M | G1 |
| F1-55 | Deployment produksi: Docker di VPS, HTTPS, Cloudflare, worker dan scheduler *(Prasyarat: `P0-09` selesai)* | L | TDD §11 |
| F1-56 | Backup database harian dan uji pemulihan | M | NFR-06 |
| F1-57 | Header keamanan dan CSP | M | TDD §10 |
| F1-58 | README lengkap (deskripsi, tangkapan layar, cara menjalankan, arsitektur, badge CI) | M | G6 |
| F1-59 | Kirim sitemap ke Google Search Console, rilis `v0.1.0` | S | G2 |

#### Konten (paralel dengan Fase 1)

| ID | Tugas | Ukuran | Ref |
|---|---|---|---|
| C-01 | Tulis teks "Tentang saya" dan hero (ID dan EN) | M | Desain §3.9 |
| C-02 | Siapkan foto profil dan CV (PDF) versi ID dan EN | M | FR-02 |
| C-03 | Studi kasus proyek #1 (teks + tangkapan layar) | L | FR-03 |
| C-04 | Studi kasus proyek #2 | L | FR-03 |
| C-05 | Studi kasus proyek #3 | L | FR-03 |
| C-06 | Artikel #1: **bagaimana web ini dibangun** (keputusan arsitektur) | L | G4 |
| C-07 | Artikel #2: eksperimen pilihan kamu | L | G4 |
| C-08 | Artikel #3: eksperimen pilihan kamu | L | G4 |
| C-09 | Terjemahkan dan tinjau minimal artikel #1 | M | FR-10 |

**Definition of Done MVP:** lihat Project Brief §12.

### Fase 2: Interaksi, SEO lanjutan, performa

| ID | Tugas | Ukuran | Ref |
|---|---|---|---|
| F2-01 | Middleware `EnsureVisitorId` (cookie UUID first-party) dan pembaruan kebijakan privasi | S | TDD §6.1 |
| F2-02 | Migrasi tabel `article_views` dan logika hash view harian | M | FR-20, TDD §6.2 |
| F2-03 | Endpoint sinyal view, filter bot, rate limit, `RecordViewJob` | L | FR-20 |
| F2-04 | Migrasi tabel `article_likes`, like anonim idempoten, komponen Livewire `LikeButton`, rate limit | L | FR-21, TDD §6.3 |
| F2-05 | Muat dan tampilkan hitungan view dan like sebagai fragmen terpisah di daftar blog & detail artikel (kompatibel dengan cache halaman) | M | TDD §6.4 |
| F2-06 | Test: view dihitung sekali per hari, like idempoten, bot tidak dihitung | M | TDD §12 |
| F2-07 | Response cache untuk halaman publik dan aturan invalidasi saat konten berubah | L | FR-25 |
| F2-08 | RSS per bahasa (`/{locale}/feed.xml`) dan tampilkan tautan RSS di footer & sidebar blog | S | FR-22 |
| F2-09 | OG image otomatis per artikel | L | FR-22 |
| F2-10 | Peningkatan artikel terkait (kategori dan kemiripan konten) | M | FR-23 |
| F2-11 | Analytics ringan (Umami atau Plausible, self-hosted) | M | FR-24 |
| F2-12 | Pemangkasan tabel `article_views` berkala (scheduler) | S | TDD §4.2 |
| F2-13 | Audit performa dan aksesibilitas, perbaiki temuan, rilis `v0.2.0` | M | G1 |

### Fase 3: Akun dan fitur lanjutan

| ID | Tugas | Ukuran | Ref |
|---|---|---|---|
| F3-01 | Migrasi tabel `social_accounts`, login sosial Google dan GitHub (Socialite) | L | FR-30, TDD §4.2 |
| F3-02 | Peran `member` dan pembatasan akses admin, 2FA admin (opsional) | M | TDD §7 |
| F3-03 | Migrasi tabel `bookmarks`, fitur bookmark (hanya login), dan halaman artikel tersimpan | M | FR-31, TDD §4.2 |
| F3-04 | Banner ajakan login: inline, sekali per sesi, dismiss disimpan | M | FR-32 |
| F3-05 | Penggabungan like anonim ke akun saat login | M | FR-33 |
| F3-06 | Pencarian artikel (full-text PostgreSQL, atau Scout + Meilisearch) | L | FR-34 |
| F3-07 | Newsletter: formulir berlangganan, double opt-in, pengiriman | L | FR-35 |
| F3-08 | Fitur AI: pencarian semantik atau "tanya tentang saya" (RAG) | L+ | FR-36 |
| F3-09 | Halaman Now/Uses dan timeline sertifikasi (opsional) | M | Brief §5 |
| F3-10 | Rilis `v0.3.0` dan tulis artikel retrospektif | M | G4 |

---

## 4. Rencana minggu pertama (contoh)

| Hari | Fokus |
|---|---|
| 1 | P0-01, P0-02 |
| 2 | P0-03, P0-04, P0-05 |
| 3 | P0-06 (spike CKEditor + Filament) |
| 4 | P0-07, P0-08 (spike terjemahan dan highlighter) |
| 5 | P0-10, perbarui TDD & progress log dengan keputusan spike, mulai F1-01 *(P0-09 ditunda hingga sebelum F1-55)* |

## 5. Risiko jadwal dan cara menyiasatinya

| Risiko | Siasat |
|---|---|
| Kuliah atau skripsi menyita waktu | Turunkan target mingguan, jangan hentikan. Kerjakan tugas ukuran S di hari padat |
| Plugin CKEditor bermasalah saat upgrade | Kunci versi minor di `composer.json`; opsi custom field tetap tersedia sebagai cadangan |
| Konten tidak siap saat kode selesai | Mulai tugas `C-xx` sejak minggu pertama Fase 1, bukan setelah kode selesai |
| Perfeksionisme pada desain | Gunakan token dan komponen minimal, rapikan setelah rilis |
| Biaya terjemahan membengkak | Tetapkan batas bulanan di penyedia, terjemahkan hanya artikel pilihan |

## 6. Peninjauan berkala

- **Setiap minggu:** tinjau papan, pindahkan tugas yang macet, tetapkan 3 tugas prioritas pekan depan.
- **Setiap akhir fase:** perbarui dokumen ini dan Technical Design jika ada keputusan yang berubah, lalu tulis catatan rilis.
- **Setiap bulan setelah rilis:** tinjau data Search Console dan analytics, lalu sesuaikan rencana konten.
