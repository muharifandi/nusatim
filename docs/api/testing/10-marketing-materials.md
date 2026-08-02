# Skenario Test — Marketing Materials

Sumber: `tests/Feature/Api/MarketingMaterialApiTest.php` (4 test, semua hijau).

Lihat penjelasan fitur & contoh payload di [../10-marketing-materials.md](../10-marketing-materials.md).

| # | Skenario | Kondisi Awal | Aksi | Hasil yang Diharapkan |
|---|---|---|---|---|
| 1 | Cuma materi aktif yang muncul, dikelompokkan per kategori | 1 materi `brosur` aktif, 1 materi `brosur` **nonaktif**, 1 materi `faq` aktif (berbasis teks) | `GET /marketing-materials` | `200`. Key `brosur` berisi **1** item (yang nonaktif tidak muncul sama sekali), `download_url`-nya berupa link publik yang bisa diakses langsung. Key `faq` berisi 1 item dengan `content` terisi teks. |
| 2 | Filter kategori bekerja | 1 materi `brosur`, 1 materi `faq` | `GET /marketing-materials?category=faq` | `200`. Key `brosur` **tidak muncul sama sekali** di response (bukan array kosong — key-nya hilang). Key `faq` berisi 1 item. |
| 3 | Materi nonaktif tidak bisa dilihat detailnya | Materi ada tapi `is_active: false` | `GET /marketing-materials/{id}` | `404`. |
| 4 | Partner belum `approved` diblokir | Partner `pending_review` | `GET /marketing-materials` | `403`. |

## Catatan khusus untuk implementasi Android

- Skenario #2: **beda dari Pipeline** (yang selalu punya 8 key tetap termasuk yang kosong) — di sini kategori yang tidak punya materi aktif **tidak muncul sebagai key sama sekali** di response JSON. Kode parsing di Android harus mengecek keberadaan key sebelum mengakses (misal pakai `jsonObject.optJSONArray("brosur")` yang mengembalikan `null` kalau tidak ada, bukan asumsi array kosong).
- Skenario #1: `download_url` di modul ini adalah **link publik biasa**, beda dari dokumen KTP/proposal/withdrawal di modul lain — bisa langsung dibuka pakai `Intent.ACTION_VIEW` tanpa perlu menyertakan header `Authorization`. Aman juga untuk fitur "Bagikan ke WhatsApp" karena link-nya tetap bisa dibuka penerima yang bukan partner.
