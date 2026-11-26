# Progress Log

Catatan keadaan proyek untuk manusia **dan** agen AI. Agen tidak mengingat percakapan sebelumnya,
jadi file ini adalah ingatan bersama. Perbarui di akhir setiap tugas.

## Status saat ini

| | |
|---|---|
| **Fase aktif** | Fase 1 (MVP): `F1-05` selesai (sub-fase 1A Fondasi & i18n selesai), berikutnya `F1-10` (Model data & admin). Aktivasi UI GitHub `P0-10` masih perlu dicek manual |
| **Rilis terakhir** | (belum ada) |
| **Terakhir diperbarui** | 2026-11-26 |

## Keputusan yang sudah ditetapkan

| Tanggal | Keputusan | Sumber |
|---|---|---|
| 2026-11-20 | Nama brand: Hafizh Rafid. Domain `hafizhrafid.dev`, GitHub `NirlekaArtha`, repo `hafizhrafid-dev` | docs/01 §11 |
| 2026-11-20 | Modular monolith Laravel + Livewire + Filament + PostgreSQL | docs/02 §2 |
| 2026-11-20 | Lisensi kode GPL-3.0-or-later, konten dilindungi terpisah | LICENSE-CONTENT.md |
| 2026-11-20 | View dan like anonim memakai hash harian dan cookie UUID, tanpa fingerprinting (aktif Fase 2) | docs/02 §6 |
| 2026-11-20 | Terjemahan artikel selalu berupa draf yang wajib ditinjau | docs/02 §5.3 |
| 2026-11-23 | Editor CKEditor menggunakan `kahusoftware/filament-ckeditor-field` (hasil spike P0-06); wajib `alt` gambar ditegakkan di server saat publikasi | docs/02 §8.1, ADR-06 |
| 2026-11-23 | JSON-LD (`Person`, `BlogPosting`, `BreadcrumbList`), daftar isi (TOC), waktu baca, dan artikel terkait (per kategori) ditetapkan masuk MVP Fase 1 | docs/01 §5.1 (FR-12, FR-15) |
| 2026-11-23 | Filter lintas bahasa di sidebar blog dihapus; setiap `/{locale}/blog` hanya menampilkan artikel sesuai locale aktif | docs/02 ADR-10, docs/03 §2.4 |
| 2026-11-23 | Data keahlian (`profile.skills`) dan timeline (`profile.timeline`) disimpan sebagai JSON terstruktur di tabel `settings` | docs/02 §4.3, ADR-09 |
| 2026-11-23 | Domain `hafizhrafid.dev` sudah dibeli; pembelian VPS dan setup Cloudflare (`P0-09`) ditunda hingga menjelang `F1-55`; seluruh development Fase 1 berjalan lokal | docs/04 §3 |
| 2026-11-24 | Layanan terjemahan menggunakan Gemini API (`gemini-3.5-flash-lite`) sebagai penyedia utama dan DeepL API Free sebagai backup; batas biaya ≤ Rp 20.000/bulan | docs/02 §14 (spike P0-07) |
| 2026-11-24 | Syntax highlighting sisi server menggunakan `phiki/phiki` (PHP murni, TextMate grammar, dual-theme `github-light`/`github-dark`) tanpa Node.js di container runtime | docs/02 §2 & §14 (spike P0-08) |

## Status spike teknis

| ID | Pertanyaan | Status | Hasil |
|---|---|---|---|
| P0-06 | Plugin CKEditor komunitas kompatibel dengan versi Filament yang dipakai? | **Selesai** | Terpilih `kahusoftware/filament-ckeditor-field` (nyaman & kompatibel dengan Filament 5). Wajib `alt` gambar ditegakkan lewat validasi server saat publikasi (`F1-16`). |
| P0-07 | DeepL atau LLM API untuk terjemahan? | **Selesai** | Terpilih **Gemini API (`gemini-3.5-flash-lite`)** sebagai penyedia utama dan **DeepL API Free** sebagai backup, dengan batas biaya ≤ Rp 20.000/bulan. Proteksi `<pre>`, `<code>`, dan URL menggunakan token `[[X0]]` via `PlaceholderProtector`. |
| P0-08 | Highlighter sisi server: PHP murni atau Shiki via Node? | **Selesai** | Terpilih **`phiki/phiki`** (PHP murni berbasis TextMate grammar, dual-theme `github-light`/`github-dark`). Tidak butuh Node.js di container `app`/`worker`, mendukung semua bahasa target (`php`, `js`, `ts`, `bash`, `python`, `sql`, `yaml`, `dockerfile`), dan benchmark 7 blok ~121 ms pada cold cache. |

