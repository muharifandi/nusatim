# Notifications (Notification Center)

Notifikasi in-app — dikirim otomatis server saat ada event yang relevan untuk partner (status lead berubah, klaim project disetujui/ditolak, komisi baru muncul, withdrawal disetujui, dsb). Sama dengan ikon lonceng di panel web partner.

## Cara kerja

- **API ini murni cara membaca notifikasi yang sudah dikirim server** — tidak ada endpoint untuk *mengirim* notifikasi dari aplikasi.
- **Tidak ada push notification (FCM/APNs) built-in di API ini** — modul ini cuma menyimpan/membaca notifikasi di database, bukan sistem push real-time. Kalau butuh notifikasi push, itu perlu integrasi terpisah (di luar cakupan API ini) — anggap endpoint di sini sebagai "kotak masuk" yang di-*poll* aplikasi, bukan pengganti push notification.
- Setiap notifikasi punya `id` berupa **string UUID** (bukan integer seperti resource lain) — perhatikan ini saat menyimpan/membandingkan ID di sisi aplikasi.
- Rekomendasi pola pakai: panggil `GET /notifications/unread-count` secara berkala (misal tiap kali app dibuka/resume, atau lewat pull-to-refresh) untuk menampilkan badge angka di ikon lonceng, baru panggil `GET /notifications` saat partner benar-benar membuka layar notifikasi.

---

## `GET /notifications`

Daftar notifikasi, terbaru dulu, dipaginasi (default 20 per halaman — beda dari modul lain yang defaultnya 15).

**Response — `200 OK`**

```json
{
  "data": [
    {
      "id": "9c858901-8a57-4791-81fe-4c455b099d2e",
      "title": "Lead Budi Santoso: status jadi won",
      "body": null,
      "read_at": null,
      "created_at": "2026-07-22T10:15:00.000000Z"
    },
    {
      "id": "7b6a5e40-1234-4abc-9def-0123456789ab",
      "title": "Klaim project \"Website PT Maju\" disetujui",
      "body": null,
      "read_at": "2026-07-21T09:00:00.000000Z",
      "created_at": "2026-07-20T14:30:00.000000Z"
    }
  ]
}
```

`read_at`: `null` berarti belum dibaca. `body` sering `null` — sebagian besar notifikasi di sistem ini cuma punya `title`, tidak semua event mengisi body tambahan; jangan asumsikan `body` selalu ada, sembunyikan elemen UI-nya kalau `null`.

---

## `GET /notifications/unread-count`

Untuk badge angka di ikon lonceng.

**Response — `200 OK`**

```json
{ "unread_count": 3 }
```

---

## `PATCH /notifications/{notification}/read`

Tandai satu notifikasi sudah dibaca (misal saat partner tap notifikasi tertentu di daftar).

**Request**: tanpa body. `{notification}` adalah `id` (UUID) notifikasi.

**Response sukses — `200 OK`**: data notifikasi, `read_at` sudah terisi timestamp sekarang.

**Response gagal — `404`**: ID tidak ditemukan, atau notifikasi itu milik partner lain.

---

## `PATCH /notifications/read-all`

Tandai **semua** notifikasi milik partner yang login sebagai sudah dibaca sekaligus — cocok untuk tombol "Tandai semua dibaca".

**Request**: tanpa body.

**Response sukses — `200 OK`**

```json
{ "message": "Semua notifikasi ditandai sudah dibaca." }
```

Setelah ini, `GET /notifications/unread-count` akan mengembalikan `0`.
