# Nusatim Partner Portal API — Panduan untuk Android Developer

REST API ini adalah versi mobile dari **Portal Partner** (panel web Filament yang partner pakai di browser). Semua data dan aturan bisnis sama persis — API ini cuma jalur akses baru, bukan sistem baru. Kalau ragu soal perilaku suatu fitur, anggap perilakunya identik dengan yang ada di panel web.

Dokumentasi tiap grup endpoint ada di file terpisah di folder ini:

| File | Grup | Isi |
|---|---|---|
| [01-auth.md](01-auth.md) | Auth | Registrasi, login, logout |
| [02-profile.md](02-profile.md) | Profile | Biodata, foto/KTP/NPWP, ganti password |
| [03-dashboard.md](03-dashboard.md) | Dashboard | Ringkasan aktivitas & keuangan |
| [04-leads.md](04-leads.md) | Leads | Lead & Opportunity, reminder, dokumen, timeline |
| [05-pipeline.md](05-pipeline.md) | Pipeline | Papan kanban sales pipeline |
| [06-customers.md](06-customers.md) | Customers | Customer Management |
| [07-projects.md](07-projects.md) | Projects | Project Board (claim/cancel) |
| [08-commissions.md](08-commissions.md) | Commissions | Riwayat komisi (read-only) |
| [09-withdrawals.md](09-withdrawals.md) | Withdrawals | Penarikan komisi |
| [10-marketing-materials.md](10-marketing-materials.md) | Marketing Materials | Marketing Center |
| [11-support-tickets.md](11-support-tickets.md) | Support Tickets | Tiket bantuan |
| [12-notifications.md](12-notifications.md) | Notifications | Notifikasi in-app |

Ada juga dokumentasi interaktif (Swagger UI) di `/api/documentation` pada server yang sama — kalau butuh coba langsung endpoint dari browser atau lihat schema JSON mentahnya, itu tempatnya. File-file `.md` di folder ini fokus ke *cara pakai* dan *alasan di balik perilakunya*, bukan cuma daftar field.

## Base URL

```
https://<domain-server>/api/v1
```

Semua path di dokumentasi ini ditulis relatif terhadap base URL itu. Contoh: `POST /auth/login` artinya `POST https://<domain-server>/api/v1/auth/login`.

## Autentikasi

API ini pakai **token Bearer** (Laravel Sanctum), bukan cookie/session — cocok untuk aplikasi native. Alurnya:

1. `POST /auth/register` (partner baru) atau `POST /auth/login` (partner lama) → server balikin `token` (string panjang).
2. Simpan token itu di penyimpanan aman di device (`EncryptedSharedPreferences` / Android Keystore, **jangan** plain `SharedPreferences`).
3. Setiap request ke endpoint lain (kecuali register/login) **wajib** kirim header:
   ```
   Authorization: Bearer <token>
   ```
4. Token tidak pernah kedaluwarsa otomatis — hanya hilang kalau partner logout (`POST /auth/logout`, cuma revoke token yang lagi dipakai) atau admin mencabutnya manual. Login dari beberapa device sekaligus diizinkan (tiap login menerbitkan token baru, tidak saling menggantikan).
5. Kalau token tidak dikirim, sudah dicabut, atau tidak valid → **401 Unauthorized**, bukan redirect ke halaman login (API ini murni JSON, tidak ada konsep redirect).

## Status approval partner — ini yang paling penting dipahami

Setiap partner (baik yang baru daftar maupun lama) punya kolom `status` dengan 4 kemungkinan nilai:

| Status | Artinya |
|---|---|
| `pending_review` | Baru daftar, menunggu admin approve |
| `approved` | Disetujui, akses penuh |
| `rejected` | Ditolak admin (lihat `rejection_reason`) |
| `suspended` | Ditangguhkan admin |

**Partner yang statusnya BUKAN `approved` tetap bisa login dan mengelola profilnya sendiri**, tapi **tidak bisa akses modul bisnis apa pun** (dashboard, leads, customers, projects, commissions, withdrawals, marketing materials, support tickets, notifications, pipeline). Percobaan akses modul-modul itu selagi belum `approved` akan dapat **403 Forbidden** dengan body:

