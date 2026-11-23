# Sitemap, Wireframe, dan Panduan Desain

| | |
|---|---|
| **Versi** | 0.2 |
| **Tanggal** | 23 November 2026 |
| **Mengacu ke** | `01-project-brief.md` (v0.2), `02-technical-design.md` (v0.2) |

> Wireframe di bawah adalah kerangka struktur dan hierarki, bukan desain visual akhir. Nilai warna, font, dan teks adalah **usulan awal** yang boleh kamu ganti.
> **Catatan Fase untuk Implementasi:** Elemen yang ditandai `[Fase 2]` atau `[Fase 3]` **jangan** dirender atau dibuatkan placeholder kosong pada saat implementasi MVP (Fase 1).

---

## 1. Peta halaman dan URL

Semua halaman publik memakai prefix bahasa `/{locale}` (`id` atau `en`). Segmen URL dibuat sama di kedua bahasa (mis. `/portfolio` dan `/blog`) agar routing sederhana. Slug artikel, kategori, dan proyek dibuat per bahasa.

| Halaman | URL | Fase | Catatan |
|---|---|---|---|
| Root | `/` | 1 | Redirect ke `/{locale}` untuk pengunjung biasa, crawler tidak dipaksa redirect |
| Beranda | `/{locale}` | 1 | Hero, CTA, cuplikan proyek dan artikel |
| Portofolio | `/{locale}/portfolio` | 1 | Profil, skill, pengalaman, proyek, unduh CV, kontak |
| Detail proyek | `/{locale}/projects/{slug}` | 1 | Format studi kasus |
| Daftar blog | `/{locale}/blog` | 1 | Hanya artikel sesuai `{locale}`, pagination, filter kategori |
| Kategori blog | `/{locale}/blog/category/{slug}` | 1 | |
| Detail artikel | `/{locale}/blog/{slug}` | 1 | Termasuk daftar isi (TOC), waktu baca, artikel terkait |
| Kebijakan privasi | `/{locale}/privacy` | 1 | |
| Unduh CV | `/cv/{locale}` | 1 | Mengirim PDF CV sesuai bahasa |
| 404 | (otomatis) | 1 | Dengan tautan balik ke beranda dan blog |
| Sitemap | `/sitemap.xml` | 1 | |
| Robots | `/robots.txt` | 1 | |
| RSS | `/{locale}/feed.xml` | 2 | |
| Login | `/{locale}/login` | 3 | Login sosial |
| Artikel tersimpan | `/{locale}/bookmarks` | 3 | Hanya pengguna login |
| Pencarian | `/{locale}/blog?q=...` | 3 | |
| Admin | `/admin` | 1 | `noindex`, hanya admin |

### Struktur navigasi

```
Header:  [Logo/Hafizh Rafid]   Beranda · Portofolio · Blog        [ID|EN]  [☾/☀]
Footer:  GitHub · LinkedIn · Email · RSS [Fase 2]      Kebijakan Privasi      © Tahun Hafizh Rafid
```

- Navigasi tetap satu baris di desktop, berubah menjadi menu ringkas di mobile.
- Tidak ada tombol Login di header pada Fase 1 dan 2. Pada Fase 3, tombol Login dibuat kecil dan tidak menonjol.
- Tautan `RSS` di footer baru ditampilkan mulai Fase 2 (`F2-08`).

---

## 2. Wireframe

Notasi: `[ ]` tombol, `( )` gambar, `▒` area gambar/blok, `≡` teks.

### 2.1 Beranda

