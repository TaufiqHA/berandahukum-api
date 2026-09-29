# API Admin (Panel Mobile)

API untuk panel admin di aplikasi Flutter. Semua respons JSON.
Base URL: `{APP_URL}/api/v1/admin`.

## Autentikasi

Login menukar email + kata sandi dengan **token**. Sertakan token pada setiap
permintaan berikutnya lewat header:

```
Authorization: Bearer <token>
```

| Metode | Endpoint | Akses | Keterangan |
|--------|----------|-------|------------|
| POST | `login` | publik | `{email, password}` → `{token, user}` |
| GET | `me` | login | Data user aktif |
| POST | `logout` | login | Hapus token aktif |
| GET | `dashboard` | login | Statistik artikel/komentar/pertanyaan |

`user.level`: `admin` (semua fitur) atau `user` (penulis — hanya artikel).

## Artikel (admin & penulis)

| Metode | Endpoint |
|--------|----------|
| GET | `options` (label + kategori + sub kategori) |
| GET | `articles?q=&status=&label=&page=` |
| GET | `articles/{id}` |
| POST | `articles` (buat) |
| POST | `articles/{id}` (ubah) |
| DELETE | `articles/{id}` |
| POST | `articles/{id}/publish` · `articles/{id}/draft` |
| GET | `articles/{id}/comments` |
| POST | `comments/{id}/publish` · `comments/{id}/unpublish` |
| DELETE | `comments/{id}` |
| GET/POST | `articles/{id}/referensi` |
| DELETE | `referensi/{id}` |

`POST articles` mendukung `multipart/form-data` untuk unggah `image` (gambar)
dan/atau `pdf`. Field teks: `title`, `content`, `label_id`, `category_id`,
`sub_category_id`, `author`, `article_date`, `status` (1 publish / 2 draft),
`headline`, `image_name` (pakai file uploads yang sudah ada), `remove_image`.

## Khusus admin

| Metode | Endpoint |
|--------|----------|
| GET | `comments?status=&q=&page=` · `POST comments/{id}/reply` |
| GET | `questions?status=&page=` · `POST questions/{id}/answer` · `DELETE questions/{id}` |
| CRUD | `categories`, `sub-categories`, `labels`, `users`, `settings` |
| Slider | `GET/POST slider` · `GET slider/articles?q=` · `POST slider/{id}` · `DELETE slider/{id}` · `POST slider/urutan` |
| POST | `settings/sys` → `{show_pertanyaan, show_youtube}` |

### Slider / sorotan

Mengatur artikel yang tampil pada slider beranda (`tbl_pilihan`, `posisi = "top"`):

| Metode | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `slider` | Daftar item slider → `{data:[{id,article_id,title,urutan}]}` |
| GET | `slider/articles?q=` | Cari artikel terbit (maks 30) untuk pemilih artikel |
| POST | `slider` | Tambah: `{article_id, urutan?}` (urutan kosong → otomatis di akhir) |
| POST | `slider/{id}` | Ubah artikel/urutan |
| DELETE | `slider/{id}` | Hapus dari slider |
| POST | `slider/urutan` | Simpan urutan: `{position:[id,id,…]}` |

### Urutan kategori

Mengatur urutan kartu kategori (accordion) di beranda. Nilai disimpan di kolom
`urutan` `tbl_category`; API publik mengurutkan kategori berdasarkan kolom ini:

| Metode | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `categories` | Daftar kategori → `{data:[{id,name,uri,show,urutan,sub_count}]}` |
| POST | `categories` | Tambah: `{name, show, urutan?}` (urutan kosong/0 → otomatis di akhir) |
| POST | `categories/{id}` | Ubah identitas kategori; `urutan` hanya diubah bila dikirim |
| DELETE | `categories/{id}` | Hapus kategori |
| POST | `categories/urutan` | Simpan urutan hasil geser: `{position:[id,id,…]}` |

### Urutan sub-kategori & artikel

Mengatur urutan sub-kategori di dalam sebuah kategori (kolom `urutan`
`tbl_sub_category`) dan urutan artikel di dalam sebuah sub-kategori (kolom
`urutan` `tbl_article_category`). Kedua listing publik mengikuti urutan ini:

| Metode | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `sub-categories` | Daftar sub-kategori → `{data:[{id,category_id,category_name,name,uri,show,urutan}]}` |
| POST | `sub-categories` | Tambah: `{category_id, name, show, urutan?}` (urutan kosong/0 → otomatis di akhir) |
| POST | `sub-categories/{id}` | Ubah; `urutan` hanya diubah bila dikirim |
| DELETE | `sub-categories/{id}` | Hapus sub-kategori |
| POST | `sub-categories/urutan` | Simpan urutan sub-kategori: `{position:[id,id,…]}` |
| GET | `sub-categories/{id}/articles` | Artikel terbit pada sub-kategori → `{data:[{id,title,date,urutan}]}` |
| POST | `sub-categories/{id}/articles/urutan` | Simpan urutan artikel: `{position:[article_id,…]}` |