## Log pekerjaan

Format entri (terbaru di atas):

```
### YYYY-MM-DD · <ID tugas> · <judul>
- Yang berubah:
- Alasan / keputusan penting:
- Verifikasi: lint ✔/✘ · analyse ✔/✘ · test ✔/✘ · build ✔/✘
- Perlu ditinjau manual:
- Tindak lanjut:
```

### 2026-11-26 · F1-05 · Halaman 404 dan kebijakan privasi (draf)
- Yang berubah: Menambahkan route `GET /{locale}/privacy` (`public.privacy`) dan `Route::fallback` 404 di `routes/web.php`, view `resources/views/privacy.blade.php` dengan terjemahan dua bahasa (`lang/id/privacy.php`, `lang/en/privacy.php`), halaman galat kustom `resources/views/errors/404.blade.php` yang memakai `<x-layouts::public>`, serta view composer `errors::404` di `AppServiceProvider`. Menghapus kunci duplikat `common.privacy.title` di `lang/{id,en}/common.php`. Tautan Kebijakan Privasi di footer aktif otomatis pada kedua bahasa. Menambahkan feature test `tests/Feature/PrivacyAndErrorPagesTest.php`.
- Alasan / keputusan penting: `Route::fallback(abort(404))` dipasang di `routes/web.php` agar URL tak cocok melewati grup middleware `web` (`EncryptCookies`) sehingga cookie `locale` terenkripsi tetap terbaca saat merender 404. Resolusi locale (`LocaleResolver`) dan `URL::defaults(['locale' => ...])` dijalankan lewat `View::composer('errors::404', ...)` di `AppServiceProvider` agar template Blade tetap presentasional murni dan helper `route()` di header/footer aman. Halaman 404 memancarkan `robots="noindex, follow"`, menekan `hreflang`, dan mengarahkan pemilih bahasa ke `/id` atau `/en` agar tidak melompat ke 404 lain.
- Verifikasi: lint ✔ (`pint`, passed) · analyse ✔ (`phpstan`, 0 error) · test ✔ (156 passed / 926 assertions via `DB_CONNECTION=sqlite DB_DATABASE=:memory:` setelah `view:clear`) · build ✔ (`npm run build`)
- Perlu ditinjau manual: Klik-through di browser untuk `/id/privacy`, `/en/privacy`, `/id/tidak-ada`, `/en/missing`, dan `/unknown-route` pada mobile (360 px) dan desktop, kedua tema (terang/gelap), serta navigasi keyboard (`Tab`/`Enter`); juga jalankan ulang `docker compose exec -T app composer test` di container PostgreSQL.
- Tindak lanjut: Masuk sub-fase 1B dengan `F1-10` (migrasi dan model Fase 1).