```
┌──────────────────────────────────────────────────────────┐
│ Logo        Beranda  Portofolio  Blog        [ID|EN] [☾] │
├──────────────────────────────────────────────────────────┤
│                                                          │
│   Halo, saya Hafizh                         ┌──────────┐  │
│   Software Engineer · Web · AI             │  (Foto)  │  │
│   Saya membangun aplikasi web dan          │          │  │
│   mengeksplorasi AI, lalu menuliskan       └──────────┘  │
│   hasilnya di sini.                                      │
│                                                          │
│   [ Lihat Portofolio ]   [ Baca Blog ]    ● Open to work │
├──────────────────────────────────────────────────────────┤
│   Proyek Pilihan                          Lihat semua →  │
│   ┌────────────┐ ┌────────────┐ ┌────────────┐           │
│   │ ▒▒▒▒▒▒▒▒▒▒ │ │ ▒▒▒▒▒▒▒▒▒▒ │ │ ▒▒▒▒▒▒▒▒▒▒ │           │
│   │ Judul      │ │ Judul      │ │ Judul      │           │
│   │ Ringkasan  │ │ Ringkasan  │ │ Ringkasan  │           │
│   │ [Laravel]  │ │ [Python]   │ │ [IoT]      │           │
│   └────────────┘ └────────────┘ └────────────┘           │
├──────────────────────────────────────────────────────────┤
│   Tulisan Terbaru                         Semua artikel →│
│   ≡ Judul artikel · [AI] · 5 mnt baca · 12 Nov 2026      │
│   ≡ Judul artikel · [DevOps] · 8 mnt baca · 2 Nov 2026   │
│   ≡ Judul artikel · [Web] · 6 mnt baca · 20 Okt 2026     │
├──────────────────────────────────────────────────────────┤
│   Tumpukan teknologi: Laravel · PHP · Python · Docker …  │
├──────────────────────────────────────────────────────────┤
│   GitHub · LinkedIn · Email · RSS [F2]       Privasi     │
└──────────────────────────────────────────────────────────┘
```

Catatan: hero harus menjawab "web apa ini dan milik siapa" dalam 5 detik. Dua CTA saja. Elemen LCP (foto atau judul) dimuat tanpa lazy loading.

### 2.2 Portofolio

```
┌──────────────────────────────────────────────────────────┐
│ Header                                                   │
├──────────────────────────────────────────────────────────┤
│  (Foto)   Tentang Saya                                   │
│           ≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡          │
│           [ Unduh CV ▾ ]  → Bahasa Indonesia / English   │
│           GitHub · LinkedIn · Email                      │
├──────────────────────────────────────────────────────────┤
│  Keahlian (dari settings: profile.skills)                │
│  Backend: Laravel, PHP …    Web: Livewire, JS …          │
│  AI: …     DevOps: Docker, CI/CD …     Tools: …          │
├──────────────────────────────────────────────────────────┤
│  Proyek                         [Semua][Web][AI][IoT]    │
│  ┌──────────────┐ ┌──────────────┐                       │
│  │ ▒▒▒▒▒▒▒▒▒▒▒▒ │ │ ▒▒▒▒▒▒▒▒▒▒▒▒ │                       │
│  │ Judul        │ │ Judul        │                       │
│  │ 1 kalimat    │ │ 1 kalimat    │                       │
│  │ Demo · Repo  │ │ Demo · Repo  │                       │
│  └──────────────┘ └──────────────┘                       │
├──────────────────────────────────────────────────────────┤
│  Pendidikan & Pengalaman (dari settings: profile.timeline)│
│   2023–kini  S1 Teknik Informatika, [Kampus]             │
│   2026       [Magang/Lomba/Sertifikasi]                  │
├──────────────────────────────────────────────────────────┤
│  Hubungi Saya                                            │
│  Nama [____]  Email [____]  Pesan [________]  [ Kirim ]  │
├──────────────────────────────────────────────────────────┤
│ Footer                                                   │
└──────────────────────────────────────────────────────────┘
```

### 2.3 Detail proyek (studi kasus)

```
┌──────────────────────────────────────────────────────────┐
│ ← Kembali ke Portofolio                                  │
│ Judul Proyek                                             │
│ Ringkasan satu kalimat                                   │
│ [Laravel] [PostgreSQL] [Docker]      [Demo ↗] [Repo ↗]   │
│ ┌──────────────────────────────────────────────────────┐ │
│ │ ▒▒▒▒▒▒▒▒▒▒▒▒▒▒ tangkapan layar utama ▒▒▒▒▒▒▒▒▒▒▒▒▒▒ │ │
│ └──────────────────────────────────────────────────────┘ │
│ Masalah     ≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡                         │
│ Solusi      ≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡                         │
│ Peran saya  ≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡                         │
│ Keputusan teknis & tantangan  ≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡           │
│ Hasil       ≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡                         │
│ Galeri (opsional)  ▒▒ ▒▒ ▒▒                              │
│ Artikel terkait →  ≡ Judul · ≡ Judul                     │
└──────────────────────────────────────────────────────────┘
```

