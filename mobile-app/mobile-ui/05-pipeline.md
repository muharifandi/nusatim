# Layar — Pipeline

API terkait: [docs/api/05-pipeline.md](../api/05-pipeline.md)

Bagian dari tab **Penjualan** (sub-tab "Pipeline", default terbuka pertama di tab ini — ini pandangan kerja sehari-hari yang paling sering dipakai).

## Papan Pipeline

**Layout**:
- `TopAppBar`: ikon filter kanan (produk/layanan, rentang tanggal — buka `BottomSheet` filter, memetakan ke `?service_id=`/`?date_from=`/`?date_to=`).
- Papan kanban **scroll horizontal**, 8 kolom (satu per status, urut `new` → `lost`). Tiap kolom: judul status + jumlah item, di bawahnya daftar kartu lead **scroll vertikal independen** per kolom.
- Kartu lead di papan: versi ringkas dari kartu di Daftar Lead (nama, telepon, nilai estimasi) — tap kartu → **Detail Lead**.

**Interaksi pindah status — adaptasi mobile, bukan drag-and-drop literal**

Drag-and-drop lintas kolom yang scroll horizontal secara bersamaan **sulit dikerjakan dengan baik di layar sentuh** (gestur tarik vs gestur scroll konflik). Rekomendasi pola yang lebih ergonomis untuk mobile:

- **Long-press kartu** → kartu sedikit terangkat (elevasi naik, skala membesar tipis) → muncul `BottomSheet` "Pindahkan ke..." berisi 7 pilihan status lain (status saat ini tidak ditampilkan sebagai pilihan) → tap pilihan → `PATCH /leads/{id}/status` → kartu berpindah kolom dengan animasi.
- Alternatif/tambahan kalau tim desain tetap ingin gestur drag: aktifkan drag **hanya di dalam kolom yang sedang di-scroll saat itu** (freeze horizontal scroll selama drag aktif), lalu tampilkan indikator kolom tujuan di tepi layar untuk auto-scroll horizontal saat kartu didekatkan ke tepi.

**State kosong per kolom**: teks kecil abu-abu "Belum ada lead" di tengah kolom kosong (bukan kolom yang hilang — API selalu mengembalikan 8 key meski isinya array kosong, lihat [docs/api/testing/05-pipeline.md](../api/testing/05-pipeline.md)).

**Sinkronisasi dengan Daftar Lead**: setelah pindah status dari sini, invalidasi cache Daftar Lead juga (kalau partner habis ini berpindah ke sub-tab "Lead", datanya harus sudah konsisten) — kedua tampilan berbagi sumber data yang sama.
