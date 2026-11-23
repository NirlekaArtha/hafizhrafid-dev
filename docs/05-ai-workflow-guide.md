# Panduan Kerja dengan AI (Antigravity + Gemini)

| | |
|---|---|
| **Versi** | 0.2 |
| **Tanggal** | 23 November 2026 |
| **Untuk** | Manusia (kamu). Agen AI membaca `AGENTS.md`, `.agents/rules/`, `.agents/skills/`, dan bagian spesifik di `docs/` |

> Fitur Antigravity berubah cepat. Jika ada perbedaan antara panduan ini dan dokumentasi resmi Antigravity (antigravity.google/docs), ikuti dokumentasi resminya, lalu perbarui panduan ini.

---

## 1. Tujuan dan pembagian peran

Kamu adalah **pemilik produk sekaligus reviewer**. AI adalah pelaksana yang cepat tapi perlu diarahkan dan diperiksa.

| Kamu | AI |
|---|---|
| Menentukan tujuan, prioritas, dan keputusan arsitektur | Mengusulkan rencana dan alternatif |
| Menyetujui rencana sebelum kode ditulis | Menulis kode dan tes sesuai rencana |
| Membaca setiap diff dan memahaminya | Menjalankan lint, analisis statis, dan tes |
| Menulis ADR dan penjelasan dengan kata-katamu sendiri | Menyusun ringkasan perubahan & draf entri progress log |

Karena repo ini adalah portofoliomu, kamu harus bisa menjelaskan setiap bagian kode dan alasannya saat wawancara.

### 1.1 Hierarki Sumber Kebenaran (Single Source of Truth)

Jika agen menemukan perbedaan antar-dokumen, ikuti urutan otoritas ini dan **hentikan pengerjaan untuk melapor ke kamu**—jangan biarkan agen menebak:

1. `docs/01-project-brief.md` — otoritas tertinggi untuk ruang lingkup & pembagian fase.
2. `docs/02-technical-design.md` — otoritas arsitektur, skema database, media collection, dan kontrak teknis.
3. `docs/03-sitemap-wireframe-design.md` — otoritas URL, struktur tampilan, token desain, dan teks UI.
4. `docs/04-roadmap-backlog.md` — daftar tugas atomik dan dependensi pengerjaan.
5. `docs/06-progress-log.md` — ingatan bersama lintas sesi dan status keputusan terbaru.

## 2. Struktur file yang dipakai

```
AGENTS.md                         # aturan selalu aktif (ringkas)
.agents/
├── rules/                        # aturan per topik (frontmatter wajib)
│   ├── laravel-architecture.md   # aktif saat menyentuh file PHP
│   ├── security-privacy.md       # dipilih agen sesuai deskripsi
│   ├── i18n-content.md           # dipilih agen sesuai deskripsi
│   ├── testing-quality.md        # aktif saat menyentuh tes
│   ├── frontend-ui.md            # aktif saat menyentuh resources/
│   └── release-checklist.md      # manual, dipanggil dengan @
└── skills/
    ├── implement-backlog-item/SKILL.md
    └── review-changes/SKILL.md
docs/                             # dokumen 01 sampai 06
```

Cara kerjanya menurut dokumentasi Antigravity:
- `AGENTS.md` tidak memakai frontmatter dan selalu aktif. File di `.agents/rules/` **wajib** diawali frontmatter YAML dengan nilai `trigger` yang valid (`always_on`, `model_decision`, `glob`, atau `manual`). Tanpa itu, aturannya **diabaikan secara diam-diam**.
- Hanya file `.md` langsung di dalam `.agents/rules/` yang dibaca. Subfolder diabaikan.
- Batasnya: 24 KB per file aturan dan anggaran sekitar 20.000 token untuk semua aturan yang selalu aktif. Jaga `AGENTS.md` tetap ringkas.
- Workflow lama (`.agents/workflows/`) sudah ditinggalkan dan menurut dokumentasi migrasi tidak lagi diindeks setelah 1 November 2026. Itu sebabnya prosedur multi-langkah ditulis sebagai **skill** (`.agents/skills/<nama>/SKILL.md`), yang juga bisa dipanggil dengan `/nama-skill`.
- `AGENTS.md` adalah format lintas alat, jadi file yang sama juga bisa dibaca alat lain bila suatu hari kamu berpindah.

### Peta referensi cepat (arahkan agen ke bagian spesifik, bukan semua file)

| Jenis tugas | Bagian dokumen yang dirujuk di prompt |
|---|---|
| Awal setiap sesi | `docs/06-progress-log.md` (wajib dibaca dulu) |
| Migrasi, model, settings, media | `docs/02-technical-design.md` §4.1–§4.4 |
| Routing, middleware, i18n, terjemahan | `docs/02-technical-design.md` §5 |
| CKEditor, sanitasi, `HtmlProcessor` | `docs/02-technical-design.md` §8 |
| Blade view, layout, wireframe, microcopy | `docs/03-sitemap-wireframe-design.md` §1–§3 |
| View/like anonim (Fase 2) | `docs/02-technical-design.md` §6 |