Catatan data (TDD §4.1–§4.2): "Keputusan teknis & tantangan" bersumber dari kolom `projects.technical_decisions`, sedangkan "Artikel terkait" bersumber dari relasi pivot `article_project` (hanya menampilkan artikel yang sudah `published` pada `{locale}` aktif).

### 2.4 Daftar blog

```
┌──────────────────────────────────────────────────────────┐
│ Header                                                   │
├──────────────────────────────────────────────────────────┤
│ Blog                                  [Fase 3: 🔍 cari]  │
│ Catatan eksperimen dan hal yang saya pelajari            │
│ [Semua] [AI] [Web] [IoT] [DevOps] [Lainnya]  ← kategori  │
├────────────────────────────────────────┬─────────────────┤
│ ┌────┐ Judul artikel                   │ Kategori        │
│ │▒▒▒▒│ Ringkasan dua baris…            │  AI (4)         │
│ └────┘ [AI] · 5 mnt · 12 Nov 2026      │  Web (6)        │
│        [Fase 2: 👁 120 · ♥ 14]         │  DevOps (3)     │
│ ──────────────────────────────────     │                 │
│ ┌────┐ Judul artikel                   │ [Fase 2]        │
│ │▒▒▒▒│ Ringkasan dua baris…            │  RSS Feed       │
│ └────┘ [DevOps] · 8 mnt · 2 Nov 2026   │                 │
│        [Fase 2: 👁 80 · ♥ 9]           │                 │
│ ──────────────────────────────────     │                 │
│            ‹ 1 2 3 ›                   │                 │
└────────────────────────────────────────┴─────────────────┘
```

Catatan:
- Daftar blog pada `/{locale}/blog` **hanya menampilkan artikel sesuai bahasa URL aktif** (tidak ada filter silang bahasa di sidebar; ganti bahasa lewat `[ID|EN]` di header, sesuai ADR-10).
- Angka view (`👁`), like (`♥`), dan tautan `RSS Feed` baru ditampilkan mulai **Fase 2**.
- Di mobile, sidebar turun ke bawah daftar atau kategori berubah menjadi baris yang bisa di-scroll horizontal.

### 2.5 Detail artikel

```
┌──────────────────────────────────────────────────────────┐
│ Header                                                   │
├──────────────────────────────────────────────────────────┤
│ Beranda › Blog › AI                                      │
│ Judul Artikel (H1)                                       │
│ Hafizh Rafid · 12 Nov 2026 · 6 mnt baca · [ID | EN]      │
│ [AI] [Eksperimen]                                        │
│ ┌──────────────────────────────────────────────────────┐ │
│ │ ▒▒▒▒▒▒▒▒▒▒▒▒▒▒ gambar sampul ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒ │ │
│ └──────────────────────────────────────────────────────┘ │
│ ┌─────────────┐   Isi artikel                            │
│ │ Daftar isi  │   H2 …                                   │
│ │ · Bagian 1  │   ≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡≡                    │
│ │ · Bagian 2  │   ┌ code ──────────────┐                 │
│ │ (sticky)    │   │ $ php artisan …    │                 │
│ └─────────────┘   └────────────────────┘                 │
│ ───────────────────────────────────────────────────────  │
│ [Fase 2: ♥ 14 Suka · 👁 120]   [Bagikan]  [Fase 3: Simpan]│
│ ───────────────────────────────────────────────────────  │
│ Banner inline [Fase 3, sekali per sesi]:                 │
│ "Suka tulisan ini? Login untuk menyimpan dan kembali     │
│  membacanya nanti."        [Login]  [Tidak, terima kasih]│
│ ───────────────────────────────────────────────────────  │
│ Artikel terkait:  ≡ Judul   ≡ Judul   ≡ Judul            │
│ Label (jika terjemahan mesin): "Diterjemahkan dengan     │
│ bantuan AI, ditinjau penulis."                           │
└──────────────────────────────────────────────────────────┘
```

Catatan:
- Daftar isi (TOC), estimasi waktu baca, breadcrumb, dan artikel terkait (per kategori) termasuk **Fase 1 (MVP)** (`FR-15`).
- Pada layar kecil, daftar isi menjadi blok yang bisa dibuka-tutup di atas konten, bukan sidebar.
- Pada Fase 1, baris aksi di bawah artikel hanya menampilkan `[Bagikan]`. Hitungan view & tombol like ditambahkan di Fase 2 (`F2-05`); tombol Simpan & banner login di Fase 3 (`F3-03`, `F3-04`).