### 2026-11-26 · F1-04 · Komponen hreflang dan meta SEO dasar (title, description, canonical, OG)
- Yang berubah: Menambahkan `Locale::ogLocale()` (`id_ID`/`en_US`) dan service `App\Domain\Localization\Services\HreflangResolver` untuk menghitung URL absolut `canonical` (bersih tanpa query string default, serta root `/` mengarah ke `/en`) dan pasangan `hreflang` (`id`, `en`, `x-default`). Membuat komponen Blade `<x-public.hreflang>` dan `<x-public.seo-meta>` (title, description, robots opsional, canonical, hreflang, Open Graph termasuk `og:locale`/`og:locale:alternate`/`og:image`/`article:*_time`, serta Twitter Card) dan memasangnya di `resources/views/layouts/public.blade.php`. Menambahkan `meta.default_description` simetris di `lang/{id,en}/layout.php`, mempersempit cek navigasi di `PublicLayoutTest` ke elemen `<nav>`, serta menambahkan `HreflangResolverTest` dan `SeoMetaTest`.
- Alasan / keputusan penting: Prop `hreflangUrls` dipisah dari `localeUrls` milik language switcher agar halaman artikel tanpa terjemahan terbit dapat tetap mengarahkan pemilih bahasa ke indeks blog bahasa lain sambil menekan output `<link rel="alternate" hreflang>` dan `og:locale:alternate` (hanya terbit bila minimal 2 locale terbit sesuai TDD §5.1). `HreflangResolver` memakai `Request::getSchemeAndHttpHost()` agar murni PHP tanpa ketergantungan container di unit test.
- Verifikasi: lint ✔ (`pint`, passed) · analyse ✔ (`phpstan`, 0 error) · test ✔ (147 passed / 766 assertions via `DB_CONNECTION=sqlite DB_DATABASE=:memory:` karena PHP host tidak punya `pdo_pgsql`) · build — (tidak ada perubahan aset JS/CSS)
- Perlu ditinjau manual: Verifikasi sumber HTML `<head>` di browser untuk `/id`, `/en`, dan `/` (dengan User-Agent crawler), serta jalankan ulang `docker compose exec -T app composer test` di container PostgreSQL. Catatan untuk `F1-57`: aktifkan `TrustHosts` di `bootstrap/app.php` sebagai pertahanan berlapis bersama Cloudflare/Caddy agar header `Host` palsu tidak mencemari URL absolut `canonical`/`hreflang`.
- Tindak lanjut: `F1-05` (halaman 404 dan kebijakan privasi draf).

### 2026-11-26 · F1-03 · File terjemahan UI (`lang/id`, `lang/en`) dan pemilih bahasa
- Yang berubah: Menambahkan `App\Domain\Localization\Services\LocaleUrlResolver` (menghitung URL tujuan per locale dengan dukungan override `localeUrls` per halaman serta sanitasi anti open-redirect) dan `App\Http\Controllers\LocaleSwitchController` pada route non-locale `GET /locale/{locale}` (`locale.switch`) yang menyimpan cookie `locale` selama 1 tahun (`HttpOnly`, `SameSite=Lax`, `Secure` saat HTTPS). Menambahkan komponen `<x-public.language-switcher>` di header (`components/public/header.blade.php`) dan prop opsional `localeUrls` di `layouts/public.blade.php`. Melengkapi file terjemahan UI simetris `lang/{id,en}/{layout,common,home,portfolio,blog}.php` beserta tes `LocaleUrlResolverTest`, `LanguageSwitcherTest`, dan pembaruan cek simetri dua arah di `PublicLayoutTest`.
- Alasan / keputusan penting: Memilih endpoint `GET /locale/{locale}?redirect=...` (`rel="nofollow"`) alih-alih `<form method="POST">` agar header publik tidak memuat CSRF token sehingga tetap kompatibel dengan full-page response cache di Fase 2 (`F2-07`), bekerja tanpa JavaScript, dan tetap menggunakan enkripsi cookie standar Laravel (`HttpOnly`). Parameter `redirect` divalidasi ketat hanya untuk path internal yang sesuai prefix locale tujuan.
- Verifikasi: lint ✔ (`pint`, passed) · analyse ✔ (`phpstan`, 0 error) · test ✔ (128 passed / 694 assertions via `DB_CONNECTION=sqlite DB_DATABASE=:memory:` karena PHP host tidak punya `pdo_pgsql` dan Podman terhalang sandbox) · build ✔ (`npm run build`)
- Perlu ditinjau manual: Klik-through di browser pada lebar 360 px dan desktop untuk kedua tema (terang/gelap) serta navigasi keyboard (`Tab`/`Enter`) pada kontrol `ID | EN`, dan jalankan ulang `docker compose exec -T app composer test` untuk konfirmasi di PostgreSQL.
- Tindak lanjut: `F1-04` (komponen `hreflang` dan meta SEO dasar: title, description, canonical, Open Graph).