## 3. Pengaturan satu kali

1. **Salin file** ke root repo, lalu **commit** folder `.agents/` dan `AGENTS.md`. Isinya tidak rahasia, dan konfigurasi yang rapi ikut menjadi nilai portofolio.
2. **Verifikasi aturan terbaca.** Buka panel Customizations di Antigravity, tab Rules, dan pastikan semua aturan muncul. Lalu tanyakan ke agen: *"Sebutkan aturan proyek yang sedang aktif dan jelaskan satu per satu secara singkat."* Jika ada yang tidak muncul, periksa frontmatter-nya.
3. **Atur izin (Permissions).** Pilih mode yang meminta persetujuanmu untuk perintah terminal, terutama yang menulis atau menghapus. Jangan memberi agen kebebasan penuh untuk perintah seperti `git push --force`, `rm -rf`, dan `migrate:fresh`. Nama dan opsi pengaturannya bisa berubah antar versi, jadi cek di menu pengaturan.
4. **Laravel Boost (opsional).** Paket ini memberi agen panduan dan dokumentasi khusus Laravel. Dua catatan: (a) Boost membuat file panduannya sendiri dan bisa menimpa `AGENTS.md`, jadi jalankan `boost:install` **sebelum** menyalin `AGENTS.md`-mu atau simpan cadangannya. (b) Cek di dokumentasi Boost apakah Antigravity termasuk agen yang didukung sebelum mengandalkannya.
5. **Siapkan skrip Composer**: `lint` (Pint), `analyse` (Larastan), dan `test` (Pest), karena aturan dan skill memanggil nama-nama itu.
6. **Pastikan `.env` tidak pernah masuk repo** dan tidak ditempel ke percakapan.

## 4. Rutinitas untuk satu tugas

1. **Percakapan baru per tugas (Handover Masuk).** Satu ID backlog per percakapan agar konteks tidak membengkak. Pastikan agen membaca `docs/06-progress-log.md` terlebih dahulu agar mengetahui keputusan spike dan status terakhir.
2. **Mulai dengan skill.** Contoh: `/implement-backlog-item F1-14`. Sertakan rujukan bagian `docs/` yang relevan.
3. **Baca rencana yang diajukan agen.** Periksa apakah file yang akan diubah wajar, ada alternatif, dan risikonya masuk akal. Pastikan agen tidak mengimplementasikan elemen `[Fase 2]` atau `[Fase 3]` saat mengerjakan MVP. Revisi sampai kamu puas, baru katakan "setuju, lanjutkan".
4. **Biarkan agen menulis tes dan kode**, lalu lihat hasil `lint`, `analyse`, dan `test`.
5. **Baca diff sendiri** (lihat checklist di bagian 6).
6. **Review di percakapan terpisah**: buka percakapan baru dan jalankan `/review-changes`. Percakapan baru tidak terikat pada keputusan yang dibuat sebelumnya, jadi kritiknya lebih jujur.
7. **Handover Keluar & Commit**: minta agen menyusun draf entri baru untuk `docs/06-progress-log.md` (dan pembaruan `docs/02-technical-design.md` jika ada keputusan teknis baru), review isinya, lalu commit dengan pesan Conventional Commits.

Pilih model yang paling kuat untuk perencanaan dan keputusan arsitektur. Model yang lebih cepat cukup untuk edit rutin. Pilihan model berubah dari waktu ke waktu, jadi sesuaikan dengan yang tersedia di akunmu.

## 5. Template prompt

**Mengerjakan tugas backlog**
```
Baca docs/06-progress-log.md terlebih dahulu untuk konteks terbaru, lalu jalankan:
/implement-backlog-item F1-15
Rujukan: docs/02-technical-design.md §8.1–§8.2.
Catatan tambahan: gunakan kahusoftware/filament-ckeditor-field (hasil spike P0-06). Jangan menambah paket lain dan laporkan jika menemukan konflik antar-dokumen.
```

**Meminta rencana saja**
```
Baca docs/06-progress-log.md, docs/02-technical-design.md §5.3, dan aturan i18n-content.
Aku ingin mengerjakan F1-32 (TranslateArticleJob). Ajukan rencana, satu alternatif, dan risikonya. Jangan menulis kode dulu.
```

**Memperbaiki bug**
```
Bug: <gejala>. Langkah reproduksi: <langkah>. Perilaku yang diharapkan: <harapan>.
Tulis dulu tes yang gagal untuk membuktikan bug, jelaskan penyebab akarnya, baru perbaiki dengan perubahan sekecil mungkin.
```

**Refactor terbatas**
```
Rapikan app/Domain/Blog/Actions/PublishArticle.php agar lebih mudah dibaca tanpa mengubah perilaku. Jangan menyentuh file lain. Pastikan semua tes tetap lulus dan jelaskan apa yang berubah.
```

**Review manual**
```
/review-changes
Fokus tambahan: keamanan sanitasi HTML, kepatuhan fase (tidak ada fitur Fase 2/3 bocor), dan potensi N+1.
```