### 2.6 Halaman 404

```
┌──────────────────────────────────────────────────────────┐
│ Header                                                   │
│            404 · Halaman tidak ditemukan                 │
│    Mungkin tautannya sudah berubah. Coba salah satu:     │
│    [ Beranda ]  [ Portofolio ]  [ Blog ]                 │
└──────────────────────────────────────────────────────────┘
```

---

## 3. Panduan desain

### 3.1 Prinsip

1. **Jelas sebelum cantik.** Pengunjung harus paham dalam 5 detik siapa kamu dan apa yang kamu kerjakan.
2. **Cepat dan ringan.** Tidak ada animasi berat atau pustaka besar demi estetika.
3. **Konten sebagai pusat.** Tipografi dan ruang kosong lebih penting daripada dekorasi.
4. **Konsisten.** Sedikit komponen yang dipakai berulang.
5. **Dapat diakses.** Kontras, fokus keyboard, dan ukuran sentuh diperhatikan sejak awal.

### 3.2 Warna (usulan awal, dapat diganti)

Gunakan CSS variable agar tema terang dan gelap mudah diatur.

| Token | Terang | Gelap | Pemakaian |
|---|---|---|---|
| `--bg` | `#FFFFFF` | `#0B0F14` | Latar halaman |
| `--surface` | `#F5F7FA` | `#121821` | Kartu, blok kode |
| `--border` | `#E3E8EF` | `#233044` | Garis dan pembatas |
| `--text` | `#0F172A` | `#E6EDF5` | Teks utama |
| `--muted` | `#475569` | `#9AA9BC` | Teks sekunder |
| `--primary` | `#4F46E5` | `#8B93FF` | Tombol, tautan, aksen |
| `--primary-contrast` | `#FFFFFF` | `#0B0F14` | Teks di atas warna primer |
| `--success` | `#15803D` | `#4ADE80` | Penanda "Open to work" |
| `--danger` | `#B91C1C` | `#F87171` | Galat |

Aturan: satu warna aksen saja. Periksa kontras teks minimal 4,5:1 untuk teks biasa (WCAG AA).

### 3.3 Tipografi (usulan)

| Peran | Font | Catatan |
|---|---|---|
| Judul dan UI | Inter (atau sans-serif modern sejenis) | Self-host, subset, `font-display: swap` |
| Isi artikel | Inter, atau serif seperti Source Serif jika ingin nuansa editorial | Pilih satu |
| Kode | JetBrains Mono | |

Skala (rem): 0.875 · 1 · 1.125 · 1.25 · 1.5 · 2 · 2.5 · 3. Lebar baca artikel sekitar 65 sampai 75 karakter (`max-w-prose`). Tinggi baris isi artikel 1.7.

### 3.4 Spasi, layout, dan breakpoint

- Skala spasi kelipatan 4 px (Tailwind default).
- Lebar kontainer: konten umum maksimal 1100 px, artikel maksimal 720 px.
- Breakpoint: mobile (< 640), tablet (640 sampai 1024), desktop (≥ 1024). Desain **mobile-first**.
- Sudut membulat konsisten (mis. 8 sampai 12 px), bayangan tipis atau tanpa bayangan.

### 3.5 Komponen inti

| Komponen | Aturan |
|---|---|
| Tombol | Varian: primer, sekunder (outline), tautan teks. Tinggi sentuh ≥ 44 px |
| Kartu proyek | Gambar 16:9, judul, satu kalimat, chip teknologi, tautan Demo dan Repo |
| Daftar artikel | Judul, ringkasan 2 baris, kategori, waktu baca, tanggal *(hitungan view dan like ditambahkan di Fase 2)* |
| Chip/Badge | Kategori dan teknologi, ukuran kecil, tidak lebih dari 3 per kartu |
| Pemilih bahasa | Toggle `ID \| EN` di header, menampilkan bahasa aktif dan menjaga posisi halaman jika terjemahan tersedia |
| Tombol tema | Terang, gelap, mengikuti sistem. Ingat pilihan, hindari kedipan saat muat |
| Blok kode | Latar `--surface`, nama bahasa, tombol salin, scroll horizontal |
| Banner login | *(Fase 3)* Inline, bisa ditutup, tidak menutupi konten, tidak muncul lagi setelah ditutup |
| Formulir | Label jelas, pesan galat dalam bahasa aktif, honeypot tersembunyi |

