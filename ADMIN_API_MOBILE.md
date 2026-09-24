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
| POST | `settings/sys` → `{show_pertanyaan, show_youtube}` |

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