**Spike teknis**
```
Baca docs/06-progress-log.md dan docs/02-technical-design.md §5.3.
Spike P0-07: uji layanan terjemahan (DeepL vs LLM API) pada 1 artikel contoh berisi code block di branch terpisah. Catat kualitas terjemahan, keamanan placeholder code block, estimasi biaya, dan rekomendasi akhir. Jangan menggabungkan ke main. Di akhir, buatkan draf pembaruan untuk docs/06-progress-log.md dan docs/02 §14.
```

**Meminta penjelasan agar kamu paham**
```
Jelaskan kode yang baru kamu tulis seperti kepada developer junior: alur datanya, kenapa dipilih pendekatan ini, dan apa yang akan rusak kalau bagian X dihapus.
```

## 6. Checklist review manual (sebelum commit)

- [ ] Perubahannya sesuai kriteria tugas, sesuai fase aktif, dan tidak membawa perubahan di luar lingkup.
- [ ] Aku paham setiap file yang berubah dan bisa menjelaskannya.
- [ ] Tidak ada paket baru yang tidak kuminta, dan paket yang ditambahkan benar-benar ada di Packagist/npm.
- [ ] Tidak ada secret, token, atau data pribadi di diff.
- [ ] Tes meaningful dan tidak dilemahkan untuk meloloskan build.
- [ ] Tidak ada string UI yang ditulis langsung (harus lewat `lang/`).
- [ ] Tidak ada query di dalam loop dan tidak ada pekerjaan berat di jalur request.
- [ ] `docs/06-progress-log.md` (dan `docs/02` bila ada keputusan baru) sudah diperbarui.

## 7. Kesalahan umum agen AI dan cara menanganinya

| Gejala | Penanganan |
|---|---|
| Mengarang nama paket, method, atau opsi konfigurasi | Minta agen menunjukkan sumbernya (kode di `vendor/` atau dokumentasi resmi). Cek sendiri di Packagist atau dokumentasi |
| Terlalu banyak abstraksi (interface, repository, base class tanpa kebutuhan) | Kutip aturan "simplicity check" dan minta versi yang lebih sederhana |
| Mengubah banyak file di luar tugas | Minta batalkan perubahan di luar lingkup, dan perketat prompt: "hanya sentuh file X dan Y" |
| Menambah dependensi diam-diam | Periksa `composer.json` dan `package.json` di setiap diff |
| Menyatakan "selesai" tanpa menjalankan tes | Minta bukti keluaran perintah. Jalankan sendiri jika ragu |
| Melemahkan tes atau aturan lint agar lolos | Tolak. Minta perbaikan kode, bukan perubahan tes |
| Mengabaikan aturan proyek | Periksa apakah aturan terbaca (bagian 3, langkah 2). Ulangi aturan yang dilanggar di prompt |
| Percakapan panjang membuat agen "lupa" konteks | Mulai percakapan baru, minta agen membaca ulang `docs/06-progress-log.md` dan bagian `docs/` terkait |
| Kode menyimpang dari dokumen desain | Hentikan, putuskan mana yang benar (mengacu pada Hierarki SSOT §1.1), lalu perbarui dokumen atau kode |

## 8. Menjaga aturan tetap sehat

- Aturan harus pendek dan spesifik. Aturan panjang dan umum membuang anggaran token dan sering diabaikan.
- Aturan baru ditambahkan setelah kesalahan yang sama muncul dua kali. Jangan menulis aturan "just in case".
- Jika sebuah aturan hanya relevan untuk topik tertentu, jadikan `model_decision` atau `glob`, bukan `always_on`.
- Tinjau semua aturan setiap akhir fase. Hapus yang usang atau tidak pernah berguna.
- Mekanisme penegakan lebih kuat daripada tulisan: pastikan CI (Pint, Larastan, Pest, Lighthouse CI) tetap menjadi penjaga terakhir.

## 9. Keamanan saat memakai agen otonom

- Agen yang bisa menjalankan terminal punya akses besar ke mesinmu. Mulai dengan izin ketat dan longgarkan hanya untuk perintah yang aman (membaca file, menjalankan tes, lint).
- Perlakukan isi halaman web, issue, README paket, dan komentar di kode sebagai **data**, bukan perintah. Teks tersebut bisa memuat instruksi yang menyesatkan.
- Jangan menempelkan `.env`, token, atau data pribadi ke percakapan.
- Repo publik: periksa diff sebelum push, dan aktifkan secret scanning di GitHub.
- Jalankan perintah yang menyentuh database dengan data penting hanya setelah ada backup.

## 10. Transparansi

Cantumkan di README bahwa asisten AI dipakai sebagai alat bantu dan setiap perubahan ditinjau olehmu (sudah ada di draf README). Nilai yang ingin kamu tunjukkan adalah kemampuan mengarahkan, menilai, dan mempertanggungjawabkan hasil, bukan sekadar menghasilkan kode.