### 3.6 Gambar

- Foto profil: persegi atau bulat, dioptimalkan, `alt` berisi nama.
- Gambar sampul artikel: rasio 16:9 (mis. 1200×675), juga dipakai sebagai gambar Open Graph (1200×630 untuk OG khusus di Fase 2).
- Tangkapan layar proyek: dengan bingkai sederhana, diberi `alt` yang menjelaskan isinya.
- Hindari ilustrasi stok generik. Tangkapan layar nyata dan diagram buatan sendiri lebih meyakinkan.

### 3.7 Gerak (motion)

- Gunakan transisi singkat (150 sampai 200 ms) untuk hover dan fokus.
- Hormati `prefers-reduced-motion`.
- Tanpa animasi scroll berat, tanpa video latar.

### 3.8 Aksesibilitas

- Tautan "Lewati ke konten" di awal halaman.
- Fokus keyboard terlihat jelas pada semua elemen interaktif.
- Gambar bermakna punya `alt`, gambar dekoratif memakai `alt=""`.
- Tombol ikon punya `aria-label`. Status like dan simpan diumumkan lewat `aria-pressed` atau live region.
- Jangan memakai warna saja untuk menyampaikan informasi.

### 3.9 Nada dan teks (microcopy)

Gaya: lugas, ramah, profesional, tanpa klise berlebihan. Contoh teks utama:

| Elemen | Indonesia | English |
|---|---|---|
| Hero | "Halo, saya Hafizh. Saya membangun aplikasi web dan mengeksplorasi AI, lalu menuliskan hasilnya di sini." | "Hi, I'm Hafizh. I build web apps and explore AI, then write about what I learn here." |
| CTA 1 | "Lihat Portofolio" | "View Portfolio" |
| CTA 2 | "Baca Blog" | "Read the Blog" |
| Unduh CV | "Unduh CV" | "Download CV" |
| Banner login (Fase 3) | "Suka tulisan ini? Login untuk menyimpannya dan membaca lagi nanti." | "Enjoyed this post? Log in to save it and read it later." |
| Label terjemahan | "Diterjemahkan dengan bantuan AI, ditinjau penulis." | "Translated with AI assistance, reviewed by the author." |

### 3.10 Pola SEO per halaman

| Halaman | `<title>` | Meta description | `<h1>` |
|---|---|---|---|
| Beranda | `Hafizh Rafid — Software Engineer (Web & AI)` | 150 sampai 160 karakter, ringkas profil | Sapaan di hero |
| Portofolio | `Portofolio — Hafizh Rafid` | Ringkas keahlian dan proyek | "Portofolio" atau "Tentang Saya" |
| Proyek | `[Judul Proyek] — Studi Kasus \| Hafizh Rafid` | Ringkasan masalah dan hasil | Judul proyek |
| Blog | `Blog — Hafizh Rafid` | Deskripsi tema blog | "Blog" |
| Artikel | `[Judul Artikel] \| Hafizh Rafid` | Dari `meta_description` artikel | Judul artikel |

---

## 4. Checklist pra-implementasi UI dan pra-rilis

### Syarat mulai implementasi UI lokal (Fase 1A–1E)
- [x] Nama brand ditetapkan: Hafizh Rafid.
- [ ] Warna aksen dan font dipilih.
- [ ] Wireframe di atas dikonfirmasi atau direvisi.
- [ ] Teks hero versi Indonesia dan Inggris selesai (bisa dikerjakan paralel).

### Syarat pra-rilis produksi (sebelum `F1-55` / Definition of Done MVP)
- [ ] Domain (`hafizhrafid.dev`) dan handle sudah dipesan (`P0-09`, tidak menghalangi development lokal).
- [ ] Foto profil siap.
- [ ] Minimal 3 proyek dan gambar tangkapan layarnya siap.
- [ ] CV versi Indonesia dan Inggris sudah dalam bentuk PDF.
