# Commissions (Commission Management)

Riwayat komisi partner. **Modul ini seluruhnya read-only** — tidak ada endpoint create/update/delete sama sekali, sama seperti panel web (komisi murni dihasilkan otomatis oleh server, prosesnya digerakkan admin).

## Cara kerja

- Komisi digenerate otomatis oleh server saat sebuah [Customer](06-customers.md) closing (biasanya begitu lead jadi `won`) — jumlahnya dihitung dari skema komisi yang berlaku, aplikasi tidak perlu (dan tidak bisa) menghitung sendiri.
- Alur status komisi:

  ```
  pending → waiting_client_payment → approved → paid
                                          │
                                          └─ rejected (lihat rejection_reason)
  ```

- **`approved` adalah status yang penting untuk ditonjolkan di UI** — cuma komisi berstatus `approved` yang masuk ke `available_balance` pada modul [Withdrawals](09-withdrawals.md) (bisa dicairkan). Komisi `pending`/`waiting_client_payment` belum bisa ditarik, `paid` berarti sudah pernah dicairkan (lihat `markPaid` di [Withdrawals](09-withdrawals.md) — satu withdrawal bisa "melunasi" beberapa komisi approved sekaligus).
- `is_bonus`: `true` kalau ini komisi bonus (bukan dari transaksi customer biasa) — tampilkan badge berbeda kalau perlu.

---

## `GET /commissions`

Dipaginasi.

**Query params**:

| Param | Keterangan |
|---|---|
| `status` | Filter status persis, contoh `?status=approved` |
| `page`, `per_page` | Pagination |

**Response — `200 OK`**

```json
{
  "data": [
    {
      "id": 77,
      "customer_name": "Budi Santoso",
      "service_name": "Website Company Profile",
      "project_value": 15000000,
      "invoice_value": 15000000,
      "percentage": 10,
      "amount": 1500000,
      "type": "percentage",
      "is_bonus": false,
      "status": "approved",
      "rejection_reason": null,
      "note": null,
      "created_at": "2026-07-23T09:00:00.000000Z"
    }
  ]
}
```

`type` menjelaskan cara perhitungan (`flat`, `percentage`, `recurring_percentage`) — murni informasi, tidak memengaruhi cara aplikasi menampilkan `amount` (selalu tampilkan `amount` apa adanya, itu sudah nominal final Rupiah).

---

## `GET /commissions/{commission}`

Detail satu komisi. Bentuk sama seperti satu item di atas. `404` kalau tidak ada / bukan milik partner yang login.

Kalau `status` adalah `rejected`, field `rejection_reason` akan terisi — tampilkan ke partner supaya tahu kenapa komisinya ditolak.