### 2026-11-26 · F1-02 · Middleware `SetLocale`, prefix `/id` & `/en`, redirect root non-crawler, dan isolasi route non-locale
- Yang berubah: Menambahkan enum `App\Domain\Localization\Enums\Locale` (`id`, `en`, default `en`), `LocaleResolver` (prioritas URL → cookie `locale` → `Accept-Language` dengan bobot `q=` → `CF-IPCountry` → default `en`), `CrawlerDetector`, middleware `SetLocale` (alias `locale` di `bootstrap/app.php`), dan `RootRedirectController` untuk `/`. Memindahkan route publik ke grup `/{locale}` (`whereIn('locale', ['id', 'en'])`, nama `public.home`) dan menamai `/` sebagai `root`. Memperbarui tautan route di `components/public/header`, `components/public/footer`, layout auth starter kit, serta tes yang ada, dan menambahkan unit & feature test (`LocaleResolverTest`, `CrawlerDetectorTest`, `LocaleRoutingTest`).
- Alasan / keputusan penting: `SetLocale` dipasang sebagai route-group middleware (bukan global) agar seluruh route sistem/non-locale (`/admin/*`, `/up`, `/login`, dan nanti `/cv/{locale}`, `/sitemap.xml`, `/robots.txt`) otomatis terisolasi dari redirect locale. Crawler di `/` menerima `200 OK` dalam bahasa default `en` tanpa redirect paksa sesuai TDD §5.1, sedangkan pengunjung biasa mendapat `302` dengan header `Vary: Accept-Language, Cookie`.
- Verifikasi: lint ✔ (`pint`, 71 files) · analyse ✔ (`phpstan`, 0 error) · test ✔ (101 passed / 256 assertions di container `app` PostgreSQL) · build — (tidak ada perubahan aset JS/CSS)
- Perlu ditinjau manual: Tidak ada. (Catatan: jika menjalankan test bergantian antara host dan container `app`, bersihkan cache view dengan `php artisan view:clear` agar path absolut compiled view Livewire/Blaze tidak bentrok antara `/home/...` dan `/var/www/html`.)
- Tindak lanjut: `F1-03` (file terjemahan UI `lang/id`, `lang/en` lengkap dan komponen pemilih bahasa di header beserta penyimpanan cookie `locale`).

### 2026-11-25 · F1-01 · Layout dasar publik, token desain, dan tema terang/gelap
- Yang berubah: Menambahkan layout `layouts/public.blade.php` dengan komponen `public/header`, `public/footer`, dan `public/theme-toggle`. Token warna `docs/03` §3.2 dipasang sebagai CSS variable (`:root` dan `.dark`) dan dipetakan ke utilitas Tailwind (`bg-bg`, `text-fg`, `text-muted`, `bg-primary`, dan seterusnya). Font diganti ke Inter dan JetBrains Mono (self-host via plugin font Vite). Tema terang/gelap/sistem memakai JS biasa di `resources/js/app.js` plus skrip inline di `<head>` agar tidak berkedip. Menambahkan `config/site.php` (tautan GitHub, LinkedIn, email), `lang/id/layout.php`, `lang/en/layout.php`, dan `tests/Feature/PublicLayoutTest.php`. `welcome.blade.php` menjadi stub sementara (`TODO F1-40`).
- Alasan / keputusan penting: Aksen tunggal indigo `#4F46E5` / `#8B93FF` dan font Inter + JetBrains Mono disetujui maintainer (menutup butir "warna aksen dan font dipilih" di `docs/03` §4). Nav hanya menampilkan item yang route-nya ada (`Route::has`), jadi saat ini hanya "Beranda"; Portofolio dan Blog muncul otomatis saat route dibuat. Toggle tema dan nav di mobile memakai HTML + JS biasa (bukan Alpine, bukan menu buka-tutup) agar halaman statis tidak memuat Livewire/Alpine dan tidak ada kontrol yang bisa rusak. Dial: ENERGY 2 / RHYTHM 2 / MOTION 1.
- Verifikasi: lint ✔ · analyse ✔ · test ✔ (60 passed, dijalankan di sqlite in-memory karena PHP host tidak punya driver pgsql dan Docker tidak bisa dipakai di sandbox) · build ✔ (butuh akses jaringan untuk mengunduh font dari Bunny)
- Perlu ditinjau manual: Klik-through di browser (toggle tema tiga keadaan, fokus keyboard, lebar 360 px, kedua tema) belum dijalankan oleh agen. Skrip tema inline perlu nonce/hash saat CSP dibuat di `F1-57`. `app.css` publik masih menyertakan CSS Flux (~42 kB gzip) dari starter kit; tinjau dan atasi sebelum `F1-54` (target Lighthouse). Jalankan ulang `composer test` di container `app` (pgsql).
- Tindak lanjut: `F1-02` (middleware locale dan prefix `/id` `/en`; ganti nama route nav) lalu `F1-03` (pemilih bahasa di header).

