# Marketing Materials (Marketing Center)

Materi promosi yang disediakan admin untuk dipakai partner: brosur, banner, company profile, template pesan WhatsApp/email, FAQ, dsb. **Read-only** — partner cuma bisa lihat/unduh, tidak bisa upload materi sendiri.

## Cara kerja

- Ada 11 kategori tetap: `brosur`, `company_profile`, `price_list`, `proposal`, `logo`, `banner`, `video`, `template_whatsapp`, `template_email`, `faq`, `selling_point`.
- **Dua jenis materi berbeda cara pakainya**:
  - **Berbasis file** (`brosur`, `company_profile`, `price_list`, `proposal`, `logo`, `banner`, `video`) — punya `download_url`, field `content` selalu `null`.
  - **Berbasis teks** (`template_whatsapp`, `template_email`, `faq`, `selling_point`) — punya `content` (teks/HTML siap-pakai, misal template pesan yang tinggal di-copy-paste partner), `download_url` selalu `null`.
  
  Cek `is_file_based` untuk menentukan cara render tanpa perlu hardcode daftar kategori mana yang file vs teks.
- **Berbeda dari dokumen lain di API ini**: `download_url` di sini adalah **link publik biasa** — bisa dibuka tanpa header `Authorization`, cocok dibagikan langsung ke calon customer lewat WhatsApp dsb. Materi berbasis file memang dirancang untuk disebarluaskan, beda dari KTP/NPWP/proposal lead yang privat.
- Materi yang `is_active = false` di sisi admin **tidak akan pernah muncul** lewat API ini sama sekali (bukan cuma disembunyikan, tapi memang tidak dikembalikan).

---

## `GET /marketing-materials`

**Query params**: `category` (opsional) — filter satu kategori saja.

**Response — `200 OK`** (dikelompokkan per kategori, **bukan** array pagination flat seperti modul lain)

```json
{
  "brosur": [
    {
      "id": 5,
      "title": "Brosur Layanan 2026",
      "category": "brosur",
      "category_label": "Brosur",
      "description": "Brosur ringkasan semua layanan.",
      "is_file_based": true,
      "download_url": "https://.../media/marketing/brosur-2026.pdf",
      "content": null
    }
  ],
  "template_whatsapp": [
    {
      "id": 9,
      "title": "Template Follow Up Awal",
      "category": "template_whatsapp",
      "category_label": "Template WhatsApp",
      "description": null,
      "is_file_based": false,
      "download_url": null,
      "content": "Halo Kak {nama}, terima kasih sudah tertarik dengan layanan kami..."
    }
  ]
}
```

Cuma kategori yang **punya minimal satu materi aktif** yang muncul sebagai key — kategori kosong tidak akan tampil sama sekali (beda dari [Pipeline](05-pipeline.md) yang selalu menampilkan 8 key tetap). Jangan asumsikan urutan atau keberadaan key tertentu; render berdasarkan key yang benar-benar ada di response.

---

## `GET /marketing-materials/{marketingMaterial}`

Detail satu materi. Bentuk sama seperti satu item di atas. `404` kalau tidak ada atau nonaktif.