Artikel baru (atau yang dipindah sub-kategori) otomatis diletakkan di akhir;
menyunting artikel tanpa mengubah sub-kategori tidak mengubah urutannya.

Catatan: `urutan` sub-kategori bersifat global (lintas kategori), tetapi
pengurutan selalu difilter per kategori sehingga urutan relatif tetap benar.

### Tata letak beranda

Mengatur urutan + tampil/sembunyikan section beranda (sama seperti menu
"Tata Letak Beranda" di admin web; data disimpan di `tbl_settings`):

| Metode | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `layout` | Daftar section → `{data:[{key,label,scope,enabled}]}` |
| POST | `layout` | Simpan: `{items:[{key,enabled},…]}` (urutan mengikuti array) |

`scope`: `both` \| `mobile` \| `desktop` — hanya penanda di mana section tampil.

### Iklan mobile

Mengelola iklan beranda aplikasi di `tbl_ads`. Mendukung dua jenis:

- **gambar** (`kind=image`, bawaan): banner gambar + tautan opsional.
- **AdMob** (`kind=admob`): iklan native (in-feed); hanya menyimpan
  Ad Unit ID, tanpa gambar.

| Metode | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `ads` | Daftar iklan → `{data:[{id,type,image,admob_unit,link,position,category_id}]}` |
| POST | `ads` | Tambah: `{kind, position, link?, category_id?, image?}` (multipart) atau `{kind:"admob", admob_unit, position, category_id?}` |
| POST | `ads/{id}` | Ubah (boleh berpindah jenis iklan) |
| POST | `ads/urutan` | Simpan urutan hasil geser: `{position:<penempatan>, ids:[id,…]}` |
| DELETE | `ads/{id}` | Hapus iklan |

`position`: `100` antar kategori, `101` bawah beranda (maks 3), `102` atas
beranda (maks 2), `103` atas artikel (maks 2). `kind=image` mewajibkan berkas
`image` saat menambah; `kind=admob` mewajibkan `admob_unit` (format
`ca-app-pub-…/…`). `category_id` hanya berlaku untuk posisi `100`.

Status HTTP: `401` belum/kadaluarsa token, `403` bukan admin, `422` validasi.

## Contoh

```bash
TOKEN=$(curl -s -X POST http://localhost:8000/api/v1/admin/login \
  -H 'Content-Type: application/json' -H 'Accept: application/json' \
  -d '{"email":"admin@contoh.com","password":"rahasia"}' | jq -r .token)

curl -s http://localhost:8000/api/v1/admin/dashboard \
  -H "Authorization: Bearer $TOKEN" -H 'Accept: application/json'
```

## Token di database

Tabel `tbl_api_token` (`user_id`, `token`, `created_at`, `last_used_at`).
Dibuat lewat migrasi `2026_09_25_000001_create_tbl_api_token.php`.

## API publik (beranda aplikasi)

`GET /api/v1/home` — dipakai beranda aplikasi mobile. Selain `slider`,
`latest`, `headline`, `pilihan_atas/bawah`, `categories`, `labels`, kini juga
mengembalikan komponen yang sama dengan situs mobile:

| Field | Isi |
|-------|-----|
| `ads_top` | Banner iklan atas (`{image, link}` atau `null`) — iklan posisi 2 |
| `ads_middle` | Banner setelah pembatas `#` — iklan posisi 12 |
| `banners` | Tile banner (posisi 9–20; posisi 8/Google Play **dikecualikan** untuk app), tiap item `{image, link}` |
| `ads_bottom` | Iklan sebelum footer (posisi 7) |
| `ads_kategori` / `ads_atas` / `ads_bawah` | Iklan mobile: `{type,image,link,category_id}` (gambar) atau `{type:"admob",admob_unit,category_id}` |
| `categories[].subs` | Sub-kategori untuk kartu accordion |

Catatan: setiap item iklan punya `type` bernilai `image` atau `admob`.
Iklan gambar (`ads_type=0` & `ads_file_type=0`) mengirim `{image, link}`;
iklan AdMob native mengirim `{admob_unit}`. Skrip/embed web tetap diabaikan.
URL gambar relatif (`uploads/i/...`).
`categories` kini hanya berisi kategori `category_show=yes` dengan
sub-kategori `sub_category_show=yes`, diurutkan kolom `urutan`. Sub-kategori
(`categories[].subs`) juga diurutkan kolom `urutan`, sedangkan daftar artikel
pada `GET /api/v1/subcategories/{uri}` diurutkan kolom `urutan`
`tbl_article_category` lalu tanggal artikel.

