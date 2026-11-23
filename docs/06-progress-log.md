# Progress Log

Catatan keadaan proyek untuk manusia **dan** agen AI. Agen tidak mengingat percakapan sebelumnya,
jadi file ini adalah ingatan bersama. Perbarui di akhir setiap tugas.

## Status saat ini

| | |
|---|---|
| **Fase aktif** | Fase 0: persiapan (sedang mengerjakan spike `P0-07`) |
| **Rilis terakhir** | (belum ada) |
| **Terakhir diperbarui** | 2026-11-23 |

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

## Status spike teknis

| ID | Pertanyaan | Status | Hasil |
|---|---|---|---|
| P0-06 | Plugin CKEditor komunitas kompatibel dengan versi Filament yang dipakai? | **Selesai** | Terpilih `kahusoftware/filament-ckeditor-field` (nyaman & kompatibel dengan Filament 5). Wajib `alt` gambar ditegakkan lewat validasi server saat publikasi (`F1-16`). |
| P0-07 | DeepL atau LLM API untuk terjemahan? | **Sedang dikerjakan** | _menunggu hasil pengujian pada artikel contoh berisi code block_ |
| P0-08 | Highlighter sisi server: PHP murni atau Shiki via Node? | _belum dikerjakan_ | _belum dikerjakan_ |

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

1. Selesaikan `P0-07` (spike terjemahan: DeepL vs LLM API) dan catat hasilnya di file ini serta `docs/02-technical-design.md` §14.
2. Selesaikan `P0-08` (spike highlighter: PHP murni vs Shiki via Node) dan catat hasilnya di file ini serta `docs/02-technical-design.md` §14.
3. Selesaikan `P0-10` (Dependabot, secret scanning, proteksi branch `main`).
4. Mulai **Fase 1 (MVP)** dari `F1-01` (layout dasar, tema terang/gelap, token desain).