```json
{
  "message": "Pendaftaran partner Anda masih menunggu approval.",
  "status": "pending_review",
  "rejection_reason": null
}
```

`message` menyesuaikan status (`pending_review`/`rejected`/`suspended`), `rejection_reason` cuma terisi kalau `status` adalah `rejected`.

**Rekomendasi alur di aplikasi**: setelah login/register, cek field `status` dari response. Kalau bukan `approved`, tampilkan layar status (mirip halaman "Menunggu Approval" di panel web) dan jangan panggil endpoint modul bisnis sampai partner login ulang dan statusnya sudah berubah — API tidak mem-push notifikasi real-time saat status berubah, jadi aplikasi perlu re-check (misal saat resume dari background, atau tarik-untuk-refresh).

## Format response

### Sukses — resource tunggal

```json
{
  "data": { ... }
}
```

### Sukses — daftar (list, dipaginasi)

Endpoint index (`GET /leads`, `GET /customers`, dst) pakai pagination bawaan Laravel:

```json
{
  "data": [ { ... }, { ... } ],
  "links": {
    "first": "https://.../api/v1/leads?page=1",
    "last": "https://.../api/v1/leads?page=5",
    "prev": null,
    "next": "https://.../api/v1/leads?page=2"
  },
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 67
  }
}
```

Ukuran halaman bisa diatur lewat query param `?per_page=` (default 15, kecuali disebutkan lain di dokumentasi modul). Untuk pindah halaman, pakai `?page=2` dst, atau langsung `GET` ke URL di `links.next`.

**Pengecualian**: `GET /pipeline` (papan kanban) dan endpoint index Marketing Materials mengembalikan bentuk yang berbeda (dikelompokkan per kategori/status, bukan pagination flat) — lihat dokumentasi modul masing-masing.

### Error validasi (422)

```json
{
  "message": "The amount field is required.",
  "errors": {
    "amount": ["The amount field is required."]
  }
}
```

### Error umum lain

| Kode | Kapan terjadi |
|---|---|
| 401 | Token tidak ada / tidak valid / sudah dicabut |
| 403 | Partner belum `approved` (lihat di atas), atau aturan bisnis lain menolak aksi |
| 404 | Record tidak ada, ATAU ada tapi bukan milik partner yang login — API sengaja **tidak membedakan** dua kasus ini demi keamanan (supaya partner tidak bisa "menebak" ID milik partner lain) |
| 409 | Konflik race-condition (khusus klaim project, lihat [07-projects.md](07-projects.md)) |
| 422 | Validasi gagal, lihat format di atas |
| 500 | Bug di server — laporkan ke tim backend beserta request yang dikirim |

## Upload file

Beberapa endpoint (register, ganti foto/KTP/NPWP, upload dokumen lead, submit withdrawal) menerima file. Kirim sebagai `multipart/form-data`, **bukan** base64 di dalam JSON. Field file dan field teks lain boleh digabung dalam satu multipart request yang sama (lihat contoh di tiap file modul).

## Dokumen privat vs publik

Ada dua jenis "file" di API ini:

- **Dokumen privat** (foto profil, KTP, NPWP, bukti transfer withdrawal, dokumen/proposal lead) — URL-nya selalu mengarah ke endpoint API sendiri (butuh header `Authorization` yang sama untuk diakses, contoh: `GET /profile/documents/ktp`). Jangan simpan/cache URL ini sebagai link publik yang bisa dibuka tanpa token.
- **Marketing Material berbasis file** (brosur, banner, dll) — URL-nya adalah link publik biasa (`asset()` Laravel), bisa dibuka tanpa token, cocok untuk dibagikan lewat WhatsApp dsb.

## Konvensi nilai

- Semua nominal uang (`amount`, `budget`, `project_value`, dst) dikirim sebagai **angka JSON murni** (bukan string berformat "Rp1.000.000") — format tampilan jadi tanggung jawab aplikasi.
- Semua timestamp pakai format ISO 8601 UTC, contoh `"2026-08-01T20:20:36.000000Z"`.
- Field yang boleh kosong selalu muncul di response dengan nilai `null` (bukan dihilangkan dari JSON) — aman untuk deserialisasi ke data class Kotlin dengan tipe nullable.