### 2026-11-24 · P0-10 · Dependabot, `composer audit` di CI, dan persiapan proteksi branch `main`
- Yang berubah: Memperbarui `.github/dependabot.yml` (menambahkan ekosistem `composer` dan `npm` mingguan dengan cooldown 5 hari & grouping minor/patch di samping `github-actions`) dan menambahkan langkah `composer audit --no-interaction` di `.github/workflows/ci.yml`.
- Alasan / keputusan penting: Menutup celah pemantauan dependensi dan audit kerentanan PHP sesuai `docs/02-technical-design.md` §10 dan §11.2. `npm audit` sengaja tidak dijadikan blocker di CI karena kerentanan `npm` dipantau melalui Dependabot alerts.
- Verifikasi: lint ✔ · analyse ✔ · test ✔ (54 passed via container `app`) · audit ✔ (`composer audit`) · build — (tidak ada perubahan aset frontend)
- Perlu ditinjau manual: Aktifkan Dependabot alerts/security updates, secret scanning + push protection, dan proteksi branch `main` melalui GitHub Settings setelah branch `chore/p0-10-repo-security` di-push/merge.
- Tindak lanjut: Push branch `chore/p0-10-repo-security`, buka & merge PR ke `main`, aktifkan proteksi branch & fitur keamanan di GitHub UI, lalu mulai **Fase 1** (`F1-01`).

### 2026-11-24 · P0-08 · Spike highlighter sisi server (PHP murni vs Shiki)
- Yang berubah: Memasang `phiki/phiki` (`^2.2`), menambahkan `CodeHighlighterInterface`, `PhikiCodeHighlighter` (dual-theme `github-light` & `github-dark`, fallback aman HTML-escaped untuk bahasa tak dikenal), command `spike:highlight`, unit test `PhikiCodeHighlighterTest`, serta pembaruan keputusan di `docs/02-technical-design.md` dan `docs/06-progress-log.md`.
- Alasan / keputusan penting: Diputuskan memakai `phiki/phiki` (PHP murni berbasis TextMate grammar) daripada `spatie/shiki-php` + Node.js agar image Docker `app`/`worker` tetap ramping tanpa runtime Node.js, sekaligus mempertahankan akurasi grammar & tema Shiki.
- Verifikasi: lint ✔ · analyse ✔ · test ✔ · build — (hanya backend/spike)
- Perlu ditinjau manual: Pastikan CSS variabel `--phiki-dark-color` ditangani di stylesheet artikel pada `F1-22` agar tema gelap aktif sesuai toggle tema.
- Tindak lanjut: Lanjutkan `P0-10` (keamanan repo GitHub), lalu masuk ke **Fase 1** (`F1-01`).

