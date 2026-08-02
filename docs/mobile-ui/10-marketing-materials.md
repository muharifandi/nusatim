# Layar — Marketing Materials (Marketing Center)

API terkait: [docs/api/10-marketing-materials.md](../api/10-marketing-materials.md)

Diakses lewat tab **Profil** → "Marketing Center". Seluruhnya read-only.

## Daftar Materi

**Layout**: `TabRow` atau `ExpandableList` (accordion) per kategori — karena response API dikelompokkan per kategori dan **kategori kosong tidak muncul sama sekali** (lihat [docs/api/testing/10-marketing-materials.md](../api/testing/10-marketing-materials.md)), render kategori secara dinamis sesuai key yang benar-benar ada, jangan hardcode 11 kategori kalau sebagian besar sering kosong.

Rekomendasi: `LazyColumn` dengan `StickyHeader` per kategori (bukan tab horizontal — dengan hingga 11 kategori potensial, tab horizontal akan penuh sesak; scroll vertikal dengan header lengket lebih skalabel dan tetap memudahkan orientasi).

Tiap item, tampilan berbeda tergantung `is_file_based`:
- **Berbasis file** (brosur, banner, dst): `Card` dengan ikon jenis file besar (PDF/gambar/video) di kiri, judul + deskripsi di kanan, ikon unduh di ujung kanan.
- **Berbasis teks** (template WhatsApp/email, FAQ, selling point): `Card` dengan judul + preview 2 baris teks dari `content`, ikon "salin" (copy) langsung di kartu — aksi paling umum untuk jenis ini adalah **salin-tempel cepat ke WhatsApp**, jangan wajibkan buka detail dulu.

## Detail Materi

**Layout tergantung jenis**:
- **Berbasis file**: preview penuh (gambar → `Dialog` zoom, PDF → viewer bawaan, video → player), tombol "Unduh" / "Bagikan" (`Intent.ACTION_SEND` — karena `download_url` adalah link publik biasa, bisa langsung dibagikan sebagai link atau file yang sudah diunduh, lihat [docs/api/10-marketing-materials.md](../api/10-marketing-materials.md)).
- **Berbasis teks**: tampilkan `content` penuh (render sebagai teks/HTML sederhana kalau berisi markup dasar), tombol besar "Salin ke Clipboard" dan "Bagikan" (`Intent.ACTION_SEND` teks).
