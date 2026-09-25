# DESIGN.md — Beranda Hukum (frontend publik)

## Arah
**Modern Magazine.** Tata letak majalah multi-kolom: lead story besar, band
section bernomor, grid asimetris, tipografi besar (serif editorial), label/meta
sans huruf kapital, dan aksen merah yang hemat.

Catatan referensi: gaya **magazine** adalah pilihan Anda (bukan reproduksi situs
tertentu). Prinsip editorial dipakai sebagai arahan, bukan menyalin aset/font
berlisensi. Font: stack sistem (`Georgia` serif + sans sistem).

## Struktur tata letak
- **Masthead** sticky + **sectionbar** (rubrik/kategori).
- **Lead**: grid `2fr 1fr` (sorotan utama + rail pilihan editor).
- **Band** (`.band`, varian `.band--tint` / `.band--dark`) sebagai sekat section.
- **Section head** bernomor (`.section__no` merah + judul + garis).
- **Grid**: `.grid--2/3/4`, `.grid--asym`; kartu `.story` (media 3:2).
- **Ranked** (`.ranked`) untuk "Paling Banyak Dibaca/Dikomentari" bernomor merah.
- **Rail** (`.side` + `.widget`) di band Terbaru.

## Prinsip
- Satu neutral ramp (`--n-*`) + satu aksen merah (`--brand`).
- Hirarki lewat tipografi, ukuran, dan garis — bukan bayangan/kartu.
- Sudut tajam (`--r-*` ≈ 0), elevasi hanya untuk popup.
- Body artikel serif dengan measure ~66ch; UI/meta sans huruf kapital kecil.

## Token (di `public/css/site.css`)
- Warna: `--n-0..900`, `--brand(-strong/-soft)` (#b31217), status success/danger/warning.
- Ruang: `--s1..s8`; Radius: 0 (tajam); Elevasi: `--shadow-md` hanya popup.
- Tipografi: `--font-serif` (Georgia) untuk judul & isi, `--font-sans` untuk UI/meta.

## Komponen
Masthead (border merah atas + nav kapital), kicker label kategori,
hero lead story, kartu artikel editorial (`--card`), daftar bernomor `.mostread`,
widget sidebar, tombol/field kotak, alert, komentar, pagination, popup, empty state.

## Aksesibilitas
- `lang="id"`, skip link, landmark, satu `h1`/halaman, fokus `:focus-visible` merah.
- Label form eksplisit; state tidak hanya warna; tombol ikon bernama aksesibel.
- `prefers-reduced-motion` mematikan transisi; gambar jatuh ke placeholder.
- Kontras: seluruh pasangan utama **PASS WCAG AA** (terukur, termasuk merah brand 6.96:1).

## Panel admin (`public/css/admin.css`)
Bahasa desain yang sama, disesuaikan untuk UI fungsional:
- Kerangka **sidebar** (264px) + topbar + konten; sidebar mengecil jadi drawer di <900px.
- Grup navigasi berlabel kapital: Utama, Konten, Interaksi, Data Master, Setting.
- **Tata Letak Beranda** (`admin/layout`): seret untuk mengurutkan + centang untuk
  menampilkan/menyembunyikan section beranda; disimpan di `tbl_settings`
  (`tipe="layout"`, nama `home`) sebagai JSON dan dibaca `App\Services\HomeLayout`.
- Tabel rapat (header kapital bergaris), form label kapital, tombol/merah brand,
  kartu ringkasan `.stat` bernilai serif.
- Override Bootstrap 4 (dipakai admin asset) via `admin.css`; token dari `site.css`.
- Halaman login/forget memakai kartu terpusat dengan garis merah atas.

## Status verifikasi
- Rute publik & admin + aset: **200**, log Laravel bersih.
- Kontras: **measured PASS** (publik & admin).
- Belum diverifikasi (butuh browser): render visual, zoom 200%, keyboard manual,
  wrapping konten panjang. → **Not verified**.

## Tata letak mobile (≤720px)
Struktur halaman beranda mengikuti urutan referensi berandahukum.com (gaya
visual tetap Modern Magazine):
- **Masthead**: lockup logo penuh; tanpa tombol hamburger dan tanpa sectionbar.
- **Urutan beranda mobile** (`.home` jadi flex kolom, blok diberi `order`):
  1. **Banner atas** — iklan posisi 2 (di desktop tampil sesudah carousel).
  2. **Carousel** (`partials.slider_atas`) dengan bar caption menempel di bawah.
  3. **Pembatas `#`** + iklan (`.home__hash`).
  4. **Tile banner** — posisi 8–20, satu kolom (`.home__tiles`).
  5. **Kartu kategori** (`.home__cats`) dari `partials.category_cards`.
  6. **Iklan** sebelum footer (`.home__foot-ad`).
  Section majalah lain ditandai `.home-extra` dan disembunyikan di mobile.
  `.home > * { min-width: 0 }` + `overflow-x: hidden` mencegah `.wrap` memuai
  mengikuti lebar trek slider (yang membuat gambar carousel terpotong).
- **Kategori**: daftar/grid desktop diganti kartu — header bergaris merah atas +
  baris subkategori berchevron (`▾`). Klik baris sub-kategori membuka **dropdown**
  berisi daftar artikelnya (diambil dari `GET /api/v1/subcategories/{uri}`).
  Urutan kategori memakai kolom `urutan`.
- **Footer**: logo terpusat, lalu kolom Informasi & Follow Us berdampingan.
Implementasi: `resources/views/partials/category_cards.blade.php`,
`resources/views/front/home.blade.php`, dan blok `@media (max-width: 720px)`
pada `public/css/site.css`.

