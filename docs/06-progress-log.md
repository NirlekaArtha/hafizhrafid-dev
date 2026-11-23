# Progress Log

Catatan keadaan proyek untuk manusia **dan** agen AI. Agen tidak mengingat percakapan sebelumnya,
jadi file ini adalah ingatan bersama. Perbarui di akhir setiap tugas.

## Status saat ini

| | |
|---|---|
| **Fase aktif** | Fase 0: persiapan |
| **Rilis terakhir** | (belum ada) |
| **Terakhir diperbarui** | 2026-11-20 |

## Keputusan yang sudah ditetapkan

| Tanggal | Keputusan | Sumber |
|---|---|---|
| 2026-11-20 | Nama brand: Hafizh Rafid. Domain `hafizhrafid.dev`, GitHub `hafizhrafid`, repo `hafizhrafid-dev` | docs/01 §11 |
| 2026-11-20 | Modular monolith Laravel + Livewire + Filament + PostgreSQL | docs/02 §2 |
| 2026-11-20 | Lisensi kode GPL-3.0-or-later, konten dilindungi terpisah | LICENSE-CONTENT.md |
| 2026-11-20 | View dan like anonim memakai hash harian dan cookie UUID, tanpa fingerprinting | docs/02 §6 |
| 2026-11-20 | Terjemahan artikel selalu berupa draf yang wajib ditinjau | docs/02 §5.3 |

## Spike yang belum diputuskan

| ID | Pertanyaan | Hasil |
|---|---|---|
| P0-06 | Plugin CKEditor komunitas kompatibel dengan versi Filament yang dipakai? | kahusoftware/filament-ckeditor-field <br>sangat kompatible dan nyaman digunakan|
| P0-07 | DeepL atau LLM API untuk terjemahan? | _belum dikerjakan_ |
| P0-08 | Highlighter sisi server: PHP murni atau Shiki via Node? | _belum dikerjakan_ |

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

_(belum ada entri)_

## Masalah terbuka

- (kosong)

## Berikutnya

1. P0-01 sampai P0-05: repo, Laravel, Docker, Pint/Larastan/Pest, CI dasar.
2. P0-06 sampai P0-08: tiga spike teknis.