### 2026-11-24 · P0-07 · Spike layanan terjemahan (Gemini vs DeepL)
- Yang berubah: Menambahkan `TranslatorInterface`, `PlaceholderProtector` (token `[[X0]]` untuk `<pre>`, `<code>`, URL), `GeminiTranslator`, `DeepLTranslator`, command `spike:translate`, konfigurasi `services.gemini` & `services.deepl`, unit test terkait, serta pembaruan keputusan di `docs/01`, `docs/02`, dan `docs/06`.
- Alasan / keputusan penting: Diputuskan memakai Gemini API (`gemini-3.5-flash-lite`) sebagai penyedia utama dan DeepL API Free sebagai backup, dengan batas biaya bulanan ≤ Rp 20.000/bulan.
- Verifikasi: lint ✔ · analyse ✔ · test ✔ · build — (hanya backend/spike)
- Perlu ditinjau manual: Rapikan penyimpanan metrik token (`GeminiTranslator::$lastUsage`) menjadi DTO/logging saat implementasi penuh di `F1-30`.
- Tindak lanjut: Lanjutkan spike `P0-08` (highlighter sisi server) dan `P0-10` (keamanan repo GitHub).

### 2026-11-23 · DOCS · Sinkronisasi dokumen v0.2 dan klarifikasi kontrak teknis
- Yang berubah: Pembaruan `docs/01` sampai `docs/06` ke versi 0.2. Penyelarasan pembagian Fase 1 vs Fase 2 (`FR-12`, `FR-15`), penambahan kontrak JSON `settings` (`§4.3`), standar Media Library collections (`§4.4`), tabel pivot (`article_category`, `category_project`, `article_project`), kolom `projects.technical_decisions`, penanda fase di wireframe, penghapusan filter lintas bahasa di blog, hierarki SSOT di `docs/05`, penyelarasan handle GitHub (`NirlekaArtha`), status domain sudah dibeli (VPS & Cloudflare `P0-09` ditunda hingga sebelum `F1-55`), dan path `lang/` di root sesuai Laravel 13.
- Alasan / keputusan penting: Menghilangkan konflik antar-dokumen dan celah skema sebelum masuk Fase 1 agar agen AI di Antigravity bekerja deterministik tanpa menebak-nebak.
- Verifikasi: lint — · analyse — · test — · build — (hanya dokumentasi)
- Perlu ditinjau manual: Pastikan detail lokal `P0-01` s/d `P0-06` di bawah sesuai dengan state repo aktual.
- Tindak lanjut: Lanjutkan spike `P0-07` (DeepL vs LLM API).

### 2026-11-22 · P0-01 s/d P0-06 · Inisialisasi proyek lokal, CI dasar, dan spike CKEditor
- Yang berubah: Repo dasar, inisialisasi Laravel + stack, container lokal, setup Pint/Larastan/Pest, CI dasar, serta spike integrasi CKEditor di Filament (`P0-06`).
- Alasan / keputusan penting: Plugin `kahusoftware/filament-ckeditor-field` kompatibel dengan Filament 5 dan nyaman digunakan; diadopsi sebagai standar editor di TDD §8.1.
- Verifikasi: lint ✔ · analyse ✔ · test ✔ · build ✔
- Perlu ditinjau manual: Pastikan skrip Composer `lint`, `analyse`, `test` dan konfigurasi `.agents/` sudah ter-commit.
- Tindak lanjut: Kerjakan `P0-07`, `P0-08`, dan `P0-10`.

## Masalah terbuka & penundaan terencana

- **`P0-09` (VPS & Cloudflare):** Domain `hafizhrafid.dev` sudah dibeli; pengadaan VPS dan setup Cloudflare sengaja ditunda hingga menjelang `F1-55` (deployment produksi) untuk menghemat biaya sewa VPS selama pengembangan lokal Fase 1.

## Berikutnya

1. Masuk sub-fase 1B (**Model data dan admin**) dimulai dari `F1-10` (migrasi dan model Fase 1).
2. Pastikan aktivasi UI GitHub `P0-10` (Dependabot alerts, secret scanning, proteksi branch `main`) sudah dicek manual.
